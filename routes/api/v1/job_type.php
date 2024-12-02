<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobTypeController;


Route::group(['prefix'=>'job_type'],function(){

    Route::get('/',[JobTypeController ::class,'index']);
    Route::get('/show',[JobTypeController::class,'show']);
    Route::delete('/delete',[JobTypeController ::class,'destroy']);
    Route::post('/',[JobTypeController ::class,'store']);
    Route::put('/update',[JobTypeController ::class,'update']);
    Route::get('/getEmployeesforJob',[JobTypeController ::class,'getEmployeesForJob']);
    Route::get('/getJobTypeNames',[JobTypeController ::class,'getJobTypeNames']);

    });
