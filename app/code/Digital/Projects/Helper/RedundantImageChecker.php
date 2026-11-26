<?php
namespace Digital\Projects\Helper;

use Digital\Projects\Model\ImageField;
use Digital\Projects\Model\ImageUploader;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class RedundantImageChecker
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * RedundantImageChecker constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param ImageUploader      $imageUploader
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ImageUploader $imageUploader
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $resourceConnection->getConnection();
        $this->imageUploader = $imageUploader;
    }

    /**
     * Delete the image un-used
     *
     * @param image $image
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function process($image)
    {
        if ($image && is_string($image) && $this->isRedundant($image)) {
            $this->imageUploader->deleteImageFile($image);
        }
    }

    /**
     * Checking Image Unused
     *
     * @param string $image
     * @return boolean
     */
    private function isRedundant($image)
    {
        $connection = $this->connection;
        $conditionArray = [];
        foreach (ImageField::getField() as $field) {
            $conditionArray[] = $connection->quoteInto($field . ' = ?', $image);
        }
        $conditions = join(' OR ', $conditionArray);
        $select = $connection->select()
            ->from($connection->getTableName('digital_projects'), 'projects_id')
            ->where($conditions)
            ->limit(1);

        return ((int) $connection->fetchOne($select)) < 1;
    }
}
