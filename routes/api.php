<?php

use Illuminate\Http\Request;
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

Route::post('login', 'AuthController@login');

Route::get('administrator/file/{file_name}', 'AdministratorController@picture');
Route::get('partner/file/{file_name}', 'PartnerController@picture');
Route::get('engineer/file/{file_name}', 'EngineerController@picture');
Route::get('equipment/file/{file_name}', 'EquipmentController@picture');
Route::get('work_order/file/{id}/{file_name}', 'WorkOrderController@picture');
Route::get('corrective_report/file/{id}/{file_name}', 'CorrectiveReportController@picture');
Route::get('corrective_signature/file/{file_name}', 'CorrectiveReportController@signature');
Route::get('preventive_report/file/{id}/{file_name}', 'PreventiveReportController@picture');
Route::get('preventive_signature/file/{file_name}', 'PreventiveReportController@signature');

Route::group([
    'middleware' => ['auth:api'],
], function() {

    /* AUTH */
    Route::get('logout', 'AuthController@logout');

    /* SETTING */
    Route::get('setting/profile', 'SettingController@profile');
    Route::post('setting/change_password', 'SettingController@change_password');
    Route::get('setting/log', 'SettingController@log');

    /* API RESOURCE */
    Route::apiResource('city', 'CityController')->except([
        'show', 'store', 'update', 'destroy'
    ]);

    Route::apiResource('province', 'ProvinceController')->except([
        'show', 'store', 'update', 'destroy'
    ]);

    Route::apiResource('administrator', 'AdministratorController');
    Route::apiResource('engineer', 'EngineerController');
    Route::apiResource('partner', 'PartnerController');
    Route::apiResource('partner_user', 'PartnerUserController');
    Route::apiResource('category', 'CategoryController');
    Route::apiResource('building', 'BuildingController');
    Route::apiResource('procedure', 'ProcedureController');
    Route::apiResource('equipment', 'EquipmentController');
    Route::apiResource('work_order', 'WorkOrderController');
    
    Route::apiResource('corrective_schedule', 'CorrectiveScheduleController');
    Route::apiResource('preventive_schedule', 'PreventiveScheduleController');

    Route::apiResource('corrective_report', 'CorrectiveReportController');
    Route::put('corrective_report/approve/{id}', 'CorrectiveReportController@approve');

    Route::apiResource('preventive_report', 'PreventiveReportController');
    Route::put('preventive_report/approve/{id}', 'PreventiveReportController@approve');

    Route::get('analytic/performance/{year}', 'AnalyticController@performance');
    Route::get('analytic/today_schedule', 'AnalyticController@today_schedule');

});
