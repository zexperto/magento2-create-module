# magento2-create-module
How To use this file
1. Include the Class File
2. This is an example of how to use this class

// --------------------------------------------------------------
<br/>$config_array = [ <br/>
		"observer" => array(<br/>
				"global"=>array("sales_order_place_after"),<br/>
				"frontend"=>array("catalog_product_load_after"),<br/>
				"adminhtml"=>array(),<br/>
				),<br/>
		"helper" => true,<br/>
		"setup" => true,<br/>
		"block" => false,<br/>
		"controller" => false,<br/>
		"model" => false,<br/>
		"view" => [ <br/>
				"frontend" => false,<br/>
				"adminhtml" => true <br/>
		],<br/>
		// vernder and module name will add as prefix to table, only id and status will create<br/>
		"backend_model" => [ <br/>
				[<br/> 
						"api" => true,<br/>
						"name" => "Contact",<br/>
						"table" => "contacts",<br/>
						"columns" =>[<br/>
								array(<br/>
									"name" =>"full_name",<br/>
									"label" =>"Full Name",<br/>
									"type" => "string",<br/>
									"size" =>"64",<br/>
									"rquired" =>"true"<br/>
								),<br/>
								array(<br/>
										"name" =>"age",<br/>
										"label" =>"Age",<br/>
										"type" => "int",<br/>
										"rquired" =>"true"<br/>
								),<br/>
								array(<br/>
										"name" =>"birth_date",<br/>
										"label" =>"Birth Date",<br/>
										"type" => "date",<br/>
										'class' => 'validate-date',<br/>
										"rquired" =>"true"<br/>
								),<br/>
								array(<br/>
										"name" =>"balance",<br/>
										"label" =>"Balance",<br/>
										"type" => "decimal",<br/>
										"rquired" =>"true"<br/>
								)<br/>								
						]<br/>
				] <br/>
		] <br/>
];<br/>

$mod = new Magento2Module ( "ZEO", "WHH" );<br/>
$mod->setConfig ( $config_array );<br/>
echo $mod->create ( $config_array );<br/>