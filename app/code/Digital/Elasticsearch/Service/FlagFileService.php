<?php
declare(strict_types=1);

namespace Digital\Elasticsearch\Service;

use Digital\Elasticsearch\Logger\Logger;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class FlagFileService
{
    public const FLAG_FILE    = 'elasticsearch_node_error.flag';
    public const FLAG_MESSAGE = 'No alive nodes found in your cluster';

    private Filesystem $filesystem;
    private Logger $logger;

    public function __construct(Filesystem $filesystem, Logger $logger)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * Create/overwrite var/elasticsearch_node_error.flag
     */
    public function create(): void
    {
        try {
            $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $varDir->writeFile(self::FLAG_FILE, self::FLAG_MESSAGE);
        } catch (\Throwable $e) {
            $this->logger->error('Unable to create ES node-error flag: ' . $e->getMessage());
        }
    }

    public function exists(): bool
    {
        $varDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        return $varDir->isExist(self::FLAG_FILE);
    }

    /**
     * True only if the flag exists AND its content carries the expected signature.
     */
    public function hasErrorSignature(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        $varDir   = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $contents = (string) $varDir->readFile(self::FLAG_FILE);

        return str_contains($contents, self::FLAG_MESSAGE);
    }

    public function delete(): void
    {
        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        if ($varDir->isExist(self::FLAG_FILE)) {
            $varDir->delete(self::FLAG_FILE);
        }
    }
}
