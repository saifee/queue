<?php

use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;



Route::get('/install', [InstallerController::class, 'index'])->name('install');
Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');
Route::get('/permissions', [InstallerController::class, 'permissions'])->name('permissions');
Route::get('/environment', [InstallerController::class, 'environmentWizard'])->name('environment');
Route::post('/test-connection', [InstallerController::class, 'testDBConnection'])->name('test-connection');
Route::post('/environment/save', [InstallerController::class, 'saveWizard'])->name('set-env');
