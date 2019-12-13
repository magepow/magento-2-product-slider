<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-06-22 00:16:27
 * @@Modify Date: 2019-01-04 16:42:01
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Adminhtml\Product\Edit\Tab;

use Magiccart\Magicproduct\Model\Status;

class Category extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;
     protected $_category;
    /**
     * @var \Magiccart\Magicproduct\Model\Magicproduct
     */

    protected $_magicproduct;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magiccart\Magicproduct\Model\Magicproduct $magicproduct,
        \Magiccart\Magicproduct\Model\System\Config\Category $category,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_category = $category;
        $this->_magicproduct = $magicproduct;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * prepare layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());

        return $this;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('magicproduct');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('magic_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Catalog Tabs Information')]);

        if ($model->getId()) {
            $fieldset->addField('magicproduct_id', 'hidden', ['name' => 'magicproduct_id']);
        }
        $categories = $this->_category->toOptionArray();
        $all_categories =   array( 'value' => 0, 'label' => __('All') );
        // $all_categories =   array(
        //                         'label' => __('All websites'),
        //                         'value' => array( 'value' => 0, 'label' => __('All') )
        //                     );

        array_unshift( $categories,  $all_categories );

        $fieldset->addField('category_id', 'select',
            [
                'label'     => __('Add Category Filter'),
                'title'     => __('Add Category Filter'),
                'name'      => 'category_id',
                'values'    => $categories,
                'disabled'  => false,
                // 'readonly' => true,
            ]
        );

        // $fieldset->addField('category_ids', 'multiselect',
        //     [
        //         'label' => __('Add Categories Filter'),
        //         'title' => __('Add Categories Filter'),
        //         'name'  => 'category_ids',
        //         'required' => false,
        //         'values' => $this->_category->toOptionArray(),
        //     ]
        // );

        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getMagicproduct()
    {
        return $this->_coreRegistry->registry('magicproduct');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getMagicproduct()->getId()
            ? __("Edit Tabs '%1'", $this->escapeHtml($this->getMagicproduct()->getTitle())) : __('New Tabs');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Categories Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
