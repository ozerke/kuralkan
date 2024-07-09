<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class ZipLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:zip-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zip all log files from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->format('Y-m-d');
        $zipFile = storage_path("logs/log-archive-{$date}.zip");
        $logPath = storage_path('logs');

        $zip = new ZipArchive;

        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($logPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($logPath) + 1);

                $zip->addFile($filePath, $relativePath);
            }

            $zip->close();
            $this->info('Logs have been zipped successfully');
        } else {
            $this->error('Failed to create zip file');
        }
    }
}
