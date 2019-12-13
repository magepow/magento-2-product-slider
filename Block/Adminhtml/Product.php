<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-01-26 19:09:51
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Adminhtml;

class Product extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'Magiccart_Magicproduct';
        $this->_headerText = __('Product Tabs');
        $this->_addButtonLabel = __('Add New Product Tabs');
        parent::_construct();
    }
}
