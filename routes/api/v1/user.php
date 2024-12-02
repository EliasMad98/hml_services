<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\NewPasswordController;



///////////////////////////////////    Authintication Users   /////////////////////////////

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::post('updateFcmToken', [UserController::class, 'updateFcmToken']);

Route::group(['middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){
    Route::get('revealPassword', [UserController::class, 'revealPassword']);
    Route::post('createUser', [UserController::class, 'CreateUser']);
    Route::get('create_user_token', [UserController::class, 'create_user_token']);
    Route::get('/users/all',[UserController::class,'getAllUsers']);


    });

    Route::group(['middleware'=>['auth:sanctum', 'ability:employee,sub-admin,tenant,non_tenant']],function(){

        Route::put('update', [UserController::class, 'update']);
        Route::put('updatepassword', [UserController::class, 'updatepassword']);
        Route::put('updateemail', [UserController::class, 'updateemail']);
        Route::put('updatename', [UserController::class, 'updatename']);
        Route::put('updatephone', [UserController::class, 'updatephone']);
        Route::get('logout', [UserController::class, 'logout']);
        Route::delete('users/delete', [UserController::class, 'deleteUser']);






        });


///////////////////////////////////   VERIFICATION EMAIL   /////////////////////////////

Route::post('email_Verification', [VerifyEmailController::class, 'email_verification']);
Route::post('reSendEmailVerification', [VerifyEmailController::class, 'reSendEmailVerification']);


///////////////////////////////////   FORGET PASSWORD   /////////////////////////////

Route::post('/forgot-password',[NewPasswordController::class, 'forgotPassword']);
// Route::get('/reset-password',  [NewPasswordController::class, 'reset']);
Route::post('/password_Verification',  [NewPasswordController::class, 'password_Verification']);
Route::post('/change-password',  [NewPasswordController::class, 'change']);

