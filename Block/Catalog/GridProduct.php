<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2019-08-12 00:43:06
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Catalog;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Catalog\Block\Product\AbstractProduct;

class GridProduct extends \Magiccart\Magicproduct\Block\Product\ListProduct
{

    protected $_limit; // Limit Product
    protected $_type; // type is type filter bestseller, featured ...
    protected $_types; // array types is types filter bestseller, featured ...
    
    /**
     * Widget Rule factory
     *
     * @var \Magento\CatalogWidget\Model\RuleFactory
     */
    protected $_ruleFactory;

    protected $sqlBuilder;

    protected $_parameters; // Condition Product


    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\CatalogWidget\Model\RuleFactory $ruleFactory,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magiccart\Magicproduct\Model\Magicproduct $magicproduct,
        array $data = []
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->sqlBuilder   = $sqlBuilder;
        $this->_magicproduct = $magicproduct;
        parent::__construct(  
            $context, 
            $postDataHelper, 
            $layerResolver, 
            $categoryRepository, 
            $urlHelper, 
            $data
        );
    }


    public function getTypeFilter() // getTypeFilter
    {
        $type = $this->escapeHtml($this->getRequest()->getParam('type'));
        if(!$type) $type = $this->getActivated(); // get form setData in Block
        return $type;
    }

    public function getWidgetCfg($cfg=null)
    {
        $info = $this->getRequest()->getPost('info');
        if($info){
            if(isset($info['identifier'])){
                $identifier = $info['identifier']; 
                $item = $this->_magicproduct->getCollection()->addFieldToSelect('config')
                                ->addFieldToFilter('identifier', $identifier)->addFieldToFilter('type_id', 3)->getFirstItem();
                $config = $item->getConfig();
                $data = @unserialize($config);
                $this->_parameters =  isset($data['parameters']) ? $data['parameters'] : '';
            }
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;          
        }else {
            $info = $this->getCfg();
            $this->_parameters =  $this->getCfg('parameters');
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;
        }
    }

    public function getPositioned()
	{
        $positioned = parent::getPositioned();
        if(parent::getPositioned() == NULL){
            return '';
        }else{
            return $positioned;
        }

	}

    protected function _getProductCollection()
    {

        $this->_limit   = (int) $this->getWidgetCfg('limit');
        $this->_type    = $this->getTypeFilter(); // $this->getActivated();
        $isCategory     = is_numeric($this->_type);
        $categoryId = $isCategory ? $this->_type : (int) $this->getWidgetCfg('category_id');
        if (is_null($this->_productCollection)) {
            $this->setCategoryId($categoryId); //$this->setCategoryId($this->getTypeFilter());
            $this->_productCollection = parent::_getProductCollection();   
        }

        // $this->_types = $this->getWidgetCfg('types');
        if($isCategory || !$this->_type) return $this->_productCollection->setPageSize($this->_limit);
        $fn = 'get' . ucfirst( $this->_type);
        $collection = $this->{$fn}($this->_productCollection);

        if(is_null($collection)) return $this->_productCollection;
        $parameters = $this->_parameters;
        if($parameters){
            $rule = $this->getRule($parameters);
            $conditions = $rule->getConditions();
            $conditions->collectValidatedAttributes($collection);
            $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        }

        return $collection->setPageSize($this->_limit);

    }
    
    protected function getRule($conditions)
    {
        $rule = $this->_ruleFactory->create();
        if(is_array($conditions)) $rule->loadPost($conditions);
        return $rule; 
    }

    public function getBestseller($collection){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $report = $objectManager->get('\Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory')->create();
        $ids = $collection->getAllIds();
        $report->addFieldToFilter('product_id', array('in' => $ids))->setPageSize($this->_limit)->setCurPage(1);
        $producIds = array();
        // $notIds = array();
        foreach ($report as $product) {
            // if(!in_array($product->getProductId(), $ids )) $notIds[] =  $product->getProductId();
            $producIds[] = $product->getProductId();
        }

        $collection->addAttributeToFilter('entity_id', array('in' => $producIds));
    
        return $collection;
        
    }

    public function getFeatured($collection)
    {

        $collection->addAttributeToFilter('featured', '1');

        return $collection;

    }

    public function getLatest($collection){

        $collection = $collection->addStoreFilter()
        ->addAttributeToSort('entity_id', 'desc');

        return $collection;

    }

    public function getMostviewed($collection){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $report = $objectManager->get('\Magento\Reports\Model\ResourceModel\Report\Product\Viewed\CollectionFactory')->create();
        $ids = $collection->getAllIds();
        $report->addFieldToFilter('product_id', array('in' => $ids))->setPageSize($this->_limit)->setCurPage(1);
        $producIds = array();
        foreach ($report as $product) {
            $producIds[] = $product->getProductId();
        }

        $collection->addAttributeToFilter('entity_id', array('in' => $producIds));
    
        return $collection;
    }

    public function getNew($collection) {

        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection = $collection->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort('news_from_date', 'desc');

        return $collection;
    }

    public function getRandom($collection) {

        $collection->getSelect()->order('rand()');
        return $collection;

    }

    public function getRecently($collection) {

        $ids = $collection->getAllIds();
        $collection = $this->getLayout()->createBlock('Magento\Reports\Block\Product\Viewed');
        if(!$collection){
            $collection = $collection
                        ->setProductIds($ids)
                        ->setPageSize($this->_limit)
                        ->getItemsCollection();
        } else {
            $report = $this->_objectManager->create('\Magento\Reports\Model\Product\Index\Viewed');
            $report = $collection->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addFieldToFilter('product_id', array('in' => $ids))
                                    ->setPageSize($this->_limit)
                                    ->setCurPage(1);          
        }
        
        return $collection;
    }

    public function getSale($collection){

        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $collection = $collection->addStoreFilter()->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'special_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'special_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort('special_to_date', 'desc');

        return $collection;

    }
    
    
    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int|null
     */
    public function getProductQty($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $qty = $stockItem->getQty();
        return $qty > 0 ? $qty : 0;
    }

    public function getCategory($categoryId)
    {
        return $this->categoryRepository->get($categoryId);
    }
    
}
