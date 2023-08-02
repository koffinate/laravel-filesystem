<?php

namespace Koffinate\Filesystem;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Storage;

class FilesystemServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/minio.php', 'filesystems.disks');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\MinioConfigCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Storage::extend('minio', function ($app, $config) {
            $config['bucket_endpoint'] = false;
            $config['use_path_style_endpoint'] = true;
            $url = ! empty($config['url']) ? $config['url'] : $config['endpoint'];
            if (! str($url)->endsWith("/{$config['bucket']}")) {
                $url .= "/{$config['bucket']}";
            }
            $config['url'] = $url;

            return (new FilesystemManager($app))->createS3Driver($config);
        });
    }
}
