<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-03-23 16:22:59
 * @@Function:
 */

namespace Magiccart\Magicproduct\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('magiccart_magicproduct'))
            ->addColumn(
                'magicproduct_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Magicproduct ID'
            )
            ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false], 'Title')
            ->addColumn('identifier', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
            ->addColumn('type_id', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Type')
            ->addColumn('config', Table::TYPE_TEXT, '2M', [], 'Config')
            ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Status')
            ->addIndex($installer->getIdxName('magicproduct_identifier', ['identifier']), ['identifier'])
            ->setComment('Magiccart Magicproduct');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
