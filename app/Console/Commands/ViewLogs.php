<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ViewLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:view {channel=daily} {--lines=50} {--follow}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'View application logs by channel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $channel = $this->argument('channel');
        $lines = $this->option('lines');
        $follow = $this->option('follow');

        // Get today's log file
        $date = now()->format('Y-m-d');
        $logFile = storage_path("logs/{$channel}-{$date}.log");

        // Fallback to old format if date-based doesn't exist
        if (!File::exists($logFile)) {
            $logFile = storage_path("logs/{$channel}.log");
        }

        if (!File::exists($logFile)) {
            $this->error("Log file not found: {$logFile}");
            $this->info('Available log files:');
            
            $logs = File::files(storage_path('logs'));
            foreach ($logs as $log) {
                $this->line('  → ' . $log->getFilename());
            }
            return 1;
        }

        $this->showLogs($logFile, $lines, $follow);
    }

    private function showLogs($logFile, $lines = 50, $follow = false)
    {
        $content = File::get($logFile);
        $logLines = explode("\n", trim($content));

        // Show last N lines
        $displayed = array_slice($logLines, -$lines);

        foreach ($displayed as $line) {
            if (empty($line)) continue;

            // Color code by level
            if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
                $this->line("<fg=red>{$line}</>");
            } elseif (strpos($line, 'WARNING') !== false) {
                $this->line("<fg=yellow>{$line}</>");
            } elseif (strpos($line, 'INFO') !== false) {
                $this->line("<fg=green>{$line}</>");
            } else {
                $this->line($line);
            }
        }

        $this->info("\n✓ Showing last {$lines} lines from: " . basename($logFile));
    }
}
