<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Digital\Cmsbanner\Api\BannerRepositoryInterface;
use Digital\Cmsbanner\Model\BannerFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractBanner implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_save';
    protected $bannerRepository;
    protected $bannerFactory;
    protected $dataPersistor;
    protected $imageUploader;
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        BannerFactory $bannerFactory,
        DataPersistorInterface $dataPersistor,
        ImageUploader $imageUploader
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->bannerFactory = $bannerFactory;
        $this->dataPersistor = $dataPersistor;
        $this->imageUploader = $imageUploader;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $bannerId = isset($data['banner_id']) && $data['banner_id'] !== '' && $data['banner_id'] !== null
                ? (int)$data['banner_id']
                : null;

            unset($data['banner_id']);

            $banner = $bannerId
                ? $this->bannerRepository->getById($bannerId)
                : $this->bannerFactory->create();

            if (isset($data['image'])) {
                if (is_array($data['image']) && !empty($data['image'])) {
                    $imageName = $data['image'][0]['name'] ?? null;
                    if ($imageName) {
                        try {
                            $mediaDirectory = \Magento\Framework\App\ObjectManager::getInstance()
                                ->get(\Magento\Framework\Filesystem::class)
                                ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                            $tmpPath = 'digital/cmsbanner/tmp/' . $imageName;
                            $finalPath = 'digital/cmsbanner/' . $imageName;

                            if ($mediaDirectory->isExist($tmpPath)) {
                                $mediaDirectory->renameFile($tmpPath, $finalPath);
                            }
                        } catch (\Exception $e) {
                        }
                        $data['image'] = $imageName;
                    } else {
                        $data['image'] = null;
                    }
                } elseif (!is_string($data['image'])) {
                    unset($data['image']);
                }
            }

            if (isset($data['mobile_image'])) {
                if (is_array($data['mobile_image']) && !empty($data['mobile_image'])) {
                    $imageName = $data['mobile_image'][0]['name'] ?? null;
                    if ($imageName) {
                        try {
                            $mediaDirectory = \Magento\Framework\App\ObjectManager::getInstance()
                                ->get(\Magento\Framework\Filesystem::class)
                                ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                            $tmpPath = 'digital/cmsbanner/tmp/' . $imageName;
                            $finalPath = 'digital/cmsbanner/' . $imageName;

                            if ($mediaDirectory->isExist($tmpPath)) {
                                $mediaDirectory->renameFile($tmpPath, $finalPath);
                            }
                        } catch (\Exception $e) {
                        }
                        $data['mobile_image'] = $imageName;
                    } else {
                        $data['mobile_image'] = null;
                    }
                } elseif (!is_string($data['mobile_image'])) {
                    unset($data['mobile_image']);
                }
            }

            if (isset($data['cms_pages']) && is_array($data['cms_pages'])) {
                $data['cms_pages'] = implode(',', array_filter($data['cms_pages']));
            }

            $banner->setData(array_merge($banner->getData(), $data));

            if ($bannerId) {
                $banner->setBannerId($bannerId);
            }

            $this->bannerRepository->save($banner);

            $this->messageManager->addSuccessMessage(__('Banner has been saved successfully.'));
            $this->dataPersistor->clear('digital_cmsbanner');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['banner_id' => $banner->getId()]);
            }

            return $resultRedirect->setPath('*/*/');

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the banner.'));
        }

        $this->dataPersistor->set('digital_cmsbanner', $data);

        return $bannerId
            ? $resultRedirect->setPath('*/*/edit', ['banner_id' => $bannerId])
            : $resultRedirect->setPath('*/*/new');
    }
}
