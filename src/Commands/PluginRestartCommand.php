<?php

namespace Sanlilin\AdminPlugins\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PluginRestartCommand extends Command
{
	protected $name = 'plugin:restart';
	protected $description = 'Restart the specified plugin';

	public function handle()
	{
		$pluginName = $this->argument('plugin');
		$plugin = $this->laravel['plugins']->find($pluginName);

		if (!$plugin) {
			$this->error("Plugin [{$pluginName}] not found!");
			return 1;
		}

		$this->info("Restarting plugin [{$pluginName}]...");

		// 禁用插件
		$this->laravel['plugins']->uninstall($pluginName);
		$this->info("Plugin [{$pluginName}] disabled successfully.");

		// 启用插件
		$this->laravel['plugins']->install($pluginName);
		$this->info("Plugin [{$pluginName}] enabled successfully.");

		// 可选：重新发布资源
		if ($this->confirm('Do you want to republish assets?', false)) {
			$this->call('vendor:publish', [
				'--tag' => $pluginName.'-assets',
				'--force' => true
			]);
			$this->info("Assets for plugin [{$pluginName}] republished.");
		}

		$this->info("Plugin [{$pluginName}] restarted successfully.");
		return 0;
	}

	protected function getArguments()
	{
		return [
			['plugin', InputArgument::REQUIRED, 'The name of the plugin'],
		];
	}
}