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
		$this->loadMigrations();
		$this->loadTranslations();

		$this->ensurePermissionsCreated();
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

	protected function loadMigrations()
	{
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
	}

	protected function loadTranslations()
	{
		$this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'plugins');
	}

	protected function ensurePermissionsCreated()
	{
		// 只在控制台运行或首次安装时执行
		if (!$this->app->runningInConsole() && Permission::where('name', 'plugins.view')->exists()) {
			return;
		}
		$system = Permission::firstOrCreate([
			'name' => 'system',
			'guard_name' => 'admin',
			'display_name' => '系统管理',
			'icon' => 'fas fa-cog',
		]);
		$parent = Permission::firstOrCreate([
			'name' => 'plugins.manage',
			'guard_name' => 'admin',
			'display_name' => '插件',
			'icon' => 'ph-duotone  ph-squares-four',
			'parent_id' => $system->id,
		]);
		$permissions = [
			[
				'name' => 'plugins.view',
				'guard_name' => 'admin',
				'display_name' => '插件管理',
				'icon' => 'ph-duotone  ph-squares-four',
				'route' => 'admin.plugins.index',
				'parent_id' => $parent->id,
			],
			[
				'name' => 'plugins.install',
				'guard_name' => 'admin',
				'display_name' => '插件安装',
				'icon' => 'ph-duotone  ph-squares-four',
				'route' => 'admin.plugins.install',
				'parent_id' => $parent->id,
			],
			[
				'name' => 'plugins.uninstall',
				'guard_name' => 'admin',
				'display_name' => '插件卸载',
				'icon' => 'ph-duotone  ph-squares-four',
				'route' => 'admin.plugins.uninstall',
				'parent_id' => $parent->id,
			],
			[
				'name' => 'plugins.delete',
				'guard_name' => 'admin',
				'display_name' => '插件删除',
				'icon' => 'ph-duotone  ph-squares-four',
				'route' => 'admin.plugins.delete',
				'parent_id' => $parent->id,
			],
			[
				'name' => 'plugins.settings',
				'guard_name' => 'admin',
				'display_name' => '插件配置',
				'icon' => 'ph-duotone  ph-squares-four',
				'route' => 'admin.plugins.settings',
				'parent_id' => $parent->id,
			],
		];

		foreach ($permissions as $permission) {
			Permission::firstOrCreate($permission);
		}

		$adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
		$adminRole->givePermissionTo($permissions);
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