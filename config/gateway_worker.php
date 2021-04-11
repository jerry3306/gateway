<?php
return [

    'protocols' => [
        'websocket' => [
            'gateway_worker' => [
                'options' => [
                    'collection' => [
                        'ip' => '0.0.0.0',
                        'port' => 8282
                    ],
                    'name' => 'ws_gateway',
                    'count' => 4,
                    'lanIp' => '127.0.0.1',
                    'startPort' => 2900,
                    'pingInterval' => 10,
                    'pingData' => '{"type" : "ping"}'
                ]
            ],
            'register_address' => [
                'options' => [
                    'protocols' => 'text',
                    'port' => 1238
                ]
            ],
            'business_worker' => [
                'options' => [
                    'name' => 'ws_worker',
                    'count' => 4,
                    'eventHandler'=> 'lib\\gateway\\events\\WebSocket',
                ]
            ]
        ],

        'http' => [
            'gateway_worker' => [
                'options' => [
                    'collection' => [
                        'ip' => '0.0.0.0',
                        'port' => 8283
                    ],
                    'name' => 'http_gateway',
                    'count' => 4,
                    'lanIp' => '127.0.0.1',
                    'startPort' => 3000,
                    'pingInterval' => 10,
                    'pingData' => '{"type" : "ping"}'
                ]
            ],
            'register_address' => [
                'options' => [
                    'protocols' => 'text',
                    'port' => 1239
                ]
            ],
            'business_worker' => [
                'options' => [
                    'name' => 'http_worker',
                    'count' => 4,
                    'eventHandler'=> 'lib\\gateway\\events\\Http',
                ]
            ]
        ]
    ]



];