<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;


Route::get('/call_back',[TransactionController::class,'paymentCallBack']);
Route::get('/error',[TransactionController::class,'paymentError']);
Route::get('/get_transaction_data',[TransactionController::class,'getTransactionData']);

Route::group(['prefix'=>'transactions','middleware'=>['auth:sanctum','ability:employee']],function(){

    Route::get('/',[TransactionController ::class,'index']);


    });
