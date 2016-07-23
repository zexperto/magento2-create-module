<?php
				
namespace ITM\File\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$setup->startSetup ();
				
		if(!$context->getVersion()) {
			// your code here" . "\n";
		}

		
		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			//code to upgrade to 1.0.1" . "\n";
		}

		if (version_compare($context->getVersion(), '1.0.2') < 0) {
			 //code to upgrade to 1.0.2" . "\n";
		}

		$setup->endSetup ();
	}
}