<?php

namespace Sanlilin\AdminPlugins\Exceptions;

use Exception;

class PluginException extends Exception
{
	/**
	 * 插件未找到异常
	 *
	 * @param string $name 插件名称
	 * @return static
	 */
	public static function pluginNotFound(string $name)
	{
		return new static("Plugin [{$name}] not found!");
	}

	/**
	 * 插件已存在异常
	 *
	 * @param string $name 插件名称
	 * @return static
	 */
	public static function pluginAlreadyExists(string $name)
	{
		return new static("Plugin [{$name}] already exists!");
	}

	/**
	 * 无效插件包异常
	 *
	 * @return static
	 */
	public static function invalidPackage()
	{
		return new static("Invalid plugin package: plugin.json not found or invalid");
	}

	/**
	 * 插件启用失败异常
	 *
	 * @param string $name 插件名称
	 * @return static
	 */
	public static function failedToEnable(string $name)
	{
		return new static("Failed to enable plugin [{$name}]");
	}

	/**
	 * 插件禁用失败异常
	 *
	 * @param string $name 插件名称
	 * @return static
	 */
	public static function failedToDisable(string $name)
	{
		return new static("Failed to disable plugin [{$name}]");
	}

	/**
	 * 插件重启失败异常
	 *
	 * @param string $name 插件名称
	 * @return static
	 */
	public static function restartFailed(string $name)
	{
		return new static("Failed to restart plugin [{$name}]");
	}

	/**
	 * 插件迁移失败异常
	 *
	 * @param string $name 插件名称
	 * @return static
	 */
	public static function migrationFailed(string $name)
	{
		return new static("Migration failed for plugin [{$name}]");
	}

	/**
	 * 插件回滚失败异常
	 *
	 * @param string $name 插件名称
	 * @return static
	 */
	public static function rollbackFailed(string $name)
	{
		return new static("Rollback failed for plugin [{$name}]");
	}

	/**
	 * 插件依赖缺失异常
	 *
	 * @param array $dependencies 缺失的依赖
	 * @return static
	 */
	public static function missingDependencies(array $dependencies)
	{
		$deps = implode(', ', $dependencies);
		return new static("Missing required dependencies: {$deps}");
	}

	/**
	 * 插件配置缺失异常
	 *
	 * @param string $key 配置键
	 * @return static
	 */
	public static function missingConfig(string $key)
	{
		return new static("Missing required config key: {$key}");
	}

	/**
	 * 插件 ZIP 包操作异常
	 *
	 * @return static
	 */
	public static function zipOperationFailed()
	{
		return new static("Unable to open or process zip file");
	}
}