<?php
namespace Digital\BannerSlider\Helper;

use Digital\BannerSlider\Model\ImageUploader;
use Digital\Base\Helper\Data as BaseHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /* @codingStandardsIgnoreStart */
    const XML_PATH_BUTTON_TEXT = 'bannerslider_section/general/button_text';

    const THUMBNAIL_WIDTH = 75;

    const THUMBNAIL_HEIGHT = 75;
    /* @codingStandardsIgnoreEnd */

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var ImageUploader
     */
    private $imageUploader;
    
    private $basehelper;

    /**
     * Data constructor.
     *
     * @param Context                     $context
     * @param StoreManagerInterface       $storeManager
     * @param AdapterFactory              $imageFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param EavConfig                   $eavConfig
     * @param ImageUploader               $imageUploader
     * @param Filesystem                  $filesystem
     * @param ScopeConfigInterface        $scopeConfig
     * @param BaseHelper                  $basehelper
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AdapterFactory $imageFactory,
        CategoryRepositoryInterface $categoryRepository,
        EavConfig $eavConfig,
        ImageUploader $imageUploader,
        Filesystem $filesystem,
        ScopeConfigInterface $scopeConfig,
        BaseHelper $basehelper
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->eavConfig = $eavConfig;
        $this->imageUploader = $imageUploader;
        $this->scopeConfig = $scopeConfig;
        $this->basehelper = $basehelper;
    }

    /**
     * Get Button Text
     */
    public function getButtonText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BUTTON_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Resize Image
     *
     * @param imagename $imageName
     * @param integer   $width
     * @param integer   $height
     * @return boolean|string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResizeImage($imageName, $width = self::THUMBNAIL_WIDTH, $height = self::THUMBNAIL_HEIGHT)
    {
        $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $imageUploader = $this->imageUploader;
        if (!$imageUploader->isFile($imageName)) {
            return false;
        }
        $cacheDirectory = $imageUploader->getBasePath() . '/cache/' . $width . 'x' . $height . '/';
        $absolutePath = $directoryRead->getAbsolutePath($imageUploader->getBasePath()) . '/' . $imageName;
        $imageResized = $directoryRead->getAbsolutePath($cacheDirectory) . $imageName;
        if (!$this->directory->isFile($imageResized)) {
            /** @var \Magento\Framework\Image\Adapter\AbstractAdapter $imageResize */
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(false);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            $imageResize->save($imageResized);
        }
        $resizeURL = $this->basehelper->getMediaPath() . ltrim($cacheDirectory . $imageName, '/');

        return $resizeURL;
    }

    /**
     * Get Image Name
     *
     * @param  imagename $imageName
     * @return boolean|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($imageName)
    {
        return $this->imageUploader->getImageUrl($imageName);
    }
}
