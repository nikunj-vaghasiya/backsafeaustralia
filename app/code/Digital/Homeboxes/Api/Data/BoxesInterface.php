<?php
namespace Digital\Homeboxes\Api\Data;

/**
 * Interface BoxesInterface
 *
 */
interface BoxesInterface
{
    /* @codingStandardsIgnoreStart */

    const CACHE_TAG = 'digital_boxes';

    const BANNER_ID = 'boxes_id';

    const TITLE = 'title';

    const STATUS = 'status';

    const BANNER_URL = 'boxes_url';

    const DESCRIPTION = 'description';

    const BUTTON_TEXT = 'button_text';

    const IMAGE_DESKTOP = 'image_desktop';

    const IMAGE_TABLET = 'image_tablet';

    const IMAGE_MOBILE = 'image_mobile';

    /* @codingStandardsIgnoreEnd */

    /**
     * Get Boxes Id
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
     * Get Boxes Url
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getBoxesUrl();

    /**
     * Set Boxes Url
     *
     * @param string $url
     *
     * @return $this
     * @since 1.0.0
     */
    public function setBoxesUrl($url);

    /**
     * Get Boxes Description
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

    /**
     * Get Button Text
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getButtonText();

    /**
     * Set Button Text
     *
     * @param string $text
     *
     * @return $this
     * @since 1.0.0
     */
    public function setButtonText($text);

    /**
     * Get Image Desktop Url
     *
     * @return string|boolean
     * @since 1.0.0
     */
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

    /**
     * Get Image Tablet Url
     *
     * @return string|boolean
     * @since 1.0.0
     */
    public function getImageTabletUrl();

    /**
     * Set Image Tablet
     *
     * @param string $imageName
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImageTablet($imageName);

    /**
     * Get Image Mobile Url
     *
     * @return string|boolean
     * @since 1.0.0
     */
    public function getImageMobileUrl();

    /**
     * Set Image Mobile
     *
     * @param string $imageName
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImageMobile($imageName);
}
