<?php
namespace Integration\ManualSync\Controller\Adminhtml\Logs;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Download sample file controller
 */
class Download extends \Magento\Backend\App\Action
{    
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $readFactory;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Integration\ManualSync\Helper\Data
     */
    protected $manualSyncHelper;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,        
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Integration\ManualSync\Helper\Data $manualSyncHelper
    ) {
        parent::__construct(
            $context
        );
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->readFactory = $readFactory;        
        $this->manualSyncHelper = $manualSyncHelper;
        $this->directoryList = $directoryList;
    }

    /**
     * Download sample file action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $entityDir = $this->manualSyncHelper->getEntityDirNameByType($this->getRequest()->getParam('type',false));
        $date = $this->getRequest()->getParam('date',false);
        $storeId = $this->getRequest()->getParam('store_id',false);

        $fileName = $this->getRequest()->getParam('filename') . '.log';

        if(!$entityDir && !$date && !$storeId){
            $this->messageManager->addError(__('There is no file for this entity.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/index');
            return $resultRedirect;
        }

        $integrationLogsDir = \Integration\Base\Helper\AbstractHelper::LOG_DIR_NAME;;
        $websiteId = $this->manualSyncHelper->getWebsiteIdByStoreId($storeId);
        if($websiteId == \Integration\ManualSync\Helper\Data::NZ_WEBSITE_ID){
            $integrationLogsDir = $integrationLogsDir.'/'.$websiteId;
        } elseif($websiteId == \Integration\ManualSync\Helper\Data::AU_WEBSITE_ID) {
            $integrationLogsDir = $integrationLogsDir.'/'.$websiteId;
        }

        if(!$integrationLogsDir) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/index');
            return $resultRedirect;
        }

        $dynamicRelativePath = $integrationLogsDir . '/' . $date . '/' . $entityDir . '/' . $fileName;

        $directoryRead = $this->readFactory->create($this->getDirPath(DirectoryList::VAR_DIR));
        $fileAbsolutePath = $directoryRead->getAbsolutePath($dynamicRelativePath);

        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);

        if (!$directoryRead->isFile($filePath)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addError(__('There is no file for this entity.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/index');
            return $resultRedirect;
        }

        $fileSize = isset($directoryRead->stat($filePath)['size'])
            ? $directoryRead->stat($filePath)['size'] : null;

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($directoryRead->readFile($filePath));
        return $resultRaw;
    }

    /**
     * Index action
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Integration_ManualSync::manual');
    }

    /**
     * Gets configuration of a directory
     *
     * @param string $code
     * @return string
     */
    protected function getDirPath($code)
    {
        return $this->directoryList->getPath($code);
    }
}
