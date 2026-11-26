<?php
namespace Digital\Ourteam\Model;

use Digital\Ourteam\Api\OurteamRepositoryInterface;
use Digital\Ourteam\Api\Data\OurteamInterface;
use Digital\Ourteam\Api\Data\OurteamInterfaceFactory;
use Digital\Ourteam\Model\ResourceModel\Ourteam as ResourceOurteam;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class OurteamRepository implements OurteamRepositoryInterface
{
    /**
     * @var Ourteam[]
     */
    protected $instances = [];

    /**
     * @var ResourceOurteam
     */
    private $resource;

    /**
     * @var OurteamInterfaceFactory
     */
    private $teamFactory;

    /**
     * OurteamRepository constructor.
     *
     * @param ResourceOurteam         $resource
     * @param OurteamInterfaceFactory $teamFactory
     */
    public function __construct(
        ResourceOurteam $resource,
        OurteamInterfaceFactory $teamFactory
    ) {

        $this->resource = $resource;
        $this->teamFactory = $teamFactory;
    }

    /**
     * Get Team by Id
     *
     * @param string  $teamId
     * @param boolean $force
     * @return null|OurteamInterface
     * @throws NoSuchEntityException
     */
    public function get($teamId, $force = false)
    {
        $cacheKey = 'all';
        if (!isset($this->instances[$teamId][$cacheKey]) || $force) {
            /** @var Team $team */
            $team = $this->teamFactory->create();
            $team->load($teamId);
            if (!$team->getId()) {
                throw NoSuchEntityException::singleField('id', $teamId);
            }
            $this->instances[$teamId][$cacheKey] = $team;
        }
        return $this->instances[$teamId][$cacheKey];
    }

    /**
     * Delete team
     *
     * @param team|OurteamInterface $team
     * @return boolean
     * @throws StateException
     */
    public function delete(OurteamInterface $team)
    {
        try {
            $teamId = $team->getId();
            $this->resource->delete($team);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete category with id %1',
                    $team->getId()
                ),
                $e
            );
        }
        unset($this->instances[$teamId]);

        return true;
    }

    /**
     * Save team
     *
     * @param team|OurteamInterface $team
     * @return OurteamInterface|null
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(OurteamInterface $team)
    {
        $existingData = $team->getData();

        if ($team->getId()) {
            $team = $this->get($team->getId());
        }
        $team->addData($existingData);
        try {
            $this->resource->save($team);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save team: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$team->getId()]);
        return $this->get($team->getId());
    }
}
