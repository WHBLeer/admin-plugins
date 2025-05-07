<?php
// src/Support/PluginManager.php

namespace Sanlilin\AdminPlugins\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Sanlilin\AdminPlugins\Models\Plugin;
use Sanlilin\AdminPlugins\Exceptions\PluginException;
use ZipArchive;

class PluginManager
{
	protected $app;
	protected $pluginsPath;
	protected $plugins = [];

	public function __construct($app)
	{
		$this->app = $app;
		$this->pluginsPath = base_path('plugins');
	}

	public function all()
	{
		return $this->getPlugins();
	}

	public function getPlugins()
	{
		if (!empty($this->plugins)) {
			return $this->plugins;
		}

		if (!File::exists($this->pluginsPath)) {
			File::makeDirectory($this->pluginsPath);
			return [];
		}

		$plugins = [];
		foreach (File::directories($this->pluginsPath) as $pluginPath) {
			$pluginName = basename($pluginPath);
			$plugin = $this->find($pluginName);
			if ($plugin) {
				$plugins[$pluginName] = $plugin;
			}
		}

		$this->plugins = $plugins;

		return $plugins;
	}

	public function find($name)
	{
		$path = $this->getPluginPath($name);

		if (!File::exists($path)) {
			return null;
		}

		$configPath = $path . '/plugin.json';

		if (!File::exists($configPath)) {
			return null;
		}

		$config = json_decode(File::get($configPath), true);

		return new Plugin($this->app, $name, $path, $config);
	}

	public function install($name)
	{
		$plugin = $this->find($name);

		if (!$plugin) {
			throw PluginException::pluginNotFound($name);
		}

		$this->registerServiceProvider($plugin);
		$this->bootServiceProvider($plugin);

		$this->publishAssets($plugin);
		$this->runMigrations($plugin);

		$plugin->enable();

		return $plugin;
	}

	public function uninstall($name)
	{
		$plugin = $this->find($name);

		if (!$plugin) {
			throw PluginException::pluginNotFound($name);
		}

		$this->rollbackMigrations($plugin);
		$plugin->disable();

		return $plugin;
	}
	public function restart($name)
	{
		$plugin = $this->find($name);

		if (!$plugin) {
			throw PluginException::pluginNotFound($name);
		}

		$this->uninstall($name);
		$this->install($name);

		return $plugin;
	}

	public function delete($name)
	{
		$plugin = $this->find($name);

		if (!$plugin) {
			throw PluginException::pluginNotFound($name);
		}

		if ($plugin->isEnabled()) {
			$this->uninstall($name);
		}

		File::deleteDirectory($this->getPluginPath($name));

		return true;
	}

	public function installFromZip($zipPath)
	{
		$zip = new ZipArchive;
		$res = $zip->open($zipPath);

		if ($res !== true) {
			throw new PluginException("Unable to open zip file");
		}

		$tempPath = storage_path('app/plugins/temp_' . Str::random(10));
		$zip->extractTo($tempPath);
		$zip->close();

		// Find plugin.json in extracted files
		$pluginJson = null;
		$files = File::allFiles($tempPath);

		foreach ($files as $file) {
			if ($file->getFilename() === 'plugin.json') {
				$pluginJson = json_decode(File::get($file->getPathname()), true);
				break;
			}
		}

		if (!$pluginJson || !isset($pluginJson['name'])) {
			File::deleteDirectory($tempPath);
			throw new PluginException("Invalid plugin package: plugin.json not found or invalid");
		}

		$pluginName = $pluginJson['name'];
		$destination = $this->getPluginPath($pluginName);

		if (File::exists($destination)) {
			File::deleteDirectory($tempPath);
			throw PluginException::pluginAlreadyExists($pluginName);

		}

		File::moveDirectory($tempPath, $destination);

		return $this->install($pluginName);
	}

	protected function registerServiceProvider(Plugin $plugin)
	{
		if (isset($plugin->config['provider'])) {
			$this->app->register($plugin->config['provider']);
		}
	}

	protected function bootServiceProvider(Plugin $plugin)
	{
		if (isset($plugin->config['provider'])) {
			$provider = $this->app->getProvider($plugin->config['provider']);
			if (method_exists($provider, 'boot')) {
				$provider->boot();
			}
		}
	}

	protected function publishAssets(Plugin $plugin)
	{
		if (isset($plugin->config['assets'])) {
			$source = $plugin->getPath() . '/' . $plugin->config['assets'];
			$destination = public_path('vendor/plugins/' . $plugin->getName());

			if (File::exists($source)) {
				File::copyDirectory($source, $destination);
			}
		}
	}

	protected function runMigrations(Plugin $plugin)
	{
		$migrationPath = $plugin->getPath() . '/Database/Migrations';

		if (File::exists($migrationPath)) {
			Artisan::call('migrate', [
				'--path' => str_replace(base_path(), '', $migrationPath),
				'--force' => true,
			]);
		}
	}

	protected function rollbackMigrations(Plugin $plugin)
	{
		$migrationPath = $plugin->getPath() . '/Database/Migrations';

		if (File::exists($migrationPath)) {
			Artisan::call('migrate:rollback', [
				'--path' => str_replace(base_path(), '', $migrationPath),
				'--force' => true,
			]);
		}
	}

	public function getPluginPath($name)
	{
		return $this->pluginsPath . '/' . $name;
	}

	public function getPluginsPath()
	{
		return $this->pluginsPath;
	}
}