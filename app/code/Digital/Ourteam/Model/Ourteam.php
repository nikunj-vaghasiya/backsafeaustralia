<?php

namespace Digital\Ourteam\Model;

use Digital\Ourteam\Api\Data\OurteamInterface;
use Digital\Ourteam\Model\ResourceModel\Ourteam as OurteamResourceModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Ourteam Model
 *
 * @method \Digital\Ourteam\Model\Resource\Page _getResource()
 * @method \Digital\Ourteam\Model\Resource\Page getResource()
 */
class Ourteam extends AbstractModel implements OurteamInterface, IdentityInterface
{

    /**
     * Sub directory image
     *
     * @const
     */
    public const ENTITY_MEDIA_PATH = 'digital/ourteam';

    /**
     * @var string
     */
    protected $_eventPrefix = 'digital_team';

    /**
     * @var string
     */
    protected $_eventObject = 'team';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * Banner constructor.
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
        $this->_init(OurteamResourceModel::class);
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
     * Get Image Url
     *
     * @return string|boolean
     * @since 1.0.0
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImage()
    {
        return $this->getImageUrl($this->_getData(self::IMAGE));
    }

    /**
     * Set Image 
     *
     * @param string $imageName
     *
     * @return $this
     * @since 1.0.0
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Get Name
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    /**
     * Set Name
     *
     * @param string $title
     *
     * @return $this
     * @since 1.0.0
     */
    public function setName($title)
    {
        return $this->setData(self::NAME);
    }

    /**
     * Get Designation
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getDesignation()
    {
        return $this->_getData(self::DESIGNATION);
    }

    /**
     * Set Designation
     *
     * @param string $designation
     *
     * @return $this
     * @since 1.0.0
     */
    public function setDesignation($designation)
    {
        return $this->setData(self::DESIGNATION, $designation);
    }

    /**
     * Get Status
     *
     * @return string|null
     * @since 1.0.0
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param string $status
     *
     * @return $this
     * @since 1.0.0
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Description
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
     * Return Check Identifier
     *
     * @param string $identifier
     * @param string $storeId
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }
}
