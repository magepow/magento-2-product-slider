<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-04-22 16:27:37
 * @@Function:
 */

namespace Magiccart\Magicproduct\Controller\Adminhtml\Product;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magiccart\Magicproduct\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {

            $model = $this->_magicproductFactory->create();
            $storeViewId = $this->getRequest()->getParam('store');

            if ($id = $this->getRequest()->getParam('magicproduct_id')) {
                $model->load($id);
            }else {
                $check = $this->_magicproductFactory->create()->getCollection()
                            ->addFieldToFilter('type_id', '1')
                            ->addFieldToFilter('identifier', $data['identifier']);
                if($check->count()){
                    $this->messageManager->addError(__('Identifier already exists.'));
                    $this->_getSession()->setFormData($data);
                    return $resultRedirect->setPath('*/*/edit');
                }
            }

            if (isset($_FILES['image']) && isset($_FILES['image']['name']) && strlen($_FILES['image']['name'])) {
                /*
                 * Save image upload
                 */
                try {
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'image']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                    /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                    $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

                    $uploader->addValidateCallback('product_image', $imageAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);

                    /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory->getAbsolutePath('magiccart/magicproduct/product/')
                    );
                    $data['image'] = 'magiccart/magicproduct/product/'.$result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                if (isset($data['image']) && isset($data['image']['value'])) {
                    if (isset($data['image']['delete'])) {
                        $data['image'] = null;
                        $data['delete_image'] = true;
                    } elseif (isset($data['image']['value'])) {
                        $data['image'] = $data['image']['value'];
                    } else {
                        $data['image'] = null;
                    }
                }
            }
            /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate */
            // $localeDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            // $data['start_time'] = $localeDate->date($data['start_time'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');
            // $data['end_time'] = $localeDate->date($data['end_time'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');

            $data['config'] = serialize($data);
            $data['type_id'] = 1;
            $model->setData($data)
                ->setStoreViewId($storeViewId);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The Product Tabs has been saved.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'magicproduct_id' => $model->getId(),
                            '_current' => true,
                            'store' => $storeViewId,
                            'current_magicproduct_id' => $this->getRequest()->getParam('current_magicproduct_id'),
                            'saveandclose' => $this->getRequest()->getParam('saveandclose'),
                        ]
                    );
                } elseif ($this->getRequest()->getParam('back') === 'new') {
                    return $resultRedirect->setPath(
                        '*/*/new',
                        ['_current' => TRUE]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the magicproduct.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['magicproduct_id' => $this->getRequest()->getParam('magicproduct_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
