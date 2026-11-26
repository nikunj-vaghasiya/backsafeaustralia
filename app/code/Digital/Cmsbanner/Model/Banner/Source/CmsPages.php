<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Model\Banner\Source;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class CmsPages implements OptionSourceInterface
{
    public function __construct(
        private readonly CollectionFactory $pageCollectionFactory
    ) {}

    public function toOptionArray(): array
    {
        $options = [];
        $collection = $this->pageCollectionFactory->create();
        $collection->addFieldToSelect(['identifier', 'title']);

        foreach ($collection as $page) {
            $options[] = [
                'value' => $page->getIdentifier(),
                'label' => $page->getTitle(),
            ];
        }

        return $options;
    }
}
