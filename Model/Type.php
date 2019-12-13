<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-11 23:15:05
 * @@Modify Date: 2016-03-30 10:24:27
 * @@Function:
 */

namespace Magiccart\Magicproduct\Model;

class Type
{
    const TYPE_PRODUCT = 1;
    const TYPE_CATEGORY = 2;
    const TYPE_MISCELLANEOUS = 3;

    /**
     * get available statuses.
     *
     * @return []
     */
    public static function getAvailableStatuses()
    {
        return [
            self::TYPE_PRODUCT => __('Product'), 
            self::TYPE_CATEGORY => __('Categoires'),
            self::TYPE_MISCELLANEOUS => __('Miscellaneous'),
        ];
    }
}
