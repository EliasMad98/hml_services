<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintController;



Route::group(['prefix'=>'complaint'],function(){
    Route::post('/determinePrice',[ComplaintController ::class,'determinePrice']);
    Route::get('/userComplaints',[ComplaintController ::class,'userComplaints']);
    Route::post('/assignComplaint',[ComplaintController ::class,'assignComplaint']);



    });
    // ,'middleware'=>['auth:sanctum', 'ability:employee,sub-admin']


Route::group(['prefix'=>'complaint'],function(){
    Route::get('/',[ComplaintController ::class,'index']);
    Route::get('/show',[ComplaintController ::class,'show']);
    Route::get('/startComplaint',[ComplaintController ::class,'startComplaint']);
    Route::put('/update',[ComplaintController ::class,'update']);
    Route::put('/updateComplaint',[ComplaintController ::class,'updateComplaint']);
    Route::post('/requestVisit',[ComplaintController ::class,'requestVisit']);
    Route::delete('/delete',[ComplaintController ::class,'destroy']);
    Route::post('/requestRepair',[ComplaintController ::class,'requestRepair']);
    Route::post('/completeComplaintInfo',[ComplaintController ::class,'completeComplaintInfo']);
    Route::get('/complaintAsset',[ComplaintController ::class,'ComplaintAsset']);
    Route::post('/paidCash',[ComplaintController ::class,'paidCash']);



    });
    // ,'middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']
    Route::group(['middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){
     Route::post('/checkout',[ComplaintController ::class,'checkout']);

});
