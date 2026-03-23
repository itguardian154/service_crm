<?php

return [
    'base_url' => env('API_WHATSAPP_URL', ''),
    'api_key'  => env('API_WHATSAPP_KEY', ''),
    'endpoint' => [
        'notifications' => '/notifications',
    ],
    'default' => [
        'master_module_id' => 6,
        'master_menu_id'   => 10,
        'title'            => 'Membership Notification',
        'delay'            => 0,
    ],
    'assets' => [
        'syarat_ketentuan' => 'https://servicecrm.salokapark.app/img/membership-template-tc.png',
    ],
];