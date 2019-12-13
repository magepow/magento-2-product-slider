<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-11 23:15:05
 * @@Modify Date: 2016-02-17 18:57:25
 * @@Function:
 */

namespace Magiccart\Magicproduct\Model\System\Config;

class Identifier implements \Magento\Framework\Option\ArrayInterface
{

	protected $scopeConfig;
	protected $_magicproduct;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magiccart\Magicproduct\Model\Magicproduct $magicproduct
	)
	{
		$this->_magicproduct = $magicproduct;
	}

    public function toOptionArray()
    {
		$magicproducts = $this->_magicproduct->getCollection();
		$options = array();
		foreach ($magicproducts as $item) {
			$options[$item->getIdentifier()] = $item->getTitle();
		}
        return $options;
    }

}
