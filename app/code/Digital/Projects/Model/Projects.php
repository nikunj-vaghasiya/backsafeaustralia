<?php
namespace Digital\Projects\Model;

use Digital\Projects\Api\Data\ProjectsInterface;
use Digital\Projects\Model\ResourceModel\Projects as ProjectsResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Projects extends AbstractModel implements ProjectsInterface, IdentityInterface
{

    /**
     * Sub directory image
     *
     * @const
     */

    /* @codingStandardsIgnoreStart */
    const ENTITY_MEDIA_PATH = '/digital/projects';
    /* @codingStandardsIgnoreEnd */

    /**
     * @var string
     */
    protected $_eventPrefix = 'digital_projects';

    /**
     * @var string
     */
    protected $_eventObject = 'projects';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * Projects constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param StoreManagerInterface $storeManager
     * @param ImageUploader         $imageUploader
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ImageUploader $imageUploader,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeManager = $storeManager;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ProjectsResourceModel::class);
    }

    /**
     * Get Image Url
     *
     * @param image $image
     * @return boolean|string
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($image)
    {
        $url = false;
        if ($image) {
            if (is_string($image)) {
                /** @var \Magento\Store\Model\Store $store */
                $store = $this->storeManager->getStore();
                $mediaBaseUrl = $store->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                );
                $url = $mediaBaseUrl
                . ltrim(self::ENTITY_MEDIA_PATH, '/')
                    . '/'
                    . $image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }

        return $url;
    }

    /**
     * Get Title
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE);
    }

    /**
     * Get Status
     *
     * @return boolean
     * @since 1.0.0
     */
    public function getStatus()
    {
        return (boolean) $this->_getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param integer|boolean $status
     *
     * @return $this
     * @since 1.0.0
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Projects Url
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getProjectsUrl()
    {
        return $this->_getData(self::BANNER_URL);
    }

    /**
     * Set Projects Url
     *
     * @param string $url
     *
     * @return $this
     * @since 1.0.0
     */
    public function setProjectsUrl($url)
    {
        return $this->setData(self::BANNER_URL, $url);
    }

    /**
     * Get Projects Description
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getDescription()
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * Set Description
     *
     * @param string $description
     *
     * @return $this
     * @since 1.0.0
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get Image Listing Url
     *
     * @return string|boolean
     * @since 1.0.0
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageListingUrl()
    {
        return $this->getImageUrl($this->_getData(self::IMAGE_LISTING));
    }

    /**
     * Set Image Listing
     *
     * @param string $imageName
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImageListing($imageName)
    {
        return $this->setData(self::IMAGE_LISTING, $imageName);
    }

    /**
     * Get Image Desktop Url
     *
     * @return string|boolean
     * @since 1.0.0
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageDesktopUrl()
    {
        return $this->getImageUrl($this->_getData(self::IMAGE_DESKTOP));
    }

    /**
     * Set Image Desktop
     *
     * @param string $imageName
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImageDesktop($imageName)
    {
        return $this->setData(self::IMAGE_DESKTOP, $imageName);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        $identities = [
            self::CACHE_TAG . '_' . $this->getId(),
        ];

        if (!$this->getId() || $this->isDeleted()) {
            $identities[] = self::CACHE_TAG;
        }

        return array_unique($identities);
    }

    /**
     * Check identifier
     *
     * @param identifier $identifier
     * @param storeid $storeId
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array) $this->getData('store_id');
    }
}
