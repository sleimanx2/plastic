<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Hosts
    |--------------------------------------------------------------------------
    |
    | The most common configuration is telling the client about your cluster: how many nodes, their addresses and ports.
    | If no hosts are specified, the client will attempt to connect to localhost:9200.
    |
    */
    'hosts' => ['127.0.0.1:9200'],

    /*
    |--------------------------------------------------------------------------
    | Reties
    |--------------------------------------------------------------------------
    |
    | By default, the client will retry n times, where n = number of nodes in your cluster.
    | A retry is only performed if the operation results in a "hard" exception: connection refusal, connection timeout, DNS lookup timeout, etc.
    | 4xx and 5xx errors are not considered retry’able events, since the node returns an operational response
    |
    */
    'retires' => 3,

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    |
    | By default elastic index used with all eloquent model
    |
    */
    'index' => 'plastic'

];