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
		$txt = "namespace ".$this->_vendor."\\".$this->_module."\Setup;"."\n\n";
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
		//$this->CreateAdminhtmlModelGridFile($model);
		//$this->CreateAdminhtmlModelEditFile($model);
		
		//$this->CreateAdminhtmlModelGridEditFormFile($model);
		//$this->CreateAdminhtmlModelGridEditTabsFile($model);
		
		//$this->CreateAdminhtmlModelGridEditTabsMainFile($model);
		
		
			// Create Controllers Files
		//$this->CreateControllersAdminhtmlModelFile($model);
		//$this->CreateControllersAdminhtmlModelDeleteFile($model);
		//$this->CreateControllersAdminhtmlModelEditFile($model);
		//$this->CreateControllersAdminhtmlModelIndexFile($model);
		//$this->CreateControllersAdminhtmlModelMassDeleteFile($model);
		//$this->CreateControllersAdminhtmlModelNewActionFile($model);
		//$this->CreateControllersAdminhtmlModelSaveFile($model);
		
		
		
		// Create Model Files
		//$this->CreateModelModelFile($model);
		//$this->CreateModelResourceModelFile($model);
		//$this->CreateModelResourceModelCollectionFile($model);
		
		// Create view Files
		$this->CreateViewAdminhtmlLayoutIndexFile($model);
		$this->CreateViewAdminhtmlLayoutEditFile($model);
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
		}
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