<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintMessageController;



Route::group(['prefix'=>'complaintMessage'],function(){


    Route::delete('/delete',[ComplaintMessageController ::class,'destroy']);
    // });
    // Route::group(['prefix'=>'complaintMessage','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){

        Route::get('/',[ComplaintMessageController ::class,'index']);
        Route::get('/show',[ComplaintMessageController::class,'show']);
        Route::post('/',[ComplaintMessageController ::class,'store']);
        Route::get('/getChatsforUser',[ComplaintMessageController ::class,'getChatsforUser']);
        });
        // ,'middleware'=>['auth:sanctum', 'ability:employee,sub-admin']
