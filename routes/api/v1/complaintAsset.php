<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintAssetController;



Route::group(['prefix'=>'complaintAsset','middleware'=>['auth:sanctum', 'ability:worker']],function(){


    Route::post('/uploadBefor',[ComplaintAssetController ::class,'UploadBefor']);
    Route::post('/uploadAfter',[ComplaintAssetController ::class,'UploadAfter']);
    Route::get('/complaintGallery',[ComplaintAssetController ::class,'ComplaintGallery']);

    });
