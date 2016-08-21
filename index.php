<?php 
include_once 'Magento2ModuleCreator.php';
// --------------------------------------------------------------
$config_array = [ 
		"helper" => true,
		"setup" => true,
		"block" => false,
		"controller" => false,
		"model" => false,
		"api" => false,
		"view" => [ 
				"frontend" => false,
				"adminhtml" => false 
		],
		// vernder and module name will add as prefix to table, only id and status will create
		"backend_model" => [ 
				[ 
						"name" => "Pricing",
						"table" => "pricing",
						"columns" =>[
								array(
									"name" =>"sku",
									"label" =>"Item Code",
									"type" => "string",
									"size" =>"20",
									"rquired" =>"true"
								),
								array(
										"name" =>"start_date",
										"label" =>"Start Date",
										"type" => "date",
										'class' => 'validate-date',
										"rquired" =>"true"
								),
								array(
										"name" =>"end_date",
										"label" =>"End Date",
										"type" => "date",
										'class' => 'validate-date',
										"rquired" =>"true"
								),
								array(
										"name" =>"available_qty",
										"label" =>"Available Qty",
										"type" => "decimal",
										"rquired" =>"true"
								),
								array(
										"name" =>"sold_qty",
										"label" =>"Sold Qty",
										"type" => "decimal",
										"rquired" =>"true"
								),array(
										"name" =>"price",
										"label" =>"Price",
										"type" => "decimal",
										"rquired" =>"true"
								)
								
						]
				] 
		] 
];

$mod = new Magento2ModuleCreator ( "ITM", "CFT" );
$mod->setConfig ( $config_array );
echo $mod->create ( $config_array );