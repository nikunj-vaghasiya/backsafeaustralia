<?php
namespace Digital\Homeboxes\Api;

use Digital\Homeboxes\Api\Data\BoxesInterface;

/**
 * Interface BoxesRepositoryInterface
 *
 */
interface BoxesRepositoryInterface
{
    /**
     * Get Boxes by Id
     *
     * @param string  $boxesId
     * @param boolean $force
     * @return null|BoxesInterface
     */
    public function get($boxesId, $force = false);

    /**
     * Delete Boxes
     *
     * @param BoxesInterface $boxes
     * @return boolean
     */
    public function delete(BoxesInterface $boxes);

    /**
     * Save Boxes
     *
     * @param BoxesInterface $boxes
     * @return BoxesInterface
     */
    public function save(BoxesInterface $boxes);
}
