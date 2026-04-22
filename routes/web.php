<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FieldVisitController;
use App\Http\Controllers\BeatOutletController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\BeatController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\EmployeeBeatOutletMapController;
use App\Http\Controllers\DailyVisitController;


use App\Models\Employee;

// Route::get('/', function () {
//     return view('welcome');
// });
/*
|--------------------------------------------------------------------------
| ROOT REDIRECT
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');
/* =========================
   AUTH ROUTES
   ========================= */

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::get('/employee/name/{emp_id}', function ($emp_id) {
    return Employee::where('emp_id', $emp_id)->value('emp_name');
});
// Route::get('/five', [FieldVisitController::class, 'create']);
// Route::post('/field-visit-entry', [FieldVisitController::class, 'store'])
//     ->name('field-visit.store');

// // Preview page
// Route::post('/field-visit/preview', [FieldVisitController::class, 'preview'])
//     ->name('field-visit.preview');

// // Final submit 
// Route::post('/field-visit/confirm', [FieldVisitController::class, 'confirm'])
//     ->name('field-visit.confirm');
// Route::get('/beats-by-emp/{empId}', [FieldVisitController::class, 'getBeatOutletByEmp']);
// Route::get('/field-visit/edit', [FieldVisitController::class, 'edit'])
//     ->name('field-visit.edit');

// // Beat Outlet Store 

// Route::post('/beat-outlet/store', [BeatOutletController::class, 'store'])
//     ->name('beat-outlet.store');

Route::middleware(['auth'])->group(function () {

    Route::get('/five', [FieldVisitController::class, 'create']);

    Route::post('/field-visit-entry', [FieldVisitController::class, 'store'])
        ->name('field-visit.store');

    Route::post('/field-visit/preview', [FieldVisitController::class, 'preview'])
        ->name('field-visit.preview');

    Route::post('/field-visit/confirm', [FieldVisitController::class, 'confirm'])
        ->name('field-visit.confirm');

    Route::get('/beats-by-emp/{empId}', [FieldVisitController::class, 'getBeatOutletByEmp']);

    Route::get('/field-visit/edit', [FieldVisitController::class, 'edit'])
        ->name('field-visit.edit');


    // Beat Outlet Store
    Route::post('/beat-outlet/store', [BeatOutletController::class, 'store'])
        ->name('beat-outlet.store');
    Route::post('/beat-outlet/import', [BeatOutletController::class, 'importBeatOutlet'])
        ->name('beat-outlet.import');
    Route::post('/distributors/import', [DistributorController::class, 'import'])
        ->name('distributors.import');
    Route::get('/field-visit/history', [FieldVisitController::class, 'history'])
        ->name('field-visit.history');
    Route::get('/field-visit/history/export', [FieldVisitController::class, 'exportExcel'])
        ->name('field-visit.history.export');
    Route::get('/field-visit/preview-pdf', [FieldVisitController::class, 'previewPdf'])->name('field-visit.preview.pdf');
    Route::get('/field-visit/map', [FieldVisitController::class, 'map'])
        ->name('field-visit.map');
    Route::get('/outlet/history', [FieldVisitController::class, 'outletHistory'])->name('outlet.history');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->name('dashboard');

    Route::get('beats/data', [BeatController::class, 'data'])
        ->name('beats.data');
    Route::get('/outlets/data', [OutletController::class, 'data'])->name('outlets.data');
    Route::get('employees/data', [EmployeeController::class, 'data'])
        ->name('employees.data');
    Route::get(
        'employee-maps/data',
        [EmployeeBeatOutletMapController::class, 'data']
    )->name('employee-maps.data');

	Route::get('/admin/daily-visits/export', [DailyVisitController::class, 'export'])
        ->name('daily-visits.export');

    // CRUD routes with route names like admin.distributors.index
    Route::resource('distributors', DistributorController::class);
    Route::resource('beats', BeatController::class);
    Route::resource('outlets', OutletController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('employee-maps', EmployeeBeatOutletMapController::class);
    Route::resource('daily-visits', DailyVisitController::class);
Route::post('daily-visit/send-report', [DailyVisitController::class, 'sendReport'])
        ->name('daily-visit.send-report');
Route::get('/visit-map', [FieldVisitController::class, 'visitMapPage'])
        ->name('visit-map.page');
    Route::get('/employee-visit-map-web/{empId}', [FieldVisitController::class, 'employeeVisitMapWeb']);
});
