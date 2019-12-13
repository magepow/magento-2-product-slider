<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-11 23:15:05
 * @@Modify Date: 2016-01-27 16:08:44
 * @@Function:
 */

namespace Magiccart\Magicproduct\Model\System\Config;

class Action implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            'cart'	=>	__('Add to Cart'),
            'compare' =>	__('Add to Compare'),
            'wishlist' =>	__('Ad to Wishlist'),
            'review' =>	__('Review'),
        ];
    }

}
