<?php

use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('info', [UserController::class, 'getUserDataByToken']);


require __DIR__ .'/api/v1/address.php';
require __DIR__ .'/api/v1/apartement.php';
require __DIR__ .'/api/v1/building.php';
require __DIR__ .'/api/v1/complaint.php';
require __DIR__ .'/api/v1/complaintAsset.php';
require __DIR__ .'/api/v1/complaintMessage.php';
require __DIR__ .'/api/v1/complaintVisit.php';
require __DIR__ .'/api/v1/conversation.php';
require __DIR__ .'/api/v1/dashboard.php';
require __DIR__ .'/api/v1/job_type.php';
require __DIR__ .'/api/v1/employee.php';
require __DIR__ .'/api/v1/nonTenant.php';
require __DIR__ .'/api/v1/tenant.php';
require __DIR__ .'/api/v1/user.php';
require __DIR__ .'/api/v1/service.php';
require __DIR__ .'/api/v1/transaction.php';


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getContactInfo', [SettingController::class, 'getContactInfo'])->name('getContactInfo');
Route::post('updateContactInfo', [SettingController::class, 'updateContactInfo'])->name('updateContactInfo');

