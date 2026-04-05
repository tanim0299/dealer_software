<!-- Quick Reference: Error Debugging Guide -->

# How to Find & Debug Errors Quickly

## 1. View Recent Logs (Terminal)
```bash
# Recent driver issue logs
php artisan logs:view driver-issues --lines=50

# Recent API errors
php artisan logs:view api --lines=100

# General application logs
php artisan logs:view daily --lines=50
```

## 2. SSH Log Viewing (Production)
Connect via SSH and check logs:

```bash
# View last 50 lines of driver issues
tail -50 storage/logs/driver-issues-2026-04-05.log

# Follow logs in real-time
tail -f storage/logs/api-2026-04-05.log

# Search for errors
grep "ERROR" storage/logs/driver-issues-*.log

# Count errors
grep -c "ERROR" storage/logs/driver-issues-*.log
```

## 3. Log Format & What to Look For

### Error Log Entry Example:
```
[2026-04-05 10:30:15] local.ERROR: Failed to retrieve issue list 
{
    "table":"driver_issues",
    "operation":"read",
    "error":"Call to undefined method hasRole()",
    "trace":"#0 /app/Controllers/DriverIssueController.php(24)...",
    "search_criteria":{"driver_id":5},
    "timestamp":"2026-04-05 10:30:15",
    "url":"http://localhost/driver-issues",
    "user_id":1,
    "ip":"127.0.0.1",
    "method":"GET"
}
```

### What Each Field Means:
- **error**: The actual error message - READ THIS FIRST
- **table**: Which database table had the issue
- **operation**: What was being attempted (create/read/update/delete)
- **trace**: Stack trace showing where error occurred
- **user_id**: Who triggered the error
- **ip**: Client IP address
- **url**: Page/API endpoint that caused error

## 4. Common Error Patterns

### Database Connection Error
```
"error": "SQLSTATE[HY000]: General error: 1030 Got error..."
→ Check DB credentials in .env (DB_HOST, DB_USERNAME, etc.)
```

### Method Not Found Error
```
"error": "Call to undefined method hasRole()..."
→ Check if User model has proper trait/method
```

### Model Not Found Error
```
"error": "No query results for model..."
→ Record doesn't exist or ID is wrong
```

### Permission Denied Error
```
"error": "Access denied for user..."
→ Database user doesn't have required permissions
```

## 5. Daily Workflow

**Morning Check:**
```bash
# Check for overnight errors
grep "ERROR\|CRITICAL" storage/logs/driver-issues-$(date +%Y-%m-%d).log
grep "ERROR\|CRITICAL" storage/logs/api-$(date +%Y-%m-%d).log
```

**Troubleshooting Issues:**
```bash
# Get logs from last 2 hours
tail -200 storage/logs/driver-issues-$(date +%Y-%m-%d).log | grep "ERROR"

# Follow logs live while testing
tail -f storage/logs/api-$(date +%Y-%m-%d).log
```

**Backup Important Logs:**
```bash
# Copy logs to safe location
cp storage/logs/driver-issues-*.log backups/
```

## 6. Hosting Server (SSH Access)

### Via Hosting Control Panel or SSH:
```bash
# Connect to your server
ssh user@powderblue-goshawk-954772.hostingersite.com

# Navigate to application
cd public_html/

# View today's errors
tail -100 storage/logs/driver-issues-$(date +%Y-%m-%d).log

# Search for specific error
grep "payment" storage/logs/api-*.log

# Count total errors per channel
wc -l storage/logs/*.log
```

## 7. Windows Development (Using PowerShell)

```powershell
# View logs
Get-Content "storage/logs/driver-issues-2026-04-05.log" -Tail 50

# Search for errors
Select-String "ERROR" "storage/logs/driver-issues-*.log"

# Follow logs (real-time)
Get-Content "storage/logs/api-2026-04-05.log" -Wait
```

## 8. Tips for Faster Debugging

1. **Look at the newest logs first** - Latest errors are at the bottom
2. **Note the timestamp** - Correlate with when user reported issue
3. **Check user_id field** - Confirm which user had the problem
4. **Read error message carefully** - It usually tells you exactly what's wrong
5. **Stack trace shows code flow** - Helps understand what happened
6. **Use grep to filter** - Find related errors quickly

## 9. When to Clean Up Logs

Logs older than 30 days are automatically deleted. No manual cleanup needed.

But to manually clear:
```bash
# Clear all logs (be careful!)
rm storage/logs/*.log

# Clear specific channel
rm storage/logs/driver-issues-*.log
```

---

**Still having issues?**
1. Check the most recent ERROR entries
2. Note the error message and stack trace
3. Search Google for the error message
4. Check if related to database, auth, or permissions
5. Review the LOGGING.md file for more details
