<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-11 23:15:05
 * @@Modify Date: 2016-01-26 13:55:29
 * @@Function:
 */

namespace Magiccart\Magicproduct\Model;

class Magicproduct extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'magicproduct_id';

    protected $_magicproductCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magiccart\Magicproduct\Model\ResourceModel\Magicproduct\CollectionFactory $magicproductCollectionFactory,
        \Magiccart\Magicproduct\Model\ResourceModel\Magicproduct $resource,
        \Magiccart\Magicproduct\Model\ResourceModel\Magicproduct\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_magicproductCollectionFactory = $magicproductCollectionFactory;
    }

}
