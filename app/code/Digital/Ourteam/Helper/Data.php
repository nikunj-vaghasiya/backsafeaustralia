<?php
declare(strict_types=1);

namespace Digital\Ourteam\Helper;

use Digital\Base\Helper\Data as BaseHelper;
use Digital\Ourteam\Model\ImageUploader;
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
    public const THUMBNAIL_WIDTH = 75;

    public const THUMBNAIL_HEIGHT = 75;
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
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var BaseHelper
     */
    private $baseHelper;

    /**
     * Data constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param AdapterFactory        $imageFactory
     * @param ImageUploader         $imageUploader
     * @param Filesystem            $filesystem
     * @param BaseHelper            $baseHelper
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AdapterFactory $imageFactory,
        ImageUploader $imageUploader,
        Filesystem $filesystem,
        BaseHelper $baseHelper
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->imageUploader = $imageUploader;
        $this->baseHelper = $baseHelper;
    }

    /**
     * Get Resize Image
     *
     * @param string  $imageName
     * @param integer $width
     * @param integer $height
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

        return $this->baseHelper->getMediaPath() . ltrim($cacheDirectory . $imageName, '/');
    }

    /**
     * Get Image Url
     *
     * @param  string $imageName
     * @return boolean|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($imageName)
    {
        return $this->imageUploader->getImageUrl($imageName);
    }
}
