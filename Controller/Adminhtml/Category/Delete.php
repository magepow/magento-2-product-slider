<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-04-22 16:50:44
 * @@Function:
 */

namespace Magiccart\Magicproduct\Controller\Adminhtml\Category;

class Delete extends \Magiccart\Magicproduct\Controller\Adminhtml\Action
{
    public function execute()
    {
        $magicproductId = $this->getRequest()->getParam('magicproduct_id');
        try {
            $item = $this->_magicproductFactory->create()->setId($magicproductId);
            $item->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
