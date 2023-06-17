<?php

namespace Koffinate\Filesystem;

use Aws\S3\S3Client;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter as AwsS3PortableVisibilityConverter;
use League\Flysystem\Visibility;

class FilesystemServiceProviderBak extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/minio.php', 'filesystems.disks');

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
            $visibility = new AwsS3PortableVisibilityConverter(
                $config["visibility"] ?? Visibility::PUBLIC
            );

            $client = new S3Client([
                'credentials' => [
                    'key' => $config["key"] ?? "",
                    'secret' => $config["secret"] ?? "",
                ],
                'region' => $config["region"] ?? "",
                'version' => "latest",
                'bucket_endpoint' => false,
                'use_path_style_endpoint' => $config["use_path_style_endpoint"] ?? true,
                'endpoint' => $config["endpoint"] ?? "",
            ]);

            $options = array_merge([
                'override_visibility_on_copy' => true
            ], (array)($config["options"] ?? []));

            return new FilesystemManager(
                new AwsS3V3Adapter(
                    client: $client,
                    bucket: $config["bucket"] ?? "",
                    prefix: '',
                    visibility: $visibility,
                    mimeTypeDetector: null,
                    options: $options,
                    streamReads: $config["stream_reads"] ?? false
                )
            );
        });
    }
}
