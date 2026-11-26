<?php

namespace Digital\Homeboxes\Model;

use Digital\Homeboxes\Api\BoxesRepositoryInterface;
use Digital\Homeboxes\Api\Data\BoxesInterface;
use Digital\Homeboxes\Api\Data\BoxesInterfaceFactory;
use Digital\Homeboxes\Model\ResourceModel\Boxes as ResourceBoxes;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class BoxesRepository implements BoxesRepositoryInterface
{
    /**
     * @var Boxes[]
     */
    protected $instances = [];

    /**
     * @var ResourceBoxes
     */
    private $resource;

    /**
     * @var BoxesInterfaceFactory
     */
    private $boxesFactory;

    /**
     * BoxesRepository constructor.
     *
     * @param ResourceBoxes         $resource
     * @param BoxesInterfaceFactory $boxesFactory
     */
    public function __construct(
        ResourceBoxes $resource,
        BoxesInterfaceFactory $boxesFactory
    ) {

        $this->resource = $resource;
        $this->boxesFactory = $boxesFactory;
    }

    /**
     * Get Boxes by Id
     *
     * @param string  $boxesId
     * @param boolean $force
     * @return null|BoxesInterface
     * @throws NoSuchEntityException
     */
    public function get($boxesId, $force = false)
    {
        $cacheKey = 'all';
        if (!isset($this->instances[$boxesId][$cacheKey]) || $force) {
            /** @var Boxes $boxes */
            $boxes = $this->boxesFactory->create();
            $boxes->load($boxesId);
            if (!$boxes->getId()) {
                throw NoSuchEntityException::singleField('id', $boxesId);
            }
            $this->instances[$boxesId][$cacheKey] = $boxes;
        }
        return $this->instances[$boxesId][$cacheKey];
    }

    /**
     * Delete Boxes
     *
     * @param Boxes|BoxesInterface $boxes
     * @return boolean
     * @throws StateException
     */
    public function delete(BoxesInterface $boxes)
    {
        try {
            $boxesId = $boxes->getId();
            $this->resource->delete($boxes);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete category with id %1',
                    $boxes->getId()
                ),
                $e
            );
        }
        unset($this->instances[$boxesId]);

        return true;
    }

    /**
     * Save Boxes
     *
     * @param Boxes|BoxesInterface $boxes
     * @return BoxesInterface|null
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(BoxesInterface $boxes)
    {
        $existingData = $boxes->getData();

        if ($boxes->getId()) {
            $boxes = $this->get($boxes->getId());
        }
        $boxes->addData($existingData);
        try {
            $this->resource->save($boxes);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save boxes: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$boxes->getId()]);
        return $this->get($boxes->getId());
    }
}
