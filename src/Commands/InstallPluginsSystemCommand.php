<?php

namespace Sanlilin\AdminPlugins\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\Role;

class InstallPluginsSystemCommand extends Command
{
	protected $signature = 'plugins-system:install';
	protected $description = 'Install the plugins system and setup permissions';

	public function handle()
	{
		$this->info('Setting up plugins system permissions...');

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
			$this->line("Created permission: {$permission['name']}");
		}

		// 确保管理员角色存在
		$adminRole = Role::firstOrCreate([
			'name' => 'admin',
			'guard_name' => 'admin'
		]);

		// 分配权限
		$adminRole->givePermissionTo($permissions);
		$this->info('All permissions assigned to admin role.');

		// 发布资源
		$this->call('vendor:publish', [
			'--provider' => 'Sanlilin\AdminPlugins\Providers\PluginServiceProvider',
			'--tag' => 'plugins-config'
		]);
		$this->call('vendor:publish', [
			'--provider' => 'Sanlilin\AdminPlugins\Providers\PluginServiceProvider',
			'--tag' => 'plugins-views'
		]);
		$this->call('vendor:publish', [
			'--provider' => 'Sanlilin\AdminPlugins\Providers\PluginServiceProvider',
			'--tag' => 'plugins-assets'
		]);

		$this->info('Plugins system installed successfully!');
	}
}