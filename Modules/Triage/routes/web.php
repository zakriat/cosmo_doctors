<?php

use Illuminate\Support\Facades\Route;
use Modules\Triage\Http\Controllers\TriageController;
use Modules\Triage\Http\Controllers\TriageCategoryController;

Route::group(['prefix' => config('app.admin_prefix', 'app'), 'as' => 'backend.', 'middleware' => ['web', 'auth']], function () {

    // ── Triage Queue & Intake ────────────────────────────────────────────────
    // Static routes MUST come before {id} wildcard
    Route::get('triage/index_data', [TriageController::class, 'index_data'])->name('triage.index_data');
    Route::get('triage/get-items', [TriageController::class, 'getItems'])->name('triage.get_items');
    Route::get('triage/appointment-search', [TriageController::class, 'appointmentSearch'])->name('triage.appointment_search');
    Route::post('triage/pre-check', [TriageController::class, 'preCheckStore'])->name('triage.pre_check');

    Route::get('triage', [TriageController::class, 'index'])->name('triage.index');
    Route::post('triage', [TriageController::class, 'store'])->name('triage.store');
    Route::get('triage/{id}', [TriageController::class, 'show'])->name('triage.show');
    Route::put('triage/{id}', [TriageController::class, 'update'])->name('triage.update');
    Route::post('triage/{id}/escalate', [TriageController::class, 'escalate'])->name('triage.escalate');
    Route::post('triage/{id}/close', [TriageController::class, 'close'])->name('triage.close');

    // ── Triage Category Admin ────────────────────────────────────────────────
    Route::get('triage-category/index_data', [TriageCategoryController::class, 'index_data'])->name('triage-category.index_data');
    Route::post('triage-category/bulk-action', [TriageCategoryController::class, 'bulk_action'])->name('triage-category.bulk_action');
    Route::post('triage-category/update-status/{id}', [TriageCategoryController::class, 'update_status'])->name('triage-category.update_status');

    Route::get('triage-category', [TriageCategoryController::class, 'index'])->name('triage-category.index');
    Route::post('triage-category', [TriageCategoryController::class, 'store'])->name('triage-category.store');
    Route::get('triage-category/{id}/edit', [TriageCategoryController::class, 'edit'])->name('triage-category.edit');
    Route::put('triage-category/{id}', [TriageCategoryController::class, 'update'])->name('triage-category.update');
    Route::delete('triage-category/{id}', [TriageCategoryController::class, 'destroy'])->name('triage-category.destroy');
});
