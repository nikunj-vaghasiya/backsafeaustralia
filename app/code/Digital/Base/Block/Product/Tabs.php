<?php

namespace Digital\Base\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Ecomteck\ProductAttachment\Helper\Data as AttachmentHelper;
use Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\CollectionFactory as AttachmentCollectionFactory;
use Ecomteck\ProductAttachment\Model\ResourceModel\FileIcon\CollectionFactory as FileIconCollectionFactory;


class Tabs extends Template
{
    protected Registry $registry;
    protected ScopeConfigInterface $scopeConfig;
    protected FilterProvider $filterProvider;
    protected ProductRepositoryInterface $productRepository;
    protected AttachmentCollectionFactory $attachmentCollectionFactory;
    protected FileIconCollectionFactory $fileIconCollectionFactory;
    protected AttachmentHelper $attachmentHelper;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        FilterProvider $filterProvider,
        ProductRepositoryInterface $productRepository,
        AttachmentCollectionFactory $attachmentCollectionFactory,
        FileIconCollectionFactory $fileIconCollectionFactory,
        AttachmentHelper $attachmentHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->filterProvider = $filterProvider;
        $this->productRepository = $productRepository;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->fileIconCollectionFactory = $fileIconCollectionFactory;
        $this->attachmentHelper = $attachmentHelper;
    }

    public function getProduct()
    {
        return $this->registry->registry('product');
    }

    public function getAttachments()
    {
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }

        $collection = $this->attachmentCollectionFactory->create();
        $collection->getSelect()->where(
            "FIND_IN_SET(" . (int)$product->getId() . ", REPLACE(products, '&', ',')) > 0"
        );
        return $collection;
    }

    public function getFileIcon($fileExt)
    {
        $fileExt = strtolower($fileExt);
        $mediaUrl = $this->attachmentHelper->getMediaUrl();
        $fileIconCollection = $this->fileIconCollectionFactory->create();
        $fileIconCollection->addFieldToFilter('icon_ext', $fileExt);
        $iconItem = $fileIconCollection->getFirstItem();

        if ($iconItem && $iconItem->getIconImage()) {
            return $mediaUrl . 'fileicon/tmp/icon/' . $iconItem->getIconImage();
        }

        $iconPath = 'Ecomteck_ProductAttachment::images/' . $fileExt . '.png';
        if (file_exists($this->getViewFileUrl($iconPath))) {
            return $this->getViewFileUrl($iconPath);
        }

        return $this->getViewFileUrl('Ecomteck_ProductAttachment::images/unknown.png');
    }

    public function getTabs(): array
    {
        $tabs = [];
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }

        if ($product->getDescription()) {
            $tabs = [
                [
                    'id' => 'description',
                    'title' => __('Product Description'),
                    'content' => $product->getDescription(),
                ],
            ];
        }
        if ($product->getData('specification')) {
            $tabs[] = [
                'id' => 'specification',
                'title' => __('Specification'),
                'content' => $product->getData('specification'),
            ];
        }

        $attachments = $this->getAttachments();

        if ($attachments->getSize() > 0 && $this->isDownloadTabEnabled()) {
            $html = '<ul class="downloadul_wrapper">';
            foreach ($attachments as $attachment) {
                if (!empty($attachment->getFile())) {
                    $url = $this->attachmentHelper->getBaseUrl() . $attachment->getFile();
                    $fileExt = pathinfo($attachment->getFile(), PATHINFO_EXTENSION);
                    $iconUrl = $this->getFileIcon($fileExt);
                    $html .= sprintf(
                        '<li class="flex items-center space-x-3">
                        <img src="%s" alt="%s" class="w-6 h-6"/>
                        <a href="%s" target="_blank" class="text-blue-600 hover:underline">%s</a>
                    </li>',
                        $iconUrl,
                        $fileExt,
                        $url,
                        $attachment->getName(),
                    );
                }
            }
            $html .= '</ul>';

            $tabs[] = [
                'id' => 'attachments',
                'title' => __('Downloads'),
                'content' => $html,
            ];
            return $tabs;
        }

        return $tabs;
    }

    public function isDownloadTabEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'productattachment/general/producttab',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFilteredContent(string $content): string
    {
        return $this->filterProvider
            ->getPageFilter()
            ->filter($content);
    }
}
