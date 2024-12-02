<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NonTenantController;



Route::group(['prefix'=>'nonTenants','middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){

    Route::get('/',[NonTenantController ::class,'index']);
    Route::get('/getnNonTenantsNames',[NonTenantController ::class,'getnNonTenantsNames']);
    // Route::get('/show',[TenantController::class,'show']);
    // Route::delete('/delete',[TenantController ::class,'destroy']);
    // Route::post('/',[TenantController ::class,'store']);
    // Route::post('/update',[TenantController ::class,'update']);
    });

