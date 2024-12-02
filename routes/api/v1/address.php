<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;



Route::group(['prefix'=>'addresses','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){


    Route::get('/user',[AddressController ::class,'getUserAddresses']);
    Route::delete('/delete',[AddressController ::class,'destroy']);
    Route::post('/',[AddressController ::class,'store']);
    Route::post('/update',[AddressController ::class,'update']);
    // Route::get('/non_tenant',[AddressController ::class,'getNon_TenantAddresses']);

    });

    Route::group(['prefix'=>'addresses','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant,worker']],function(){

        Route::get('/',[AddressController ::class,'index']);
        Route::get('/show',[AddressController::class,'show']);

        });