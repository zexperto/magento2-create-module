<?php 
class Magento2Module{
	var $_vendor;
	var $_module;
	var $_version;
	var $_config;
	var $_helper;
	function __construct($vendor,$module,$version = "1.0.0"){
		$this->_vendor = $vendor;
		$this->_module = $module;
		$this->_version = $version;
		$this->_config = [];
	}
	function CreateFolder($path){
		if(!file_exists($path))
			mkdir($path);
	}
	function CreateModuleXmlFile(){
		$url = $this->_vendor."/".$this->_module."/"."etc"."/"."module.xml";
		$module_xml = fopen($url, "w") or die("Unable to open file!");
		$txt = '<?xml version="1.0"?>'."\n";
		$txt .='<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/Module/etc/module.xsd">'."\n";
		$txt .="\t".'<module name="'.$this->_vendor.'_'.$this->_module.'" setup_version="'.$this->_version.'" />'."\n";
		$txt .='</config>';
		fwrite($module_xml, $txt);
		fclose($module_xml);
	}
	function setConfig($config){
		$this->_config = $config;
	}
	function CreateRegistrationFile(){
		$url = $this->_vendor."/".$this->_module."/"."registration.php";
		$registration_php = fopen($url, "w") or die("Unable to open file!");
		$txt = "<?php"."\n\n";
		$txt .="\Magento\Framework\Component\ComponentRegistrar::register("."\n";
		$txt .="\t"."\Magento\Framework\Component\ComponentRegistrar::MODULE,"."\n";
		$txt .="\t"."'".$this->_vendor."_".$this->_module."',"."\n";
		$txt .="\t"."__DIR__ );";
		fwrite($registration_php, $txt);
		fclose($registration_php);
	}
	function CreateDataFile(){
		$url = $this->_vendor."/".$this->_module."/"."Helper"."/"."Data.php";
		$file = fopen($url, "w") or die("Unable to open file!");
		
		$txt = "<?php"."\n\n";
		$txt .= "namespace ".$this->_vendor."\\".$this->_module."\Helper;"."\n\n";
		$txt .= "class Data extends \Magento\Framework\App\Helper\AbstractHelper {"."\n";
		$txt .= "\t".'public function __construct(\Magento\Framework\App\Helper\Context $context) {'."\n";
		$txt .= "\t\t".'parent::__construct ( $context );'."\n";
		$txt .= "\t"."}"."\n";
		$txt .= "}";
		
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateHelper(){
		if($this->_config["helper"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/"."Helper");
			$this->CreateDataFile();
		}
	}
	function CreateInstallSchemaFile(){
		$url = $this->_vendor."/".$this->_module."/"."Setup"."/"."InstallSchema.php";
		$file = fopen($url, "w") or die("Unable to open file!");
		
		$txt = "<?php"."\n\n";
		$txt .= "namespace ".$this->_vendor."\\".$this->_module."\Setup;"."\n\n";
		$txt .= "use Magento\Framework\Setup\InstallSchemaInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\SchemaSetupInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\ModuleContextInterface;"."\n\n";
		$txt .= "class InstallSchema implements InstallSchemaInterface {"."\n\n";
		$txt .= "\t".'public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {'."\n";
		$txt .= "\t\t".'$setup->startSetup ();'."\n";
		$txt .= "\t\t"."// your code here"."\n";
		$txt .= "\t\t".'$setup->endSetup ();'."\n";
		$txt .= "\t"."}"."\n";
		$txt .= "}";
		
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateUninstallFile(){
		$url = $this->_vendor."/".$this->_module."/"."Setup"."/"."Uninstall.php";
		$file = fopen($url, "w") or die("Unable to open file!");
		
		$txt = "<?php"."\n\n";
		$txt = "namespace ".$this->_vendor."\\".$this->_module."\Setup;"."\n\n";
		$txt .= "use Magento\Framework\Setup\UninstallInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\SchemaSetupInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\ModuleContextInterface;"."\n\n";
		$txt .= "class Uninstall implements UninstallInterface {"."\n\n";
		$txt .= "\t".'public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context){'."\n";
		$txt .= "\t\t".'$setup->startSetup ();'."\n";
		$txt .= "\t\t"."// your code here"."\n";
		$txt .= "\t\t".'$setup->endSetup ();'."\n";
		$txt .= "\t"."}"."\n";
		$txt .= "}";
		
		fwrite($file, $txt);
		fclose($file);
		
	}
	function CreateUpgradeSchemaFile(){
		$url = $this->_vendor."/".$this->_module."/"."Setup"."/"."UpgradeSchema.php";
		$file = fopen($url, "w") or die("Unable to open file!");
		
		$txt = "<?php"."\n\n";
		$txt = "namespace ".$this->_vendor."\\".$this->_module."\Setup;"."\n\n";
		$txt .= "use Magento\Framework\Setup\UpgradeSchemaInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\SchemaSetupInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\ModuleContextInterface;"."\n\n";
		$txt .= "class UpgradeSchema implements UpgradeSchemaInterface {"."\n\n";
		$txt .= "\t".'public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){'."\n";
		$txt .= "\t\t".'$setup->startSetup ();'."\n\n";
		
		$txt .= "\t\t".'if(!$context->getVersion()) {'."\n";
		$txt .= "\t\t\t"."// your code here"."\n";
		$txt .= "\t\t".'}'."\n\n";
		
		
		$txt .= "\t\t".'if (version_compare($context->getVersion(), \'1.0.1\') < 0) {'."\n";
		$txt .= "\t\t\t"."//code to upgrade to 1.0.1"."\n";
		$txt .= "\t\t".'}'."\n\n";
		
		
		$txt .= "\t\t".'if (version_compare($context->getVersion(), \'1.0.2\') < 0) {'."\n";
		$txt .= "\t\t\t"." //code to upgrade to 1.0.2"."\n";
		$txt .= "\t\t".'}'."\n\n";
		
		$txt .= "\t\t".'$setup->endSetup ();'."\n";
		$txt .= "\t"."}"."\n";
		$txt .= "}";
		
		fwrite($file, $txt);
		fclose($file);
		
	}
	function CreateUpgradeDataFile(){
		$url = $this->_vendor."/".$this->_module."/"."Setup"."/"."UpgradeData.php";
		$file = fopen($url, "w") or die("Unable to open file!");
		
		$txt = "<?php"."\n\n";
		$txt .= "namespace ".$this->_vendor."\\".$this->_module."\Setup;"."\n\n";
		$txt .= "use Magento\Eav\Setup\EavSetup;"."\n";
		$txt .= "use Magento\Eav\Setup\EavSetupFactory;"."\n";
		$txt .= "use Magento\Framework\Setup\UpgradeDataInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\ModuleContextInterface;"."\n";
		$txt .= "use Magento\Framework\Setup\ModuleDataSetupInterface;"."\n\n";
		
		$txt .= "class UpgradeData implements UpgradeDataInterface {"."\n\n";
		$txt .= "\t".'public function __construct(EavSetupFactory $eavSetupFactory) {'."\n";
		$txt .= "\t\t".'$this->eavSetupFactory = $eavSetupFactory;'."\n\n";
		$txt .= "\t".'}'."\n\n\n";
		
		$txt .= "\t".'public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {'."\n";
		
		$txt .= "\t\t".'$eavSetup = $this->eavSetupFactory->create ( ['."\n";
		$txt .= "\t\t\t\t".'\'setup\' => $setup'."\n";
		$txt .= "\t\t".'] );'."\n\n\n";
		
		$txt .= "\t\t".'if (version_compare ( $context->getVersion (), \'1.0.1\' ) < 0) {'."\n";
		$txt .= "\t\t\t"."//your code here"."\n";
		$txt .= "\t\t".'}'."\n\n";
		
		$txt .= "\t".'}'."\n\n";

		$txt .= "}";
		
		fwrite($file, $txt);
		fclose($file);
		
	}
	function CreateSetup(){
		if($this->_config["setup"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/"."Setup");
			$this->CreateInstallSchemaFile();
			$this->CreateUninstallFile();
			$this->CreateUpgradeSchemaFile();
			$this->CreateUpgradeDataFile();
		}
	}
	function CreateBlock(){
		if($this->_config["block"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/"."Block");
		}
	}
	function CreateApi(){
		if($this->_config["api"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/"."Api");
		}
	}
	function CreateModel(){
		if($this->_config["model"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/"."Model");
		}
	}
	function CreateController(){
		if($this->_config["controller"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/"."Controller");
		}
	}
	function CreateView(){
		if($this->_config["view"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/view");
		}
		
		if($this->_config["view"]["frontend"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/view/frontend");
			$this->CreateFolder($this->_vendor."/".$this->_module."/view/frontend/layout");
			$this->CreateFolder($this->_vendor."/".$this->_module."/view/frontend/templates");
		}
		
		if($this->_config["view"]["adminhtml"]){
			$this->CreateFolder($this->_vendor."/".$this->_module."/view/adminhtml");
			$this->CreateFolder($this->_vendor."/".$this->_module."/view/adminhtml/layout");
			$this->CreateFolder($this->_vendor."/".$this->_module."/view/adminhtml/templates");
		}
	}
	
	function CreateBackEndModel($model){
		// Create Block Folder
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Block");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Block/Adminhtml");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]);
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]."/Edit");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]."/Edit/Tab");
		
		// Create Controller Folder		
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Controller");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Controller/Adminhtml");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]);
		
		// Create Model Folder		
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Model");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Model/Resource");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Model/Resource/".$model["name"]);
		
			// Create Block Files
			// Adminhtml/ {model}.php
		$this->CreateAdminhtmlModelFile($model);
		$this->CreateAdminhtmlModelGridFile($model);
		$this->CreateAdminhtmlModelEditFile($model);
		
		
		$this->CreateAdminhtmlModelGridEditFormFile($model);
		$this->CreateAdminhtmlModelGridEditTabsFile($model);
		
		$this->CreateAdminhtmlModelGridEditTabsMainFile($model);
		
		
		// Create Controllers Files
		$this->CreateControllersAdminhtmlModelFile($model);
		$this->CreateControllersAdminhtmlModelDeleteFile($model);
		$this->CreateControllersAdminhtmlModelEditFile($model);
		$this->CreateControllersAdminhtmlModelIndexFile($model);
		$this->CreateControllersAdminhtmlModelMassDeleteFile($model);
		$this->CreateControllersAdminhtmlModelNewActionFile($model);
		$this->CreateControllersAdminhtmlModelSaveFile($model);
		
		
		// Create Model Files
		$this->CreateModelModelFile($model);
		$this->CreateModelResourceModelFile($model);
		$this->CreateModelResourceModelCollectionFile($model);
		
		// Create view Files
		$this->CreateViewAdminhtmlLayoutIndexFile($model);
		$this->CreateViewAdminhtmlLayoutEditFile($model);
		
		// Create Config Status File
		$this->CreateModelSystemConfigStatusFile($model);
	}
	function CreateModelSystemConfigStatusFile($model){
		
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Model/System");
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."Model/System/Config");
		
		$url = $this->_vendor."/".$this->_module."/"."Model/System/Config/Status.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Model\System\Config;'."\n\n";
		
		$txt .= 'use Magento\Framework\Option\ArrayInterface;'."\n";
		
		$txt .= "class Status implements ArrayInterface {"."\n";
	
	
		$txt .= "\t".'const ENABLED = 1;'."\n";
		$txt .= "\t".'const DISABLED = 0;'."\n";
		
		$txt .= "\t".'public function toOptionArray() {'."\n";
		$txt .= "\t\t".'$options = ['."\n";
		$txt .= "\t\t\t".'self::ENABLED => __ ( \'Enabled\' ),'."\n";
		$txt .= "\t\t\t".'self::DISABLED => __ ( \'Disabled\' )'."\n";
		$txt .= "\t\t".'];'."\n";
		$txt .= "\t\t".'return $options;'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= "\t".''."\n";
	
		
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateAdminhtmlModelGridEditTabsMainFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]."/Edit/Tab/Main.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].'\Edit\Tab;'."\n\n";
		$txt .= 'use Magento\Backend\Block\Widget\Form\Generic;'."\n";
		$txt .= 'use Magento\Backend\Block\Widget\Tab\TabInterface;'."\n";
		$txt .= 'use Magento\Backend\Block\Template\Context;'."\n";
		$txt .= 'use Magento\Framework\Registry;'."\n";
		$txt .= 'use Magento\Framework\Data\FormFactory;'."\n";
		$txt .= 'use Magento\Cms\Model\Wysiwyg\Config;'."\n";
		$txt .= 'use ITM\Pricing\Model\System\Config\Status;'."\n";
		
		$txt .= "class Main extends Generic implements TabInterface {"."\n";
	
		$txt .= "\t".'protected $_status;'."\n";
		$txt .= "\t".'public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Status $status, array $data = []) {'."\n";
		$txt .= "\t".'$this->_status = $status;'."\n";	
		$txt .= "\t\t".'parent::__construct ( $context, $registry, $formFactory, $data );'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= "\t".'public function getTabLabel() {'."\n";
		$txt .= "\t\t".'return __ ( \'Item Information\' );'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= "\t".'public function getTabTitle() {'."\n";
		$txt .= "\t\t".'return __ ( \'Item Information\' );'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= "\t".'public function canShowTab() {'."\n";
		$txt .= "\t\t".'return true;'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= "\t".'public function isHidden() {'."\n";
		$txt .= "\t\t".'return false;'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= "\t".'protected function _prepareForm() {'."\n";
		$txt .= "\t\t".'$model = $this->_coreRegistry->registry ( \'current_'.strtolower($this->_vendor).'_'.strtolower($this->_module).'_'.strtolower($model["name"]).'\' );'."\n";
		$txt .= "\t\t".'$form = $this->_formFactory->create ();'."\n";
		$txt .= "\t\t".'$form->setHtmlIdPrefix ( \'item_\' );'."\n";
		$txt .= "\t\t".'$fieldset = $form->addFieldset ( \'base_fieldset\', ['."\n";
		$txt .= "\t\t\t".'\'legend\' => __ ( \'Item Information\' )'."\n";
		$txt .= "\t\t".'] );'."\n";
		$txt .= "\t\t".'if ($model->getId ()) {'."\n";
		$txt .= "\t\t\t".'$fieldset->addField ( \'id\', \'hidden\', ['."\n";
		$txt .= "\t\t\t\t".'\'name\' => \'id\''."\n";
		$txt .= "\t\t\t".'] );'."\n";
		$txt .= "\t\t".'}'."\n";
		
		$txt .= "\t\t".'$fieldset->addField ( \'status\', \'select\', ['."\n";
		$txt .= "\t\t\t".'\'name\' => \'status\','."\n";
		$txt .= "\t\t\t".'\'label\' => __ ( \'Status\' ),'."\n";
		$txt .= "\t\t\t".'\'options\' => $this->_status->toOptionArray ()'."\n";
		$txt .= "\t\t\t".'] );'."\n";
		$txt .= "\t\t".'$form->setValues ( $model->getData () );'."\n";
		$txt .= "\t\t".'$this->setForm ( $form );'."\n";
		$txt .= "\t\t".'return parent::_prepareForm ();'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	function ff($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]."/Edit.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml\\'.$model["name"].';'."\n\n";
	
		$txt .= "class Edit extends \\".$this->_vendor."\\".$this->_module."\Controller\Adminhtml\\".$model["name"]." {"."\n";
	
	
		$txt .= "\t".'public function execute() {'."\n";
	
		$txt .= "\t".''."\n";
	
		$txt .= "\t"."}"."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateAdminhtmlModelGridEditTabsFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]."/Edit/Tabs.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].'\Edit;'."\n\n";
		$txt .= "class Tabs extends \Magento\Backend\Block\Widget\Tabs {"."\n";
		$txt .= "\t".'protected function _construct() {'."\n";
		$txt .= "\t\t".'parent::_construct ();'."\n";
		$txt .= "\t\t".'$this->setId ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'_'.strtolower($model["name"]).'_edit_tabs\' );'."\n";
		$txt .= "\t\t".'$this->setDestElementId ( \'edit_form\' );'."\n";
		$txt .= "\t\t".'$this->setTitle ( __ ( \''.$model["name"].'\' ) );'."\n";
		$txt .= "\t"."}"."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateAdminhtmlModelGridEditFormFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]."/Edit/Form.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].'\Edit;'."\n\n";
	
		$txt .= "class Form extends \Magento\Backend\Block\Widget\Form\Generic {"."\n";
	
		$txt .= "\t".'protected function _construct() {'."\n";
	
		$txt .= "\t\t".'parent::_construct ();'."\n";
		$txt .= "\t\t".'$this->setId ( \''.strtolower($this->_vendor).'_'.strtolower($model["name"]).'_form\' );'."\n";
		$txt .= "\t\t".'$this->setTitle ( __ ( \''.$model["name"].' Information\' ) );'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= "\t".'protected function _prepareForm() {'."\n";
		$txt .= "\t\t".'$form = $this->_formFactory->create ( ['."\n";
		$txt .= "\t\t\t".'\'data\' => ['."\n";
		$txt .= "\t\t\t\t".'\'id\' => \'edit_form\','."\n";
		$txt .= "\t\t\t\t".'\'action\' => $this->getUrl ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/'.strtolower($model["name"]).'/save\' ),'."\n";
		$txt .= "\t\t\t\t".'\'method\' => \'post\''."\n";
		$txt .= "\t\t\t".']'."\n";
		$txt .= "\t\t\t".''."\n";
		$txt .= "\t\t".'] );'."\n";
		$txt .= "\t\t".'$form->setUseContainer ( true );'."\n";
		$txt .= "\t\t".'$this->setForm ( $form );'."\n";
		$txt .= "\t\t".'return parent::_prepareForm ();'."\n";
		$txt .= "\t".'}'."\n";
	
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateAdminhtmlModelEditFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]."/Edit.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].';'."\n\n";
		$txt .= "class Edit extends \Magento\Backend\Block\Widget\Form\Container {"."\n";
		
		$txt .= "\t".'protected $_coreRegistry = null;'."\n";
		$txt .= "\t".'public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, array $data = []) {'."\n";
		$txt .= "\t\t".'$this->_coreRegistry = $registry;'."\n";
		$txt .= "\t\t".'parent::__construct ( $context, $data );'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= "\t".'protected function _construct() {'."\n";
		$txt .= "\t\t".'$this->_objectId = \'id\';'."\n";
		$txt .= "\t\t".'$this->_controller = \'adminhtml_'.strtolower($model["name"]).'\';'."\n";
		$txt .= "\t\t".'$this->_blockGroup = \''.$this->_vendor.'_'.$this->_module.'\';'."\n";
		$txt .= "\t\t".'parent::_construct ();'."\n";
		$txt .= "\t\t".'$this->buttonList->add ( \'save_and_continue_edit\', ['."\n";
		$txt .= "\t\t\t".'\'class\' => \'save\','."\n";
		$txt .= "\t\t\t".'\'label\' => __ ( \'Save and Continue Edit\' ),'."\n";
		$txt .= "\t\t\t".'\'data_attribute\' => ['."\n";
		$txt .= "\t\t\t\t\t".'\'mage-init\' => ['."\n";
		$txt .= "\t\t\t\t\t\t\t".'\'button\' => ['."\n";
		$txt .= "\t\t\t\t\t\t\t\t\t".'\'event\' => \'saveAndContinueEdit\','."\n";
		$txt .= "\t\t\t\t\t\t\t\t\t".'\'target\' => \'#edit_form\''."\n";
		$txt .= "\t\t\t\t\t\t\t".'] '."\n";
		$txt .= "\t\t\t\t\t".'] '."\n";
		$txt .= "\t\t\t".'] '."\n";
		$txt .= "\t\t".'], 10 );'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= "\t".'public function getHeaderText() {'."\n";
		$txt .= "\t\t".'$item = $this->_coreRegistry->registry ( \'current_'.strtolower($this->_vendor).'_'.strtolower($this->_module).'_'.strtolower($model["name"]).'\' );'."\n";
		$txt .= "\t\t".'if ($item->getId ()) {'."\n";
		$txt .= "\t\t\t".'return __ ( "Edit Item \'%1\'", $this->escapeHtml ( $item->getId () ) );'."\n";
		$txt .= "\t\t".'} else {'."\n";
		$txt .= "\t\t\t".'return __ ( \'New Item\' );'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateAdminhtmlModelGridFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Block/Adminhtml/".$model["name"]."/Grid.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].';'."\n\n";
		$txt .= 'use '.$this->_vendor.'\\'.$this->_module.'\Model\System\Config\Status;'."\n\n";
		$txt .= "class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {"."\n";
	
	
		$txt .= "\t".'protected $_status;'."\n";
		$txt .= "\t".'protected $_collectionFactory;'."\n";
		$txt .= "\t".'public function __construct('."\n";
		$txt .= "\t\t\t".'\Magento\Backend\Block\Template\Context $context,'."\n";
		$txt .= "\t\t\t".'\Magento\Backend\Helper\Data $backendHelper,'."\n";
		$txt .= "\t\t\t".'\\'.$this->_vendor.'\\'.$this->_module.'\Model\Resource\\'.$model["name"].'\Collection $collectionFactory,'."\n";
		$txt .= "\t\t\t".'Status $status,'."\n";
		$txt .= "\t\t\t".'array $data = []'."\n";
		$txt .= "\t\t".') {'."\n";
		$txt .= "\t\t\t".'$this->_status = $status;'."\n";
		$txt .= "\t\t\t".'$this->_collectionFactory = $collectionFactory;'."\n";
		$txt .= "\t\t\t".'parent::__construct($context, $backendHelper, $data);'."\n";
		$txt .= "\t\t".'}'."\n";
		
		
		$txt .= "\t".'protected function _construct() {'."\n";
		$txt .= "\t\t".'parent::_construct();'."\n";
		$txt .= "\t\t".'$this->setId(\''.strtolower($model["name"]).'Grid\');'."\n";
		$txt .= "\t\t".'$this->setDefaultSort(\'id\');'."\n";
		$txt .= "\t\t".'$this->setDefaultDir(\'DESC\');'."\n";
		$txt .= "\t\t".'$this->setSaveParametersInSession(true);'."\n";
		$txt .= "\t\t".'$this->setUseAjax(false);'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= "\t".'protected function _getStore() {'."\n";
		$txt .= "\t\t".'$storeId = ( int ) $this->getRequest ()->getParam ( \'store\', 0 );'."\n";
		$txt .= "\t\t".'return $this->_storeManager->getStore ( $storeId );'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= "\t".'protected function _prepareCollection() {'."\n";
		$txt .= "\t\t".'try {'."\n";
		$txt .= "\t\t\t".'$collection = $this->_collectionFactory->load ();'."\n";
		$txt .= "\t\t\t".'$this->setCollection ( $collection );'."\n";
		$txt .= "\t\t\t".'parent::_prepareCollection ();'."\n";
		$txt .= "\t\t\t".'return $this;'."\n";
		$txt .= "\t\t".'} catch ( Exception $e ) {'."\n";
		$txt .= "\t\t\t".'echo $e->getMessage ();'."\n";
		$txt .= "\t\t\t".'die ();'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t".'}'."\n";
		
		
		
		$txt .= "\t".'protected function _prepareColumns() {'."\n";
		$txt .= "\t\t".'$this->addColumn ( \'id\', [ '."\n";
		$txt .= "\t\t\t\t".'\'header\' => __ ( \'ID\' ),'."\n";
		$txt .= "\t\t\t\t".'\'type\' => \'number\','."\n";
		$txt .= "\t\t\t\t".'\'index\' => \'id\','."\n";
		$txt .= "\t\t\t\t".'\'header_css_class\' => \'col-id\','."\n";
		$txt .= "\t\t\t\t".'\'column_css_class\' => \'col-id\''."\n";
		$txt .= "\t\t".'] );'."\n";
		$txt .= "\t\t".'$this->addColumn ( \'status\', ['."\n";
		$txt .= "\t\t\t\t".'\'header\' => __ ( \'Status\' ),'."\n";
		$txt .= "\t\t\t\t".'\'index\' => \'status\','."\n";
		$txt .= "\t\t\t\t".'\'class\' => \'status\','."\n";
		$txt .= "\t\t\t\t".'\'type\' => \'options\','."\n";
		$txt .= "\t\t\t\t".'\'options\' => $this->_status->toOptionArray ()'."\n";
		$txt .= "\t\t".'] );'."\n";
		$txt .= "\t\t".'$block = $this->getLayout ()->getBlock ( \'grid.bottom.links\' );'."\n";
		$txt .= "\t\t".'if ($block) {'."\n";
		$txt .= "\t\t\t".'$this->setChild ( \'grid.bottom.links\', $block );'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t\t".'return parent::_prepareColumns ();'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= "\t".'protected function _prepareMassaction() {'."\n";
		$txt .= "\t\t".'$this->setMassactionIdField ( \'id\' );'."\n";
		$txt .= "\t\t".'$this->getMassactionBlock ()->setFormFieldName ( \'id\' );'."\n";
		$txt .= "\t\t".'$this->getMassactionBlock ()->addItem ( \'delete\', array( '."\n";
		$txt .= "\t\t\t\t".'\'label\' => __ ( \'Delete\' ),'."\n";
		$txt .= "\t\t\t\t".'\'url\' => $this->getUrl ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/massDelete\' ),'."\n";
		$txt .= "\t\t\t\t".'\'confirm\' => __ ( \'Are you sure?\' )'."\n";
		$txt .= "\t\t".') );'."\n";
		$txt .= "\t\t".'return $this;'."\n";
		$txt .= "\t".'}'."\n";
		
		
		$txt .= "\t".'public function getGridUrl() {'."\n";
		$txt .= "\t\t".'return $this->getUrl ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/index\', ['."\n";
		$txt .= "\t\t\t".'\'_current\' => true '."\n";
		$txt .= "\t\t".'] );'."\n";
		$txt .= "\t".'}'."\n";
		
		$txt .= "\t".'public function getRowUrl($row) {'."\n";
		$txt .= "\t\t".'return $this->getUrl ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/edit\', ['."\n";
		$txt .= "\t\t\t".'\'store\' => $this->getRequest ()->getParam ( \'store\' ),'."\n";
		$txt .= "\t\t\t".'\'id\' => $row->getId ()'."\n";
		$txt .= "\t\t".'] );'."\n";
		$txt .= "\t".'}'."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateAdminhtmlModelFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Block/Adminhtml"."/".$model["name"].".php";
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = "<?php"."\n\n";
		$txt .= "namespace ".$this->_vendor."\\".$this->_module."\Block\Adminhtml;"."\n\n";
		$txt .="class ".$model["name"]." extends \Magento\Backend\Block\Widget\Grid\Container {"."\n";
		$txt .="\t"."protected function _construct() {"."\n";
		$txt .="\t\t".'$this->_controller = \'adminhtml_'.strtolower($model["name"]).'\';'."\n";
		$txt .="\t\t".'$this->_blockGroup = \''.$this->_vendor.'_'.$this->_module.'\';'."\n";
		$txt .="\t\t".'$this->_headerText = __ ( \''.$model["name"].'\' );'."\n";
		$txt .="\t\t".'$this->_addButtonLabel = __ ( \'Add New Entry\' );'."\n";
		$txt .="\t\t".'parent::_construct ();'."\n";
		$txt .="\t"."}"."\n";
		$txt .="}";
	
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateControllersAdminhtmlModelSaveFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]."/Save.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml\\'.$model["name"].';'."\n\n";
	
		$txt .= "class Save extends \\".$this->_vendor."\\".$this->_module."\Controller\Adminhtml\\".$model["name"]." {"."\n";
	
	
		$txt .= "\t".'public function execute() {'."\n";
	
		$txt .= "\t\t".'if ($this->getRequest ()->getPostValue ()) {'."\n";
		$txt .= "\t\t\t".'try {'."\n";
		$txt .= "\t\t\t\t".'$model = $this->_objectManager->create ( \''.$this->_vendor.'\\'.$this->_module.'\Model\\'.$model["name"].'\' );'."\n";
		$txt .= "\t\t\t\t".'$data = $this->getRequest ()->getPostValue ();'."\n";
		$txt .= "\t\t\t\t".'$inputFilter = new \Zend_Filter_Input ( [ ], [ ], $data );'."\n";
		$txt .= "\t\t\t\t".'$data = $inputFilter->getUnescaped ();'."\n";
		$txt .= "\t\t\t\t".'$id = $this->getRequest ()->getParam ( \'id\' );'."\n";
		$txt .= "\t\t\t\t".'if ($id) {'."\n";
		$txt .= "\t\t\t\t\t".'$model->load ( $id );'."\n";
		$txt .= "\t\t\t\t\t".'if ($id != $model->getId ()) {'."\n";
		$txt .= "\t\t\t\t\t\t".'throw new \Magento\Framework\Exception\LocalizedException ( __ ( \'The wrong item is specified.\' ) );'."\n";
		$txt .= "\t\t\t\t\t".'}'."\n";
		$txt .= "\t\t\t\t".'}'."\n";
		$txt .= "\t\t\t\t".'$model->setData ( $data );'."\n";
		$txt .= "\t\t\t\t".'$session = $this->_objectManager->get ( \'Magento\Backend\Model\Session\' );'."\n";
		$txt .= "\t\t\t\t".'$session->setPageData ( $model->getData () );'."\n";
		$txt .= "\t\t\t\t".'$model->save ();'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addSuccess ( __ ( \'You saved the item.\' ) );'."\n";
		$txt .= "\t\t\t\t".'$session->setPageData ( false );'."\n";
		$txt .= "\t\t\t\t".'if ($this->getRequest ()->getParam ( \'back\' )) {'."\n";
		$txt .= "\t\t\t\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/edit\', ['."\n";
		$txt .= "\t\t\t\t\t\t".'\'id\' => $model->getId ()'."\n";
		$txt .= "\t\t\t\t\t".'] );'."\n";
		$txt .= "\t\t\t\t\t".'return;'."\n";
		$txt .= "\t\t\t\t".'}'."\n";
		$txt .= "\t\t\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/\' );'."\n";
		$txt .= "\t\t\t\t".'return;'."\n";
		$txt .= "\t\t\t".'} catch ( \Magento\Framework\Exception\LocalizedException $e ) {'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addError ( $e->getMessage () );'."\n";
		$txt .= "\t\t\t\t".'$id = ( int ) $this->getRequest ()->getParam ( \'id\' );'."\n";
		$txt .= "\t\t\t\t".'if (! empty ( $id )) {'."\n";
		$txt .= "\t\t\t\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/edit\', ['."\n";
		$txt .= "\t\t\t\t\t\t".'\'id\' => $id'."\n";
		$txt .= "\t\t\t\t\t".'] );'."\n";
		$txt .= "\t\t\t\t".'} else {'."\n";
		$txt .= "\t\t\t\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/new\');'."\n";
		$txt .= "\t\t\t\t".'}'."\n";
		$txt .= "\t\t\t\t".'return;'."\n";
		$txt .= "\t\t\t".'} catch ( \Exception $e ) {'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addError ( __ ( \'Something went wrong while saving the item data. Please review the error log.\' ) );'."\n";
		$txt .= "\t\t\t\t".'$this->_objectManager->get ( \'Psr\Log\LoggerInterface\' )->critical ( $e );'."\n";
		$txt .= "\t\t\t\t".'$this->_objectManager->get ( \'Magento\Backend\Model\Session\' )->setPageData ( $data );'."\n";
		$txt .= "\t\t\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/edit\', [ '."\n";
		$txt .= "\t\t\t\t\t".'\'id\' => $this->getRequest ()->getParam ( \'id\' )'."\n";
		$txt .= "\t\t\t\t".'] );'."\n";
		$txt .= "\t\t\t\t".'return;'."\n";
		$txt .= "\t\t\t".'}'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/\' );'."\n";
		
		$txt .= "\t"."}"."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateControllersAdminhtmlModelNewActionFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]."/NewAction.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml\\'.$model["name"].';'."\n\n";
	
		$txt .= "class NewAction extends \\".$this->_vendor."\\".$this->_module."\Controller\Adminhtml\\".$model["name"]." {"."\n";
		$txt .= "\t".'public function execute() {'."\n";
		$txt .= "\t\t".'$this->_forward(\'edit\');'."\n";
		$txt .= "\t"."}"."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateControllersAdminhtmlModelMassDeleteFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]."/MassDelete.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml\\'.$model["name"].';'."\n\n";
	
		$txt .= "class MassDelete extends \\".$this->_vendor."\\".$this->_module."\Controller\Adminhtml\\".$model["name"]." {"."\n";
		$txt .= "\t".'public function execute() {'."\n";
	
		$txt .= "\t\t".'$itemsIds = $this->getRequest()->getParam(\'id\');'."\n";
		$txt .= "\t\t".'if (!is_array($itemsIds)) {'."\n";
		$txt .= "\t\t\t".'$this->messageManager->addError(__(\'Please select item(s).\'));'."\n";
		$txt .= "\t\t".'} else {'."\n";
		$txt .= "\t\t\t".'try {'."\n";
		$txt .= "\t\t\t\t".'foreach ($itemsIds as $itemId) {'."\n";
		$txt .= "\t\t\t\t\t".'$model = $this->_objectManager->create(\''.$this->_vendor.'\\'.$this->_module.'\Model\\'.$model["name"].'\');'."\n";
		$txt .= "\t\t\t\t\t".'$model->load($itemId);'."\n";
		$txt .= "\t\t\t\t\t".'$model->delete();'."\n";
		$txt .= "\t\t\t\t".'}'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addSuccess('."\n";
		$txt .= "\t\t\t\t\t".'__(\'A total of %1 record(s) have been deleted.\', count($itemsIds))'."\n";
		$txt .= "\t\t\t\t".');'."\n";
		$txt .= "\t\t\t".'} catch (\Magento\Framework\Exception\LocalizedException $e) {'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addError($e->getMessage());'."\n";
		$txt .= "\t\t\t".'} catch (\Exception $e) {'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addException($e, __(\'An error occurred while deleting record(s).\'));'."\n";
		$txt .= "\t\t\t".'}'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t\t".'$this->_redirect(\''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/\');'."\n";
	
		$txt .= "\t"."}"."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateControllersAdminhtmlModelIndexFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]."/Index.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml\\'.$model["name"].';'."\n\n";
	
		$txt .= "class Index extends \\".$this->_vendor."\\".$this->_module."\Controller\Adminhtml\\".$model["name"]." {"."\n";
	
	
		$txt .= "\t".'public function execute() {'."\n";
		$txt .= "\t\t".'$resultPage = $this->resultPageFactory->create();'."\n";
		$txt .= "\t\t".'$resultPage->setActiveMenu(\''.$this->_vendor.'_'.$this->_module.'::'.strtolower($this->_module).'\');'."\n";
		$txt .= "\t\t".'$resultPage->getConfig()->getTitle()->prepend(__(\''.$model["name"].'\'));'."\n";
		$txt .= "\t\t".'$resultPage->addBreadcrumb(__(\''.$this->_vendor.'\'), __(\''.$this->_vendor.'\'));'."\n";
		$txt .= "\t\t".'$resultPage->addBreadcrumb(__(\''.$model["name"].'\'), __(\''.$model["name"].'\'));'."\n";
		$txt .= "\t\t".'return $resultPage;'."\n";
	
	
		$txt .= "\t"."}"."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateControllersAdminhtmlModelEditFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]."/Edit.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
	
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml\\'.$model["name"].';'."\n\n";
	
		$txt .= "class Edit extends \\".$this->_vendor."\\".$this->_module."\Controller\Adminhtml\\".$model["name"]." {"."\n";
	
	
		$txt .= "\t".'public function execute() {'."\n";
		$txt .= "\t\t".' $id = $this->getRequest()->getParam(\'id\');'."\n";
		$txt .= "\t\t".'$model = $this->_objectManager->create(\''.$this->_vendor.'\\'.$this->_module.'\Model\\'.$model["name"].'\');'."\n";
		$txt .= "\t\t".'if ($id) {'."\n";
		$txt .= "\t\t\t".'$model->load($id);'."\n";
		$txt .= "\t\t\t".'if (!$model->getId()) {'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addError(__(\'This item no longer exists.\'));'."\n";
		$txt .= "\t\t\t\t".'$this->_redirect(\''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*\');'."\n";
		$txt .= "\t\t\t\t".'return;'."\n";
		$txt .= "\t\t\t".'}'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t\t".'$data = $this->_objectManager->get(\'Magento\Backend\Model\Session\')->getPageData(true);'."\n";
		$txt .= "\t\t".'if (!empty($data)) {'."\n";
		$txt .= "\t\t\t".'$model->addData($data);'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t\t".'$resultPage = $this->resultPageFactory->create();'."\n";
		$txt .= "\t\t".'if ($id) {'."\n";
		$txt .= "\t\t\t".'$resultPage->getConfig()->getTitle()->prepend(__(\'Edit Items Entry\'));'."\n";
		$txt .= "\t\t".'}else{'."\n";
		$txt .= "\t\t\t".'$resultPage->getConfig()->getTitle()->prepend(__(\'Add Items Entry\'));'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t\t".'$this->_coreRegistry->register(\'current_'.strtolower($this->_vendor).'_'.strtolower($this->_module).'_'.strtolower($model["name"]).'\', $model);'."\n";
		$txt .= "\t\t".'$this->_initAction();'."\n";
		$txt .= "\t\t".'$this->_view->getLayout()->getBlock(\''.strtolower($model["name"]).'_'.strtolower($model["name"]).'_edit\');'."\n";
		$txt .= "\t\t".'$this->_view->renderLayout();'."\n";
		
	
		$txt .= "\t"."}"."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateControllersAdminhtmlModelDeleteFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"]."/Delete.php";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
		
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml\\'.$model["name"].';'."\n\n";
		
		$txt .= "class Delete extends \\".$this->_vendor."\\".$this->_module."\Controller\Adminhtml\\".$model["name"]." {"."\n";
		
	
		$txt .= "\t".'public function execute() {'."\n";
		
		$txt .= "\t\t".'$id = $this->getRequest ()->getParam ( \'id\' );'."\n";
		$txt .= "\t\t".'if ($id) {'."\n";
		$txt .= "\t\t\t".'try {'."\n";
		$txt .= "\t\t\t\t".'$model = $this->_objectManager->create ( \''.$this->_vendor.'\\'.$this->_module.'\Model\\'.$model["name"].'\' );'."\n";
		$txt .= "\t\t\t\t".'$model->load ( $id );'."\n";
		$txt .= "\t\t\t\t".'$model->delete ();'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addSuccess ( __ ( \'You deleted the item.\' ) );'."\n";
		$txt .= "\t\t\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/\' );'."\n";
		
		$txt .= "\t\t\t\t".'return;'."\n";
		$txt .= "\t\t\t".'} catch ( \Magento\Framework\Exception\LocalizedException $e ) {'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addError ( $e->getMessage () );'."\n";
		$txt .= "\t\t\t".'} catch ( \Exception $e ) {'."\n";
		$txt .= "\t\t\t\t".'$this->messageManager->addError ( __ ( \'We can\\\'t delete item right now. Please review the log and try again.\' ) );'."\n";
		$txt .= "\t\t\t\t".'$this->_objectManager->get ( \'Psr\Log\LoggerInterface\' )->critical ( $e );'."\n";
		$txt .= "\t\t\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/edit\', ['."\n";
		$txt .= "\t\t\t\t\t\t".'\'id\' => $this->getRequest ()->getParam ( \'id\' )'."\n";
		$txt .= "\t\t\t\t".'] );'."\n";
		$txt .= "\t\t\t\t".'return;'."\n";
		$txt .= "\t\t\t".'}'."\n";
		$txt .= "\t\t".'}'."\n";
		$txt .= "\t\t".'$this->messageManager->addError ( __ ( \'We can\\\'t find a item to delete.\' ) );'."\n";
		$txt .= "\t\t".'$this->_redirect ( \''.strtolower($this->_vendor).'_'.strtolower($this->_module).'/*/\' );'."\n";
		
		$txt .= "\t"."}"."\n";
	
	
	
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateControllersAdminhtmlModelFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Controller/Adminhtml/".$model["name"].".php";
		
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Controller\Adminhtml;'."\n\n";
		$txt .= "abstract class ".$model["name"]." extends \Magento\Backend\App\Action {"."\n";
		$txt .= "\t".'protected $_coreRegistry;'."\n";
		$txt .= "\t".'protected $resultForwardFactory;'."\n";
		$txt .= "\t".'protected $resultPageFactory;'."\n";
		
		$txt .= "\t".'public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {'."\n";
		$txt .= "\t\t".'$this->_coreRegistry = $coreRegistry;'."\n";
		$txt .= "\t\t".'parent::__construct ( $context );'."\n";
		$txt .= "\t\t".'$this->resultForwardFactory = $resultForwardFactory;'."\n";
		$txt .= "\t\t".'$this->resultPageFactory = $resultPageFactory;'."\n";
		$txt .= "\t"."}"."\n";
		
		$txt .= "\t".'protected function _initAction() {'."\n";
		$txt .= "\t\t".'$this->_view->loadLayout ();'."\n";
		$txt .= "\t\t".'$this->_setActiveMenu ( \''.$this->_vendor.'_'.$this->_module.'::'.strtolower($model["name"]).'\' )->_addBreadcrumb ( __ ( \''.$model["name"].'\' ), __ ( \''.$model["name"].'\' ) );'."\n";
		$txt .= "\t\t".'return $this;'."\n";
		$txt .= "\t"."}"."\n";
		
		$txt .= "\t".'protected function _isAllowed() {'."\n";
		$txt .= "\t\t".'return $this->_authorization->isAllowed ( \''.$this->_vendor.'_'.$this->_module.'::'.strtolower($model["name"]).'\' );'."\n";
		$txt .= "\t"."}"."\n";
		
		
		
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateModelResourceModelCollectionFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Model/Resource/"."/".$model["name"]."/Collection.php";
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Model\Resource\\'.$model["name"].';'."\n\n";
		$txt .= "class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {"."\n";
		$txt .= "\t".'protected function _construct() {'."\n";
		$txt .= "\t\t".'$this->_init(\''.$this->_vendor.'\\'.$this->_module.'\Model\\'.$model["name"].'\', \''.$this->_vendor.'\\'.$this->_module.'\Model\Resource\\'.$model["name"].'\');'."\n";
		$txt .= "\t".'}'."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateModelResourceModelFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Model/Resource/"."/".$model["name"].".php";
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Model\Resource;'."\n\n";
		$txt .= "class ".$model["name"]." extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {"."\n";
		$txt .= "\t".'protected function _construct() {'."\n";
		$txt .= "\t\t".'$this->_init(\''.strtolower($this->_vendor.'_'.$this->_module.'_'.$model["table"]).'\', \'id\');'."\n";
		$txt .= "\t".'}'."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateModelModelFile($model){
		$url = $this->_vendor."/".$this->_module."/"."Model/"."/".$model["name"].".php";
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?php'."\n\n";
		$txt .= 'namespace '.$this->_vendor.'\\'.$this->_module.'\Model;'."\n\n";
		$txt .= "class ".$model["name"]." extends \Magento\Framework\Model\AbstractModel {"."\n";
		$txt .= "\t".'protected function _construct() {'."\n";
		$txt .= "\t\t".'parent::_construct();'."\n";
		$txt .= "\t\t".'$this->_init(\''.$this->_vendor.'\\'.$this->_module.'\Model\Resource\\'.$model["name"].'\');'."\n";
		$txt .= "\t".'}'."\n";
		$txt .= '}';
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateViewAdminhtmlLayoutIndexFile($model){
		$url = $this->_vendor."/".$this->_module."/"."view/adminhtml/layout"."/".strtolower($this->_vendor)."_".strtolower($this->_module)."_".strtolower($model["name"])."_edit".".xml";
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?xml version="1.0"?>'."\n";
		$txt .= '<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="admin-2columns-left" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/Layout/etc/page_configuration.xsd">'."\n";
		$txt .= "\t"."<body>"."\n";
		
		$txt .= "\t\t".'<referenceContainer name="left">'."\n";
		$txt .= "\t\t\t".'<block class="'.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].'\Edit\Tabs" name="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'_'.strtolower($model["name"]).'_edit_tabs">'."\n";
		$txt .= "\t\t\t\t\t".'<block class="'.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].'\Edit\Tab\Main" name="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'_'.strtolower($model["name"]).'_edit_tab_main"/>'."\n";
		$txt .= "\t\t\t\t\t".'<action method="addTab">'."\n";
		$txt .= "\t\t\t\t\t\t".'<argument name="name" xsi:type="string">main_section</argument>'."\n";
		$txt .= "\t\t\t\t\t\t".'<argument name="block" xsi:type="string">'.strtolower($this->_vendor).'_'.strtolower($this->_module).'_'.strtolower($model["name"]).'_edit_tab_main</argument>'."\n";
		$txt .= "\t\t\t\t\t".'</action>'."\n";
		$txt .= "\t\t\t".'</block>'."\n";
		$txt .= "\t\t".'</referenceContainer>'."\n\n";
		
		$txt .= "\t\t".'<referenceContainer name="content">'."\n";
		$txt .= "\t\t\t".'<block class="'.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].'\Edit" name="'.strtolower($this->_module).'_'.strtolower($model["name"]).'_edit"/>'."\n";
		$txt .= "\t\t".'</referenceContainer>'."\n";
		
		$txt .= "\t".'</body>'."\n";
		$txt .= "</page>";
	
		fwrite($file, $txt);
		fclose($file);
	}
	function CreateViewAdminhtmlLayoutEditFile($model){
		$url = $this->_vendor."/".$this->_module."/"."view/adminhtml/layout"."/".strtolower($this->_vendor)."_".strtolower($this->_module)."_".strtolower($model["name"])."_index".".xml";
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?xml version="1.0"?>'."\n";
		$txt .= '<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/Layout/etc/page_configuration.xsd">'."\n";
		$txt .= "\t"."<body>"."\n";
		$txt .= "\t\t".'<referenceContainer name="content">'."\n";
		$txt .= "\t\t\t".'<block class="'.$this->_vendor.'\\'.$this->_module.'\Block\Adminhtml\\'.$model["name"].'" name="'.strtolower($this->_module).'_'.strtolower($model["name"]).'_container"/>'."\n";
		$txt .= "\t\t".'</referenceContainer>'."\n";
		$txt .= "\t".'</body>'."\n";
		$txt .= "</page>";
		
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateBackEndModels(){
		if($this->_config["backend_model"]){
			foreach($this->_config["backend_model"] as $model){
				$this->CreateBackEndModel($model);
			}
			$this->CreateMenuFile($model);
			$this->CreateRoutesFile($model);
		}
	}
	
	
	function CreateMenuFile($backend_model){
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."etc/adminhtml");
		$url = $this->_vendor."/".$this->_module."/"."etc/adminhtml/menu.xml";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?xml version="1.0"?>'."\n";
		$txt .= '<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../Backend/etc/menu.xsd">'."\n";
		$txt .= "\t".'<menu>'."\n";
		$txt .= "\t\t".'<add id="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'::'.strtolower($this->_module).'" title="'.$this->_module.'" module="'.$this->_vendor.'_'.$this->_module.'" sortOrder="0" parent="itm::base"  resource="'.$this->_vendor.'_'.$this->_module.'::main"/>'."\n";
		foreach($this->_config["backend_model"] as $model){
			$txt .= "\t\t".'<add id="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'::'.strtolower($model["name"]).'" title="'.$model["name"].'" module="'.$this->_vendor.'_'.$this->_module.'" sortOrder="10" parent="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'::'.strtolower($this->_module).'" action="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'/'.strtolower($model["name"]).'/" resource="'.$this->_vendor.'_'.$this->_module.'::'.$model["name"].'"/>'."\n";
		}
		$txt .= "\t".'</menu>'."\n";
		$txt .= '</config>';
	
		fwrite($file, $txt);
		fclose($file);
	}
	
	function CreateRoutesFile($backend_model){
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."etc/adminhtml");
		$url = $this->_vendor."/".$this->_module."/"."etc/adminhtml/routes.xml";
	
		$file = fopen($url, "w") or die("Unable to open file!");
	
		$txt = '<?xml version="1.0"?>'."\n";
		$txt .= '<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../lib/internal/Magento/Framework/App/etc/routes.xsd">'."\n";
		$txt .= "\t".'<router id="admin">'."\n";
		$txt .= "\t\t".'<route id="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'" frontName="'.strtolower($this->_vendor).'_'.strtolower($this->_module).'">'."\n";
		$txt .= "\t\t\t".'<module name="'.$this->_vendor.'_'.$this->_module.'" />'."\n";
		$txt .= "\t\t".'</route>'."\n";
		$txt .= "\t".'</router>'."\n";
		$txt .= '</config>';
	
		fwrite($file, $txt);
		fclose($file);
	}
	
	
	function create(){
		$this->CreateFolder($this->_vendor);
		$this->CreateFolder($this->_vendor."/".$this->_module);
		$this->CreateFolder($this->_vendor."/".$this->_module."/"."etc");
		
		$this->CreateModuleXmlFile();
		$this->CreateRegistrationFile();
		
		$this->CreateHelper();
		$this->CreateSetup();
		$this->CreateBlock();
		$this->CreateApi();
		$this->CreateModel();
		$this->CreateController();
		$this->CreateView();
		$this->CreateBackEndModels();
	}
	function __toString(){
		return $this->_vendor." - ".$this->_module." - ".$this->_version;
	}
}

// --------------------------------------------------------------
$config_array = [
		"helper"		=>	true,
		"setup"			=>	true,
		"block"			=>	true,
		"controller"	=>	true,
		"model"			=>	true,
		"api"			=>	true,
		"view"			=>	["frontend" => true,"adminhtml" => true],
		// vernder and module name will add as prefix to table, only id and status will create
		"backend_model" => 	[["name" => "Box","table" =>"box"],],
		];

$mod = new Magento2Module("ITM","File");
$mod->setConfig($config_array);
echo $mod->create($config_array);