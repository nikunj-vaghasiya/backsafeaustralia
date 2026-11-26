<?php
declare(strict_types=1);

namespace Digital\Elasticsearch\Service;

use Digital\Elasticsearch\Logger\Logger;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Search\EngineResolverInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sends the "Elasticsearch is down" alert email.
 *
 * Fully self-contained: every failure path is caught and logged, so a mail
 * problem can NEVER break the cron / health-check flow. This class is only
 * invoked by Cron\EsHealthCheck at first detection (the point where the flag
 * is created and indexers are invalidated), so it is not sent on repeat runs.
 *
 * Dependencies verified for Magento 2.4.5-p1:
 *   - Magento\Framework\Mail\Template\TransportBuilder
 *   - Magento\Store\Model\StoreManagerInterface
 *   - Magento\Framework\Search\EngineResolverInterface
 *   - Magento\Framework\Stdlib\DateTime\TimezoneInterface
 */
class AlertMailer
{
    private const TEMPLATE_ID      = 'digital_elasticsearch_alert';
    private const SENDER_IDENTITY  = 'general';

    private const EMAIL_TO = 'nikunj.vaghasiya@yodep.co.in';
    private const EMAIL_CC = [
        'dhrumil@23digital.com.au',
        'divyesh.nimavat@yodep.co.in',
        'rohit.ranpara@yodep.co.in',
    ];

    private TransportBuilder $transportBuilder;
    private StateInterface $inlineTranslation;
    private StoreManagerInterface $storeManager;
    private EngineResolverInterface $engineResolver;
    private TimezoneInterface $timezone;
    private Logger $logger;

    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        EngineResolverInterface $engineResolver,
        TimezoneInterface $timezone,
        Logger $logger
    ) {
        $this->transportBuilder  = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager      = $storeManager;
        $this->engineResolver   = $engineResolver;
        $this->timezone         = $timezone;
        $this->logger           = $logger;
    }

    /**
     * Build and send the down-alert email. Exceptions are never propagated.
     *
     * @param string $errorMessage The actual ES error message (e.g.
     *                             "No alive nodes found in your cluster").
     */
    public function sendDownAlert(string $errorMessage): void
    {
        try {
            $this->inlineTranslation->suspend();
            $storeId = $this->resolveStoreId();

            $templateVars = [
                'errorMessage'   => $errorMessage,
                'indexerMessage' => 'All Magento indexers have been reset (invalidated).',
                'detectedAt'     => $this->timezone->date()->format('Y-m-d H:i:s'),
                'searchEngine'   => (string) $this->engineResolver->getCurrentSearchEngine(),
            ];

            $this->transportBuilder
                ->setTemplateIdentifier(self::TEMPLATE_ID)
                ->setTemplateOptions([
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ])
                ->setTemplateVars($templateVars)
                ->setFromByScope(self::SENDER_IDENTITY, $storeId)
                ->addTo(self::EMAIL_TO);

            foreach (self::EMAIL_CC as $ccAddress) {
                $this->transportBuilder->addCc($ccAddress);
            }

            $this->transportBuilder->getTransport()->sendMessage();

            $this->logger->info('Elasticsearch down-alert email sent to ' . self::EMAIL_TO . '.');
        } catch (\Throwable $e) {
            // Graceful: log and continue. The cron must never fail on email issues.
            $this->logger->error('Failed to send Elasticsearch down-alert email: ' . $e->getMessage());
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    private function resolveStoreId(): int
    {
        try {
            $store = $this->storeManager->getDefaultStoreView();
            if ($store !== null) {
                return (int) $store->getId();
            }
        } catch (\Throwable $e) {
            $this->logger->error('Unable to resolve default store view for email: ' . $e->getMessage());
        }

        return (int) Store::DEFAULT_STORE_ID;
    }
}
