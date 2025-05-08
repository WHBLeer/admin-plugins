<?php

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
			'Commands',
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
		$this->createConfig();
		$this->createController();
		$this->createConfigController();
		$this->createServiceProvider();
		$this->createModel();
		$this->createView();
		$this->createLang();
		$this->createRoute();
		$this->createMigration();
	}

	protected function createPluginJson()
	{
		$stub = File::get($this->stubsPath . '/plugin.json.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyName', 'DummyTitle'],
			[$this->getNamespace(), $this->name, $this->getTitle()],
			$stub
		);

		File::put($this->pluginPath . '/plugin.json', $content);
	}

	protected function createConfig()
	{
		$stub = File::get($this->stubsPath . '/config.php.stub');
		$content = str_replace(
			['DummyTitle', 'DummySlug'],
			[$this->getTitle(), $this->getSlugName()],
			$stub
		);

		File::put($this->pluginPath . '/config.php', $content);
	}

	protected function createController()
	{
		$stub = File::get($this->stubsPath . '/controller.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyClass', 'DummyName', 'DummyModel', 'DummySlug'],
			[$this->getNamespace(), $this->getStudlyName() . 'Controller', $this->name, $this->getStudlyName(),$this->getSlugName()],
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
			['DummyNamespace', 'DummyClass', 'DummyName', 'DummySlug'],
			[$this->getNamespace(), $this->getStudlyName() . 'ConfigController', $this->name, $this->getSlugName()],
			$stub
		);

		File::put(
			$this->pluginPath . '/Http/Controllers/' . $this->getStudlyName() . 'ConfigController.php',
			$content
		);
	}

	protected function createModel()
	{
		$stub = File::get($this->stubsPath . '/model.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyClass', 'DummyTable'],
			[$this->getNamespace(), $this->getStudlyName(), $this->getSlugName()],
			$stub
		);

		File::put(
			$this->pluginPath . '/Models/' . $this->getStudlyName() .'.php',
			$content
		);
	}

	protected function createServiceProvider()
	{
		$stub = File::get($this->stubsPath . '/PluginServiceProvider.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyClass','DummyCommandClass'],
			[$this->getNamespace(), $this->getStudlyName() . 'ServiceProvider','\\Plugins\\'.$this->getStudlyName().'\\Commands\\'.$this->getStudlyName().'Command'],
			$stub
		);

		File::put(
			$this->pluginPath . '/' . $this->getStudlyName() . 'ServiceProvider.php',
			$content
		);
	}

	protected function createView()
	{
		$stubPath = $this->stubsPath . '/view';
		$viewPath = $this->pluginPath . '/Resources/views';

		// 遍历所有 .stub 文件
		$files = File::files($stubPath);

		foreach ($files as $file) {
			$fileName = pathinfo($file, PATHINFO_BASENAME, '.stub'); // 获取不带扩展名的文件名
			$stubContent = File::get($file);

			// 替换占位符
			$content = str_replace(
				['DummyName','DummySlug'],
				[$this->name, $this->getSlugName()],
				$stubContent
			);

			// 写入到目标路径
			File::put($viewPath . DIRECTORY_SEPARATOR . $fileName . '.blade.php', $content);
		}
	}

	protected function createLang()
	{
		$stubPath = $this->stubsPath . '/lang';
		$viewPath = $this->pluginPath . '/Resources/lang';

		// 遍历所有 .stub 文件
		$files = File::files($stubPath);

		foreach ($files as $file) {
			$fileName = pathinfo($file, PATHINFO_BASENAME, '.stub'); // 获取不带扩展名的文件名
			$stubContent = File::get($file);

			// 替换占位符
			$content = str_replace(
				['DummyName'],
				[$this->name],
				$stubContent
			);

			// 写入到目标路径
			File::put($viewPath . DIRECTORY_SEPARATOR . $fileName . '.php', $content);
		}
	}

	protected function createRoute()
	{
		$stub = File::get($this->stubsPath . '/route.stub');
		$content = str_replace(
			['DummyNamespace', 'DummyClass', 'DummyConfigClass', 'DummySlug'],
			[$this->getNamespace(), $this->getStudlyName() . 'Controller', $this->getStudlyName() . 'ConfigController', $this->getSlugName()],
			$stub
		);

		File::put(
			$this->pluginPath . '/Routes/web.php',
			$content
		);
	}

	protected function createMigration()
	{
		$stub = File::get($this->stubsPath . '/migration.stub');
		$content = str_replace(
			['DummyTable'],
			[$this->getSlugName()],
			$stub
		);

		$migrationPath = $this->pluginPath . '/Database/Migrations';
		$migrationFile = date('Y_m_d_His') . '_create_' . $this->getSlugName() . '.php';

		File::put($migrationPath . '/' . $migrationFile, $content);
	}
	protected function createCommand()
	{
		$stub = File::get($this->stubsPath . '/command.stub');
		$content = str_replace(
			['DummyNamespace','DummyClass','DummySlug'],
			[$this->getNamespace(),$this->getStudlyName().'Command',$this->getSlugName()],
			$stub
		);
		$commandPath = $this->pluginPath . '/Commands';
		$commandFile = $this->getStudlyName().'Command.php';

		File::put($commandPath . '/' . $commandFile, $content);
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