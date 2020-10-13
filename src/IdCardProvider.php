<?php


namespace Xinmoumomo\Idcard;

use Illuminate\Support\ServiceProvider;

class IdCardProvider extends ServiceProvider
{
    /**
     * 指示是否推迟提供程序的加载
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * 引导应用程序服务
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Config path.
        $config_path = __DIR__ . '/idcard.php';

        // 发布配置文件到项目的 config 目录中.
        $this->publishes(
            [$config_path => config_path('idcard.php')],
            'invoice'
        );
    }

    /**
     * 注册应用程序服务
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Config path.
        $config_path = __DIR__ . '/idcard.php';

        // 发布配置文件到项目的 config 目录中.
        $this->mergeConfigFrom(
            $config_path,
            'idcard'
        );
    }
}
