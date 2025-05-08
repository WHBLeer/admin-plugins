<?php

namespace Sanlilin\AdminPlugins\Commands;

use Illuminate\Console\Command;
use Sanlilin\AdminPlugins\Support\PluginGenerator;
use Symfony\Component\Console\Input\InputArgument;

class MakePluginCommand extends Command
{
	protected $name = 'plugin:make';
	protected $description = 'Create a new plugin';

	public function handle()
	{
		$names = $this->argument('name');
		$success = true;

		foreach ($names as $name) {
			$generator = new PluginGenerator($name);

			if ($generator->generate()) {
				$this->info("Plugin [{$name}] created successfully.");
			} else {
				$this->error("Plugin [{$name}] already exists!");
				$success = false;
			}
		}

		return $success ? 0 : 1;
	}

	protected function getArguments()
	{
		return [
			['name', InputArgument::IS_ARRAY, 'The names of plugins will be created.'],
		];
	}
}