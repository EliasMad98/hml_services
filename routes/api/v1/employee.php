<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\NewPasswordController;


Route::get('create_employee_token', [EmployeeController::class, 'create_employee_token']);


Route::group(['prefix'=>'employee'],function(){


Route::post('createEmployee', [EmployeeController::class, 'createEmployee']);
Route::get('getEmployeeDataByToken', [EmployeeController::class, 'getEmployeeDataByToken']);
Route::get('/',[EmployeeController ::class,'index']);
Route::delete('/delete',[EmployeeController ::class,'destroy']);
Route::put('/update',[EmployeeController ::class,'update']);
Route::get('revealPassword', [EmployeeController::class, 'revealPassword']);

///////////////////////////////////   FORGET PASSWORD   /////////////////////////////
// Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Route::post('/change-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset']);
// Route::post('/forgot-password', [NewPasswordController::class, 'sendPasswordResetLink']);
// Route::post('/change-password', [NewPasswordController::class, 'resetPassword']);
// Route::post('/forgot-password',[NewPasswordController::class, 'forgotEmployeePassword']);
// // Route::post('/password_Verification',  [NewPasswordController::class, 'password_Verification']);
// Route::post('/change-password',  [NewPasswordController::class, 'changeEmployeeePassword']);


});

Route::group(['prefix'=>'employee','middleware'=>['auth:sanctum', 'ability:employee,sub-admin']],function(){


    Route::get('/',[EmployeeController ::class,'index']);
    Route::get('/show',[EmployeeController::class,'show']);
    Route::get('/getworker',[EmployeeController ::class,'getWorker']);
    Route::get('/getworkersNames',[EmployeeController ::class,'getWorkersNames']);
    Route::get('/notifications/all',[EmployeeController ::class,'getAllEmployeeNotifications']);
    Route::get('/notifications/not_readen',[EmployeeController ::class,'getNotReadenEmployeeNotifications']);
    Route::get('/notifications/readall',[EmployeeController ::class,'markEmployeeNotificationsAsRead']);

    ///////////////////////////////////   FORGET PASSWORD   /////////////////////////////
    // Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    // Route::post('/change-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset']);
    // Route::post('/forgot-password', [NewPasswordController::class, 'sendPasswordResetLink']);
    // Route::post('/change-password', [NewPasswordController::class, 'resetPassword']);
    // Route::post('/forgot-password',[NewPasswordController::class, 'forgotEmployeePassword']);
    // // Route::post('/password_Verification',  [NewPasswordController::class, 'password_Verification']);
    // Route::post('/change-password',  [NewPasswordController::class, 'changeEmployeeePassword']);


    });



Route::group(['prefix'=>'employee','middleware'=>['auth:sanctum', 'ability:employee,sub-admin,worker']],function(){

    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
    Route::get('jobs', [EmployeeController::class, 'EmployeeJobs']);
    Route::get('log', [EmployeeController::class, 'EmployeeLog']);
    Route::post('updateFcmToken', [EmployeeController::class, 'updateFcmToken']);

    });
    Route::get('employee/dataCounts',[EmployeeController::class,'DataCounts']);
    Route::post('employee/login', [EmployeeController::class, 'login']);
    Route::get('employee/logout', [EmployeeController::class, 'logout']);
