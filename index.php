<?php 
include_once 'Magento2ModuleCreator.php';
// --------------------------------------------------------------
$config_array = [ 
		"helper" => true,
		"setup" => true,
		"block" => false,
		"controller" => false,
		"model" => false,
		"view" => [ 
				"frontend" => false,
				"adminhtml" => false 
		],
		// vernder and module name will add as prefix to table, only id and status will create
		"backend_model" => [ 
				[ 
						"api" => true,
						"name" => "Contacts",
						"table" => "contacts",
						"columns" =>[
								array(
									"name" =>"full_name",
									"label" =>"Full Name",
									"type" => "string",
									"size" =>"64",
									"rquired" =>"true"
								),
								array(
										"name" =>"age",
										"label" =>"Age",
										"type" => "int",
										"rquired" =>"true"
								),
								array(
										"name" =>"birth_date",
										"label" =>"Birth Date",
										"type" => "date",
										'class' => 'validate-date',
										"rquired" =>"true"
								),
								array(
										"name" =>"balance",
										"label" =>"Balance",
										"type" => "decimal",
										"rquired" =>"true"
								)								
						]
				] 
		] 
];

$mod = new Magento2ModuleCreator ( "ITM", "WHH" );
$mod->setConfig ( $config_array );
echo $mod->create ( $config_array );