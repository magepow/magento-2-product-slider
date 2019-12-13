<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-06-16 22:32:54
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Adminhtml\Catalog\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * construct.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magicproduct_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Catalog Tabs Information'));
    }
}
