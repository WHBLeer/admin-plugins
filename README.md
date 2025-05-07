# Admin Plugin


## 关于
Admin Plugin是一个插件机制解决方案，为需要建立自己的生态系统的开发人员，有了它，你可以建立一个插件化的生态系统。它可以帮助您如下:

* 加载插件基于服务注册。
* 通过命令行，插件开发人员可以轻松快速地构建插件并将插件上传到插件市场。
* 提供插件编写器包支持。在创建的插件中单独引用composer。
* 以事件监控的方式执行插件的安装、卸载、启用、禁用逻辑。易于开发人员扩展。
* 插槽式插件市场支持，通过修改配置文件，开发者可以无缝连接到自己的插件市场。
* 附带一个基本的插件市场，开发人员可以上传插件并对其进行审查。

## 适用环境

```yml
"php": "^8.2",
"ext-zip": "*",
"laravel/framework": "^11.0",
"spatie/laravel-permission": "^6.12",
```


## 安装

* Step 1
```shell
composer require sanlilin/admin-plugin
php artisan plugins-system:install
php artisan migrate
```

* Step 2
```php
php artisan vendor:publish --provider="Sanlilin\AdminPlugins\Providers\PluginServiceProvider" --tag=plugins-config
php artisan vendor:publish --provider="Sanlilin\AdminPlugins\Providers\PluginServiceProvider" --tag=plugins-views
php artisan vendor:publish --provider="Sanlilin\AdminPlugins\Providers\PluginServiceProvider" --tag=plugins-assets
```

## Use

* 创建插件
```php
php artisan make:plugin ArticlePlugin
```

* 重启插件
```php
php artisan plugin:restart ArticlePlugin
```

* 创建插件控制器
```php
php artisan plugin:make-controller ArticlePlugin ArticleController
```

* 创建插件模型
```php
php artisan plugin:make-model ArticlePlugin Article --migration
```

* 运行插件迁移
```php
php artisan plugin:migrate ArticlePlugin
```

* 回滚插件迁移
```php
php artisan plugin:rollback ArticlePlugin
```

* 删除插件
```php
php artisan plugin:delete ArticlePlugin
```

* 图形界面管理 访问 /admin/plugins
- 查看所有插件列表
- 上传 ZIP 格式的插件包
- 启用/禁用/重启插件
- 配置插件设置
- 删除插件












