<?php
namespace Digital\Ourteam\Api\Data;

/**
 * Interface OurteamInterface
 *
 */
interface OurteamInterface
{
    public const CACHE_TAG = 'digital_ourteam';

    public const TEAM_ID = 'team_id';

    public const NAME = 'name';

    public const DESIGNATION = 'designation';

    public const DESCRIPTION = 'description';

    public const IMAGE = 'image';

    public const STATUS = 'status';

    public const SORT_ORDER = 'sort_order';

    /**
     * Get Banner Id
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getId();

    /**
     * Get Name
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return $this
     * @since 1.0.0
     */
    public function setName($name);

    /**
     * Get Image
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getImage();

    /**
     * Set Image
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImage($Image);

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
     * Get Team Designation
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getDesignation();

    /**
     * Set Team Designation
     *
     * @param string $url
     *
     * @return $this
     * @since 1.0.0
     */
    public function setDesignation($designation);

    /**
     * Get Team Description
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

}
