<?php
// src/Commands/MakePluginModelCommand.php

namespace Sanlilin\AdminPlugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakePluginModelCommand extends Command
{
	protected $name = 'plugin:make-model';
	protected $description = 'Create a new model for the specified plugin';

	public function handle()
	{
		$pluginName = $this->argument('plugin');
		$modelName = $this->argument('model');
		$plugin = $this->laravel['plugins']->find($pluginName);

		if (!$plugin) {
			$this->error("Plugin [{$pluginName}] does not exist!");
			return 1;
		}

		$namespace = $plugin->getNamespace() . '\\Models';
		$modelClass = Str::studly($modelName);
		$modelPath = $plugin->getPath() . '/Models';
		$modelFile = $modelPath . '/' . $modelClass . '.php';

		if (!File::exists($modelPath)) {
			File::makeDirectory($modelPath, 0755, true);
		}

		if (File::exists($modelFile)) {
			$this->error("Model [{$modelClass}] already exists!");
			return 1;
		}

		$stub = $this->getStub();
		$content = str_replace(
			['DummyNamespace', 'DummyClass'],
			[$namespace, $modelClass],
			$stub
		);

		File::put($modelFile, $content);

		$this->info("Model [{$modelClass}] created successfully.");
		$this->line("<info>Created Model:</info> {$modelFile}");

		return 0;
	}

	protected function getStub()
	{
		return File::get(__DIR__ . '/../../Support/Stubs/model.stub');
	}

	protected function getArguments()
	{
		return [
			['plugin', InputArgument::REQUIRED, 'The name of the plugin'],
			['model', InputArgument::REQUIRED, 'The name of the model'],
		];
	}

	protected function getOptions()
	{
		return [
			['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
		];
	}
}