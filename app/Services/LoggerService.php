<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Centralized logging service for structured error tracking
 * 
 * Usage:
 *   LoggerService::driver('driver-issues')->info('Issue created', ['id' => 1, 'driver_id' => 5])
 *   LoggerService::driver('api')->error('API Error', ['status' => 500, 'path' => '/api/issues'])
 */
class LoggerService
{
    /**
     * Log entry points for different modules
     */
    const CHANNELS = [
        'driver-issues' => 'driver-issues',
        'api' => 'api',
        'database' => 'database',
        'payment' => 'payment',
        'stock' => 'stock',
        'auth' => 'auth',
        'general' => 'daily',
    ];

    /**
     * Get logger instance for a specific driver/module
     * 
     * @param string $channel Module name (driver-issues, api, database, etc.)
     * @return \Illuminate\Log\LogManager
     */
    public static function driver($channel = 'general')
    {
        $channelName = self::CHANNELS[$channel] ?? 'daily';
        return Log::channel($channelName);
    }

    /**
     * Log with context information
     * 
     * @param string $channel Module name
     * @param string $level Log level (info, error, warning, debug)
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public static function logWithContext($channel, $level, $message, array $context = [])
    {
        $enrichedContext = array_merge([
            'timestamp' => now()->toDateTimeString(),
            'url' => request()->fullUrl() ?? 'CLI',
            'user_id' => auth()->id() ?? null,
            'ip' => request()->ip() ?? 'UNKNOWN',
            'method' => request()->method() ?? 'UNKNOWN',
        ], $context);

        self::driver($channel)->log($level, $message, $enrichedContext);
    }

    /**
     * Log API errors with request/response details
     * 
     * @param string $message
     * @param string $statusCode
     * @param int|null $userId
     * @param array $extra
     */
    public static function logApiError($message, $statusCode = 'UNKNOWN', $userId = null, array $extra = [])
    {
        self::driver('api')->error($message, array_merge([
            'status_code' => $statusCode,
            'user_id' => $userId ?? auth()->id(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $extra));
    }

    /**
     * Log database operation errors
     * 
     * @param string $message
     * @param string $table
     * @param string|null $operation (create, update, delete, read)
     * @param array $details
     */
    public static function logDatabaseError($message, $table, $operation = null, array $details = [])
    {
        self::driver('database')->error($message, array_merge([
            'table' => $table,
            'operation' => $operation,
            'timestamp' => now()->toDateTimeString(),
        ], $details));
    }

    /**
     * Log driver issue operations
     * 
     * @param string $action (create, update, accept, reject, delete)
     * @param array $data Operation details
     */
    public static function logDriverIssue($action, array $data = [])
    {
        self::driver('driver-issues')->info("Driver Issue {$action}", array_merge([
            'action' => $action,
            'timestamp' => now()->toDateTimeString(),
            'user_id' => auth()->id(),
        ], $data));
    }

    /**
     * Get path to logs directory
     */
    public static function getLogsPath()
    {
        return storage_path('logs');
    }

    /**
     * Get path to specific log file
     */
    public static function getLogFile($channel = 'daily')
    {
        $date = now()->format('Y-m-d');
        return storage_path("logs/{$channel}-{$date}.log");
    }
}
