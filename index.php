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

require_once 'Magento2ModuleCreator.php';

// --------------------------------------------------------------
$config_array = [
    "observer" => [
        "global" => [
            "sales_order_place_after"
        ],
        "frontend" => [
            "catalog_product_load_after"
        ],
        "adminhtml" => []
    ],
    "attributes" => [
        "customer" => [], // processing
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

$mod = new Magento2ModuleCreator("ZEO", "WHH");
$mod->setConfig($config_array);
echo $mod->create($config_array);