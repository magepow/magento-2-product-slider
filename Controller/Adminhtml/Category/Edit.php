<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-04-22 16:50:27
 * @@Function:
 */

namespace Magiccart\Magicproduct\Controller\Adminhtml\Category;

class Edit extends \Magiccart\Magicproduct\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('magicproduct_id');
        // $storeViewId = $this->getRequest()->getParam('store', 0);
        $model = $this->_magicproductFactory->create();

        if ($id) {
            $model->load($id); //$model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getMagicproductId()) {
                $this->messageManager->addError(__('This Catgegory Tabs no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }else {
                $tmp = @unserialize($model->getConfig());
                if(is_array($tmp)){
                    unset($tmp['form_key']);
                    unset($tmp['magicproduct_id']);
                    $model->addData($tmp);
                }
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('magicproduct', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
