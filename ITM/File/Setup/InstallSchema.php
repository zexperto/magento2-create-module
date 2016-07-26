<?php

namespace ITM\File\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface {
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup ();
		$table = $setup->getConnection ()
				->newTable ( $setup->getTable ( 'itm_file_box' ) )
				->addColumn ( 'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary' => true
						], 'Id' )
				->addColumn ( 'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [
						'default' => null
						], 'Status' );
		$setup->getConnection ()->createTable ( $table );
						// your code here
				$setup->endSetup ();
	}
}