<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;
use App\Methods\UserList;
use App\Http\Controllers\SlackController;

// ✅ 認証用ルート
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ✅ 認証が必要なルート
Route::middleware('auth:api')->group(function () { // JWT 用


});

// Slack関連のエンドポイント
Route::post('/slack/events', [SlackController::class, 'handleEvent']);
Route::post('/slack/commands', [SlackController::class, 'handleCommand']);
Route::post('/slack/interactions', [SlackController::class, 'handleInteraction']);