<?php

namespace Koffinate\Filesystem\Console;

class MinioConfigCommand extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'koffinate:minio-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish default S3 configuration with Minio support';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle(): void
    {
        $configFile = config_path('filesystems.php');
        $koffinateConfig = __DIR__ . '/../../config/config.stub';

        if (!is_writable($configFile) && !$this->tryToWrite($configFile)) {
            $this->components->error("Config file is not writable, cannot write config to $configFile");
            die();
        }

        $config = include $configFile;
        if (!empty($config['disks']['minio'])) {
            $this->components->error("Minio disk already applied on $configFile");
            die();
        }

        try {
            $configContent = file_get_contents($configFile);
            $koffinateConfigContent = file_get_contents($koffinateConfig);

            $configContent = preg_replace(
                pattern: '/([\'|\"]disks[\'|\"](\W?)+=>(\W?)+\[)/',
                replacement: "$1\n\n$koffinateConfigContent",
                subject: $configContent
            );

            file_put_contents($configFile, $configContent);

            $this->components->info("Koffinate S3 configuration successfully applied");

        } catch (\Exception $e) {
            $this->components->error("failed on applying koffinate s3 config on $configFile with error message: {$e->getMessage()}");
        }
    }

    /**
     * @param  string  $path
     *
     * @return bool
     */
    private function tryToWrite(string $path): bool
    {
        try {
            return chmod($path, 0754);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param  string  $path
     *
     * @return string
     */
    private function filemod(string $path): string
    {
        $perms = fileperms($path);

        $mod = match ($perms & 0xF000) {
            0xC000 => 's',  // socket
            0xA000 => 'l',  // symbolic link
            0x8000 => ' ',  // regular
            0x6000 => 'b',  // block special
            0x4000 => 'd',  // directory
            0x2000 => 'c',  // character special
            0x1000 => 'p',  // FIFO pipe
            default => 'u', // unknown
        };

        // Owner
        $mod .= (($perms & 0x0100) ? 'r' : '-');
        $mod .= (($perms & 0x0080) ? 'w' : '-');
        $mod .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $mod .= (($perms & 0x0020) ? 'r' : '-');
        $mod .= (($perms & 0x0010) ? 'w' : '-');
        $mod .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

        // Other
        $mod .= (($perms & 0x0004) ? 'r' : '-');
        $mod .= (($perms & 0x0002) ? 'w' : '-');
        $mod .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

        return $mod;
    }
}
