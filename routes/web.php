<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DispensingController;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\StaffController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'active'])->group(function () {
    Route::resource('patients', PatientController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::resource('appointments', AppointmentController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::get('consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::get('appointments/{appointment}/consultation/create', [ConsultationController::class, 'create'])->name('consultations.create');
    Route::post('appointments/{appointment}/consultation', [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    Route::get('consultations/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
    Route::put('consultations/{consultation}', [ConsultationController::class, 'update'])->name('consultations.update');

    Route::get('prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('consultations/{consultation}/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('consultations/{consultation}/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');

    Route::get('laboratory', [LabOrderController::class, 'index'])->name('laboratory.index');
    Route::get('consultations/{consultation}/laboratory/create', [LabOrderController::class, 'create'])->name('laboratory.create');
    Route::post('consultations/{consultation}/laboratory', [LabOrderController::class, 'store'])->name('laboratory.store');
    Route::get('laboratory/{order}', [LabOrderController::class, 'show'])->name('laboratory.show');
    Route::get('laboratory/items/{item}/result/create', [LabResultController::class, 'create'])->name('laboratory.result.create');
    Route::post('laboratory/items/{item}/result', [LabResultController::class, 'store'])->name('laboratory.result.store');

    Route::get('pharmacy', [DispensingController::class, 'index'])->name('pharmacy.index');
    Route::get('pharmacy/prescriptions/{prescription}/dispense', [DispensingController::class, 'create'])->name('pharmacy.dispense.create');
    Route::post('pharmacy/prescriptions/{prescription}/dispense', [DispensingController::class, 'store'])->name('pharmacy.dispense.store');
});

Route::middleware(['auth', 'active', 'role:patient'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PatientPortalController::class, 'index'])->name('index');
});

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('facilities', FacilityController::class)->except(['show', 'destroy']);
    Route::post('facilities/{facility}/deactivate', [FacilityController::class, 'deactivate'])->name('facilities.deactivate');

    Route::resource('staff', StaffController::class)->parameters(['staff' => 'staffMember'])->except(['show', 'destroy']);
    Route::post('staff/{staffMember}/deactivate', [StaffController::class, 'deactivate'])->name('staff.deactivate');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
});
