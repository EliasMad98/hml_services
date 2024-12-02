<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;



Route::group(['prefix'=>'dashboard','middleware'=>['auth:sanctum', 'ability:employee']],function(){


    Route::post('sendCustomNotification',[DashboardController::class,'sendCustomNotification']);
    Route::post('sendFcmCustomNotification',[DashboardController::class,'sendFcmCustomNotification']);
    Route::get('/download-table', [DashboardController::class, 'downloadTable']);
    Route::post('/updateComplaintPrice',[DashboardController::class,'updateComplaintPrice']);
    Route::get('/getIncomeData',[DashboardController::class,'getIncomeData']);

});

Route::group(['prefix'=>'dashboard','middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){

    Route::post('sendCustomNotification',[DashboardController::class,'sendCustomNotification']);
    Route::post('sendFcmCustomNotification',[DashboardController::class,'sendFcmCustomNotification']);
    Route::post('/updateComplaintPrice',[DashboardController::class,'updateComplaintPrice']);
    Route::get('/dataCounts',[DashboardController::class,'DataCounts']);
    Route::get('/dataCharts',[DashboardController::class,'dataCharts']);

});

