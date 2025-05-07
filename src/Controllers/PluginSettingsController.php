<?php

namespace Sanlilin\AdminPlugins\Controllers;

use Illuminate\Http\Request;

class PluginSettingsController extends Controller
{
	public function edit($plugin)
	{
		$plugin = $this->pluginManager->find($plugin);

		if (!$plugin) {
			return $this->respond('error', 'Plugin not found!');
		}

		return view('plugins::settings', compact('plugin'));
	}

	public function update(Request $request, $plugin)
	{
		$plugin = $this->pluginManager->find($plugin);

		if (!$plugin) {
			return $this->respond('error', 'Plugin not found!');
		}

		// 这里可以根据插件的配置项动态处理
		$config = $request->except('_token', '_method');
		$plugin->setConfig($config);
		$plugin->saveConfig();
		return $this->respond('success', "Plugin [{$plugin->getName()}] settings updated!", ['plugin' => $plugin->getName()]);
	}
}