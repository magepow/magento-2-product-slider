<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2015-12-14 20:26:27
 * @@Modify Date: 2016-01-25 16:44:21
 * @@Function:
 */

namespace Magiccart\Magicproduct\Helper;

// use \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const SECTIONS      = 'magicproduct';   // module name
    const GROUPS        = 'general';        // setup general
    const GROUPS_PLUS   = 'product';        // custom group
    const FEATURED      = 'featured';       // attribute featured

    public function getConfig($cfg=null)
    {
        return $this->scopeConfig->getValue(
            $cfg,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getGeneralCfg($cfg=null) 
    {
        $config = $this->scopeConfig->getValue(
            self::SECTIONS.'/'.self::GROUPS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if(isset($config[$cfg])) return $config[$cfg];
        return $config;
    }

    public function getProductCfg($cfg=null)
    {
        $config = $this->scopeConfig->getValue(
            self::SECTIONS .'/'.self::GROUPS_PLUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if(isset($config[$cfg])) return $config[$cfg];
        return $config;
    }

}
