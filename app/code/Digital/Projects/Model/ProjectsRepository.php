<?php
namespace Digital\Projects\Model;

use Digital\Projects\Api\ProjectsRepositoryInterface;
use Digital\Projects\Api\Data\ProjectsInterface;
use Digital\Projects\Api\Data\ProjectsInterfaceFactory;
use Digital\Projects\Model\ResourceModel\Projects as ResourceProjects;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class ProjectsRepository implements ProjectsRepositoryInterface
{
    /**
     * @var Projects[]
     */
    protected $instances = [];

    /**
     * @var ResourceProjects
     */
    private $resource;

    /**
     * @var ProjectsInterfaceFactory
     */
    private $projectsFactory;

    /**
     * projectsRepository constructor.
     *
     * @param ResourceProjects         $projects
     * @param ProjectsInterfaceFactory $projectsFactory
     */
    public function __construct(
        ResourceProjects $resource,
        ProjectsInterfaceFactory $projectsFactory
    ) {

        $this->resource = $resource;
        $this->projectsFactory = $projectsFactory;
    }

    /**
     * Get Projects by Id
     *
     * @param string  $projectsId
     * @param boolean $force
     * @return null|ProjectsInterface
     * @throws NoSuchEntityException
     */
    public function get($projectsId, $force = false)
    {
        $cacheKey = 'all';
        if (!isset($this->instances[$projectsId][$cacheKey]) || $force) {
            /** @var Projects $projects */
            $projects = $this->projectsFactory->create();
            $projects->load($projectsId);
            if (!$projects->getId()) {
                throw NoSuchEntityException::singleField('id', $projectsId);
            }
            $this->instances[$projectsId][$cacheKey] = $projects;
        }
        return $this->instances[$projectsId][$cacheKey];
    }

    /**
     * Delete Projects
     *
     * @param Projects|ProjectsInterface $projects
     * @return boolean
     * @throws StateException
     */
    public function delete(ProjectsInterface $projects)
    {
        try {
            $projectsId = $projects->getId();
            $this->resource->delete($projects);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete category with id %1',
                    $projects->getId()
                ),
                $e
            );
        }
        unset($this->instances[$projectsId]);

        return true;
    }

    /**
     * Save Projects
     *
     * @param Projects|ProjectsInterface $projects
     * @return ProjectsInterface|null
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(ProjectsInterface $projects)
    {
        $existingData = $projects->getData();

        if ($projects->getId()) {
            $projects = $this->get($projects->getId());
        }
        $projects->addData($existingData);
        try {
            $this->resource->save($projects);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save projects: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$projects->getId()]);
        return $this->get($projects->getId());
    }
}
