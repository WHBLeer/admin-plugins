<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Sanlilin\AdminPlugins\Controllers\{
	PluginController,
	PluginSettingsController
};

Route::middleware(['auth:admin'])->prefix('admin/plugins')->name('plugins.')->group(function () {
	Route::get('/', [PluginController::class, 'index'])->name('index');
	Route::get('/{plugin}/download', [PluginController::class, 'download'])->name('download');
	Route::post('/upload', [PluginController::class, 'upload'])->name('upload');
	Route::post('/{plugin}/install', [PluginController::class, 'install'])->name('install');
	Route::post('/{plugin}/uninstall', [PluginController::class, 'uninstall'])->name('uninstall');
	Route::delete('/{plugin}/delete', [PluginController::class, 'delete'])->name('delete');

	Route::prefix('{plugin}/settings')
		->name('settings.')
		->group(function () {
			Route::get('/', [PluginSettingsController::class, 'edit'])->name('edit');
			Route::put('/', [PluginSettingsController::class, 'update'])->name('update');
		});
});