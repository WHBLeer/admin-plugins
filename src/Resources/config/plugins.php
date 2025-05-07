<?php
// Resources/config/plugins.php

return [
	'path' => base_path('plugins'),
	'assets_path' => public_path('vendor/plugins'),
	'namespace' => 'Plugins',
	'default' => [
		'enabled' => false,
	],
	'cache' => [
		'enabled' => false,
		'key' => 'admin-plugins',
		'lifetime' => 86400,
	],
];