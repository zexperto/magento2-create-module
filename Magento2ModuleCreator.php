<?php
class Magento2ModuleCreator
{

	var $_vendor;
	var $_module;
	var $_version;
	var $_config;
	var $_helper;
	function __construct($vendor, $module, $version = "1.0.0")
{

		$this->_vendor = $vendor;
		$this->_module = $module;
		$this->_version = $version;
		$this->_config = [ ];
	}
	function CreateFolder($path)
{

		if (! file_exists($path ))
			mkdir($path);
	}
	function setConfig($config)
{

		$this->_config = $config;
	}
	function CreateModuleXmlFile()
{

	    
		$txt = sprintf('<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/Module/etc/module.xsd">
	<module name="%s_%s" setup_version="%s" />
</config>', $this->_vendor, $this->_module, $this->_version);
		
		$path = sprintf('%s/%s/etc/module.xml', $this->_vendor, $this->_module);
		$this->saveFileData($path, $txt);
	}
	
	function CreateRegistrationFile()
{

		$path = sprintf('%s/%s/registration.php', $this->_vendor, $this->_module);
		
		$txt = sprintf("<?php

\Magento\Framework\Component\ComponentRegistrar::register(
	\Magento\Framework\Component\ComponentRegistrar::MODULE,
	'%s_%s',
	__DIR__);", $this->_vendor, $this->_module);
		
		$this->saveFileData($path, $txt);
	}
	function CreateDataFile()
{

		$path = sprintf('%s/%s/Helper/Data.php', $this->_vendor, $this->_module);
		
		
		$txt = sprintf('<?php
				
namespace %s\%s\Helper;
		
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function __construct(\Magento\Framework\App\Helper\Context $context)
{

		parent::__construct($context);
	}}', $this->_vendor, $this->_module);
		
		$this->saveFileData($path, $txt);
	}
	function CreateHelper()
{

		if ($this->_config ["helper"])
{

			$path = sprintf('%s/%s/Helper', $this->_vendor, $this->_module);
			$this->CreateFolder($path);
			$this->CreateDataFile();
		}
	}
	function CreateInstallSchemaFile()
{

		$path = sprintf('%s/%s/Setup/InstallSchema.php', $this->_vendor, $this->_module);
		
		
		$txt = sprintf('<?php

namespace %s\%s\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
{

		$setup->startSetup();', $this->_vendor, $this->_module);
		
		if ($this->_config ["backend_model"])
{

			foreach($this->_config ["backend_model"] as $model )
{

				$columns = "";
				
				foreach($model ["columns"] as $column )
{

					
					if ($column ["type"] == "string") :
						$columns .= sprintf('->addColumn(\'%1$s\', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, \'%3$s\', [\'nullable\' => false ], \'%2$s\' )', $column ["name"], $column ["label"], $column ["size"]);
					
					endif;
					
					if ($column ["type"] == "int") :
						$columns .= sprintf('->addColumn(\'%1$s\', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [\'nullable\' => false ], \'%2$s\' )', $column ["name"], $column ["label"]);
					
					endif;
					
					if ($column ["type"] == "date") :
						$columns .= sprintf('->addColumn(\'%1$s\',\Magento\Framework\DB\Ddl\Table::TYPE_DATE,	null,[],\'%2$s\')', $column ["name"], $column ["label"]);
					
					endif;
					
					if ($column ["type"] == "decimal") :
						$columns .= sprintf('->addColumn(\'%1$s\', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, \'12,4\',  [\'nullable\' => false, \'default\' => \'0.0000\'], \'%2$s\' )', $column ["name"], $column ["label"]);
					
					endif;
					
					$columns .= "\n\t\t\t\t";
				}
				
				$txt .= sprintf('
		$table = $setup->getConnection()
				->newTable($setup->getTable(\'%s_%s_%s\' ) )
				->addColumn(\'id\', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
						\'identity\' => true,
						\'unsigned\' => true,
						\'nullable\' => false,
						\'primary\' => true
						], \'Id\' )
				%4$s
				->addColumn(\'status\', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, [
						\'default\' => null
						], \'Status\');
		$setup->getConnection()->createTable($table);
						', strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"]), $columns);
			}
		}
		
		$txt .= '// your code here
				$setup->endSetup();
	}}';
		
		$this->saveFileData($path, $txt);
	}
	function CreateUninstallFile()
{

		$path = sprintf('%s/%s/Setup/Uninstall.php', $this->_vendor, $this->_module);
		
		
		
		$txt = sprintf('<?php

namespace %s\%s\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{

	public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context){
		
		// php bin/magento module:uninstall %1$s_%2$s
		$setup->startSetup();
		// your code here"
		$setup->endSetup();
	}}', $this->_vendor, $this->_module);
		
		$this->saveFileData($path, $txt);
	}
	function CreateUpgradeSchemaFile()
{

		$path = sprintf('%s/%s/Setup/UpgradeSchema.php', $this->_vendor, $this->_module);
		
		
		
		$txt = sprintf('<?php
				
namespace %s\%s\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{


	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
		$setup->startSetup();
				
		if (!$context->getVersion())
{

			// your code here" . "\n";
		}

		
		if (version_compare($context->getVersion(), \'1.0.1\') < 0)
{

			//code to upgrade to 1.0.1" . "\n";
		}

		if (version_compare($context->getVersion(), \'1.0.2\') < 0)
{

			 //code to upgrade to 1.0.2" . "\n";
		}

		$setup->endSetup();
	}}', $this->_vendor, $this->_module);
		
		
		$this->saveFileData($path, $txt);
	}
	function CreateUpgradeDataFile()
{

		$path = sprintf('%s/%s/Setup/UpgradeData.php', $this->_vendor, $this->_module);
		
		
		
		$txt = sprintf('<?php

namespace %s\%s\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{


	public function __construct(EavSetupFactory $eavSetupFactory)
{

		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
{

		$eavSetup = $this->eavSetupFactory->create([
			\'setup\' => $setup
		]);

		if (version_compare($context->getVersion(), \'1.0.1\' ) < 0)
{

			//your code here" . "\n";
		}

	}}', $this->_vendor, $this->_module);
		
		$this->saveFileData($path, $txt);
	}
	function CreateSetup()
{

		if ($this->_config ["setup"])
{

			
			$this->CreateFolder(sprintf('%s/%s/Setup', $this->_vendor, $this->_module ));
			$this->CreateInstallSchemaFile();
			$this->CreateUninstallFile();
			$this->CreateUpgradeSchemaFile();
			$this->CreateUpgradeDataFile();
		}
	}
	function CreateBlock()
{

		if ($this->_config ["block"])
{

			$this->CreateFolder(sprintf('%s/%s/Block', $this->_vendor, $this->_module ));
		}
	}
	function CreateApiModelInterface($model)
{

		$path = sprintf('%s/%s/Api/%sInterface.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
		
namespace %s\%s\Api;
		
interface %sInterface
{

	/**
     * Get list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
	
    /**
     *
     * @api
     * @param %1$s\%2$s\Api\Data\%3$sDataInterface $entity.
     * @return %1$s\%2$s\Api\Data\%3$sDataInterface
     */
    public function save($entity);
    
    /**
     * @param int $id
     * @return bool Will returned true if deleted
     */
    public function deleteById($id);
}', $this->_vendor, $this->_module, $model ["name"]);
		
		$this->saveFileData($path, $txt);
	}
	function CreateApiModelDataInterface($model)
{

		$path = sprintf('%s/%s/Api/Data/%sDataInterface.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		// start columns
		$columns = "";
		$columns .= '
	/**
	 *
	 * @api
	 * @return int id.
	 */
	public function getId();
	
	/**
	 *
	 * @api
	 * @param $value id.
	 * @return null
	 */
	public function setId($value);
				';
		foreach($model ["columns"] as $column )
{

			$property = str_replace(' ', '', ucwords(str_replace('_', ' ', $column ["name"] ) ));
			
			if ($column ["type"] == "string") :
				$columns .= sprintf('
	/**
     *
     * Get %2$s
     * @return string|null.
     */
    public function get%3$s();

    /**
     *
     * Set %2$s
     * @param string $value.
     * @return null
     */
    public function set%3$s($value);', $column ["name"], $column ["label"], $property);
			
endif;
			
			if ($column ["type"] == "int") :
				$columns .= sprintf('

	/**
     *
     * Get %2$s
     * @return int|null.
     */
    public function get%3$s();

    /**
     *
     * Set %2$s
     * @param int $value
     * @return null
     */
    public function set%3$s($value);', $column ["name"], $column ["label"], $property);
			
endif;
			
			if ($column ["type"] == "date") :
				$columns .= sprintf('
	
	/**
     * Get %2$s
     *
     * @return string|null
     */
    public function get%3$s();

    /**
     * Set %2$s
     *
     * @param string $value
     * @return null
     */
    public function set%3$s($value);', $column ["name"], $column ["label"], $property);
			
endif;
			
			if ($column ["type"] == "decimal") :
				$columns .= sprintf('
	
	/**
     *
     * Get %2$s
     * @return float|null.
     */
    public function get%3$s();
			
    /**
     *
     * Set %2$s
     * @param float $value
     * @return null
     */
    public function set%3$s($value);', $column ["name"], $column ["label"], $property);
			
endif;
		}
		
		$columns .= sprintf('
	 
	/**
     * Get %1$s status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set %1$s status
     *
     * @param int $status
     * @return null
     */
    public function setStatus($status);',
		        $model ["name"]);
		
		// End columns
		
		$txt = sprintf('<?php
	
namespace %1$s\%2$s\Api\Data;
	
interface %3$sDataInterface
{
	%4$s
}', $this->_vendor, $this->_module, $model ["name"], $columns);
		
		$this->saveFileData($path, $txt);
	}
	function CreateModelApiModel($model)
{

		$path = sprintf('%s/%s/Model/Api/%s.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$save_fields = '';
		foreach($model ["columns"] as $column )
{

			$property = str_replace(' ', '', ucwords(str_replace('_', ' ', $column ["name"] ) ));
			$save_fields .= sprintf('$model->set%1$s($entity->get%1$s());', $property ) . "\n\t\t";
		}
		
		$save_fields .= '$model->save();';
		$txt = sprintf('<?php
	
namespace %s\%s\Model\APi;
	
use %1$s\%2$s\Api\%3$sInterface;
				
class %s  implements %3$sInterface
{

	
	/**
	 *
	 * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
	 */
	protected $_searchResultsFactory;
	protected $_objectManager;
	
	/**
	 *
	 * @param \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory
	 */
	public function __construct(\Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory)
	{
		$this->_searchResultsFactory = $searchResultsFactory;
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	}
				
	/**
	 *
	 *
{
@inheritdoc}
	 *
	 */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
			
		$collection = $this->_objectManager->create(\'\%1$s\%2$s\Model\Resource\%3$s\Collection\');
		$result=$collection->getData();
		$searchResult = $this->_searchResultsFactory->create();
		$searchResult->setSearchCriteria($searchCriteria);
		$searchResult->setItems($result);
		$searchResult->setTotalCount(count($result ));
		return $searchResult;
	}
	
    /**
	 *
	 *
{
@inheritdoc}
	 *
	 */
    public function save($entity){
				
		$collection = $this->_objectManager->create(\'\%1$s\%2$s\Model\Resource\%3$s\Collection\');
		$collection->addFieldToFilter("id",$entity->getId());
		$item = $collection->getFirstItem();
		$model = $this->_objectManager->create(\'%1$s\%2$s\Model\%3$s\');
		
		if ($item->getId()){
			$model->load($item->getId());
		}
			
		%4$s
		
			
		return $model;
	}
	
    /**
	 *
	 *
{
@inheritdoc}
	 *
	 */
    public function deleteById($id){
		try
{

			
			$model = $this->_objectManager->create(\'%1$s\%2$s\Model\%3$s\');
			$model->load($id);
			$model->delete();
			return true;
		
		} catch (\Exception $e)
{

			return $e->getMessage();
		}
		
		return true;
	}
	}', $this->_vendor, $this->_module, $model ["name"], $save_fields);
		
		$this->saveFileData($path, $txt);
	}
	function CreateModelApiModelData($model)
{

		$path = sprintf('%s/%s/Model/Api/Data/%sData.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		// Private
		$private = "";
		foreach($model ["columns"] as $column )
{

			$private .= 'private $' . $column ["name"] . ';' . "\n\t";
		}
		// start columns
		$columns = "";
		$columns .= '
	/**
	 *
	 *
{
@inheritdoc}
	 *
	 */
	public function getId()
{
	return $this->id;
    }
	
	/**
	 *
	 *
{
@inheritdoc}
	 *
	 */
	public function setId($value)
{
	$this->id = $value;
    }
				';
		foreach($model ["columns"] as $column )
{

			$property = str_replace(' ', '', ucwords(str_replace('_', ' ', $column ["name"] ) ));
			
			$columns .= sprintf('
	/**
	 *
	 *
{
@inheritdoc}
	 *
	 */
    public function get%3$s(){
		return $this->%1$s;
	}
			
   /**
	 *
	 *
{
@inheritdoc}
	 *
	 */
    public function set%3$s($value){
		$this->%1$s = $value;
	}', $column ["name"], $column ["label"], $property);
		}
		
		$columns .= sprintf('
	
	/**
	 *
	 *
{
@inheritdoc}
	 *
	 */
    public function getStatus(){
		return $this->status;
	}
	
   /**
	 *
	 *
{
@inheritdoc}
	 *
	 */
    public function setStatus($status){
		$this->status = $status;
	}
				', $model ["name"]);
		
		// End columns
		
		$txt = sprintf('<?php
	
namespace %1$s\%2$s\Model\Api\Data;
	
use %1$s\%2$s\Api\Data\%3$sDataInterface;
	
class %3$sData implements %3$sDataInterface
{

	
	%4$s
	
	%5$s}', $this->_vendor, $this->_module, $model ["name"], $private, $columns);
		
		$this->saveFileData($path, $txt);
	}
	function CreateApi($model)
{

		$this->CreateFolder(sprintf('%s/%s/Api', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Api/Data', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Model/Api', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Model/Api/Data', $this->_vendor, $this->_module ));
		$this->CreateApiModelInterface($model);
		$this->CreateApiModelDataInterface($model);
		$this->CreateModelApiModel($model);
		$this->CreateModelApiModelData($model);
	}
	function CreateDiFile()
{

		$this->CreateFolder($this->_vendor . "/" . $this->_module . "/" . "etc/adminhtml");
		
		$path = sprintf('%s/%s/etc/di.xml', $this->_vendor, $this->_module);
		
		
		
		$preference = "";
		foreach($this->_config ["backend_model"] as $model )
{

			$preference .= "\n\t" . sprintf(' <preference for="%1$s\%2$s\Api\%3$sInterface" type="%1$s\%2$s\Model\Api\%3$s" />', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ) ) . "\n";
			$preference .= "\n\t" . sprintf(' <preference for="%1$s\%2$s\Api\Data\%3$sDataInterface" type="%1$s\%2$s\Model\Api\Data\%3$sData" />', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ) ) . "\n";
		}
		$txt = sprintf('<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
	%1$s
</config>', $preference);
		
		$this->saveFileData($path, $txt);
	}
	function CreateWebapiFile()
{

		$this->CreateFolder($this->_vendor . "/" . $this->_module . "/" . "etc/adminhtml");
		
		$path = sprintf('%s/%s/etc/webapi.xml', $this->_vendor, $this->_module);
		
		
		
		$route = "";
		foreach($this->_config ["backend_model"] as $model )
{

			
			$route .= "\n\t" . sprintf('
	
	<!-- %3$s API -->
					
	<!-- end point = /V1/%4$s/%5$s/%6$s/list -->
	<route url="/V1/%4$s/%5$s/%6$s/list" method="GET">
		<service class="%1$s\%2$s\Api\%3$sInterface" method="getList" />
		<resources>
			<resource ref="Magento_Backend::admin" />
		</resources>
	</route>
	
	<!-- end point = /V1/%4$s/%5$s/%6$s/save -->
	<!-- Json  = "{"entity": json object here }" -->
	<route url="/V1/%4$s/%5$s/%6$s/save" method="POST">
		<service class="%1$s\%2$s\Api\%3$sInterface" method="save" />
		<resources>
			<resource ref="Magento_Backend::admin" />
		</resources>
	</route>
					
	<route url="/V1/%4$s/%5$s/%6$s/save" method="PUT">
		<service class="%1$s\%2$s\Api\%3$sInterface" method="save" />
		<resources>
			<resource ref="Magento_Backend::admin" />
		</resources>
	</route>

	<!-- end point = /V1/%4$s/%5$s/%6$s/delete/:id -->
	<route url="/V1/%4$s/%5$s/%6$s/delete/:id" method="DELETE">
		<service class="%1$s\%2$s\Api\%3$sInterface" method="deleteById" />
		<resources>
			<resource ref="Magento_Backend::admin" />
		</resources>
	</route>
					
		', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ) ) . "\n";
		}
		$txt = sprintf('<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"	xsi:noNamespaceSchemaLocation="../../../../../app/code/Magento/Webapi/etc/webapi.xsd">
	%1$s
</routes>', $route);
		
		$this->saveFileData($path, $txt);
	}
	function CreateModel()
{

		if ($this->_config ["model"])
{

			$this->CreateFolder(sprintf('%s/%s/Model', $this->_vendor, $this->_module ));
		}
	}
	function CreateController()
{

		if ($this->_config ["controller"])
{

			$this->CreateFolder(sprintf('%s/%s/Controller', $this->_vendor, $this->_module ));
		}
	}
	function CreateView()
{

		if (count($this->_config ["view"] ) > 0)
{

			$this->CreateFolder(sprintf('%s/%s/view', $this->_vendor, $this->_module ));
		}
		
		if ($this->_config ["view"] ["frontend"])
{

			$this->CreateFolder(sprintf('%s/%s/view/frontend', $this->_vendor, $this->_module ));
			$this->CreateFolder(sprintf('%s/%s/view/frontend/layout', $this->_vendor, $this->_module ));
			$this->CreateFolder(sprintf('%s/%s/view/frontend/templates', $this->_vendor, $this->_module ));
		}
		
		if ($this->_config ["view"] ["adminhtml"])
{

			$this->CreateFolder(sprintf('%s/%s/view/adminhtml', $this->_vendor, $this->_module ));
			$this->CreateFolder(sprintf('%s/%s/view/adminhtml/layout', $this->_vendor, $this->_module ));
			$this->CreateFolder(sprintf('%s/%s/view/adminhtml/templates', $this->_vendor, $this->_module ));
		}
	}
	function example($model)
{

		$path = sprintf('%s/%s/', $this->_vendor, $this->_module);
		
		
		
		$txt = sprintf('<?php
			%s/%s
		', $this->_vendor, $this->_module);
		
		$this->saveFileData($path, $txt);
	}
	function CreateBackEndModels()
{

		if ($this->_config ["backend_model"])
{

			foreach($this->_config ["backend_model"] as $model )
{

				$this->CreateBackEndModel($model);
				if ($model ["api"])
{

					$this->CreateApi($model);
				}
			}
			$this->CreateMenuFile();
			$this->CreateRoutesFile();
			$this->CreateDiFile();
			$this->CreateWebapiFile();
		}
	}
	function CreateMenuFile()
{

		$this->CreateFolder($this->_vendor . "/" . $this->_module . "/" . "etc/adminhtml");
		
		$path = sprintf('%s/%s/etc/adminhtml/menu.xml', $this->_vendor, $this->_module);
		
		
		
		$txt = sprintf('<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../Backend/etc/menu.xsd">
	<menu>
		<add id="%3$s::base" title="%1$s" module="%1$s_%2$s" sortOrder="90" resource="%1$s_%2$s::main"/>
		<add id="%3$s_%4$s::%4$s" title="%2$s" module="%1$s_%2$s" sortOrder="0" parent="%3$s::base"  resource="%1$s_%2$s::main"/>', $this->_vendor, $this->_module, strtolower($this->_vendor), strtolower($this->_module ));
		$txt .= "\n";
		
		foreach($this->_config ["backend_model"] as $model )
{

			$txt .= "\t\t" . sprintf('<add id="%4$s_%5$s::%6$s" title="%3$s" module="%1$s_%2$s" sortOrder="10" parent="%4$s_%5$s::%5$s" action="%4$s_%5$s/%6$s/" resource="%1$s_%2$s::main"/>', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ) ) . "\n";
		}
		$txt .= '	</menu>
</config>';
		
		$this->saveFileData($path, $txt);
	}
	function CreateRoutesFile()
{

		$this->CreateFolder($this->_vendor . "/" . $this->_module . "/" . "etc/adminhtml");
		
		$path = sprintf('%s/%s/etc/adminhtml/routes.xml', $this->_vendor, $this->_module);
		
		
		
		$txt = sprintf('<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../lib/internal/Magento/Framework/App/etc/routes.xsd">
	<router id="admin">
		<route id="%3$s_%4$s" frontName="%3$s_%4$s">
			<module name="%1$s_%2$s" />
		</route>
	</router>
</config>', $this->_vendor, $this->_module, strtolower($this->_vendor), strtolower($this->_module ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateAdminhtmlModelFile($model)
{

		$path = sprintf('%s/%s/Block/Adminhtml/%s.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Block\Adminhtml;
	
class %s extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor
     *
     * @return void
     */
	protected function _construct()
	{
		$this->_controller = \'adminhtml_%6$s\'; /* block grid.php directory */
		$this->_blockGroup = \'%1$s_%2$s\';
		$this->_headerText = __(\'%3$s\');
		$this->_addButtonLabel = __(\'Add New Entry\');
		parent::_construct();
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateAdminhtmlModelGridFile($model)
{

		$path = sprintf('%s/%s/Block/Adminhtml/%s/Grid.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		$columns = "";
		foreach($model ["columns"] as $column )
{

			
			if ($column ["type"] == "string") :
				$columns .= sprintf('$this->addColumn(
		    \'%1$s\',
            [
			\'header\' => __(\'%2$s\'),
			\'index\' => \'%1$s\',
			\'class\' => \'%1$s\'
			]
        );', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			if ($column ["type"] == "int") :
				$columns .= sprintf('$this->addColumn(
		    \'%1$s\',
            [
			\'header\' => __(\'%2$s\'),
			\'index\' => \'%1$s\',
			\'class\' => \'%1$s\'
			]
        );', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			if ($column ["type"] == "date") :
				$columns .= sprintf('$this->addColumn(
		    \'%1$s\',
            [
			\'header\' => __(\'%2$s\'),
			\'type\' => \'date\',
			\'align\' => \'center\',
			\'index\' => \'%1$s\',
			\'default\' => \' ---- \'
			]
        );', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			if ($column ["type"] == "decimal") :
				$columns .= sprintf('$this->addColumn(
			\'%1$s\',
            [
			\'header\' => __(\'%2$s\'),
			\'index\' => \'%1$s\',
			\'class\' => \'%1$s\'
			]
        );', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			$columns .= "\n\n\t\t";
		}
		$columns =trim ($columns,"\t");
		$columns =trim ($columns,"\n");
		$txt = sprintf('<?php
	
namespace %s\%s\Block\Adminhtml\%s;
	
use %1$s\%2$s\Model\System\Config\Status;
use Magento\Framework\Exception;
	
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	 
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
	protected $_status;
	protected $_collectionFactory;
	
	/**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
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

	/**
     * @return void
     */
	protected function _construct()
    {

		parent::_construct();
		$this->setId(\'%6$sGrid\');
		$this->setDefaultSort(\'id\');
		$this->setDefaultDir(\'DESC\');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(false);
	}
				
	/**
     * @return Store
     */
	protected function _getStore()
    {
		$storeId =(int )$this->getRequest()->getParam(\'store\', 0);
		return $this->_storeManager->getStore($storeId);
	}
				
	/**
     * @return $this
     */
	protected function _prepareCollection()
    {

		try {
    		$collection = $this->_collectionFactory->load();
	        $this->setCollection($collection);
			parent::_prepareCollection();
			return $this;
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}
	}
				
	/**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
	protected function _prepareColumns()
    {

		$this->addColumn(
		    \'id\',
            [
			\'header\' => __(\'ID\'),
			\'type\' => \'number\',
			\'index\' => \'id\',
			\'header_css_class\' => \'col-id\',
			\'column_css_class\' => \'col-id\'
		    ]
        );
		
		%7$s
		        
		$this->addColumn(
		    \'status\',
            [
			\'header\' => __(\'Status\'),
			\'index\' => \'status\',
			\'class\' => \'status\',
			\'type\' => \'options\',
			\'options\' => $this->_status->toOptionArray()
            ]
        );
		$block = $this->getLayout()->getBlock(\'grid.bottom.links\');
		if ($block) {
			$this->setChild(\'grid.bottom.links\', $block);
		}
		return parent::_prepareColumns();
	}
				
	/**
     * @return $this
     */
	protected function _prepareMassaction()
    {

		$this->setMassactionIdField(\'id\');
		$this->getMassactionBlock()->setFormFieldName(\'id\');
		$this->getMassactionBlock()->addItem(
		    \'delete\',
            [
            \'label\' => __(\'Delete\'),
			\'url\' => $this->getUrl(\'%4$s_%5$s/*/massDelete\'),
			\'confirm\' => __(\'Are you sure?\')
            ]
        );
		return $this;
	}
	
	/**
     * @return string
     */
	public function getGridUrl()
    {
		return $this->getUrl(\'%4$s_%5$s/*/index\', [\'_current\' => true]);
	}
	
	/**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
	public function getRowUrl($row)
    {
		return $this->getUrl(
            \'%4$s_%5$s/*/edit\',
            [
			\'store\' => $this->getRequest()->getParam(\'store\'),
			\'id\' => $row->getId()
            ]
        );
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"]), $columns);
		
		$txt = str_replace("\r", "", $txt);
		
		$this->saveFileData($path, $txt);
	}
	function CreateAdminhtmlModelEditFile($model)
{

		$path = sprintf('%s/%s/Block/Adminhtml/%s/Edit.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace ' . $this->_vendor . '\\' . $this->_module . '\Block\Adminhtml\\' . $model ["name"] . ';
	
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;
	
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
	public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
		$this->_coreRegistry = $registry;
		parent::__construct($context, $data);
	}

	/**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
	protected function _construct()
    {
		$this->_objectId = \'id\';
		$this->_controller = \'adminhtml_' . strtolower($model ["name"] ) . '\';
		$this->_blockGroup = \'' . $this->_vendor . '_' . $this->_module . '\';
		parent::_construct();
		$this->buttonList->add(
            \'save_and_continue_edit\',
            [
			\'class\' => \'save\',
			\'label\' => __(\'Save and Continue Edit\'),
			\'data_attribute\' => [
				\'mage-init\' => [
					\'button\' => [
						\'event\' => \'saveAndContinueEdit\',
						\'target\' => \'#edit_form\'
					]
				]
			 ]
		    ],
            10
        );
	}
	
	/**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
	public function getHeaderText()
    {

		$item = $this->_coreRegistry->registry(\'current_' . strtolower($this->_vendor ) . '_' . strtolower($this->_module ) . '_' . strtolower($model ["name"] ) . '\');
		if ($item->getId()) {
			return __("Edit Item \'%1\'", $this->escapeHtml($item->getId()));
		} else {
			return __(\'New Item\');
		}
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		$this->saveFileData($path, $txt);
	}
	function CreateAdminhtmlModelGridEditFormFile($model)
{

		$path = sprintf('%s/%s/Block/Adminhtml/%s/Edit/Form.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Block\Adminhtml\%s\Edit;
	
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		parent::_construct();
		$this->setId(\'%s_%s_form\');
        $this->setTitle(__(\'%1$s Information\'));
    }
	
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
	protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create(
	        [
			\'data\' => [
				\'id\' => \'edit_form\',
				\'action\' => $this->getUrl(\'' . strtolower($this->_vendor ) . '_' . strtolower($this->_module ) . '/' . strtolower($model ["name"] ) . '/save\'),
				\'method\' => \'post\'
			]
		    ]
        );
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateAdminhtmlModelGridEditTabsFile($model)
{

		$path = sprintf('%s/%s/Block/Adminhtml/%s/Edit/Tabs.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Block\Adminhtml\%s\Edit;
	
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

	/**
     * Constructor
     *
     * @return void
     */
	protected function _construct()
    {
		parent::_construct();
			$this->setId(\'%s_%s_%s_edit_tabs\');
			$this->setDestElementId(\'edit_form\');
			$this->setTitle(__(\'%3$s\'));
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateAdminhtmlModelGridEditTabsMainFile($model)
{

		$path = sprintf('%s/%s/Block/Adminhtml/%s/Edit/Tab/Main.php', $this->_vendor, $this->_module, $model ["name"]);
		
		
		$columns = "";
		foreach($model ["columns"] as $column )
{

			
			if ($column ["type"] == "string") :
				$columns .= sprintf('$fieldset->addField(\'%1$s\', \'text\', [
			\'name\' => \'%1$s\',
			\'required\' => %3$s,
			\'label\' => __(\'%2$s\'),
			\'title\' => __(\'%2$s\'),
			]);', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			if ($column ["type"] == "int") :
				$columns .= sprintf('$fieldset->addField(\'%1$s\', \'text\', [
			\'name\' => \'%1$s\',
			\'required\' => %3$s,
			\'label\' => __(\'%2$s\'),
			\'title\' => __(\'%2$s\'),
			]);', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			if ($column ["type"] == "date") :
				$columns .= sprintf('$fieldset->addField(\'%1$s\', \'date\', [
			\'name\' => \'%1$s\',
			\'required\' => %3$s,
			\'label\' => __(\'%2$s\'),
			\'title\' => __(\'%2$s\'),
			\'date_format\' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
        	\'class\' => \'validate-date\'
			]);', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			if ($column ["type"] == "decimal") :
				$columns .= sprintf('$fieldset->addField(\'%1$s\', \'text\', [
			\'name\' => \'%1$s\',
			\'required\' => %3$s,
			\'label\' => __(\'%2$s\'),
			\'title\' => __(\'%2$s\'),
        	\'class\' => \'validate-zero-or-greater\'
			]);', $column ["name"], $column ["label"], $column ["rquired"]);
			
			endif;
			
			$columns .= "\n\n\t\t";
		}
		
		$columns =trim ($columns,"\t");
		$columns =trim ($columns,"\n");
		$txt = sprintf('<?php
	
namespace %s\%s\Block\Adminhtml\%s\Edit\Tab;
	
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use %1$s\%2$s\Model\System\Config\Status;
	
class Main extends Generic implements TabInterface
{

	protected $_status;
	
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $status,
        array $data = []
    ) {
        $this->_status = $status;
		parent::__construct($context, $registry, $formFactory, $data);
	}
	
   /**
    *
    * {@inheritdoc}
    */
	public function getTabLabel()
    {
		return __(\'Item Information\');
	}
				
   /**
    *
    * {@inheritdoc}
    */
	public function getTabTitle()
    {
		return __(\'Item Information\');
	}
				
   /**
    *
    * {@inheritdoc}
    */
    public function canShowTab()
    {
		return true;
	}
				
   /**
    *
    * {@inheritdoc}
    */
    public function isHidden()
    {
		return false;
	}
				
	/**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
	protected function _prepareForm()
    {
		$model = $this->_coreRegistry->registry(\'current_%s_%s_%s\');
		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create();
		$form->setHtmlIdPrefix(\'item_\');
		$fieldset = $form->addFieldset(\'base_fieldset\', [
			\'legend\' => __(\'Item Information\')
		]);
		if ($model->getId()) {
			$fieldset->addField(\'id\', \'hidden\', [\'name\' => \'id\']);
		}
		
		%7$s
				
		$fieldset->addField(
            \'status\',
            \'select\',
            [
			\'name\' => \'status\',
			\'label\' => __(\'Status\'),
			\'options\' => $this->_status->toOptionArray()
			]
        );
		        
		$form->setValues($model->getData());
		$this->setForm($form);
		return parent::_prepareForm();
	}
}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"]), $columns);
		$this->saveFileData($path, $txt);
	}
	function CreateControllersAdminhtmlModelFile($model)
{

		$path = sprintf("%s/%s/Controller/Adminhtml/%s.php", $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Controller\Adminhtml;
	
abstract class %s extends \Magento\Backend\App\Action
{


	 /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry;

	/**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
	protected $resultForwardFactory;
	
	
	/**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	protected $resultPageFactory;
	
				
	 /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
	public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
{

		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context);
		$this->resultForwardFactory = $resultForwardFactory;
		$this->resultPageFactory = $resultPageFactory;
	}
	
	  /**
     * Initiate action
     *
     * @return this
     */
	protected function _initAction()
{

		$this->_view->loadLayout();
		$this->_setActiveMenu(\'%1$s_%2$s::%4$s\' )->_addBreadcrumb(__(\'%3$s\'), __(\'%3$s\' ));
		return $this;
	}
	
	/**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
	protected function _isAllowed()
{

		return $this->_authorization->isAllowed(\'%1$s_%2$s::%5$s\');
	}}', $this->_vendor, $this->_module, $model ["name"], strtolower($model ["table"]), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateControllersAdminhtmlModelDeleteFile($model)
{

		$path = sprintf("%s/%s/Controller/Adminhtml/%s/Delete.php", $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Controller\Adminhtml\%s;
	
class Delete extends \%1$s\%2$s\Controller\Adminhtml\%3$s
{

	public function execute()
{

		$id = $this->getRequest()->getParam(\'id\');
		if ($id)
{

			try
{

				$model = $this->_objectManager->create(\'%1$s\%2$s\Model\%3$s\');
				$model->load($id);
				$model->delete();
				$this->messageManager->addSuccess(__(\'You deleted the item.\' ));
				$this->_redirect(\'%s_%s/*/\');
				return;
			} catch (\Magento\Framework\Exception\LocalizedException $e )
{

				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e )
{

				$this->messageManager->addError(__(\'We can\\\'t delete item right now. Please review the log and try again.\' ));
				$this->_objectManager->get(\'Psr\Log\LoggerInterface\' )->critical($e);
				$this->_redirect(\'%4$s_%5$s/*/edit\', [
						\'id\' => $this->getRequest()->getParam(\'id\' )
				]);
				return;
			}
		}
		$this->messageManager->addError(__(\'We can\\\'t find a item to delete.\' ));
		$this->_redirect(\'%4$s_%5$s/*/\');
	}}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateControllersAdminhtmlModelEditFile($model)
{

		$path = sprintf("%s/%s/Controller/Adminhtml/%s/Edit.php", $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Controller\Adminhtml\%s;
	
class Edit extends \%1$s\%2$s\Controller\Adminhtml\%3$s
{

	
	public function execute()
{

		$id = $this->getRequest()->getParam(\'id\');
		$model = $this->_objectManager->create(\'%1$s\%2$s\Model\%3$s\');
		if ($id)
{

			$model->load($id);
			if (!$model->getId())
{

				$this->messageManager->addError(__(\'This item no longer exists.\'));
				$this->_redirect(\'%s_%s/*\');
				return;
			}
		}
		$data = $this->_objectManager->get(\'Magento\Backend\Model\Session\')->getPageData(true);
		if (!empty($data))
{

			$model->addData($data);
		}
		$resultPage = $this->resultPageFactory->create();
		if ($id)
{

			$resultPage->getConfig()->getTitle()->prepend(__(\'Edit Items Entry\'));
		}else{
			$resultPage->getConfig()->getTitle()->prepend(__(\'Add Items Entry\'));
		}
	
		$this->_coreRegistry->register(\'current_%4$s_%5$s_%6$s\', $model);
		$this->_initAction();
		$this->_view->getLayout()->getBlock(\'%6$s_%6$s_edit\');
		$this->_view->renderLayout();
	}}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		$this->saveFileData($path, $txt);
	}
	function CreateControllersAdminhtmlModelIndexFile($model)
{

		$path = sprintf("%s/%s/Controller/Adminhtml/%s/Index.php", $this->_vendor, $this->_module, $model ["name"]);
		;
		
		
		
		$txt = sprintf('<?php
	
namespace %1$s\%2$s\Controller\Adminhtml\%3$s;
	
class Index extends \%1$s\%2$s\Controller\Adminhtml\%3$s
{

	
	/**
     * %3$s list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
	public function execute()
{

		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu(\'%1$s_%2$s::%5$s\');
		$resultPage->getConfig()->getTitle()->prepend(__(\'%3$s\'));
		$resultPage->addBreadcrumb(__(\'%1$s\'), __(\'%1$s\'));
		$resultPage->addBreadcrumb(__(\'%3$s\'), __(\'%3$s\'));
		return $resultPage;
	}}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateControllersAdminhtmlModelMassDeleteFile($model)
{

		$path = sprintf("%s/%s/Controller/Adminhtml/%s/MassDelete.php", $this->_vendor, $this->_module, $model ["name"]);
		;
		
		
		
		$txt = sprintf('<?php
	
namespace %1$s\%2$s\Controller\Adminhtml\%3$s;
	
class MassDelete extends \%1$s\%2$s\Controller\Adminhtml\%3$s
{

	
	/**
     * @return void
     */
	public function execute()
{

		$itemsIds = $this->getRequest()->getParam(\'id\');
		if (!is_array($itemsIds))
{

			$this->messageManager->addError(__(\'Please select item(s).\'));
		} else
{

				try
{

					foreach($itemsIds as $itemId)
{

						$model = $this->_objectManager->create(\'%1$s\%2$s\Model\%3$s\');
						$model->load($itemId);
						$model->delete();
					}
					$this->messageManager->addSuccess(
						__(\'A total of %1 record(s) have been deleted.\', count($itemsIds))
					);
				} catch (\Magento\Framework\Exception\LocalizedException $e)
{

					$this->messageManager->addError($e->getMessage());
				} catch (\Exception $e)
{

					$this->messageManager->addException($e, __(\'An error occurred while deleting record(s).\'));
				}
		}
		$this->_redirect(\'%4$s_%5$s/*/\');
	}}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		$this->saveFileData($path, $txt);
	}
	function CreateControllersAdminhtmlModelNewActionFile($model)
{

		$path = sprintf("%s/%s/Controller/Adminhtml/%s/NewAction.php", $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Controller\Adminhtml\%s;
	
class NewAction extends \%1$s\%2$s\Controller\Adminhtml\%3$s
{

	public function execute()
{

		$this->_forward(\'edit\');
	}}', $this->_vendor, $this->_module, $model ["name"]);
		$this->saveFileData($path, $txt);
	}
	function CreateControllersAdminhtmlModelSaveFile($model)
{

		$path = sprintf("%s/%s/Controller/Adminhtml/%s/Save.php", $this->_vendor, $this->_module, $model ["name"]);
		;
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Controller\Adminhtml\%s;
	
class Save extends \%1$s\%2$s\Controller\Adminhtml\%3$s
{

	
	public function execute()
{

		if ($this->getRequest()->getPostValue())
{

			try
{

				$model = $this->_objectManager->create(\'%1$s\%2$s\Model\%3$s\');
				$data = $this->getRequest()->getPostValue();
				$inputFilter = new \Zend_Filter_Input([ ], [ ], $data);
				$data = $inputFilter->getUnescaped();
				$id = $this->getRequest()->getParam(\'id\');
				if ($id)
{

					$model->load($id);
					if ($id != $model->getId())
{

						throw new \Magento\Framework\Exception\LocalizedException(__(\'The wrong item is specified.\' ));
					}
				}
				$model->setData($data);
				$session = $this->_objectManager->get(\'Magento\Backend\Model\Session\');
				$session->setPageData($model->getData());
				$model->save();
				$this->messageManager->addSuccess(__(\'You saved the item.\' ));
				$session->setPageData(false);
				if ($this->getRequest()->getParam(\'back\' ))
{

					$this->_redirect(\'%s_%s/*/edit\', [
						\'id\' => $model->getId()
					]);
				return;
				}
				$this->_redirect(\'%4$s_%5$s/*/\');
				return;
			} catch (\Magento\Framework\Exception\LocalizedException $e )
{

				$this->messageManager->addError($e->getMessage());
				$id =(int ) $this->getRequest()->getParam(\'id\');
				if (! empty($id ))
{

					$this->_redirect(\'%4$s_%5$s/*/edit\', [
						\'id\' => $id
					]);
				} else
{

					$this->_redirect(\'%4$s_%5$s/*/new\');
				}
				return;
			} catch (\Exception $e )
{

				$this->messageManager->addError(__(\'Something went wrong while saving the item data. Please review the error log.\' ));
				$this->_objectManager->get(\'Psr\Log\LoggerInterface\' )->critical($e);
				$this->_objectManager->get(\'Magento\Backend\Model\Session\' )->setPageData($data);
				$this->_redirect(\'%4$s_%5$s/*/edit\', [
					\'id\' => $this->getRequest()->getParam(\'id\' )
				]);
				return;
			}
		}
		$this->_redirect(\'%4$s_%5$s/*/\');
	}}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateModelModelFile($model)
{

		$path = sprintf("%s/%s/Model/%s.php", $this->_vendor, $this->_module, $model ["name"]);
		;
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Model;
	
class %s extends \Magento\Framework\Model\AbstractModel
{

	
	 /**
     * Constructor
     *
     * @return void
     */
		protected function _construct()
{

			parent::_construct();
			$this->_init(\'%1$s\%2$s\Model\Resource\%3$s\');
		}}', $this->_vendor, $this->_module, $model ["name"]);
		
		$this->saveFileData($path, $txt);
	}
	function CreateModelResourceModelFile($model)
{

		$path = sprintf("%s/%s/Model/Resource/%s.php", $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Model\Resource;
	
class %s extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

	
	 /**
     * Model Initialization
     *
     * @return void
     */
	protected function _construct()
{

		$this->_init(\'%s_%s_%s\', \'id\');
	}}', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["table"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateModelResourceModelCollectionFile($model)
{

		$path = sprintf("%s/%s/Model/Resource/%s/Collection.php", $this->_vendor, $this->_module, $model ["name"]);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Model\Resource\%s ;
	
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

  	
	/**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
{

		$this->_init(\'%1$s\%2$s\Model\%3$s\', \'%1$s\%2$s\Model\Resource\%3$s\');
	}}', $this->_vendor, $this->_module, $model ["name"]);
		$this->saveFileData($path, $txt);
	}
	function CreateViewAdminhtmlLayoutIndexFile($model)
{

		$path = sprintf("%s/%s/view/adminhtml/layout/%s_%s_%s_edit" . ".xml", $this->_vendor, $this->_module, strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		
		
		$txt = sprintf('<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="admin-2columns-left" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/Layout/etc/page_configuration.xsd">
	<body>
		<referenceContainer name="left">
			<block class="%s\%s\Block\Adminhtml\%s\Edit\Tabs" name="%s_%s_%s_edit_tabs">
				<block class="%1$s\%2$s\Block\Adminhtml\%3$s\Edit\Tab\Main" name="' . strtolower($this->_vendor ) . '_' . strtolower($this->_module ) . '_' . strtolower($model ["name"] ) . '_edit_tab_main"/>
				<action method="addTab">
					<argument name="name" xsi:type="string">main_section</argument>
					<argument name="block" xsi:type="string">%4$s_%5$s_%6$s_edit_tab_main</argument>
				</action>
			</block>
		</referenceContainer>
		<referenceContainer name="content">
			<block class="' . $this->_vendor . '\\' . $this->_module . '\Block\Adminhtml\\' . $model ["name"] . '\Edit" name="' . strtolower($this->_module ) . '_' . strtolower($model ["name"] ) . '_edit"/>
		</referenceContainer>
	</body>
</page>', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateViewAdminhtmlLayoutEditFile($model)
{

		$path = sprintf("%s/%s/view/adminhtml/layout/%s_%s_%s_index" . ".xml", $this->_vendor, $this->_module, strtolower($this->_vendor), strtolower($this->_module), strtolower($model ["name"] ));
		
		
		$txt = sprintf('<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/Layout/etc/page_configuration.xsd">
	<body>
		<referenceContainer name="content">
			<block class="%s\%s\Block\Adminhtml\%s" name="%s_%s_container"/>
		</referenceContainer>
	</body>
</page>', $this->_vendor, $this->_module, $model ["name"], strtolower($this->_module), strtolower($model ["name"] ));
		
		$this->saveFileData($path, $txt);
	}
	function CreateModelSystemConfigStatusFile($model)
{

		$this->CreateFolder(sprintf('%s/%s/Model/System', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Model/System/Config', $this->_vendor, $this->_module ));
		
		$path = sprintf('%s/%s/Model/System/Config/Status.php', $this->_vendor, $this->_module);
		
		
		
		$txt = sprintf('<?php
	
namespace %s\%s\Model\System\Config;
	
use Magento\Framework\Option\ArrayInterface;
	
class Status implements ArrayInterface
{

	const ENABLED = 1;
	const DISABLED = 0;
	public function toOptionArray()
{

		$options = [
			self::ENABLED => __(\'Enabled\'),
			self::DISABLED => __(\'Disabled\' )
		];
		return $options;
	}}', $this->_vendor, $this->_module);
		
		$this->saveFileData($path, $txt);
	}
	function CreateBackEndModel($model)
{

		// Create Block Folder
		$this->CreateFolder(sprintf('%s/%s/Block', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Block/Adminhtml', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Block/Adminhtml/%s', $this->_vendor, $this->_module, $model ["name"] ));
		$this->CreateFolder(sprintf('%s/%s/Block/Adminhtml/%s', $this->_vendor, $this->_module, $model ["name"] ));
		$this->CreateFolder(sprintf('%s/%s/Block/Adminhtml/%s/Edit', $this->_vendor, $this->_module, $model ["name"] ));
		$this->CreateFolder(sprintf('%s/%s/Block/Adminhtml/%s/Edit/Tab', $this->_vendor, $this->_module, $model ["name"] ));
		
		// Create Controller Folder
		$this->CreateFolder(sprintf('%s/%s/Controller', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Controller/Adminhtml', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Controller/Adminhtml/%s', $this->_vendor, $this->_module, $model ["name"] ));
		
		// Create Model Folder
		$this->CreateFolder(sprintf('%s/%s/Model', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Model/Resource', $this->_vendor, $this->_module ));
		$this->CreateFolder(sprintf('%s/%s/Model/Resource/%s', $this->_vendor, $this->_module, $model ["name"] ));
		
		// Create Block Files
		// Adminhtml/{model}.php
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
		$this->_config ["view"] ["adminhtml"] = true;
		$this->CreateView();
		$this->CreateViewAdminhtmlLayoutIndexFile($model);
		$this->CreateViewAdminhtmlLayoutEditFile($model);
		
		// Create Config Status File
		$this->CreateModelSystemConfigStatusFile($model);
	}
	function CreateGlobalEventsXML()
{

		$path = sprintf('%s/%s/etc/events.xml', $this->_vendor, $this->_module);
		$ext_file = fopen($path, "w" ) or die("Unable to open file!");
		$events = "";
		foreach($this->_config ["observer"] ["global"] as $event )
{

			$events .= sprintf('<event name="%5$s">
        <observer name="%3$s_%4$s_%5$s" instance="%1$s\%2$s\Model\Observer\Observer" />
    </event>', $this->_vendor, $this->_module, strtolower($this->_vendor), strtolower($this->_module), $event);
		}
		
		$txt = sprintf('<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Event/etc/events.xsd">
    %1$s
</config>
		
				', $events);
		
		fwrite($ext_file, $txt);
		fclose($ext_file);
	}
	function CreateFrontendEventsXML()
{

		$path = sprintf('%s/%s/etc/frontend/events.xml', $this->_vendor, $this->_module);
		$ext_file = fopen($path, "w" ) or die("Unable to open file!");
		$events = "";
		foreach($this->_config ["observer"] ["frontend"] as $event )
{

			$events .= sprintf('<event name="%5$s">
        <observer name="%3$s_%4$s_%5$s" instance="%1$s\%2$s\Model\Observer\Frontend\Observer" />
    </event>', $this->_vendor, $this->_module, strtolower($this->_vendor), strtolower($this->_module), $event);
		}
		
		$txt = sprintf('<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Event/etc/events.xsd">
    %1$s
</config>
	
				', $events);
		
		fwrite($ext_file, $txt);
		fclose($ext_file);
	}
	function CreateAdminhtmlEventsXML()
{

		$path = sprintf('%s/%s/etc/adminhtml/events.xml', $this->_vendor, $this->_module);
		$ext_file = fopen($path, "w" ) or die("Unable to open file!");
		$events = "";
		foreach($this->_config ["observer"] ["adminhtml"] as $event )
{

			$events .= sprintf('<event name="%5$s">
        <observer name="%3$s_%4$s_%5$s" instance="%1$s\%2$s\Model\Observer\Adminhtml\Observer" />
    </event>', $this->_vendor, $this->_module, strtolower($this->_vendor), strtolower($this->_module), $event);
		}
		
		$txt = sprintf('<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Event/etc/events.xsd">
    %1$s
</config>
	
				', $events);
		
		fwrite($ext_file, $txt);
		fclose($ext_file);
	}
	function CreateGlobalEventsObserver()
{

		$path = sprintf('%s/%s/Model/Observer/Observer.php', $this->_vendor, $this->_module);
		
		$events = "";
		foreach($this->_config ["observer"] ["global"] as $event )
{

			$events .= sprintf('case "%1$s" :{
				
				}
				break;
					', $event);
			$events .= "\n\t\t\t";
		}
		
		$txt = sprintf('<?php
		
namespace %s\%s\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
				
class Observer implements ObserverInterface
{

				
	public function execute(\Magento\Framework\Event\Observer $observer)
{

		
		$event_name = $observer->getEvent()->getName();
		switch($event_name)
{

			
			%3$s
				
		}
		return $this;
	}}', $this->_vendor, $this->_module, $events);
		
		$this->saveFileData($path, $txt);
	}
	function CreateFrontendEventsObserver()
{

		$path = sprintf('%s/%s/Model/Observer/Frontend/Observer.php', $this->_vendor, $this->_module);
		
		$events = "";
		foreach($this->_config ["observer"] ["frontend"] as $event )
{

			$events .= sprintf('case "%1$s" :{
	
				}
				break;
					', $event);
			$events .= "\n\t\t\t";
		}
		
		$txt = sprintf('<?php
	
namespace %s\%s\Model\Observer\Frontend;
	
use Magento\Framework\Event\ObserverInterface;
	
class Observer implements ObserverInterface
{

	
	public function execute(\Magento\Framework\Event\Observer $observer)
{

	
		$event_name = $observer->getEvent()->getName();
		switch($event_name)
{

		
			%3$s
	
		}
		return $this;
	}}', $this->_vendor, $this->_module, $events);
		
		$this->saveFileData($path, $txt);
	}
	function CreateAdminhtmlEventsObserver()
{

		$path = sprintf('%s/%s/Model/Observer/Adminhtml/Observer.php', $this->_vendor, $this->_module);
		
		$events = "";
		foreach($this->_config ["observer"] ["adminhtml"] as $event )
{

			$events .= sprintf('case "%1$s" :{
	
				}
				break;
					', $event);
			$events .= "\n\t\t\t";
		}
		
		$txt = sprintf('<?php
	
namespace %s\%s\Model\Observer\Frontend;
	
use Magento\Framework\Event\ObserverInterface;
	
class Observer implements ObserverInterface
{

	
	public function execute(\Magento\Framework\Event\Observer $observer)
{

	
		$event_name = $observer->getEvent()->getName();
		switch($event_name)
{

	
			%3$s
	
		}
		return $this;
	}}', $this->_vendor, $this->_module, $events);
		
		$this->saveFileData($path, $txt);
	}
	function CreateObserver()
{

		if (count($this->_config ["observer"] ) == 0)
			return;
		if ($this->_config ["observer"])
{

			$this->CreateFolder(sprintf('%s/%s/Model/Observer', $this->_vendor, $this->_module ));
			$this->CreateGlobalEventsXML();
			$this->CreateGlobalEventsObserver();
		}
		if (isset($this->_config ["observer"] ["frontend"] ))
{

			$this->CreateFolder(sprintf('%s/%s/etc/frontend', $this->_vendor, $this->_module ));
			$this->CreateFolder(sprintf('%s/%s/Model/Observer/Frontend', $this->_vendor, $this->_module ));
			$this->CreateFrontendEventsXML();
			$this->CreateFrontendEventsObserver();
		}
		if (isset($this->_config ["observer"] ["adminhtml"] ))
{

			$this->CreateFolder(sprintf('%s/%s/etc/adminhtml', $this->_vendor, $this->_module ));
			$this->CreateFolder(sprintf('%s/%s/Model/Observer/Adminhtml', $this->_vendor, $this->_module ));
			$this->CreateAdminhtmlEventsXML();
			$this->CreateAdminhtmlEventsObserver();
		}
	}
	function CreateComposerFile()
{

		$path = sprintf('%s/%s/composer.json', $this->_vendor, $this->_module);
		
	
		$txt = sprintf('{
    "name": "%4$s/%5$s",
    "description": "",
    "require":
{
    "magento/project-community-edition": "*",
        "magento/magento-composer-installer": "*"
    },
    "type": "magento2-module",
    "version": "%3$s",
    "extra":
{
    "map": [
            [
                "*",
                "%1$s/%2$s/"
            ]
        ]
    },
    "authors": [
       
{
        "name": "WISAM HAKIM",
            "homepage": "https://www.zexperto.com/",
            "role": "Developer"
        }
    ]}
				
				', $this->_vendor, $this->_module,$this->_version,strtolower($this->_vendor),strtolower($this->_module));
		
		$this->saveFileData($path, $txt);
	}
	function saveFileData($path, $txt){
	    //$txt .= $txt."<\n>";
	    $file = fopen($path, "w" ) or die("Unable to open file!");
	    $txt = str_replace("\r", "", $txt);
	    $txt = str_replace("\t", "    ", $txt);
	    $txt .="\n";
	    fwrite($file, $txt);
	    fclose($file);
	}
	function validateExtension(){
		if (strlen($this->_module ) < 3)
{

			throw new Exception('The module name should me more than three Characters');
		}
	}
	function create()
{

		try
{

			$this->validateExtension();
				
			$this->CreateFolder($this->_vendor);
			$this->CreateFolder($this->_vendor . "/" . $this->_module);
			$this->CreateFolder($this->_vendor . "/" . $this->_module . "/" . "etc");
			
			$this->CreateModuleXmlFile();
			$this->CreateRegistrationFile();
			
			$this->CreateHelper();
			$this->CreateSetup();
			$this->CreateBlock();
			$this->CreateModel();
			$this->CreateController();
			$this->CreateView();
			
			$this->CreateBackEndModels();
			$this->CreateObserver();
			$this->CreateComposerFile();
		} catch (Exception $ex )
{

			echo $ex->getMessage();
		}
	}
	
	function __toString()
{

		return $this->_vendor . " - " . $this->_module . " - " . $this->_version;
	}
}