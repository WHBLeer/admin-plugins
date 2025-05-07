<?php

use Illuminate\Support\Facades\Route;
use Sanlilin\AdminPlugins\Controllers\{
	PluginController,
	PluginSettingsController
};
Route::middleware(['web'])->prefix('admin')->as('admin.')->group(function () {
	Route::middleware(['auth:admin'])->prefix('plugins')->as('plugins.')->group(function () {
		Route::get('/', [PluginController::class, 'index'])->name('index');
		Route::get('/{plugin}/download', [PluginController::class, 'download'])->name('download');
		Route::post('/upload', [PluginController::class, 'upload'])->name('upload');
		Route::post('/{plugin}/install', [PluginController::class, 'install'])->name('install');
		Route::post('/{plugin}/uninstall', [PluginController::class, 'uninstall'])->name('uninstall');
		Route::post('/{plugin}/restart', [PluginController::class, 'restart'])->name('restart');
		Route::delete('/{plugin}/delete', [PluginController::class, 'delete'])->name('delete');

		Route::get('{plugin}/settings', [PluginSettingsController::class, 'edit'])->name('settings.edit');
		Route::put('{plugin}/settings', [PluginSettingsController::class, 'update'])->name('settings.update');
	});
});
