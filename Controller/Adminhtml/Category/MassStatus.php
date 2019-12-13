<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-04-22 16:49:36
 * @@Function:
 */

namespace Magiccart\Magicproduct\Controller\Adminhtml\Category;

class MassStatus extends \Magiccart\Magicproduct\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $magicproductIds = $this->getRequest()->getParam('magicproduct');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');

        if (!is_array($magicproductIds) || empty($magicproductIds)) {
            $this->messageManager->addError(__('Please select Category Tabs(s).'));
        } else {
            $collection = $this->_magicproductCollectionFactory->create()
                //->setStoreViewId($storeViewId)
                ->addFieldToFilter('magicproduct_id', ['in' => $magicproductIds]);
            try {
                foreach ($collection as $item) {
                    $item->setStoreViewId($storeViewId)
                        ->setStatus($status)
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($magicproductIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/', ['store' => $this->getRequest()->getParam('store')]);
    }
}
