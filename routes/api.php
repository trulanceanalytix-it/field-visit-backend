<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\FieldVisitController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\Api\CompetitorBrandController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\Api\FieldVisitSodController;


Route::post('/login', [AuthController::class, 'login']);

// PUBLIC � no token
Route::get('/employee/name/{emp_id}', [AuthController::class, 'employeeName']);
Route::get('/app-version', [AppController::class, 'version']);


// PROTECTED � token required
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/field-visit/history', [FieldVisitController::class, 'historyApi']);
	Route::get('/outlet/history', [FieldVisitController::class, 'outletHistory']);
    Route::get('/beats-by-emp/{empId}', [FieldVisitController::class, 'getBeatOutletByEmp']);
    Route::get('/five/create', [FieldVisitController::class, 'apiCreate']);
    Route::post('/field-visit/store', [FieldVisitController::class, 'store']);
 // New routes for competitor brands (index + store)
    Route::apiResource('competitor-brands', CompetitorBrandController::class)->only([
        'index',   // GET /api/competitor-brands ? list grouped by category
        'store',   // POST /api/competitor-brands ? add new brand from app
    ]);
    Route::post('mobile/outlets', [OutletController::class, 'storeMobile']);
    Route::get('/admin/field-visits/{emp_id}', [FieldVisitController::class, 'adminVisitsByDate']);
    Route::get('/admin/employee-visit-map/{empId}', [FieldVisitController::class, 'employeeVisitMap']);
    Route::get('/admin/dashboard/{empId}', [AdminDashboardController::class, 'dashboard']);
    Route::get('/admin/employee-beats/{empId}', [AdminDashboardController::class, 'employeeBeats']);
    Route::post('/field-visit/sod', [FieldVisitSodController::class, 'store']);
    Route::get(
    '/field-visit/check-sod',
    [FieldVisitSodController::class, 'checkSod']
);
});
