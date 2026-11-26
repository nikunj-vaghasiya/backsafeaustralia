<?php

namespace Digital\Homeboxes\Helper;

use Digital\Homeboxes\Model\ImageUploader;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Visibility;

class Data extends AbstractHelper
{
    /* @codingStandardsIgnoreStart */
    const THUMBNAIL_WIDTH = 75;

    const THUMBNAIL_HEIGHT = 75;

    const XML_PATH_PAGE_TITLE = "homeboxes_section/general/title";

    const XML_PATH_PAGE_LIMIT = "homeboxes_section/general/box_limit";

    const XML_PATH_BUTTON_TEXT = 'homeboxes_section/general/button_text';
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

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Visibility
     */
    private $productVisibility;

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
     * @param CategoryCollectionFactory   $categoryCollectionFactory
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
        CategoryCollectionFactory $categoryCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,  // ADD
        Visibility $productVisibility                        // ADD
    ) {
        parent::__construct($context);
        $this->filesystem                = $filesystem;
        $this->directory                 = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory              = $imageFactory;
        $this->storeManager             = $storeManager;
        $this->categoryRepository       = $categoryRepository;
        $this->eavConfig                 = $eavConfig;
        $this->imageUploader             = $imageUploader;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory  = $productCollectionFactory; // ADD
        $this->productVisibility         = $productVisibility;        // ADD
    }

    // -------------------------------------------------------------------------
    // SUBCATEGORY METHODS
    // -------------------------------------------------------------------------

    /**
     * Get all active subcategories for a given parent category ID.
     *
     * Returns a collection of \Magento\Catalog\Model\Category with:
     *   - entity_id, name, url_key, image, thumbnail, is_active
     *
     * @param  int $parentCategoryId  e.g. 168
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubCategoriesByCategoryId(int $parentCategoryId)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'url_key', 'image', 'thumbnail', 'is_active', 'description'])
            ->addAttributeToFilter('parent_id', $parentCategoryId)
            ->addAttributeToFilter('is_active', 1)
            ->addIsActiveFilter()
            ->setOrder('position', 'ASC');

        return $collection;
    }

    /**
     * Get the thumbnail image URL for a category.
     *
     * Magento stores category images in  pub/media/catalog/category/
     * The `thumbnail` attribute is separate from `image` — use it if set,
     * otherwise fall back to the main `image` attribute.
     *
     * @param  \Magento\Catalog\Model\Category $category
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryThumbnailUrl(\Magento\Catalog\Model\Category $category): ?string
    {
        // Prefer the dedicated `thumbnail` attribute; fall back to `image`
        $imageFile = $category->getData('thumbnail') ?: $category->getData('image');

        if (!$imageFile) {
            return null;
        }

        // Strip leading slashes / "catalog/category/" prefix if already included
        $imageFile = ltrim($imageFile, '/');
        if (strpos($imageFile, 'catalog/category/') === false) {
            $imageFile = 'catalog/category/' . $imageFile;
        }

        return $this->getBaseMediaUrl() . $imageFile;
    }

    /**
     * Get a resized version of a category image using Magento's image adapter.
     *
     * @param  \Magento\Catalog\Model\Category $category
     * @param  int                             $width
     * @param  int                             $height
     * @return string|null
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryThumbnailResized(
        \Magento\Catalog\Model\Category $category,
        int $width  = self::THUMBNAIL_WIDTH,
        int $height = self::THUMBNAIL_HEIGHT
    ): ?string {
        $imageFile = $category->getData('thumbnail') ?: $category->getData('image');

        if (!$imageFile) {
            return null;
        }

        $imageFile    = ltrim(basename($imageFile), '/');
        $basePath     = 'catalog/category';
        $cacheDir     = $basePath . '/cache/' . $width . 'x' . $height . '/';

        $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absoluteSrc   = $directoryRead->getAbsolutePath($basePath) . '/' . $imageFile;
        $absoluteDest  = $directoryRead->getAbsolutePath($cacheDir) . $imageFile;

        if (!file_exists($absoluteSrc)) {
            return $this->getCategoryThumbnailUrl($category);
        }

        if (!file_exists($absoluteDest)) {
            /** @var \Magento\Framework\Image\Adapter\AbstractAdapter $imageResize */
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absoluteSrc);
            $imageResize->constrainOnly(false);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            $imageResize->save($absoluteDest);
        }

        return $this->getBaseMediaUrl() . $cacheDir . $imageFile;
    }

    // -------------------------------------------------------------------------
    // EXISTING METHODS (unchanged)
    // -------------------------------------------------------------------------

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
        $absolutePath   = $directoryRead->getAbsolutePath($imageUploader->getBasePath()) . '/' . $imageName;
        $imageResized   = $directoryRead->getAbsolutePath($cacheDirectory) . $imageName;

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
        return $this->getBaseMediaUrl() . ltrim($cacheDirectory . $imageName, '/');
    }

    /**
     * Get Image URL from image uploader base path
     *
     * @param  string $imageName
     * @return boolean|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl($imageName)
    {
        return $this->imageUploader->getImageUrl($imageName);
    }

    /**
     * Get Base Media URL for the current store
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get Homeboxes Page Title from config
     *
     * @return string
     */
    public function getHomeboxesPageTitle()
    {
        return $this->scopeConfig
            ->getValue(self::XML_PATH_PAGE_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Homeboxes Collection Limit from config
     *
     * @return string
     */
    public function getHomeboxesCollectionLimit()
    {
        return $this->scopeConfig
            ->getValue(self::XML_PATH_PAGE_LIMIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Button Text from config
     *
     * @return string
     */
    public function getButtonText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BUTTON_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get active, visible products assigned to a given category.
     *
     * Returns a collection with: entity_id, name, sku, price,
     * small_image, url_key, status, visibility.
     *
     * @param  int      $categoryId  e.g. 187
     * @param  int|null $pageSize    null = no limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductsByCategoryId(int $categoryId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection
            ->addAttributeToSelect([
                'name',
                'sku',
                'thumbnail',
                'image',
                'url_key',
                'short_description',
                'status',
                'rent_this_today'
            ])
            ->addAttributeToFilter('rent_this_today', ['eq' => 1])
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->addUrlRewrite()
            ->addMinimalPrice()
            ->addFinalPrice()
            ->setOrder('position', 'ASC');

        return $collection;
    }

    /**
     * Get the base image URL for a product.
     *
     * 'image' attribute = base/main product image
     * Falls back to small_image, then thumbnail if base image not set.
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductImageUrl(\Magento\Catalog\Model\Product $product): ?string
    {
        $image = $product->getData('image')
            ?: $product->getData('small_image')
            ?: $product->getData('thumbnail');

        if (!$image || $image === 'no_selection') {
            return null;
        }

        // image attribute already includes leading slash e.g. /w/h/whatever.jpg
        return $this->getBaseMediaUrl() . 'catalog/product' . $image;
    }


    public function getProductsListByCategoryId(int $categoryId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection
            ->addAttributeToSelect([
                'name',
                'sku',
                'thumbnail',
                'image',
                'url_key',
                'status',
            ])
            ->addCategoryFilter(
                $this->categoryRepository->get($categoryId, $storeId)
            )
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setOrder('position', 'ASC');

        return $collection;
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getVideoList()
    {
        return $this->scopeConfig
            ->getValue('video_section/general/video_links', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
