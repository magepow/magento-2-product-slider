<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2019-01-25 12:14:40
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Adminhtml\Category;

use Magiccart\Magicproduct\Model\Status;
use Magiccart\Magicproduct\Model\Type;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * magicproduct collection factory.
     *
     * @var \Magiccart\Magicproduct\Model\ResourceModel\Magicproduct\CollectionFactory
     */
    protected $_magicproductCollectionFactory;


    /**
     * construct.
     *
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Magiccart\Magicproduct\Model\ResourceModel\Magicproduct\CollectionFactory $magicproductCollectionFactory
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magiccart\Magicproduct\Model\ResourceModel\Magicproduct\CollectionFactory $magicproductCollectionFactory,
    
        array $data = []
    ) {
        $this->_magicproductCollectionFactory = $magicproductCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('magicproductGrid');
        $this->setDefaultSort('magicproduct_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        
        $collection = $this->_magicproductCollectionFactory->create();
        $collection->addFieldToFilter('type_id', Type::TYPE_CATEGORY);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'magicproduct_id',
            [
                'header' => __('Magicproduct ID'),
                'type' => 'number',
                'index' => 'magicproduct_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'type' => 'text',
                'index' => 'title',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'identifier',
            [
                'header' => __('Identifier'),
                'type' => 'text',
                'index' => 'identifier',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => Status::getAvailableStatuses(),
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'magicproduct_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );
        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));
        $this->addExportType('*/*/exportExcel', __('Excel'));

        return parent::_prepareColumns();
    }

    /**
     * get slider vailable option
     *
     * @return array
     */

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('magicproduct_id');
        $this->getMassactionBlock()->setFormFieldName('magicproduct');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('magicproduct/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        $statuses = Status::getAvailableStatuses();

        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('magicproduct/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses,
                    ],
                ],
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['magicproduct_id' => $row->getId()]
        );
    }
}
