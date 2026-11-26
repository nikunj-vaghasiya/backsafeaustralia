<?php
namespace Digital\Projects\Helper;

use Digital\Projects\Model\ImageUploader;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /* @codingStandardsIgnoreStart */
    const THUMBNAIL_WIDTH = 75;

    const THUMBNAIL_HEIGHT = 75;

    const XML_PATH_ENABLE = "projects_section/general/enable";
    
    const XML_PATH_PAGE_TITLE = "projects_section/general/title";

    const XML_PATH_PAGE_LIMIT = "projects_section/general/projects_limit";

    const XML_PATH_MAIN_BANNER = "projects_section/general/main_banner";

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

    protected $_scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    protected $_storeInterface;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

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
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AdapterFactory $imageFactory,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeInterface,
        EavConfig $eavConfig,
        ImageUploader $imageUploader,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->_storeInterface = $storeInterface;
        $this->categoryRepository = $categoryRepository;
        $this->eavConfig = $eavConfig;
        $this->imageUploader = $imageUploader;
    }

    public function isEnabled()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
        $resizeURL = $this->getBaseMediaUrl() . ltrim($cacheDirectory . $imageName, '/');

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

    /**
     * Get Basemediaurl
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get PageTitle
     *
     * @return string
     */
    public function getProjectsPageTitle()
    {

        return $this->scopeConfig
            ->getValue(self::XML_PATH_PAGE_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get PageTitle
     *
     * @return string
     */
    public function getProjectsCollectionLimit()
    {

        return $this->scopeConfig
            ->getValue(self::XML_PATH_PAGE_LIMIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

     public function getMediaPath()
    {
        $mediaUrl = $this->_storeInterface
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediaUrl;
    }

     public function getMainBanner(){
        return $this->getMediaPath().'wysiwyg/enquiry/'.$this->scopeConfig->getValue(self::XML_PATH_MAIN_BANNER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}
