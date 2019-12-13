<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-04-22 16:29:11
 * @@Function:
 */

namespace Magiccart\Magicproduct\Controller\Adminhtml\Product;

class MassDelete extends \Magiccart\Magicproduct\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $magicproductIds = $this->getRequest()->getParam('magicproduct');
        if (!is_array($magicproductIds) || empty($magicproductIds)) {
            $this->messageManager->addError(__('Please select magicproduct(s).'));
        } else {
            $collection = $this->_magicproductCollectionFactory->create()
                ->addFieldToFilter('magicproduct_id', ['in' => $magicproductIds]);
            try {
                foreach ($collection as $item) {
                    $item->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($magicproductIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
