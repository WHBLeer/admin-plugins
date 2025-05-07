<?php
// src/Providers/PluginServiceProvider.php

namespace Sanlilin\AdminPlugins\Providers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\ServiceProvider;
use Sanlilin\AdminPlugins\Support\PluginManager;

class PluginServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/../Resources/config/plugins.php', 'plugins'
		);

		$this->app->singleton('plugins', function ($app) {
			return new PluginManager($app);
		});

		$this->registerCommands();
	}

	public function boot()
	{
		$this->registerPublishes();
		$this->loadRoutes();
		$this->loadViews();
		$this->loadTranslations();
	}

	protected function registerPublishes()
	{
		$this->publishes([
			__DIR__.'/../Resources/config/plugins.php' => config_path('plugins.php'),
		], 'plugins-config');

		$this->publishes([
			__DIR__.'/../Resources/views' => resource_path('views/vendor/plugins'),
		], 'plugins-views');

		$this->publishes([
			__DIR__.'/../Resources/assets' => public_path('assets/vendor/plugins'),
		], 'plugins-assets');
	}

	protected function loadRoutes()
	{
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
	}

	protected function loadViews()
	{
		$this->loadViewsFrom(__DIR__.'/../Resources/views', 'plugins');
	}


	protected function loadTranslations()
	{
		$this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'plugins');
	}

	protected function registerCommands()
	{
		$this->commands([
			\Sanlilin\AdminPlugins\Commands\InstallPluginsSystemCommand::class,
			\Sanlilin\AdminPlugins\Commands\MakePluginCommand::class,
			\Sanlilin\AdminPlugins\Commands\PluginRestartCommand::class,
			\Sanlilin\AdminPlugins\Commands\DeletePluginCommand::class,
			\Sanlilin\AdminPlugins\Commands\MakePluginControllerCommand::class,
			\Sanlilin\AdminPlugins\Commands\MakePluginModelCommand::class,
			\Sanlilin\AdminPlugins\Commands\PluginMigrateCommand::class,
			\Sanlilin\AdminPlugins\Commands\PluginRollbackCommand::class,
			\Sanlilin\AdminPlugins\Commands\DeletePluginCommand::class,
		]);
	}
}