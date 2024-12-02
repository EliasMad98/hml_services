<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintVisitController;





Route::group(['prefix'=>'complaintVisit','middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){

    Route::get('/',[ComplaintVisitController ::class,'index']);
    Route::get('/show',[ComplaintVisitController::class,'show']);
    Route::delete('/delete',[ComplaintVisitController ::class,'destroy']);
    Route::post('/assignVisit',[ComplaintVisitController ::class,'assignVisitToEmployee']);
    });


Route::group(['prefix'=>'complaintVisit','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){

    Route::get('/',[ComplaintVisitController ::class,'index']);
    Route::get('/show',[ComplaintVisitController::class,'show']);
    Route::post('/store',[ComplaintVisitController ::class,'store']);
    });
