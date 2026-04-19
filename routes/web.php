<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// -----------------------------------------------
// 求人管理（掲載主向け）
// -----------------------------------------------
Route::get('/jobs/create', [\App\Http\Controllers\JobController::class, 'create'])->name('jobs.create');
Route::post('/jobs', [\App\Http\Controllers\JobController::class, 'store'])->name('jobs.store');
Route::get('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'manage'])->name('jobs.manage');
Route::put('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'update'])->name('jobs.update');
Route::patch('/jobs/{token}/close', [\App\Http\Controllers\JobController::class, 'close'])->name('jobs.close');
Route::patch('/jobs/{token}/reopen', [\App\Http\Controllers\JobController::class, 'reopen'])->name('jobs.reopen');
Route::delete('/jobs/{token}', [\App\Http\Controllers\JobController::class, 'destroy'])->name('jobs.destroy');

// -----------------------------------------------
// 求人LP（求職者向け）
// -----------------------------------------------
Route::get('/lp/{token}', [\App\Http\Controllers\LpController::class, 'show'])->name('lp.show');
