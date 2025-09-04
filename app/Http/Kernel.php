<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\Authenticate; 
use Tymon\JWTAuth\Http\Middleware\Authenticate as JWTAuthenticate;
use App\Http\Middleware\Cors; // Cors ミドルウェアを明示的に指定

class Kernel extends HttpKernel
{
    /**
     * ルートミドルウェア
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'jwt.auth' => JWTAuthenticate::class,
        'cors' => Cors::class, // Cors ミドルウェアを個別ルートでも利用可能に
    ];

    protected $middlewareGroups = [
        'api' => [
            'cors', // CORS ミドルウェアを API ミドルウェアとしても利用
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'auth:api', // JWT 認証を適用
        ],
    ];
    
    protected $middleware = [
        \App\Http\Middleware\Cors::class, // グローバルミドルウェアとしての CORS 対応
    ];
}
