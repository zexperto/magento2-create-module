<?php

 /**
  * User Module Creator Class
  * PHP version 5.6 and later
  *
  * @category  PHP_Script
  * @package   ZEO
  * @author    wisam hakim <zexperto@hotmail.com>
  * @copyright 2016 ZEO
  * @license   http://framework.zend.com/license/new-bsd New BSD License
  * @link      [//www.zexperto.com/magento2x/magento2-module-creator-script] [Description in the URL]
  */

require_once 'Magento2ModuleCreatorID.php';

// --------------------------------------------------------------
$config_array = [
    "primary_key" =>"entity_id",
    "observer" => [
        "global" => [
            "sales_order_place_after","sales_order_place_after"
        ],
        "frontend" => [
            "catalog_product_load_after"
        ],
        "adminhtml" => []
    ],
    "attributes" => [
        "customer" => [
            [
                "code"=>"zeo_test1",
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Test1',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => 'GLOBAL',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ],
            [
                "code"=>"zeo_test2",
                'type' => 'varchar',
                'label' => 'Test2',
                'input' => 'text',
            ],
        ], // processing
        "product" => [],  // processing
        "category" => []  // processing
    ],
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
            "columns" => [
                [
                    "name" => "full_name",
                    "label" => "Full Name",
                    "type" => "string",
                    "size" => "64",
                    "rquired" => "true"
                ],
                [
                    "name" => "age",
                    "label" => "Age",
                    "type" => "int",
                    "rquired" => "true"
                ],
                [
                    "name" => "birth_date",
                    "label" => "Birth Date",
                    "type" => "date",
                    'class' => 'validate-date',
                    "rquired" => "true"
                ],
                [
                    "name" => "balance",
                    "label" => "Balance",
                    "type" => "decimal",
                    "rquired" => "true"
                ]
            ]
        ]
    ]
];

$mod = new Magento2ModuleCreator("ZEO", "Whh");
$mod->setConfig($config_array);
$mod->create($config_array);
