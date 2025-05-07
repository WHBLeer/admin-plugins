<?php

namespace Sanlilin\AdminPlugins\Controllers;

use Sanlilin\AdminPlugins\Support\PluginManager;

class Controller extends \Illuminate\Routing\Controller
{

	protected PluginManager $pluginManager;

	public function __construct(PluginManager $pluginManager)
	{
		$this->pluginManager = $pluginManager;
	}

	protected function respond($type = 'success', $message = null, $data = null)
	{
		if (request()->ajax() || request()->wantsJson()) {
			$response = [];

			if ($type === 'success') {
				$response['status'] = 'success';
				$response['message'] = $message ?? 'Operation successful.';
				if ($data !== null) {
					$response['data'] = $data;
				}
			} elseif ($type === 'error') {
				$response['status'] = 'error';
				$response['message'] = $message ?? 'An error occurred.';
			}

			return response()->json($response);
		}

		if ($type === 'success') {
			return redirect()->back()->with('success', $message);
		}

		return redirect()->back()->with('error', $message);
	}

}