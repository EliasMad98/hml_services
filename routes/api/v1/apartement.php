<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApartmentController;



Route::group(['prefix'=>'apartments','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant']],function(){

    Route::get('/user',[ApartmentController ::class,'getUserApartments']);
    Route::delete('/delete',[ApartmentController ::class,'destroy']);
    Route::post('/',[ApartmentController ::class,'store']);
    Route::post('/update',[ApartmentController ::class,'update']);
    Route::get('/getApartementByBuildingId',[ApartmentController ::class,'getApartementByBuildingId']);

    });


Route::group(['prefix'=>'apartments','middleware'=> ['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){

    Route::get('/',[ApartmentController ::class,'index']);
    Route::get('/show',[ApartmentController::class,'show']);

    });
