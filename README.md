# magento2-create-module
How To use this file
1. Include the Class File
2. This is an example of how to use this class

// --------------------------------------------------------------
$config_array = [ <br/>
		"helper" => true,
		"setup" => true,
		"block" => false,
		"controller" => false,
		"model" => false,
		"api" => false,
		"view" => [ 
				"frontend" => false,
				"adminhtml" => true 
		],
		// vernder and module name will add as prefix to table, only id and status will create
		"backend_model" => [ 
				[ 
						"name" => "Contact",
						"table" => "Contct",
						"columns" =>[
								array(
									"name" =>"first_name",
									"label" =>"First Name",
									"type" => "string",
									"size" =>"50",
									"rquired" =>"true"
								),
								array(
										"name" =>"birthday",
										"label" =>"Birthday",
										"type" => "date",
										'class' => 'validate-date',
										"rquired" =>"true"
								),
								array(
										"name" =>"age",
										"label" =>"Age",
										"type" => "decimal",
										"rquired" =>"true"
								)
								
						]
				] 
		] 
];

$mod = new Magento2Module ( "ZEO", "File" );
$mod->setConfig ( $config_array );
echo $mod->create ( $config_array );