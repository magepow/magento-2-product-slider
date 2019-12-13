<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2018-03-15 00:46:59
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Product;

class ProductList extends \Magento\Catalog\Block\Product\ListProduct
{

    protected function _getProductCollection()
    {

        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $collection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
            /** Apply filters here */
            $collection->addAttributeToSelect('*');
            $this->_limit = $this->getToolbarBlock()->getLimit(); //$this->getData('limit');
            $this->_types = $this->escapeHtml($this->getData('types'));

            if(!$this->_types) return $collection->setPageSize($this->_limit);
            $fn = 'get' . ucfirst( $this->_types );
            $collection = $this->{$fn}($collection);
            $page = (int) $this->getRequest()->getParam('p', 1);
            $collection->setPageSize($this->_limit);
            $collection->setCurPage($page);
            $this->_eventManager->dispatch(
                'catalog_block_product_list_collection',
                ['collection' => $collection]
            );
            
            $this->_productCollection = $collection;
            // $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;

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
        $report = $this->_objectManager->create('\Magento\Reports\Model\Product\Index\Viewed');
        $report = $collection->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('product_id', array('in' => $ids))
                                ->setPageSize($this->_limit)
                                ->setCurPage(1);
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

}
