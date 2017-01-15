<?php

return [
    'package' => [
        'module' => 'GoShippo',
        'version' => '1.0',
    ],
    'routes' => [
        'static' => [
            'admin/orders/fulfillment/goshippo' => 'Modules\\GoShippo\\Pages\\Label',
        ]
    ],
    'modules' => [
        'checkout' => [
            'fulfillment_handlers' => [
                'goshippo' => 'Modules\\GoShippo\\Connector\\Checkout',
            ]
        ]
    ]
];
