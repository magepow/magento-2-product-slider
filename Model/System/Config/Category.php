<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-11 23:15:05
 * @@Modify Date: 2016-03-30 11:13:02
 * @@Function:
 */

namespace Magiccart\Magicproduct\Model\System\Config;

class Category implements \Magento\Framework\Option\ArrayInterface
{

    const PREFIX_ROOT = '*';    
    const REPEATER = '*';
    const PREFIX_END = '';

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Config\Source\Category
     */
    protected $_category;  

    protected $_request;

    protected $_storeManager;

    protected  $_options = array();

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Config\Source\Category $category
    )
    {
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_category = $category;
    }
 
    public function toOptionArray()
    {
        if(!$this->_options){
            $store = $this->_request->getParam('store');
            if(!$store) $categories = $this->_category->toOptionArray();
            else {
                $rootCategoryId = $this->_storeManager->getStore($store)->getRootCategoryId();
                $label = $this->_categoryFactory->create()->load($rootCategoryId)->getName();
                $categories = [['value' => $rootCategoryId, 'label' => $label]];
            }
            $options = [['label' => __('All'), 'value' => '0']];
            foreach ($categories as $category) {
                $this->_options = [];
                if($category['value']) {
                    $this->_options[] = ['label' => __('All in "%1"', $category['label']), 'value' => $category['value']];
                    $_categories = $this->_categoryFactory->create()->getCategories($category['value']);
                    if($_categories){
                        // $rootOption = ['label' => $category['label']];
                        foreach ($_categories as $_category) {
                            $this->_options[] = [
                                'label' => self::PREFIX_ROOT .$_category->getName(),
                                'value' => $_category->getEntityId()
                            ];
                            if ($_category->hasChildren()) $this->_getChildOptions($_category->getChildren());
                        }
                        // $rootOption['value'] = $this->_options;
                        // $options[] = $rootOption;
                        if($this->_options){
                            $options[] = [
                                'label' => $category['label'],
                                'value' => $this->_options
                            ];
                        }
                    }
                }
            }
            $this->_options = $options;
        }
        return $this->_options;
    }
 
    protected function _getChildOptions($categories)
    {
        foreach ($categories as $category) {
            $prefix = str_repeat(self::REPEATER, $category->getLevel() * 1) . self::PREFIX_END;
            $this->_options[] = [
                'label' => $prefix . $category->getName(),
                'value' => $category->getEntityId()
            ];
            if ($category->hasChildren()) $this->_getChildOptions($category->getChildren());
        }
    }

}
