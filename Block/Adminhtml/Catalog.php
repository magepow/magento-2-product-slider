<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-01-26 19:10:03
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Adminhtml;

class Catalog extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_catalog';
        $this->_blockGroup = 'Magiccart_Magicproduct';
        $this->_headerText = __('Catalog Tabs');
        $this->_addButtonLabel = __('Add New Catalog Tabs');
        parent::_construct();
    }
}
