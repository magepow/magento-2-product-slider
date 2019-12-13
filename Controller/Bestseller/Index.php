<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-02-01 15:15:37
 * @@Modify Date: 2018-02-27 21:43:47
 * @@Function:
 */

namespace Magiccart\Magicproduct\Controller\Bestseller;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $_storeManager = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($_storeManager->getStore()->getRootCategoryId());
        if ($category->getId()) {
            $category->setImage('');
            $_registry =  $this->_objectManager->get('Magento\Framework\Registry');
            $_registry->register('category', $category);
            $_registry->register('current_category', $category);
        }

        $this->_view->loadLayout();
        // $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }  
}
