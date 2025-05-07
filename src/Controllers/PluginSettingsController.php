<?php
// src/Controllers/PluginSettingsController.php

namespace Sanlilin\AdminPlugins\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sanlilin\AdminPlugins\Support\PluginManager;

class PluginSettingsController extends Controller
{
	protected $pluginManager;

	public function __construct(PluginManager $pluginManager)
	{
		$this->pluginManager = $pluginManager;
	}

	public function edit($plugin)
	{
		$plugin = $this->pluginManager->find($plugin);

		if (!$plugin) {
			abort(404, 'Plugin not found');
		}

		return view('plugins::admin.settings', compact('plugin'));
	}

	public function update(Request $request, $plugin)
	{
		$plugin = $this->pluginManager->find($plugin);

		if (!$plugin) {
			abort(404, 'Plugin not found');
		}

		// 这里可以根据插件的配置项动态处理
		$config = $request->except('_token', '_method');
		$plugin->setConfig($config);
		$plugin->saveConfig();

		return redirect()->back()->with('success', 'Plugin settings updated!');
	}
}