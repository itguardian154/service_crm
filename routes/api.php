<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// declare Controller
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\UserClientController;
use App\Http\Controllers\UserMemberController;
use App\Http\Controllers\AttendanceRecord;
use App\Http\Controllers\UserMemberSalesController;

use App\Http\Controllers\Location;


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

// USER ADMIN API
// Controller UserAdminController
Route::controller(UserAdminController::class)->group(function () {
    Route::post('register_admin', 'register'); // ok
    Route::post('login_admin', 'login'); // ok
    Route::get('get_all_admin', 'getAllData'); // ok
    Route::post('get_byId_admin', 'getByIDData'); // ok
    route::post('disable_byId','disableByIdData'); // ok
    route::post('enable_byId','enableByIdData'); // ok
    route::post('remove_byId','removeById'); // ok
    route::post('reset_password','resetPassword'); // ok
    route::post('edit_profile','editProfile'); // ok
});

// USER CLINET API
// Controller UserClientController
Route::controller(UserClientController::class)->group(function () {
    // Marketing & Sales
    Route::post('register_client', 'register'); // ok
    
    Route::post('login_client', 'login'); // ok
    Route::get('get_all_Client', 'getAllData'); // ok
    Route::post('get_profile_client', 'getProfile'); // ok
    route::post('disable_client_byId','disableClient'); // ok
    route::post('enable_client_byId','enableClient'); // ok 
    route::post('remove_client_byId','removeClient'); // ok
    route::post('reset_password_client','resetPassword'); // ok
    route::post('edit_profile_client','editProfile'); // ok

    // export
    Route::get('export_user_client', 'exportUserClient'); // ok
});

// Controller UserClientController
Route::controller(UserMemberController::class)->group(function () {
    Route::post('register_member', 'register'); // ok
    Route::get('get_count_member_today', 'getCount'); // ok
    Route::get('get_count_attendance_record', 'getCountAttendanceRecord'); // ok
    Route::get('get_all_member', 'getAllDataMember'); // ok
    Route::get('get_type_member', 'getTypeMember'); // ok
    Route::post('re_member', 'reMember'); 

    // Email
    route::post('send_mail_eMember','sentEMemberEmail'); // ok
    // Whatsapp
    route::post('send_whatsapp_eMember','sentEMemberWhastapp'); // ok

    // export
    Route::get('export_member', 'exportMember'); 
});

// Controller UserClientController
Route::controller(UserMemberSalesController::class)->group(function () {
    Route::post('register_member_sales', 'register');
});

// Controller AttendanceRecord
Route::controller(AttendanceRecord::class)->group(function () {
    Route::post('reedim_member', 'reedimMember'); // ok
    Route::post('get_all_attendance_record', 'getAllAttendanceRecord'); // ok

    // export
    Route::get('export_attendance_record', 'exportAttendanceRecord'); 
});

// Controller Location
Route::controller(Location::class)->group(function () {
    Route::get('get_provinces', 'getProvinces'); // ok
    Route::get('get_cities', 'getCities'); // ok
});

