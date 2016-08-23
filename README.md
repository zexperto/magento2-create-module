# magento2-create-module
How To use this file
1. Include the Class File
2. This is an example of how to use this class

// --------------------------------------------------------------
<br/>$config_array = [ <br/>
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
						"table" => "Contct",<br/>
						"columns" =>[<br/>
								array(<br/>
									"name" =>"first_name",<br/>
									"label" =>"First Name",<br/>
									"type" => "string",<br/>
									"size" =>"50",<br/>
									"rquired" =>"true"<br/>
								),<br/>
								array(<br/>
										"name" =>"birthday",<br/>
										"label" =>"Birthday",<br/>
										"type" => "date",<br/>
										'class' => 'validate-date',<br/>
										"rquired" =>"true"<br/>
								),<br/>
								array(<br/>
										"name" =>"age",<br/>
										"label" =>"Age",<br/>
										"type" => "decimal",<br/>
										"rquired" =>"true"<br/>
								)<br/>
						]<br/>
				] <br/>
		] <br/>
];<br/>

$mod = new Magento2Module ( "ZEO", "File" );<br/>
$mod->setConfig ( $config_array );<br/>
echo $mod->create ( $config_array );<br/>