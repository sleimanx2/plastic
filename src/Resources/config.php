<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    |
    | The default elastic index used with all eloquent model
    |
    */
    'index' => 'plastic',

    /*
     * Connection settings
     */
    'connection'     => [

        /*
        |--------------------------------------------------------------------------
        | Hosts
        |--------------------------------------------------------------------------
        |
        | The most common configuration is telling the client about your cluster: how many nodes, their addresses and ports.
        | If no hosts are specified, the client will attempt to connect to localhost:9200.
        |
        */
        'hosts'   => ['127.0.0.1:9200'],

        /*
        |--------------------------------------------------------------------------
        | Reties
        |--------------------------------------------------------------------------
        |
        | By default, the client will retry n times, where n = number of nodes in your cluster.
        | A retry is only performed if the operation results in a "hard" exception.
        |
        */
        'retries' => 3,

        /*
        |------------------------------------------------------------------
        | Logging
        |------------------------------------------------------------------
        |
        | Logging is disabled by default for performance reasons. The recommended logger is Monolog (used by Laravel),
        | but any logger that implements the PSR/Log interface will work.
        |
        | @more https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_configuration.html#enabling_logger
        |
        */
        'logging' => [
            'enabled' => false,
            'path'    => storage_path('logs/plastic.log'),
            'level'   => 200,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mapping repository table
    |--------------------------------------------------------------------------
    |
    | The sql table to store the mappings logs
    |
    */
    'mappings'       => 'mappings',

];
