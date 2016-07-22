<?php

namespace ITM\File\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface {

	public function __construct(EavSetupFactory $eavSetupFactory) {
		$this->eavSetupFactory = $eavSetupFactory;

	}


	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$eavSetup = $this->eavSetupFactory->create ( [
				'setup' => $setup
		] );


		if (version_compare ( $context->getVersion (), '1.0.1' ) < 0) {
			//your code here
		}

	}

}