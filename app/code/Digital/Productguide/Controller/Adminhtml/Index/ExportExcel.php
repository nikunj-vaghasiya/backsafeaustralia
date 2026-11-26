<?php

namespace Digital\Productguide\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportExcel extends \Magento\Backend\App\Action
{
    /**
     * @var _fileFactory
     */
    protected $_fileFactory;

    /**
     * Export Excel Data
     */
    public function execute()
    {
        /*@codingStandardsIgnoreStart*/
        $this->_view->loadLayout(false);
        /*@codingStandardsIgnoreEnd*/
        $fileName = 'productguide.xml';
        $exportBlock = $this->_view->getLayout()
            ->createBlock(\Digital\Productguide\Block\Adminhtml\Productguide\Export::class);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_fileFactory = $objectManager
            ->create(\Magento\Framework\App\Response\Http\FileFactory::class);
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile(),
            DirectoryList::VAR_DIR
        );
    }
}
