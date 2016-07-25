# magento2-create-module
How To use this file
1. Include the Class File
2. This is an example of how to use this class

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

$mod = new Magento2Module ( "ZEO", "File" );
$mod->setConfig ( $config_array );
echo $mod->create ( $config_array );