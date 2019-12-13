<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-05-09 10:04:29
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Adminhtml\Category\Edit\Tab;

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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Tabs Information')]);

        if ($model->getId()) {
            $fieldset->addField('magicproduct_id', 'hidden', ['name' => 'magicproduct_id']);
        }

        $category_ids = $fieldset->addField('category_ids', 'multiselect',
            [
                'label' => __('Categories'),
                'title' => __('Categories'),
                'name'  => 'category_ids',
                'required' => true,
                'values' => $this->_category->toOptionArray(),
            ]
        );

        $activated = $fieldset->addField('activated', 'select',
            [
                'label' => __('Category Activated'),
                'title' => __('Category Activated'),
                'name'  => 'activated',
                'values' => $this->_category->toOptionArray(),
                'disabled' => true,
                // 'readonly' => true,
            ]
        );

        $activated->setAfterElementHtml(
            '<p class="nm"><small>Product show default</small></p>
            <script type="text/javascript">
            require([
                "jquery",
            ],  function($){
                    jQuery(document).ready(function($) {
                        var map     = "#'.$category_ids->getHtmlId().'";
                        var depend  = "#'.$activated->getHtmlId().'";                  
                        if (!$(map).val()) {$(depend).prop("disabled", true); }
                        else {
                            var activated = $(depend).find(":selected").attr("value");
                            $(depend).prop("disabled", false).html("");
                            $(map).find(":selected").each(function() {
                                var value = $(this).attr("value");
                                var selected = (value == activated) ? true : false;
                                $(depend).append($("<option></option>").attr("value", value).attr("selected", selected).text($(this).text()));
                            });
                        }
                        $(map).change(function() {
                            if (!$(map).val()) $(depend).html("").prop("disabled", true); 
                            else{
                                $(depend).prop("disabled", false).html("");
                                $(map).find(":selected").each(function() {  $(depend).append($("<option></option>").attr("value", $(this).attr("value")).text($(this).text())); });
                            }
                        });
                    })
            })
            </script>
            '
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
            ? __("Edit Category Tabs '%1'", $this->escapeHtml($this->getMagicproduct()->getTitle())) : __('New Category Tabs');
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
