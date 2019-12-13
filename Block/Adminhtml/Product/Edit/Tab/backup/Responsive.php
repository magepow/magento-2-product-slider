<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-03-29 22:52:56
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Adminhtml\Product\Edit\Tab;

use Magiccart\Magicproduct\Model\Status;
use Magiccart\Magicproduct\Model\System\Config\Col;

class Responsive extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;
    protected $_col;

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
        Col $col,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_magicproduct = $magicproduct;
        $this->_col = $col;
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Tabs Information')]);

        if ($model->getId()) {
            $fieldset->addField('magicproduct_id', 'hidden', ['name' => 'magicproduct_id']);
        }

        $fieldset->addField('mobile', 'select',
            [
                'label' => __('Display in Screen <= 360:'),
                'title' => __('Display in Screen <= 360:'),
                'name' => 'mobile',
                'options' => $this->_col->toOptionArray(),
                'value' => 1,
            ]
        );

        $fieldset->addField('portrait', 'select',
            [
                'label' => __('Display in Screen 480:'),
                'title' => __('Display in Screen 480:'),
                'name' => 'portrait',
                'options' => $this->_col->toOptionArray(),
                'value' => 2,
            ]
        );

        $fieldset->addField('landscape', 'select',
            [
                'label' => __('Display in Screen 640:'),
                'title' => __('Display in Screen 640:'),
                'name' => 'landscape',
                'options' => $this->_col->toOptionArray(),
                'value' => 3,
            ]
        );

        $fieldset->addField('tablet', 'select',
            [
                'label' => __('Display in Screen 768:'),
                'title' => __('Display in Screen 768:'),
                'name' => 'tablet',
                'options' => $this->_col->toOptionArray(),
                'value' => 3,
            ]
        );

        $fieldset->addField('desktop', 'select',
            [
                'label' => __('Display in Screen 992:'),
                'title' => __('Display in Screen 992:'),
                'name' => 'desktop',
                'options' => $this->_col->toOptionArray(),
                'value' => 4,
            ]
        );

        $fieldset->addField('visible', 'select',
            [
                'label' => __('Display Visible Items:'),
                'title' => __('Display Visible Items:'),
                'name' => 'visible',
                'options' => $this->_col->toOptionArray(),
                'value' => 4,
            ]
        );

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
            ? __("Edit Product Tabs '%1'", $this->escapeHtml($this->getMagicproduct()->getTitle())) : __('New Product Tabs');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Responsive Information');
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
