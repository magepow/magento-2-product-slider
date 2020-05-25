<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-01-14 15:55:01
 * @@Function:
 */

namespace Magiccart\Magicproduct\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)

    {

        $this->eavSetupFactory = $eavSetupFactory;

    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        /** @var EavSetup $eavSetup */

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

     

        /**
        * Add attributes to the eav/attribute
        */

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'featured',
            [
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Featured Product',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'is_used_in_grid' => true,
            'is_filterable_in_grid' => true,
            'apply_to' => ''
            ]
            );

    }
    
}
