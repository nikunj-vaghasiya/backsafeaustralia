<?php
namespace Digital\BannerSlider\Api;

use Digital\BannerSlider\Api\Data\BannerInterface;

/**
 * Interface BannerRepositoryInterface
 *
 */
interface BannerRepositoryInterface
{
    /**
     * Get Banner by Id
     *
     * @param string  $bannerId
     * @param boolean $force
     * @return null|BannerInterface
     */
    public function get($bannerId, $force = false);

    /**
     * Delete Banner
     *
     * @param BannerInterface $banner
     * @return boolean
     */
    public function delete(BannerInterface $banner);

    /**
     * Save Banner
     *
     * @param BannerInterface $banner
     * @return BannerInterface
     */
    public function save(BannerInterface $banner);
}
