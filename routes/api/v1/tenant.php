<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;




Route::group(['prefix'=>'tenants','middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){


    Route::delete('/delete',[TenantController ::class,'destroy']);
    Route::post('/',[TenantController ::class,'store']);
    Route::post('/update',[TenantController ::class,'update']);
    Route::get('/getTenantsNames',[TenantController ::class,'getTenantsNames']);
    Route::get('/',[TenantController ::class,'index']);
    Route::get('/show',[TenantController::class,'show']);


    });


