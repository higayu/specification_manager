<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login'], // 'login' を追加
    'allowed_methods' => ['*'], // すべてのメソッドを許可
    'allowed_origins' => [
        'http://localhost:3000', // 追加
        'http://192.168.1.8:3000',
        'http://192.168.1.40:3000', // 追加
        'http://192.168.1.140:3000', // 追加
        'http://192.168.1.160:3000', // 追加
        'http://192.168.1.67:3000', // 追加
        'http://192.168.1.68:3000', // 追加
        'http://192.168.3.33:3000', // 追加
        'http://192.168.3.56:3000',// 追加
        'https://192.168.1.67:3000', // 追加
        'https://shotaki.we-labo.com', // 追加
        'https://shotaki.we-labo.com/public', // 追加
        'http://192.168.1.49:3000', // 追加
        'http://192.168.1.145:3000', // 追加
        'http://192.168.1.146:3000', // 追加
        'http://192.168.1.147:3000', // 追加
        'https://shotaki.we-labo.com', // 追加
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // すべてのヘッダーを許可
    'exposed_headers' => ['Authorization'], // クライアントが `Authorization` を読めるようにする
    'max_age' => 0,
    'supports_credentials' => true, // 認証情報 (Cookie, Authorization ヘッダー) を許可
];



