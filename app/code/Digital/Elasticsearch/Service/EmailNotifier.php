<?php
declare(strict_types=1);

namespace Digital\Elasticsearch\Service;

use Digital\Elasticsearch\Logger\Logger;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sends the "Elasticsearch Health Check Alert" email via Magento's Mail
 * framework (TransportBuilder). Fully self-contained: any failure is caught and
 * logged so the caller (cron) can never fail because of email problems.
 *
 * All dependencies are verified present in Magento 2.4.5-p1.
 */
class EmailNotifier
{
    private const TEMPLATE_ID = 'digital_elasticsearch_health_alert';

    private TransportBuilder $transportBuilder;
    private StateInterface $inlineTranslation;
    private StoreManagerInterface $storeManager;
    private Logger $logger;
    private string $recipientEmail;
    private string $recipientName;
    private string $senderIdentity;

    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        Logger $logger,
        string $recipientEmail = '',
        string $recipientName = 'Administrator',
        string $senderIdentity = 'general'
    ) {
        $this->transportBuilder  = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager      = $storeManager;
        $this->logger            = $logger;
        $this->recipientEmail    = $recipientEmail;
        $this->recipientName     = $recipientName;
        $this->senderIdentity    = $senderIdentity;
    }

    /**
     * @param string   $errorMessage The actual ES error (e.g. "No alive nodes found in your cluster")
     * @param string   $searchEngine The configured search engine
     * @param string   $detectedAt   Human-readable detection timestamp
     * @param string[] $indexers     Indexer ids that were reset
     */
    public function sendDownAlert(
        string $errorMessage,
        string $searchEngine,
        string $detectedAt,
        array $indexers
    ): void {
        if ($this->recipientEmail === '') {
            $this->logger->error('ES health alert email not sent: recipient is not configured.');
            return;
        }

        try {
            $storeId = $this->resolveStoreId();

            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder
                ->setTemplateIdentifier(self::TEMPLATE_ID)
                ->setTemplateOptions([
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ])
                ->setTemplateVars([
                    'errorMessage' => $errorMessage,
                    'searchEngine' => $searchEngine,
                    'detectedAt'   => $detectedAt,
                    'indexerList'  => implode(', ', $indexers),
                    'indexerCount' => count($indexers),
                ])
                ->setFromByScope($this->senderIdentity, $storeId)
                ->addTo($this->recipientEmail, $this->recipientName)
                ->getTransport();

            $transport->sendMessage();

            $this->logger->info('ES health alert email sent to ' . $this->recipientEmail);
        } catch (\Throwable $e) {
            // Never let email problems affect the cron workflow.
            $this->logger->error('Failed to send ES health alert email: ' . $e->getMessage());
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * Resolve a frontend store id for template rendering (cron has no active store).
     */
    private function resolveStoreId(): int
    {
        try {
            $store = $this->storeManager->getDefaultStoreView();
            if ($store !== null) {
                return (int) $store->getId();
            }
        } catch (\Throwable $e) {
            $this->logger->error('Unable to resolve default store view: ' . $e->getMessage());
        }

        return (int) Store::DISTRO_STORE_ID;
    }
}
