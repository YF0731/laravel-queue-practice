<?php

use App\Http\Controllers\DynamoDbController;
use App\Http\Controllers\JobDispatchController;
use App\Http\Controllers\SqsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/send', [SqsController::class, 'send']);
Route::get('/receive', [SqsController::class, 'receive']);

Route::get('/dynamo', [DynamoDbController::class, 'test']);

Route::get('/worker', [JobDispatchController::class, 'send']);

Route::get('/test', [TestController::class, 'test']);
