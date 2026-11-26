<?php
namespace Digital\Projects\Api;

use Digital\Projects\Api\Data\ProjectsInterface;

/**
 * Interface ProjectsRepositoryInterface
 *
 */
interface ProjectsRepositoryInterface
{
    /**
     * Get Projects by Id
     *
     * @param string  $projectsId
     * @param boolean $force
     * @return null|ProjectsInterface
     */
    public function get($projectsId, $force = false);

    /**
     * Delete Projects
     *
     * @param ProjectsInterface $projects
     * @return boolean
     */
    public function delete(ProjectsInterface $projects);

    /**
     * Save Projects
     *
     * @param ProjectsInterface $projects
     * @return ProjectsInterface
     */
    public function save(ProjectsInterface $projects);
}
