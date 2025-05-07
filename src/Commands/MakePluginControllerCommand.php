<?php
// src/Commands/MakePluginControllerCommand.php

namespace Sanlilin\AdminPlugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakePluginControllerCommand extends Command
{
	protected $name = 'plugin:make-controller';
	protected $description = 'Create a new controller for the specified plugin';

	public function handle()
	{
		$pluginName = $this->argument('plugin');
		$controllerName = $this->argument('controller');
		$plugin = $this->laravel['plugins']->find($pluginName);

		if (!$plugin) {
			$this->error("Plugin [{$pluginName}] does not exist!");
			return 1;
		}

		$namespace = $plugin->getNamespace() . '\\Http\\Controllers';
		$controllerClass = Str::studly($controllerName);
		$controllerPath = $plugin->getPath() . '/Http/Controllers';
		$controllerFile = $controllerPath . '/' . $controllerClass . '.php';

		if (!File::exists($controllerPath)) {
			File::makeDirectory($controllerPath, 0755, true);
		}

		if (File::exists($controllerFile)) {
			$this->error("Controller [{$controllerClass}] already exists!");
			return 1;
		}

		$stub = $this->getStub();
		$content = str_replace(
			['DummyNamespace', 'DummyClass'],
			[$namespace, $controllerClass],
			$stub
		);

		File::put($controllerFile, $content);

		$this->info("Controller [{$controllerClass}] created successfully.");
		$this->line("<info>Created Controller:</info> {$controllerFile}");

		return 0;
	}

	protected function getStub()
	{
		return File::get(__DIR__ . '/../../Support/Stubs/controller.stub');
	}

	protected function getArguments()
	{
		return [
			['plugin', InputArgument::REQUIRED, 'The name of the plugin'],
			['controller', InputArgument::REQUIRED, 'The name of the controller'],
		];
	}

	protected function getOptions()
	{
		return [
			['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller'],
		];
	}
}