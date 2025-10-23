<?php

declare(strict_types=1);

namespace EasyQCloudApi;

use EasyQCloudApi\Support\Config;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

if (! function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return dirname(__DIR__, 2).($path ? DIRECTORY_SEPARATOR.$path : '');
    }
}

if (! function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config'.($path ? DIRECTORY_SEPARATOR.$path : ''));
    }
}

/**
 * Laravel服务提供者
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * 注册服务
     */
    public function register(): void
    {
        // 注册工厂类
        $this->app->singleton(QCloudFactory::class, function ($app) {
            $config = $app['config']->get('easy-qcloud', []);

            return new QCloudFactory(new Config($config));
        });

        // 注册别名
        $this->app->alias(QCloudFactory::class, 'qcloud.factory');
    }

    /**
     * 启动服务
     */
    public function boot(): void
    {
        // 发布配置文件
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/easy-qcloud.php' => config_path('easy-qcloud.php'),
            ], 'easy-qcloud-config');
        }

        // 合并配置
        $this->mergeConfigFrom(
            __DIR__.'/../config/easy-qcloud.php',
            'easy-qcloud'
        );
    }

    /**
     * 获取提供的服务
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            QCloudFactory::class,
            'qcloud.factory',
        ];
    }
}
