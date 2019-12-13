<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-11 23:15:05
 * @@Modify Date: 2016-01-26 13:56:54
 * @@Function:
 */

namespace Magiccart\Magicproduct\Model\ResourceModel\Magicproduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Magiccart\Magicproduct\Model\Magicproduct', 'Magiccart\Magicproduct\Model\ResourceModel\Magicproduct');
    }
}
