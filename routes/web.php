<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\StudyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StudyController::class, 'index'])->name('home');
Route::get('/index.php', [StudyController::class, 'index']);
Route::get('/view.php', [StudyController::class, 'view'])->name('studies.view');
Route::post('/export_excel.php', [StudyController::class, 'exportExcel']);

Route::match(['get', 'post'], '/reserv.php', [AppointmentController::class, 'index'])->name('appointments.index');
Route::get('/new_appoint.php', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('/new_appoint.php', [AppointmentController::class, 'store']);
Route::get('/edit_appointment.php', [AppointmentController::class, 'edit'])->name('appointments.edit');
Route::post('/edit_appointment.php', [AppointmentController::class, 'update']);
Route::get('/export_excel2.php', [AppointmentController::class, 'exportExcel']);
Route::get('/patient_history.php', [AppointmentController::class, 'history'])->name('appointments.history');
