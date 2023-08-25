<?php

use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::group(['namespace' => 'Api\Auth'], function () {
    Route::post('/register',[UserAuthController::class,'register']);
    Route::post('/login', [UserAuthController::class,'login']);
    Route::post('/logout', [UserAuthController::class,'logout'])->middleware('auth:api');
});


