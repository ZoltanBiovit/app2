<?php
return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'install' => [
        'date' => 'Sat, 20 Mar 2021 18:21:56 +0000'
    ],
    'crypt' => [
        'key' => 'AxSa1CIzaFwgxWaqGbPHNXmYFPLhwvIM'
    ],
    'session' => [
        'save' => 'files'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'akfxmyajgk',
                'username' => 'akfxmyajgk',
                'password' => 'ZkvFprsg92',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1'
            ]
        ]
    ],
    'queue' => [
        'amqp' => [
            'host' => 'amqps://evqtmqtw:3cPS_sHa4LSpqC1ZS7nw_7TXpwNPIhWt@cow.rmq2.cloudamqp.com/evqtmqtw',
            'port' => 5672,
            'user' => 'evqtmqtw',
            'password' => '3cPS_sHa4LSpqC1ZS7nw_7TXpwNPIhWt',
            'virtualhost' => '/'
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'translate' => 1,
        'config_webservice' => 1,
        'compiled_config' => 1,
        'customer_notification' => 1,
        'vertex' => 1
    ]
];
