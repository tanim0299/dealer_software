# Logging Setup Guide

## Overview
This project now has comprehensive logging configured for easy error debugging.

### Log Channels
The application uses the following log channels:

- **driver-issues** - Driver issue operations (create, update, delete, accept, reject)
- **api** - API errors and request tracking
- **database** - Database operation errors
- **payment** - Payment processing errors
- **stock** - Stock management and warehouse operations
- **auth** - Authentication and authorization events
- **daily** - General application logs (default)

### Log File Locations
All logs are stored in `storage/logs/` directory:

```
storage/logs/
├── driver-issues-2026-04-05.log
├── api-2026-04-05.log
├── database-2026-04-05.log
├── payment-2026-04-05.log
├── stock-2026-04-05.log
├── auth-2026-04-05.log
└── laravel.log (single file log, rotating daily)
```

## Viewing Logs

### Using Artisan Command
```bash
# View latest 50 lines from a specific channel
php artisan logs:view driver-issues

# View latest 100 lines
php artisan logs:view api --lines=100

# View different channels
php artisan logs:view database
php artisan logs:view payment
php artisan logs:view stock
php artisan logs:view auth
php artisan logs:view daily
```

### Manual Access
SSH/FTP into your server and navigate to `storage/logs/` directory to view log files directly.

## Using the Logger Service

### Basic Usage
```php
use App\Services\LoggerService;

// Log driver issue operations
LoggerService::logDriverIssue('created', [
    'driver_id' => $drivers->id,
    'issue_id' => $issue->id,
    'items_count' => count($items),
]);

// Log database errors
LoggerService::logDatabaseError('Failed to fetch issues', 'driver_issues', 'read', [
    'error' => $exception->getMessage(),
    'search_criteria' => $search,
]);

// Log API errors
LoggerService::logApiError('Payment processing failed', 500, auth()->id(), [
    'payment_id' => $paymentId,
    'amount' => $amount,
]);

// Custom logging with context
LoggerService::logWithContext('driver-issues', 'info', 'Issue accepted', [
    'issue_id' => $issue->id,
    'accepted_by' => auth()->user()->name,
]);

// Direct channel access
LoggerService::driver('stock')->warning('Low stock alert', [
    'product_id' => $product->id,
    'current_qty' => $stock,
    'threshold' => 10,
]);
```

### Log Context Enrichment
All logs automatically include:
- `timestamp` - When the log was created
- `url` - Request URL
- `user_id` - Authenticated user ID
- `ip` - Client IP address
- `method` - HTTP method (GET, POST, etc.)

## Frontend Log Viewer (Optional)
You can create a web-based log viewer admin page:

```php
// routes/web.php
Route::get('/admin/logs/{channel?}', function ($channel = 'daily') {
    $date = request('date', now()->format('Y-m-d'));
    $logFile = storage_path("logs/{$channel}-{$date}.log");
    
    if (!File::exists($logFile)) {
        return response('Log file not found', 404);
    }
    
    $logs = array_reverse(explode("\n", File::get($logFile)));
    return view('admin.logs', compact('logs', 'channel', 'date'));
})->middleware('auth', 'admin');
```

## Best Practices

1. **Use Appropriate Channels** - Log to the correct channel (driver-issues, payment, etc.)
2. **Include Context** - Always provide useful context in the log message
3. **Log Errors Properly** - Include exception messages and stack traces
4. **Monitor File Size** - Logs rotate daily and keep 30 days of history
5. **Production Settings** - On production, set `APP_DEBUG=false` and `LOG_LEVEL=warning`

## Troubleshooting

### Logs Not Appearing
- Check if `storage/logs/` directory is writable
- Verify `APP_ENV` and `LOG_CHANNEL` settings in `.env`
- Ensure Laravel cache and config are cleared: `php artisan config:clear`

### File Permissions
```bash
# Make logs directory writable (Linux/Mac)
chmod -R 755 storage/logs/
chmod -R 775 storage/logs/

# On Windows, ensure the user running PHP has write access
```

### Finding Errors Quickly
```bash
# Filter errors only
grep "ERROR\|CRITICAL" storage/logs/driver-issues-2026-04-05.log

# Monitor logs in real-time (Linux)
tail -f storage/logs/driver-issues-2026-04-05.log

# Count errors by type
grep -c "ERROR" storage/logs/*.log
```

## Environment Configuration

Update `.env` file for production:

```env
# Development
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Production
LOG_CHANNEL=stack
LOG_LEVEL=warning
```

## Log Retention

Logs are automatically kept for:
- **driver-issues**: 30 days
- **api**: 30 days
- **database**: 30 days
- **payment**: 30 days
- **stock**: 30 days
- **auth**: 30 days
- **daily** (general): 14 days

Older logs are automatically deleted by Laravel's maintenance commands.
