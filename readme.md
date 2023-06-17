# Filesystem Compatibility with Minio support 

## Installation
```bash
composer require koffinate/laravel-filesystem
```

## Configuration
add this to your environment
```dotenv
MINIO_ACCESS_KEY_ID="minio-access-key"
MINIO_SECRET_ACCESS_KEY="minio-secret-access-key"
MINIO_DEFAULT_REGION="minio-region"
MINIO_BUCKET="minio-bucket"
MINIO_USE_PATH_STYLE_ENDPOINT=true
MINIO_URL="minio-full-url-with-bucket-include"
MINIO_ENDPOINT="minio-endpoint-without-bucket-included"
MINIO_VISIBILITY="public"
```
or use default laravel aws-s3 environment,
```dotenv
AWS_ACCESS_KEY_ID="s3-access-key"
AWS_SECRET_ACCESS_KEY="s3-secret-access-key"
AWS_DEFAULT_REGION="s3-region"
AWS_BUCKET="s3-bucket"
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_URL="s3-full-url-with-bucket-include"
AWS_ENDPOINT="s3-endpoint-without-bucket-included"
AWS_VISIBILITY="public"
```
actually if you want to use both of minio and s3 together, use `AWS_` and `MINIO_` on your `.env`.

### Custom configuration
You can still customize the configuration by defining new disks with `minio` key to `config/filesystem.php`. \
or use this command to generate from default config
```bash
php artisan koffinate:minio-config
```

## Usage
use normally laravel filesystem as you go.

make sure your `FILESYSTEM_DISK` on `.env` set to `minio` as default,

```dotenv
...
FILESYSTEM_DISK=minio
...
```

or `MEDIA_DISK` if using `spatie/laravel-medialibrary`'s package.
```dotenv
...
MEDIA_DISK=minio
...
```

### Obtain disk usage
you can use it directly using method `disk` from `Storage`,

```php
// put content into file
Storage::disk('minio')->put('file.jpg', $contents);

// read file contents
$contents = Storage::disk('minio')->get('file.jpg');

// check file is exists
if (Storage::disk('minio')->exists('file.jpg')) {
    // ...
}

// check file is missing or not exists
if (Storage::disk('minio')->missing('file.jpg')) {
    // ...
}
```

on file upload,
```php
// store on folder
$request->file('files')->store('path-to-folder', 'minio');

// store on folder with new name
$request->file('files')->storeAs('path-to-folder', 'file.jpg', 'minio');
```

read more usage on [Laravel Filesystem](https://laravel.com/docs/filesystem).
