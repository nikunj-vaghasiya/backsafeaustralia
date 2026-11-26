<?php
namespace Digital\Projects\Api\Data;

/**
 * Interface ProjectsInterface
 *
 */
interface ProjectsInterface
{
    /* @codingStandardsIgnoreStart */

    const CACHE_TAG = 'digital_projects';

    const BANNER_ID = 'projetcs_id';

    const TITLE = 'title';

    const STATUS = 'status';

    const BANNER_URL = 'projetcs_id';

    const DESCRIPTION = 'description';

    const IMAGE_DESKTOP = 'image_desktop';

    const IMAGE_LISTING = 'image_listing';

    /* @codingStandardsIgnoreEnd */

    /**
     * Get Projects Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getId();

    /**
     * Get Title
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getTitle();

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setTitle($title);

    /**
     * Get Status
     *
     * @return boolean
     * @since 1.0.0
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param integer|boolean $status
     *
     * @return $this
     * @since 1.0.0
     */
    public function setStatus($status);

    /**
     * Get Projects Url
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getProjectsUrl();

    /**
     * Set Projects Url
     *
     * @param string $url
     *
     * @return $this
     * @since 1.0.0
     */
    public function setProjectsUrl($url);

    /**
     * Get Projects Description
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $description
     *
     * @return $this
     * @since 1.0.0
     */
    public function setDescription($description);

    public function getImageListingUrl();

    /**
     * Set Image Listing
     *
     * @param string $imageName
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImageListing($imageName);

    public function getImageDesktopUrl();

    /**
     * Set Image Desktop
     *
     * @param string $imageName
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImageDesktop($imageName);

}
