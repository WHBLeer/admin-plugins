<?php

namespace Sanlilin\AdminPlugins\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sanlilin\AdminPlugins\Support\PluginManager;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PluginController extends Controller
{
	protected $pluginManager;

	public function __construct(PluginManager $pluginManager)
	{
		$this->pluginManager = $pluginManager;
	}

	public function index()
	{
		$plugins = $this->pluginManager->all();

		return view('plugins::admin.index', compact('plugins'));
	}

	public function install($plugin)
	{
		try {
			$plugin = $this->pluginManager->find($plugin);

			if (!$plugin) {
				return redirect()->back()->with('error', 'Plugin not found!');
			}

			$this->pluginManager->install($plugin->getName());

			return redirect()->back()->with('success', "Plugin [{$plugin->getName()}] installed successfully!");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', $e->getMessage());
		}
	}

	public function uninstall($plugin)
	{
		try {
			$plugin = $this->pluginManager->find($plugin);

			if (!$plugin) {
				return redirect()->back()->with('error', 'Plugin not found!');
			}

			$this->pluginManager->uninstall($plugin->getName());

			return redirect()->back()->with('success', "Plugin [{$plugin->getName()}] uninstalled successfully!");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', $e->getMessage());
		}
	}

	public function upload(Request $request)
	{
		$request->validate([
			'plugin' => 'required|file|mimes:zip'
		]);

		try {
			$file = $request->file('plugin');
			$path = $file->storeAs('plugins', $file->getClientOriginalName());

			$plugin = $this->pluginManager->installFromZip(storage_path('app/' . $path));

			// 安装完成后删除上传的 zip 文件
			Storage::delete($path);

			return redirect()->back()->with('success', "Plugin [{$plugin->getName()}] installed successfully!");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', $e->getMessage());
		}
	}

	public function download($plugin)
	{
		$plugin = $this->pluginManager->find($plugin);

		if (!$plugin) {
			return redirect()->back()->with('error', 'Plugin not found!');
		}

		// 插件目录路径
		$pluginPath = base_path('plugins/' . $plugin);

		// 检查插件是否存在
		if (!is_dir($pluginPath)) {
			return redirect()->back()->with('error', "Plugin [{$plugin}] does not exist!");
		}

		// 创建 Zip 文件
		$zipFileName = sys_get_temp_dir() . '/' . $plugin . '.zip';
		$zip = new ZipArchive();

		if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
			return redirect()->back()->with('error', 'Failed to create zip file.');
		}

		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($pluginPath),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file) {
			// 跳过目录（只添加文件）
			if (!$file->isDir()) {
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen(dirname($pluginPath)) + 1);
				$zip->addFile($filePath, $relativePath);
			}
		}

		$zip->close();

		// 返回下载响应
		return response()->download($zipFileName)->deleteFileAfterSend(true);
	}


	public function delete($plugin)
	{
		try {
			$plugin = $this->pluginManager->find($plugin);

			if (!$plugin) {
				return redirect()->back()->with('error', 'Plugin not found!');
			}

			$this->pluginManager->delete($plugin->getName());

			// 删除插件目录
			Storage::deleteDirectory('plugins/' . $plugin);

			return redirect()->back()->with('success', "Plugin [{$plugin->getName()}] deleted successfully!");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', $e->getMessage());
		}
	}
}