<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// -----------------------------------------------
// 求人登録（掲載主向け）
// -----------------------------------------------
Route::get('/jobs/create', [\App\Http\Controllers\JobController::class, 'create'])->name('jobs.create');
Route::post('/jobs', [\App\Http\Controllers\JobController::class, 'store'])->name('jobs.store');
Route::get('/jobs/{token}/complete', [\App\Http\Controllers\JobController::class, 'complete'])->name('jobs.complete');
Route::get('/jobs/{token}/edit', [\App\Http\Controllers\JobController::class, 'edit'])->name('jobs.edit');
Route::put('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'update'])->name('jobs.update');
