<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;

Route::group(['prefix'=>'services','middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){

    Route::delete('/delete',[ServiceController ::class,'destroy']);
    Route::post('/',[ServiceController ::class,'store']);
    Route::post('/update',[ServiceController ::class,'update']);
    });


Route::group(['prefix'=>'services','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){

    Route::get('/',[ServiceController ::class,'index']);
    Route::get('/show',[ServiceController::class,'show']);
    });
