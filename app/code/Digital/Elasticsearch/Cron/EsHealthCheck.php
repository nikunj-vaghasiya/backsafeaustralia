<?php
declare(strict_types=1);

namespace Digital\Elasticsearch\Cron;

use Digital\Elasticsearch\Logger\Logger;
use Digital\Elasticsearch\Service\AlertMailer;
use Digital\Elasticsearch\Service\FlagFileService;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Framework\Indexer\ConfigInterface as IndexerConfig;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Search\EngineResolverInterface;

/**
 * Runs every 5 minutes (etc/crontab.xml) and is the ONLY place this module
 * touches Elasticsearch. It never executes during a storefront/admin request,
 * so the catalog product collection, layered navigation, search filters,
 * sorting, pagination, visibility and stock filters are completely unaffected.
 *
 * State machine (unchanged):
 *   - ES unreachable AND no flag yet -> write flag, reset (invalidate) all
 *     indexers, send alert email, log it.
 *   - ES unreachable AND flag already exists -> do nothing (already handled).
 *   - ES healthy AND flag exists -> recovery: remove flag, log it.
 *   - ES healthy AND no flag -> nothing to do.
 *   - Non-Elasticsearch engine configured -> inert.
 *
 * Dependencies (all verified present in Magento 2.4.5-p1):
 *   - Magento\Framework\Search\EngineResolverInterface::getCurrentSearchEngine()
 *   - Magento\Elasticsearch\SearchAdapter\ConnectionManager::getConnection()
 *   - Magento\AdvancedSearch\Model\Client\ClientInterface::testConnection()
 *   - Magento\Framework\Indexer\ConfigInterface::getIndexers()
 *   - Magento\Framework\Indexer\IndexerRegistry::get()->invalidate()
 */
class EsHealthCheck
{
    private FlagFileService $flagFileService;
    private ConnectionManager $connectionManager;
    private EngineResolverInterface $engineResolver;
    private IndexerConfig $indexerConfig;
    private IndexerRegistry $indexerRegistry;
    private Logger $logger;
    private AlertMailer $alertMailer;

    /**
     * Captured ES error message from the most recent failed connection test.
     */
    private string $lastErrorMessage = '';

    public function __construct(
        FlagFileService $flagFileService,
        ConnectionManager $connectionManager,
        EngineResolverInterface $engineResolver,
        IndexerConfig $indexerConfig,
        IndexerRegistry $indexerRegistry,
        Logger $logger,
        AlertMailer $alertMailer
    ) {
        $this->flagFileService   = $flagFileService;
        $this->connectionManager = $connectionManager;
        $this->engineResolver    = $engineResolver;
        $this->indexerConfig     = $indexerConfig;
        $this->indexerRegistry   = $indexerRegistry;
        $this->logger            = $logger;
        $this->alertMailer       = $alertMailer;
    }

    public function execute(): void
    {
        // Only act if the store actually uses an Elasticsearch engine.
        $engine = (string) $this->engineResolver->getCurrentSearchEngine();
        if (stripos($engine, 'elasticsearch') === false) {
            return;
        }

        try {
            if ($this->isElasticsearchAlive()) {
                $this->handleHealthy();
            } else {
                $this->handleDown();
            }
        } catch (\Throwable $e) {
            $this->logger->error('Health-check handling failed: ' . $e->getMessage());
        }
    }

    /**
     * Reachability check via Magento's own configured connection.
     * testConnection() is declared on Magento\AdvancedSearch\Model\Client\ClientInterface,
     * so this is type-safe across ES5/6/7 adapters. Any error (including
     * NoNodesAvailableException) is treated as "not alive".
     */
    private function isElasticsearchAlive(): bool
    {
        try {
            $this->lastErrorMessage = '';
            return (bool) $this->connectionManager->getConnection()->testConnection();
        } catch (\Throwable $e) {
            $this->lastErrorMessage = $e->getMessage();
            $this->logger->error('Elasticsearch connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    private function handleDown(): void
    {
        if ($this->flagFileService->exists()) {
            // Already detected and handled on a previous run; avoid repeat work/log/email.
            return;
        }

        $errorMessage = $this->lastErrorMessage !== ''
            ? $this->lastErrorMessage
            : FlagFileService::FLAG_MESSAGE;

        $this->logger->error('Elasticsearch has no alive nodes. Writing flag and resetting indexers.');
        $this->flagFileService->create();
        $this->resetIndexers();

        // Additive: notify on first detection only. handleDown() returns early
        // once the flag exists, so this runs exactly once per outage episode.
        $this->alertMailer->sendDownAlert($errorMessage);
    }

    private function handleHealthy(): void
    {
        if ($this->flagFileService->exists()) {
            $this->logger->info('Elasticsearch is healthy again. Clearing node-error flag.');
            $this->flagFileService->delete();
        }
    }

    /**
     * Programmatic equivalent of "php bin/magento indexer:reset" (invalidate all indexers).
     */
    private function resetIndexers(): void
    {
        $reset = [];
        foreach (array_keys($this->indexerConfig->getIndexers()) as $indexerId) {
            $this->indexerRegistry->get($indexerId)->invalidate();
            $reset[] = $indexerId;
        }
        $this->logger->info('Indexers reset (invalidated): ' . implode(', ', $reset));
    }
}
