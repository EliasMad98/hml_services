<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;


Route::group(['prefix'=>'buildings','middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){
    Route::delete('/delete',[BuildingController ::class,'destroy']);
    Route::post('/',[BuildingController ::class,'store']);
    Route::put('/update',[BuildingController ::class,'update']);
    Route::get('/getBuildingsName',[BuildingController ::class,'getBuildingsName']);

    });


Route::group(['prefix'=>'buildings','middleware'=> ['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){
    Route::get('/',[BuildingController ::class,'index']);
    Route::get('/show',[BuildingController::class,'show']);

    });
