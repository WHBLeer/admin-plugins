<?php
// src/Support/PluginGenerator.php

namespace Sanlilin\AdminPlugins\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PluginGenerator
{
	protected $name;
	protected $pluginPath;
	protected $stubsPath;

	public function __construct($name)
	{
		$this->name = $name;
		$this->pluginPath = base_path('plugins/' . $this->name);
		$this->stubsPath = __DIR__ . '/Stubs';
	}

	public function generate()
	{
		if (File::exists($this->pluginPath)) {
			return false;
		}

		$this->createFolders();
		$this->createFiles();

		return true;
	}

	protected function createFolders()
	{
		$folders = [
			'Database/Migrations',
			'Http/Controllers',
			'Models',
			'Resources/views',
			'Resources/lang',
			'Routes',
			'Assets',
		];

		foreach ($folders as $folder) {
			File::makeDirectory($this->pluginPath . '/' . $folder, 0755, true);
		}
	}

	protected function createFiles()
	{
		$this->createPluginJson();
		$this->createController();
		$this->createConfigController();
		$this->createServiceProvider();
		$this->createConfigView();
		$this->createDefaultView();
		$this->createRoutesFile();
	}

	protected function createPluginJson()
	{
		$stub = File::get($this->stubsPath . '/plugin.json.stub');
		$content = str_replace(
			['DummyName', 'DummyNamespace', 'DummyTitle'],
			[$this->name, $this->getNamespace(), $this->getTitle()],
			$stub
		);

		File::put($this->pluginPath . '/plugin.json', $content);
	}

	protected function createController()
	{
		$stub = File::get($this->stubsPath . '/controller.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyClass', 'DummyName'],
			[$this->getNamespace(), $this->getStudlyName() . 'Controller', $this->name],
			$stub
		);

		File::put(
			$this->pluginPath . '/Http/Controllers/' . $this->getStudlyName() . 'Controller.php',
			$content
		);
	}

	protected function createConfigController()
	{
		$stub = File::get($this->stubsPath . '/ConfigController.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyName'],
			[$this->getNamespace(), $this->name],
			$stub
		);

		File::put(
			$this->pluginPath . '/Http/Controllers/' . $this->getStudlyName() . 'ConfigController.php',
			$content
		);
	}

	protected function createServiceProvider()
	{
		$stub = File::get($this->stubsPath . '/PluginServiceProvider.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyClass'],
			[$this->getNamespace(), $this->getStudlyName() . 'ServiceProvider'],
			$stub
		);

		File::put(
			$this->pluginPath . '/' . $this->getStudlyName() . 'ServiceProvider.php',
			$content
		);
	}

	protected function createConfigView()
	{
		$stub = File::get($this->stubsPath . '/view/config.stub');
		$content = str_replace(
			['DummySlug'],
			[$this->getSlugName()],
			$stub
		);

		File::put(
			$this->pluginPath . '/Resources/views/config.blade.php',
			$content
		);
	}

	protected function createDefaultView()
	{
		$stub = File::get($this->stubsPath . '/view/index.stub');
		$content = str_replace(
			['DummySlug'],
			[$this->getSlugName()],
			$stub
		);

		File::put(
			$this->pluginPath . '/Resources/views/index.blade.php',
			$content
		);
	}

	protected function createRoutesFile()
	{
		$stub = File::get($this->stubsPath . '/route.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyClass', 'DummySlug'],
			[$this->getNamespace(), $this->getStudlyName() . 'Controller', $this->getSlugName()],
			$stub
		);

		File::put(
			$this->pluginPath . '/Routes/web.php',
			$content
		);
	}

	protected function getNamespace()
	{
		return 'Plugins\\' . $this->getStudlyName();
	}

	protected function getStudlyName()
	{
		return Str::studly($this->name);
	}

	protected function getSlugName()
	{
		return Str::slug($this->name);
	}

	protected function getTitle()
	{
		return Str::title(str_replace(['-', '_'], ' ', $this->name));
	}
}