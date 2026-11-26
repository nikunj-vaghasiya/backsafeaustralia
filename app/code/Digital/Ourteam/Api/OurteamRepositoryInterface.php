<?php
namespace Digital\Ourteam\Api;

use Digital\Ourteam\Api\Data\OurteamInterface;

/**
 * Interface OurteamRepositoryInterface
 *
 */
interface OurteamRepositoryInterface
{
    /**
     * Get Team by Id
     *
     * @param string  $teamId
     * @param boolean $force
     * @return null|OurteamInterface
     */
    public function get($teamId, $force = false);

    /**
     * Delete Team
     *
     * @param OurteamInterface $team
     * @return boolean
     */
    public function delete(OurteamInterface $team);

    /**
     * Save Team
     *
     * @param OurteamInterface $team
     * @return OurteamInterface
     */
    public function save(OurteamInterface $team);
}
