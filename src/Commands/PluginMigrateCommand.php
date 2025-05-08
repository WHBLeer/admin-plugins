<?php

namespace Sanlilin\AdminPlugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;

class PluginMigrateCommand extends Command
{
	protected $name = 'plugin:migrate';
	protected $description = 'Run migrations for the specified plugin';

	public function handle()
	{
		$pluginName = $this->argument('plugin');
		$plugin = $this->laravel['plugins']->find($pluginName);

		if (!$plugin) {
			$this->error("Plugin [{$pluginName}] does not exist!");
			return 1;
		}

		$migrationPath = $plugin->getPath() . '/Database/Migrations';

		if (!File::exists($migrationPath)) {
			$this->error("No migrations found for plugin [{$pluginName}]!");
			return 1;
		}

		$this->call('migrate', [
			'--path' => str_replace(base_path(), '', $migrationPath),
			'--force' => true,
		]);

		$this->info("Migrations for plugin [{$pluginName}] run successfully.");

		return 0;
	}

	protected function getArguments()
	{
		return [
			['plugin', InputArgument::REQUIRED, 'The name of the plugin'],
		];
	}
}