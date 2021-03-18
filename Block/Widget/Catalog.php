<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2020-04-26 09:50:18
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Widget;

class Catalog extends Product
{
    protected $_categoryInstance;
    protected $_category;
    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    protected $_typeId = '3';
    protected $_options = array('limit', 'speed', 'timer', 'cart', 'compare', 'wishlist', 'review', 'category_id'); //'widthImages', 'heightImages'

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magiccart\Magicproduct\Model\MagicproductFactory $magicproductFactory,
        \Magiccart\Magicproduct\Model\System\Config\Types $types,
        array $data = []
    ) {

        $this->_catalogCategory = $catalogCategory;
        $this->_categoryInstance = $categoryFactory->create();

        parent::__construct($context, $backendUrl, $magicproductFactory, $types, $data);
    }

    public function getCatName()
    {
        $cat = $this->getCategory();
        if($cat) return $cat->getName();
    }

    public function getCategory()
    {
        if( !$this->_category ) $this->_category = $this->_categoryInstance->load($this->getData('category_id'));
        return $this->_category;
    }

    public function getTabs()
    {
        if(!$this->_tabs){
            $tabs = array();
            $cfg = $this->getTypes();
            if(!$cfg) return $tabs;
            $types = $this->_types->toOptionArray();
            foreach ($types as $type) {
                if(in_array($type['value'], $cfg)) $tabs[$type['value']] = $type['label'];
            }
            $this->_tabs = $tabs;
        }
        return $this->_tabs;
    }

    public function getRelatedTabs()
    {

        $categoryIds = $this->getCategoryIds();
        $categories =  $this->_categoryInstance->getCollection()
                        // ->setStoreId()
                        ->addAttributeToFilter('entity_id', array('in' => $categoryIds))
                        ->addAttributeToSelect('name');
        return $categories;
    }

    public function getContent($template='')
    {
        if($template) $this->setTemplateProduct($template); 
        $content = '';   
        $tabs = ($this->getAjax()) ? $tabs = array($this->getTabActivated() => 'Activated') : $this->getTabs();
        foreach ($tabs as $type => $name) {
            $content .= $this->getLayout()->createBlock('Magiccart\Magicproduct\Block\Catalog\GridProduct') //, "magicproduct.category.$type"
            ->setActivated($type) //or ->setData('activated', $this->getTabActivated())
            ->setCfg($this->getData())
            ->setTemplate($template)
            ->toHtml();
        }
        return $content;
    }

    // public function getImage($file='')
    // {
    //     $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .'magiccart/magicproduct'. $file;
    //     return $resizedURL;
    // }

}
