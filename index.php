<?php
class Magento2Module {
	var $_vendor;
	var $_module;
	var $_version;
	var $_config;
	var $_helper;
	function __construct($vendor, $module, $version = "1.0.0") {
		$this->_vendor = $vendor;
		$this->_module = $module;
		$this->_version = $version;
		$this->_config = [ ];
	}
	function CreateFolder($path) {
		if (! file_exists ( $path ))
			mkdir ( $path );
	}
	function CreateModuleXmlFile() {
		$path = sprintf ( '%s/%s/etc/module.xml', $this->_vendor, $this->_module );
		$ext_file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/Module/etc/module.xsd">
	<module name="%s_%s" setup_version="%s" />
</config>', $this->_vendor, $this->_module, $this->_version );
		
		fwrite ( $ext_file, $txt );
		fclose ( $ext_file );
	}
	function setConfig($config) {
		$this->_config = $config;
	}
	function CreateRegistrationFile() {
		$path = sprintf ( '%s/%s/registration.php', $this->_vendor, $this->_module );
		$ext_file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( "<?php

\Magento\Framework\Component\ComponentRegistrar::register(
	\Magento\Framework\Component\ComponentRegistrar::MODULE,
	'%s_%s',
	__DIR__ );", $this->_vendor, $this->_module );
		
		fwrite ( $ext_file, $txt );
		fclose ( $ext_file );
	}
	function CreateDataFile() {
		$path = sprintf ( '%s/%s/Helper/Data.php', $this->_vendor, $this->_module );
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php
				
namespace %s\%s\Helper;
		
class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	public function __construct(\Magento\Framework\App\Helper\Context $context) {
		parent::__construct ( $context );
	}
}', $this->_vendor, $this->_module );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateHelper() {
		if ($this->_config ["helper"]) {
			$path = sprintf ( '%s/%s/Helper', $this->_vendor, $this->_module );
			$this->CreateFolder ( $path );
			$this->CreateDataFile ();
		}
	}
	function CreateInstallSchemaFile() {
		$path = sprintf ( '%s/%s/Setup/InstallSchema.php', $this->_vendor, $this->_module );
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php

namespace %s\%s\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface {
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup ();', $this->_vendor, $this->_module );
		
		if ($this->_config ["backend_model"]) {
			foreach ( $this->_config ["backend_model"] as $model ) {
				$txt .= sprintf ( '
		$table = $setup->getConnection ()
				->newTable ( $setup->getTable ( \'%s_%s_%s\' ) )
				->addColumn ( \'id\', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
						\'identity\' => true,
						\'unsigned\' => true,
						\'nullable\' => false,
						\'primary\' => true
						], \'Id\' )
				->addColumn ( \'status\', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [
						\'default\' => null
						], \'Status\' );
		$setup->getConnection ()->createTable ( $table );
						', strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
			}
		}
		
		$txt .= '// your code here
				$setup->endSetup ();
	}
}';
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateUninstallFile() {
		$path = sprintf ( '%s/%s/Setup/Uninstall.php', $this->_vendor, $this->_module );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php

namespace %s\%s\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface {
	public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$setup->startSetup ();
		// your code here" 
		$setup->endSetup ();
	}
}', $this->_vendor, $this->_module );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateUpgradeSchemaFile() {
		$path = sprintf ( '%s/%s/Setup/UpgradeSchema.php', $this->_vendor, $this->_module );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php
				
namespace %s\%s\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$setup->startSetup ();
				
		if(!$context->getVersion()) {
			// your code here" . "\n";
		}

		
		if (version_compare($context->getVersion(), \'1.0.1\') < 0) {
			//code to upgrade to 1.0.1" . "\n";
		}

		if (version_compare($context->getVersion(), \'1.0.2\') < 0) {
			 //code to upgrade to 1.0.2" . "\n";
		}

		$setup->endSetup ();
	}
}', $this->_vendor, $this->_module );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateUpgradeDataFile() {
		$path = sprintf ( '%s/%s/Setup/UpgradeData.php', $this->_vendor, $this->_module );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php

namespace %s\%s\Setup;

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
			\'setup\' => $setup
		] );

		if (version_compare ( $context->getVersion (), \'1.0.1\' ) < 0) {
			//your code here" . "\n";
		}

	}

}', $this->_vendor, $this->_module );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateSetup() {
		if ($this->_config ["setup"]) {
			
			$this->CreateFolder ( sprintf ( '%s/%s/Setup', $this->_vendor, $this->_module ) );
			$this->CreateInstallSchemaFile ();
			$this->CreateUninstallFile ();
			$this->CreateUpgradeSchemaFile ();
			$this->CreateUpgradeDataFile ();
		}
	}
	function CreateBlock() {
		if ($this->_config ["block"]) {
			$this->CreateFolder ( sprintf ( '%s/%s/Block', $this->_vendor, $this->_module ) );
		}
	}
	function CreateApi() {
		if ($this->_config ["api"]) {
			$this->CreateFolder ( sprintf ( '%s/%s/Api', $this->_vendor, $this->_module ) );
		}
	}
	function CreateModel() {
		if ($this->_config ["model"]) {
			$this->CreateFolder ( sprintf ( '%s/%s/Model', $this->_vendor, $this->_module ) );
		}
	}
	function CreateController() {
		if ($this->_config ["controller"]) {
			$this->CreateFolder ( sprintf ( '%s/%s/Controller', $this->_vendor, $this->_module ) );
		}
	}
	function CreateView() {
		if ($this->_config ["view"]) {
			$this->CreateFolder ( sprintf ( '%s/%s/view', $this->_vendor, $this->_module ) );
		}
		
		if ($this->_config ["view"] ["frontend"]) {
			$this->CreateFolder ( sprintf ( '%s/%s/view/frontend', $this->_vendor, $this->_module ) );
			$this->CreateFolder ( sprintf ( '%s/%s/view/frontend/layout', $this->_vendor, $this->_module ) );
			$this->CreateFolder ( sprintf ( '%s/%s/view/frontend/templates', $this->_vendor, $this->_module ) );
		}
		
		if ($this->_config ["view"] ["adminhtml"]) {
			$this->CreateFolder ( sprintf ( '%s/%s/view/adminhtml', $this->_vendor, $this->_module ) );
			$this->CreateFolder ( sprintf ( '%s/%s/view/adminhtml/layout', $this->_vendor, $this->_module ) );
			$this->CreateFolder ( sprintf ( '%s/%s/view/adminhtml/templates', $this->_vendor, $this->_module ) );
		}
	}
	function CreateBackEndModel($model) {
		// Create Block Folder
		$this->CreateFolder ( sprintf ( '%s/%s/Block', $this->_vendor, $this->_module ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Block/Adminhtml', $this->_vendor, $this->_module ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Block/Adminhtml/%s', $this->_vendor, $this->_module, $model ["name"] ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Block/Adminhtml/%s', $this->_vendor, $this->_module, $model ["name"] ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Block/Adminhtml/%s/Edit', $this->_vendor, $this->_module, $model ["name"] ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Block/Adminhtml/%s/Edit/Tab', $this->_vendor, $this->_module, $model ["name"] ) );
		
		// Create Controller Folder
		$this->CreateFolder ( sprintf ( '%s/%s/Controller', $this->_vendor, $this->_module ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Controller/Adminhtml', $this->_vendor, $this->_module ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Controller/Adminhtml/%s', $this->_vendor, $this->_module, $model ["name"] ) );
		
		// Create Model Folder
		$this->CreateFolder ( sprintf ( '%s/%s/Model', $this->_vendor, $this->_module ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Model/Resource', $this->_vendor, $this->_module ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Model/Resource/%s', $this->_vendor, $this->_module, $model ["name"] ) );
		
		// Create Block Files
		// Adminhtml/ {model}.php
		$this->CreateAdminhtmlModelFile ( $model );
		$this->CreateAdminhtmlModelGridFile ( $model );
		$this->CreateAdminhtmlModelEditFile ( $model );
		
		$this->CreateAdminhtmlModelGridEditFormFile ( $model );
		$this->CreateAdminhtmlModelGridEditTabsFile ( $model );
		
		$this->CreateAdminhtmlModelGridEditTabsMainFile ( $model );
		
		// Create Controllers Files
		$this->CreateControllersAdminhtmlModelFile ( $model );
		$this->CreateControllersAdminhtmlModelDeleteFile ( $model );
		$this->CreateControllersAdminhtmlModelEditFile ( $model );
		$this->CreateControllersAdminhtmlModelIndexFile ( $model );
		$this->CreateControllersAdminhtmlModelMassDeleteFile ( $model );
		$this->CreateControllersAdminhtmlModelNewActionFile ( $model );
		$this->CreateControllersAdminhtmlModelSaveFile ( $model );
		
		// Create Model Files
		$this->CreateModelModelFile ( $model );
		$this->CreateModelResourceModelFile ( $model );
		$this->CreateModelResourceModelCollectionFile ( $model );
		
		// Create view Files
		$this->CreateViewAdminhtmlLayoutIndexFile ( $model );
		$this->CreateViewAdminhtmlLayoutEditFile ( $model );
		
		// Create Config Status File
		$this->CreateModelSystemConfigStatusFile ( $model );
	}
	function CreateModelSystemConfigStatusFile($model) {
		$this->CreateFolder ( sprintf ( '%s/%s/Model/System', $this->_vendor, $this->_module ) );
		$this->CreateFolder ( sprintf ( '%s/%s/Model/System/Config', $this->_vendor, $this->_module ) );
		
		$path = sprintf ( '%s/%s/Model/System/Config/Status.php', $this->_vendor, $this->_module );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php
		
namespace %s\%s\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;
		
class Status implements ArrayInterface {
	const ENABLED = 1;
	const DISABLED = 0;
	public function toOptionArray() {
		$options = [
			self::ENABLED => __ ( \'Enabled\' ),
			self::DISABLED => __ ( \'Disabled\' )
		];
		return $options;
	}
}', $this->_vendor, $this->_module );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateAdminhtmlModelGridEditTabsMainFile($model) {
		$path = sprintf ( '%s/%s/Block/Adminhtml/%s/Edit/Tab/Main.php', $this->_vendor, $this->_module, $model ["name"] );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php
		
namespace %s\%s\Block\Adminhtml\%s\Edit\Tab;
				
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use %1$s\%2$s\Model\System\Config\Status;
				
class Main extends Generic implements TabInterface {
	protected $_status;
	public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Status $status, array $data = []) {
		$this->_status = $status;
		parent::__construct ( $context, $registry, $formFactory, $data );
	}

	public function getTabLabel() {
		return __ ( \'Item Information\');
	}

	public function getTabTitle() {
		return __ ( \'Item Information\' );
	}

	public function canShowTab() {
		return true;
	}
	public function isHidden() {
		return false;
	}
	protected function _prepareForm() {
		$model = $this->_coreRegistry->registry ( \'current_%s_%s_%s\' );
		$form = $this->_formFactory->create ();
		$form->setHtmlIdPrefix ( \'item_\' );
		$fieldset = $form->addFieldset ( \'base_fieldset\', [
			\'legend\' => __ ( \'Item Information\' )
		] );
		if ($model->getId ()) {
			$fieldset->addField ( \'id\', \'hidden\', [
				\'name\' => \'id\'
			] );
		}
		$fieldset->addField ( \'status\', \'select\', [
			\'name\' => \'status\',
			\'label\' => __ ( \'Status\' ),
			\'options\' => $this->_status->toOptionArray ()
			] );
		$form->setValues ( $model->getData () );
		$this->setForm ( $form );
		return parent::_prepareForm ();
	}
}
', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function example($model) {
		$path = sprintf ( '%s/%s/', $this->_vendor, $this->_module );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php
			%s/%s
		', $this->_vendor, $this->_module );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateAdminhtmlModelGridEditTabsFile($model) {
		$path = sprintf ( '%s/%s/Block/Adminhtml/%s/Edit/Tabs.php', $this->_vendor, $this->_module, $model ["name"] );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php

namespace %s\%s\Block\Adminhtml\%s\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {
	protected function _construct() {
		parent::_construct ();
			$this->setId ( \'%s_%s_%s_edit_tabs\' );
			$this->setDestElementId ( \'edit_form\' );
			$this->setTitle ( __ ( \'%3$s\' ) );
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateAdminhtmlModelGridEditFormFile($model) {
		$path = sprintf ( '%s/%s/Block/Adminhtml/%s/Edit/Form.php', $this->_vendor, $this->_module, $model ["name"] );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php

namespace %s\%s\Block\Adminhtml\%s\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {

		protected function _construct() {
			parent::_construct ();
			$this->setId ( \'%s_%s_form\' );
			$this->setTitle ( __ ( \'%1$s Information\' ) );
		}
		
		protected function _prepareForm() {
			$form = $this->_formFactory->create ( [
				\'data\' => [
					\'id\' => \'edit_form\',
					\'action\' => $this->getUrl ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/' . strtolower ( $model ["name"] ) . '/save\' ),
					\'method\' => \'post\'
				]
			] );
		$form->setUseContainer ( true );
		$this->setForm ( $form );
		return parent::_prepareForm ();
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateAdminhtmlModelEditFile($model) {
		$path = sprintf ( '%s/%s/Block/Adminhtml/%s/Edit.php', $this->_vendor, $this->_module, $model ["name"] );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php
		
namespace ' . $this->_vendor . '\\' . $this->_module . '\Block\Adminhtml\\' . $model ["name"] . ';
		
class Edit extends \Magento\Backend\Block\Widget\Form\Container {

	protected $_coreRegistry = null;
	
	public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, array $data = []) {
		$this->_coreRegistry = $registry;
		parent::__construct ( $context, $data );
	}
	
	protected function _construct() {
		$this->_objectId = \'id\';
		$this->_controller = \'adminhtml_' . strtolower ( $model ["name"] ) . '\';
		$this->_blockGroup = \'' . $this->_vendor . '_' . $this->_module . '\';
		parent::_construct ();
		$this->buttonList->add ( \'save_and_continue_edit\', [
			\'class\' => \'save\',
			\'label\' => __ ( \'Save and Continue Edit\' ),
			\'data_attribute\' => [
				\'mage-init\' => [
					\'button\' => [
						\'event\' => \'saveAndContinueEdit\',
						\'target\' => \'#edit_form\'
					] 
				] 
			] 
		], 10 );
	}
	public function getHeaderText() {
		$item = $this->_coreRegistry->registry ( \'current_' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '_' . strtolower ( $model ["name"] ) . '\' );
		if ($item->getId ()) {
			return __ ( "Edit Item \'%1\'", $this->escapeHtml ( $item->getId () ) );
		} else {
			return __ ( \'New Item\' );
		}
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateAdminhtmlModelGridFile($model) {
		$path = sprintf ( '%s/%s/Block/Adminhtml/%s/Grid.php', $this->_vendor, $this->_module, $model ["name"] );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		$txt = sprintf ( '<?php
		
namespace %s\%s\Block\Adminhtml\%s;
		
use %1$s\%2$s\Model\System\Config\Status;
		
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
	protected $_status;
	protected $_collectionFactory;
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Backend\Helper\Data $backendHelper,
			\%1$s\%2$s\Model\Resource\%3$s\Collection $collectionFactory,
			Status $status,
			array $data = []
	) {
		$this->_status = $status;
		$this->_collectionFactory = $collectionFactory;
		parent::__construct($context, $backendHelper, $data);
	}
	protected function _construct() {
		parent::_construct();
		$this->setId(\'%6$sGrid\');
		$this->setDefaultSort(\'id\');
		$this->setDefaultDir(\'DESC\');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(false);
	}
	protected function _getStore() {
		$storeId = ( int ) $this->getRequest ()->getParam ( \'store\', 0 );
		return $this->_storeManager->getStore ( $storeId );
	}
	protected function _prepareCollection() {
		try {
			$collection = $this->_collectionFactory->load ();
			$this->setCollection ( $collection );
			parent::_prepareCollection ();
			return $this;
		} catch ( Exception $e ) {
			echo $e->getMessage ();
			die ();
		}
	}
	protected function _prepareColumns() {
		$this->addColumn ( \'id\', [
			\'header\' => __ ( \'ID\' ),
			\'type\' => \'number\',
			\'index\' => \'id\',
			\'header_css_class\' => \'col-id\',
			\'column_css_class\' => \'col-id\'
		] );
		$this->addColumn ( \'status\', [
			\'header\' => __ ( \'Status\' ),
			\'index\' => \'status\',
			\'class\' => \'status\',
			\'type\' => \'options\',
			\'options\' => $this->_status->toOptionArray ()
		] );
		$block = $this->getLayout ()->getBlock ( \'grid.bottom.links\' );
		if ($block) {
			$this->setChild ( \'grid.bottom.links\', $block );
		}
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( \'id\' );
		$this->getMassactionBlock ()->setFormFieldName ( \'id\' );
		$this->getMassactionBlock ()->addItem ( \'delete\', array(
			\'label\' => __ ( \'Delete\' ),
			\'url\' => $this->getUrl ( \'%4$s_%5$s/*/massDelete\' ),
			\'confirm\' => __ ( \'Are you sure?\' )
		) );
		return $this;
	}
	public function getGridUrl() {
		return $this->getUrl ( \'%4$s_%5$s/*/index\', [
			\'_current\' => true
		] );
	}
	public function getRowUrl($row) {
		return $this->getUrl ( \'%4$s_%5$s/*/edit\', [
			\'store\' => $this->getRequest ()->getParam ( \'store\' ),
			\'id\' => $row->getId ()
		] );
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateAdminhtmlModelFile($model) {
		$path = sprintf ( '%s/%s/Block/Adminhtml/%s.php', $this->_vendor, $this->_module, $model ["name"] );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?php

namespace %s\%s\Block\Adminhtml;

class %s extends \Magento\Backend\Block\Widget\Grid\Container {

	protected function _construct() {
		$this->_controller = \'adminhtml_%6$s\';
		$this->_blockGroup = \'%1$s_%2$s\';
		$this->_headerText = __ ( \'%3$s\' );
		$this->_addButtonLabel = __ ( \'Add New Entry\' );
		parent::_construct ();
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateControllersAdminhtmlModelSaveFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Controller/Adminhtml/" . $model ["name"] . "/Save.php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Controller\Adminhtml\\' . $model ["name"] . ';' . "\n\n";
		
		$txt .= "class Save extends \\" . $this->_vendor . "\\" . $this->_module . "\Controller\Adminhtml\\" . $model ["name"] . " {" . "\n";
		
		$txt .= "\t" . 'public function execute() {' . "\n";
		
		$txt .= "\t\t" . 'if ($this->getRequest ()->getPostValue ()) {' . "\n";
		$txt .= "\t\t\t" . 'try {' . "\n";
		$txt .= "\t\t\t\t" . '$model = $this->_objectManager->create ( \'' . $this->_vendor . '\\' . $this->_module . '\Model\\' . $model ["name"] . '\' );' . "\n";
		$txt .= "\t\t\t\t" . '$data = $this->getRequest ()->getPostValue ();' . "\n";
		$txt .= "\t\t\t\t" . '$inputFilter = new \Zend_Filter_Input ( [ ], [ ], $data );' . "\n";
		$txt .= "\t\t\t\t" . '$data = $inputFilter->getUnescaped ();' . "\n";
		$txt .= "\t\t\t\t" . '$id = $this->getRequest ()->getParam ( \'id\' );' . "\n";
		$txt .= "\t\t\t\t" . 'if ($id) {' . "\n";
		$txt .= "\t\t\t\t\t" . '$model->load ( $id );' . "\n";
		$txt .= "\t\t\t\t\t" . 'if ($id != $model->getId ()) {' . "\n";
		$txt .= "\t\t\t\t\t\t" . 'throw new \Magento\Framework\Exception\LocalizedException ( __ ( \'The wrong item is specified.\' ) );' . "\n";
		$txt .= "\t\t\t\t\t" . '}' . "\n";
		$txt .= "\t\t\t\t" . '}' . "\n";
		$txt .= "\t\t\t\t" . '$model->setData ( $data );' . "\n";
		$txt .= "\t\t\t\t" . '$session = $this->_objectManager->get ( \'Magento\Backend\Model\Session\' );' . "\n";
		$txt .= "\t\t\t\t" . '$session->setPageData ( $model->getData () );' . "\n";
		$txt .= "\t\t\t\t" . '$model->save ();' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addSuccess ( __ ( \'You saved the item.\' ) );' . "\n";
		$txt .= "\t\t\t\t" . '$session->setPageData ( false );' . "\n";
		$txt .= "\t\t\t\t" . 'if ($this->getRequest ()->getParam ( \'back\' )) {' . "\n";
		$txt .= "\t\t\t\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/edit\', [' . "\n";
		$txt .= "\t\t\t\t\t\t" . '\'id\' => $model->getId ()' . "\n";
		$txt .= "\t\t\t\t\t" . '] );' . "\n";
		$txt .= "\t\t\t\t\t" . 'return;' . "\n";
		$txt .= "\t\t\t\t" . '}' . "\n";
		$txt .= "\t\t\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/\' );' . "\n";
		$txt .= "\t\t\t\t" . 'return;' . "\n";
		$txt .= "\t\t\t" . '} catch ( \Magento\Framework\Exception\LocalizedException $e ) {' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addError ( $e->getMessage () );' . "\n";
		$txt .= "\t\t\t\t" . '$id = ( int ) $this->getRequest ()->getParam ( \'id\' );' . "\n";
		$txt .= "\t\t\t\t" . 'if (! empty ( $id )) {' . "\n";
		$txt .= "\t\t\t\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/edit\', [' . "\n";
		$txt .= "\t\t\t\t\t\t" . '\'id\' => $id' . "\n";
		$txt .= "\t\t\t\t\t" . '] );' . "\n";
		$txt .= "\t\t\t\t" . '} else {' . "\n";
		$txt .= "\t\t\t\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/new\');' . "\n";
		$txt .= "\t\t\t\t" . '}' . "\n";
		$txt .= "\t\t\t\t" . 'return;' . "\n";
		$txt .= "\t\t\t" . '} catch ( \Exception $e ) {' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addError ( __ ( \'Something went wrong while saving the item data. Please review the error log.\' ) );' . "\n";
		$txt .= "\t\t\t\t" . '$this->_objectManager->get ( \'Psr\Log\LoggerInterface\' )->critical ( $e );' . "\n";
		$txt .= "\t\t\t\t" . '$this->_objectManager->get ( \'Magento\Backend\Model\Session\' )->setPageData ( $data );' . "\n";
		$txt .= "\t\t\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/edit\', [ ' . "\n";
		$txt .= "\t\t\t\t\t" . '\'id\' => $this->getRequest ()->getParam ( \'id\' )' . "\n";
		$txt .= "\t\t\t\t" . '] );' . "\n";
		$txt .= "\t\t\t\t" . 'return;' . "\n";
		$txt .= "\t\t\t" . '}' . "\n";
		$txt .= "\t\t" . '}' . "\n";
		$txt .= "\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/\' );' . "\n";
		
		$txt .= "\t" . "}" . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateControllersAdminhtmlModelNewActionFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Controller/Adminhtml/" . $model ["name"] . "/NewAction.php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Controller\Adminhtml\\' . $model ["name"] . ';' . "\n\n";
		
		$txt .= "class NewAction extends \\" . $this->_vendor . "\\" . $this->_module . "\Controller\Adminhtml\\" . $model ["name"] . " {" . "\n";
		$txt .= "\t" . 'public function execute() {' . "\n";
		$txt .= "\t\t" . '$this->_forward(\'edit\');' . "\n";
		$txt .= "\t" . "}" . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateControllersAdminhtmlModelMassDeleteFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Controller/Adminhtml/" . $model ["name"] . "/MassDelete.php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Controller\Adminhtml\\' . $model ["name"] . ';' . "\n\n";
		
		$txt .= "class MassDelete extends \\" . $this->_vendor . "\\" . $this->_module . "\Controller\Adminhtml\\" . $model ["name"] . " {" . "\n";
		$txt .= "\t" . 'public function execute() {' . "\n";
		
		$txt .= "\t\t" . '$itemsIds = $this->getRequest()->getParam(\'id\');' . "\n";
		$txt .= "\t\t" . 'if (!is_array($itemsIds)) {' . "\n";
		$txt .= "\t\t\t" . '$this->messageManager->addError(__(\'Please select item(s).\'));' . "\n";
		$txt .= "\t\t" . '} else {' . "\n";
		$txt .= "\t\t\t" . 'try {' . "\n";
		$txt .= "\t\t\t\t" . 'foreach ($itemsIds as $itemId) {' . "\n";
		$txt .= "\t\t\t\t\t" . '$model = $this->_objectManager->create(\'' . $this->_vendor . '\\' . $this->_module . '\Model\\' . $model ["name"] . '\');' . "\n";
		$txt .= "\t\t\t\t\t" . '$model->load($itemId);' . "\n";
		$txt .= "\t\t\t\t\t" . '$model->delete();' . "\n";
		$txt .= "\t\t\t\t" . '}' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addSuccess(' . "\n";
		$txt .= "\t\t\t\t\t" . '__(\'A total of %1 record(s) have been deleted.\', count($itemsIds))' . "\n";
		$txt .= "\t\t\t\t" . ');' . "\n";
		$txt .= "\t\t\t" . '} catch (\Magento\Framework\Exception\LocalizedException $e) {' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addError($e->getMessage());' . "\n";
		$txt .= "\t\t\t" . '} catch (\Exception $e) {' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addException($e, __(\'An error occurred while deleting record(s).\'));' . "\n";
		$txt .= "\t\t\t" . '}' . "\n";
		$txt .= "\t\t" . '}' . "\n";
		$txt .= "\t\t" . '$this->_redirect(\'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/\');' . "\n";
		
		$txt .= "\t" . "}" . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateControllersAdminhtmlModelIndexFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Controller/Adminhtml/" . $model ["name"] . "/Index.php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Controller\Adminhtml\\' . $model ["name"] . ';' . "\n\n";
		
		$txt .= "class Index extends \\" . $this->_vendor . "\\" . $this->_module . "\Controller\Adminhtml\\" . $model ["name"] . " {" . "\n";
		
		$txt .= "\t" . 'public function execute() {' . "\n";
		$txt .= "\t\t" . '$resultPage = $this->resultPageFactory->create();' . "\n";
		$txt .= "\t\t" . '$resultPage->setActiveMenu(\'' . $this->_vendor . '_' . $this->_module . '::' . strtolower ( $this->_module ) . '\');' . "\n";
		$txt .= "\t\t" . '$resultPage->getConfig()->getTitle()->prepend(__(\'' . $model ["name"] . '\'));' . "\n";
		$txt .= "\t\t" . '$resultPage->addBreadcrumb(__(\'' . $this->_vendor . '\'), __(\'' . $this->_vendor . '\'));' . "\n";
		$txt .= "\t\t" . '$resultPage->addBreadcrumb(__(\'' . $model ["name"] . '\'), __(\'' . $model ["name"] . '\'));' . "\n";
		$txt .= "\t\t" . 'return $resultPage;' . "\n";
		
		$txt .= "\t" . "}" . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateControllersAdminhtmlModelEditFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Controller/Adminhtml/" . $model ["name"] . "/Edit.php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Controller\Adminhtml\\' . $model ["name"] . ';' . "\n\n";
		
		$txt .= "class Edit extends \\" . $this->_vendor . "\\" . $this->_module . "\Controller\Adminhtml\\" . $model ["name"] . " {" . "\n";
		
		$txt .= "\t" . 'public function execute() {' . "\n";
		$txt .= "\t\t" . ' $id = $this->getRequest()->getParam(\'id\');' . "\n";
		$txt .= "\t\t" . '$model = $this->_objectManager->create(\'' . $this->_vendor . '\\' . $this->_module . '\Model\\' . $model ["name"] . '\');' . "\n";
		$txt .= "\t\t" . 'if ($id) {' . "\n";
		$txt .= "\t\t\t" . '$model->load($id);' . "\n";
		$txt .= "\t\t\t" . 'if (!$model->getId()) {' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addError(__(\'This item no longer exists.\'));' . "\n";
		$txt .= "\t\t\t\t" . '$this->_redirect(\'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*\');' . "\n";
		$txt .= "\t\t\t\t" . 'return;' . "\n";
		$txt .= "\t\t\t" . '}' . "\n";
		$txt .= "\t\t" . '}' . "\n";
		$txt .= "\t\t" . '$data = $this->_objectManager->get(\'Magento\Backend\Model\Session\')->getPageData(true);' . "\n";
		$txt .= "\t\t" . 'if (!empty($data)) {' . "\n";
		$txt .= "\t\t\t" . '$model->addData($data);' . "\n";
		$txt .= "\t\t" . '}' . "\n";
		$txt .= "\t\t" . '$resultPage = $this->resultPageFactory->create();' . "\n";
		$txt .= "\t\t" . 'if ($id) {' . "\n";
		$txt .= "\t\t\t" . '$resultPage->getConfig()->getTitle()->prepend(__(\'Edit Items Entry\'));' . "\n";
		$txt .= "\t\t" . '}else{' . "\n";
		$txt .= "\t\t\t" . '$resultPage->getConfig()->getTitle()->prepend(__(\'Add Items Entry\'));' . "\n";
		$txt .= "\t\t" . '}' . "\n";
		$txt .= "\t\t" . '$this->_coreRegistry->register(\'current_' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '_' . strtolower ( $model ["name"] ) . '\', $model);' . "\n";
		$txt .= "\t\t" . '$this->_initAction();' . "\n";
		$txt .= "\t\t" . '$this->_view->getLayout()->getBlock(\'' . strtolower ( $model ["name"] ) . '_' . strtolower ( $model ["name"] ) . '_edit\');' . "\n";
		$txt .= "\t\t" . '$this->_view->renderLayout();' . "\n";
		
		$txt .= "\t" . "}" . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateControllersAdminhtmlModelDeleteFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Controller/Adminhtml/" . $model ["name"] . "/Delete.php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Controller\Adminhtml\\' . $model ["name"] . ';' . "\n\n";
		
		$txt .= "class Delete extends \\" . $this->_vendor . "\\" . $this->_module . "\Controller\Adminhtml\\" . $model ["name"] . " {" . "\n";
		
		$txt .= "\t" . 'public function execute() {' . "\n";
		
		$txt .= "\t\t" . '$id = $this->getRequest ()->getParam ( \'id\' );' . "\n";
		$txt .= "\t\t" . 'if ($id) {' . "\n";
		$txt .= "\t\t\t" . 'try {' . "\n";
		$txt .= "\t\t\t\t" . '$model = $this->_objectManager->create ( \'' . $this->_vendor . '\\' . $this->_module . '\Model\\' . $model ["name"] . '\' );' . "\n";
		$txt .= "\t\t\t\t" . '$model->load ( $id );' . "\n";
		$txt .= "\t\t\t\t" . '$model->delete ();' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addSuccess ( __ ( \'You deleted the item.\' ) );' . "\n";
		$txt .= "\t\t\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/\' );' . "\n";
		
		$txt .= "\t\t\t\t" . 'return;' . "\n";
		$txt .= "\t\t\t" . '} catch ( \Magento\Framework\Exception\LocalizedException $e ) {' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addError ( $e->getMessage () );' . "\n";
		$txt .= "\t\t\t" . '} catch ( \Exception $e ) {' . "\n";
		$txt .= "\t\t\t\t" . '$this->messageManager->addError ( __ ( \'We can\\\'t delete item right now. Please review the log and try again.\' ) );' . "\n";
		$txt .= "\t\t\t\t" . '$this->_objectManager->get ( \'Psr\Log\LoggerInterface\' )->critical ( $e );' . "\n";
		$txt .= "\t\t\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/edit\', [' . "\n";
		$txt .= "\t\t\t\t\t\t" . '\'id\' => $this->getRequest ()->getParam ( \'id\' )' . "\n";
		$txt .= "\t\t\t\t" . '] );' . "\n";
		$txt .= "\t\t\t\t" . 'return;' . "\n";
		$txt .= "\t\t\t" . '}' . "\n";
		$txt .= "\t\t" . '}' . "\n";
		$txt .= "\t\t" . '$this->messageManager->addError ( __ ( \'We can\\\'t find a item to delete.\' ) );' . "\n";
		$txt .= "\t\t" . '$this->_redirect ( \'' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '/*/\' );' . "\n";
		
		$txt .= "\t" . "}" . "\n";
		
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateControllersAdminhtmlModelFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Controller/Adminhtml/" . $model ["name"] . ".php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Controller\Adminhtml;' . "\n\n";
		$txt .= "abstract class " . $model ["name"] . " extends \Magento\Backend\App\Action {" . "\n";
		$txt .= "\t" . 'protected $_coreRegistry;' . "\n";
		$txt .= "\t" . 'protected $resultForwardFactory;' . "\n";
		$txt .= "\t" . 'protected $resultPageFactory;' . "\n";
		
		$txt .= "\t" . 'public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {' . "\n";
		$txt .= "\t\t" . '$this->_coreRegistry = $coreRegistry;' . "\n";
		$txt .= "\t\t" . 'parent::__construct ( $context );' . "\n";
		$txt .= "\t\t" . '$this->resultForwardFactory = $resultForwardFactory;' . "\n";
		$txt .= "\t\t" . '$this->resultPageFactory = $resultPageFactory;' . "\n";
		$txt .= "\t" . "}" . "\n";
		
		$txt .= "\t" . 'protected function _initAction() {' . "\n";
		$txt .= "\t\t" . '$this->_view->loadLayout ();' . "\n";
		$txt .= "\t\t" . '$this->_setActiveMenu ( \'' . $this->_vendor . '_' . $this->_module . '::' . strtolower ( $model ["name"] ) . '\' )->_addBreadcrumb ( __ ( \'' . $model ["name"] . '\' ), __ ( \'' . $model ["name"] . '\' ) );' . "\n";
		$txt .= "\t\t" . 'return $this;' . "\n";
		$txt .= "\t" . "}" . "\n";
		
		$txt .= "\t" . 'protected function _isAllowed() {' . "\n";
		$txt .= "\t\t" . 'return $this->_authorization->isAllowed ( \'' . $this->_vendor . '_' . $this->_module . '::' . strtolower ( $model ["name"] ) . '\' );' . "\n";
		$txt .= "\t" . "}" . "\n";
		
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateModelResourceModelCollectionFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Model/Resource/" . "/" . $model ["name"] . "/Collection.php";
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Model\Resource\\' . $model ["name"] . ';' . "\n\n";
		$txt .= "class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {" . "\n";
		$txt .= "\t" . 'protected function _construct() {' . "\n";
		$txt .= "\t\t" . '$this->_init(\'' . $this->_vendor . '\\' . $this->_module . '\Model\\' . $model ["name"] . '\', \'' . $this->_vendor . '\\' . $this->_module . '\Model\Resource\\' . $model ["name"] . '\');' . "\n";
		$txt .= "\t" . '}' . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateModelResourceModelFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Model/Resource/" . "/" . $model ["name"] . ".php";
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Model\Resource;' . "\n\n";
		$txt .= "class " . $model ["name"] . " extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {" . "\n";
		$txt .= "\t" . 'protected function _construct() {' . "\n";
		$txt .= "\t\t" . '$this->_init(\'' . strtolower ( $this->_vendor . '_' . $this->_module . '_' . $model ["table"] ) . '\', \'id\');' . "\n";
		$txt .= "\t" . '}' . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateModelModelFile($model) {
		$path = $this->_vendor . "/" . $this->_module . "/" . "Model/" . "/" . $model ["name"] . ".php";
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = '<?php' . "\n\n";
		$txt .= 'namespace ' . $this->_vendor . '\\' . $this->_module . '\Model;' . "\n\n";
		$txt .= "class " . $model ["name"] . " extends \Magento\Framework\Model\AbstractModel {" . "\n";
		$txt .= "\t" . 'protected function _construct() {' . "\n";
		$txt .= "\t\t" . 'parent::_construct();' . "\n";
		$txt .= "\t\t" . '$this->_init(\'' . $this->_vendor . '\\' . $this->_module . '\Model\Resource\\' . $model ["name"] . '\');' . "\n";
		$txt .= "\t" . '}' . "\n";
		$txt .= '}';
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	
	
	function CreateViewAdminhtmlLayoutIndexFile($model) {
		$path = sprintf ( "%s/%s/view/adminhtml/layout/%s_%s_%s_edit" . ".xml", $this->_vendor, $this->_module, strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="admin-2columns-left" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/Layout/etc/page_configuration.xsd">
	<body>
		<referenceContainer name="left">
			<block class="%s\%s\Block\Adminhtml\%s\Edit\Tabs" name="%s_%s_%s_edit_tabs">
				<block class="%1$s\%2$s\Block\Adminhtml\%3$s\Edit\Tab\Main" name="' . strtolower ( $this->_vendor ) . '_' . strtolower ( $this->_module ) . '_' . strtolower ( $model ["name"] ) . '_edit_tab_main"/>
				<action method="addTab">
					<argument name="name" xsi:type="string">main_section</argument>
					<argument name="block" xsi:type="string">%4$s_%5$s_%6$s_edit_tab_main</argument>
				</action>
			</block>
		</referenceContainer>
		<referenceContainer name="content">
			<block class="' . $this->_vendor . '\\' . $this->_module . '\Block\Adminhtml\\' . $model ["name"] . '\Edit" name="' . strtolower ( $this->_module ) . '_' . strtolower ( $model ["name"] ) . '_edit"/>
		</referenceContainer>
	</body>
</page>', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	
	
	function CreateViewAdminhtmlLayoutEditFile($model) {
		// $path = sprintf('%s/%s/view/adminhtml/layout/menu.xml',$this->_vendor,$this->_module);
		$path = sprintf ( "%s/%s/view/adminhtml/layout/%s_%s_%s_index" . ".xml", $this->_vendor, $this->_module, strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/Layout/etc/page_configuration.xsd">
	<body>
		<referenceContainer name="content">
			<block class="%s\%s\Block\Adminhtml\%s" name="%s_%s_container"/>
		</referenceContainer>
	</body>
</page>', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_module ), strtolower ( $model ["name"] ) );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateBackEndModels() {
		if ($this->_config ["backend_model"]) {
			foreach ( $this->_config ["backend_model"] as $model ) {
				$this->CreateBackEndModel ( $model );
			}
			$this->CreateMenuFile ( $model );
			$this->CreateRoutesFile ( $model );
		}
	}
	function CreateMenuFile($backend_model) {
		$this->CreateFolder ( $this->_vendor . "/" . $this->_module . "/" . "etc/adminhtml" );
		
		$path = sprintf ( '%s/%s/etc/adminhtml/menu.xml', $this->_vendor, $this->_module );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../Backend/etc/menu.xsd">
	<menu>
		<add id="%3$s_%4$s::%4$s" title="%2$s" module="%1$s_%2$s" sortOrder="0" parent="%3$s::base"  resource="%1$s_%2$s::main"/>', $this->_vendor, $this->_module, strtolower ( $this->_vendor ), strtolower ( $this->_module ) );
		$txt .= "\n";
		
		foreach ( $this->_config ["backend_model"] as $model ) {
			$txt .= "\t\t" . sprintf ( '<add id="%4$s_%5$s::%6$s" title="%3$s" module="%1$s_%2$s" sortOrder="10" parent="%4$s_%5$s::%5$s" action="%4$s_%5$s/%6$s/" resource="%1$s_%2$s::%3$s"/>', $this->_vendor, $this->_module, $model ["name"], strtolower ( $this->_vendor ), strtolower ( $this->_module ), strtolower ( $model ["name"] ) ) . "\n";
		}
		
		$txt .= '	</menu>
</config>';
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function CreateRoutesFile($backend_model) {
		$this->CreateFolder ( $this->_vendor . "/" . $this->_module . "/" . "etc/adminhtml" );
		
		$path = sprintf ( '%s/%s/etc/adminhtml/routes.xml', $this->_vendor, $this->_module );
		
		$file = fopen ( $path, "w" ) or die ( "Unable to open file!" );
		
		$txt = sprintf ( '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../lib/internal/Magento/Framework/App/etc/routes.xsd">
	<router id="admin">
		<route id="%3$s_%4$s" frontName="%3$s_%4$s">
			<module name="%1$s_%2$s" />
		</route>
	</router>
</config>', $this->_vendor, $this->_module, strtolower ( $this->_vendor ), strtolower ( $this->_module ) );
		
		fwrite ( $file, $txt );
		fclose ( $file );
	}
	function create() {
		$this->CreateFolder ( $this->_vendor );
		$this->CreateFolder ( $this->_vendor . "/" . $this->_module );
		$this->CreateFolder ( $this->_vendor . "/" . $this->_module . "/" . "etc" );
		
		$this->CreateModuleXmlFile ();
		$this->CreateRegistrationFile ();
		
		$this->CreateHelper ();
		$this->CreateSetup ();
		$this->CreateBlock ();
		$this->CreateApi ();
		$this->CreateModel ();
		$this->CreateController ();
		$this->CreateView ();
		$this->CreateBackEndModels ();
	}
	function __toString() {
		return $this->_vendor . " - " . $this->_module . " - " . $this->_version;
	}
}

// --------------------------------------------------------------
$config_array = [ 
		"helper" => true,
		"setup" => true,
		"block" => true,
		"controller" => true,
		"model" => true,
		"api" => true,
		"view" => [ 
				"frontend" => true,
				"adminhtml" => true 
		],
		// vernder and module name will add as prefix to table, only id and status will create
		"backend_model" => [ 
				[ 
						"name" => "Box",
						"table" => "box" 
				] 
		] 
];

$mod = new Magento2Module ( "ITM", "File" );
$mod->setConfig ( $config_array );
echo $mod->create ( $config_array );