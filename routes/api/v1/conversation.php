<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;

Route::group(['prefix'=>'conversation','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,worker']],function(){

    Route::get('/',[ConversationController::class,'index']);

    Route::get('/show',[ConversationController::class,'show']);

    Route::post('/store',[ConversationController::class,'store']);

    Route::post('/',[MessageController::class,'store']);


});



