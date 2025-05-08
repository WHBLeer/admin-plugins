<?php

namespace Sanlilin\AdminPlugins\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DeletePluginCommand extends Command
{
	protected $name = 'plugin:delete';
	protected $description = 'Delete the specified plugin';

	public function handle()
	{
		$pluginName = $this->argument('plugin');

		if (!$this->laravel['plugins']->find($pluginName)) {
			$this->error("Plugin [{$pluginName}] does not exist!");
			return 1;
		}

		if ($this->confirm("Are you sure you want to delete plugin [{$pluginName}]? This cannot be undone!")) {
			$this->laravel['plugins']->delete($pluginName);
			$this->info("Plugin [{$pluginName}] deleted successfully.");
		}

		return 0;
	}

	protected function getArguments()
	{
		return [
			['plugin', InputArgument::REQUIRED, 'The name of the plugin'],
		];
	}
}