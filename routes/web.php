<?php

use Illuminate\Support\Facades\Route;
use SomeoneFamous\PermissionTree\Http\Controllers\PermissionController;

Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
