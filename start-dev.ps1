# ============================================
# Coffee Beans — One-Click Dev Server Starter
# ============================================
Clear-Host
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Coffee Beans — Dev Server Starter    ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# 1. Kill existing PHP servers
Write-Host "[1/4] Stopping old servers..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Seconds 1
Write-Host "      ✅ Old servers stopped" -ForegroundColor Green

# 2. Clear caches
Write-Host "[2/4] Clearing caches..." -ForegroundColor Yellow
php artisan config:clear 2>&1 | Out-Null
php artisan cache:clear 2>&1 | Out-Null
php artisan view:clear 2>&1 | Out-Null
php artisan route:clear 2>&1 | Out-Null
Write-Host "      ✅ Caches cleared" -ForegroundColor Green

# 3. Detect mobile hotspot IP
Write-Host "[3/4] Detecting IP addresses..." -ForegroundColor Yellow
$ips = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {
    $_.IPAddress -notlike "127.*" -and
    $_.IPAddress -notlike "169.254.*" -and
    $_.PrefixOrigin -ne "WellKnown"
}).IPAddress

$hotspotIP = $ips | Where-Object { $_ -like "192.168.137.*" } | Select-Object -First 1
$wifiIP = $ips | Where-Object { $_ -like "192.168.*" -and $_ -notlike "192.168.137.*" -and $_ -notlike "192.168.56.*" } | Select-Object -First 1
$corpIP = $ips | Where-Object { $_ -like "172.*" } | Select-Object -First 1

Write-Host ""
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║         ACCESS URLs (Mobile)           ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Green

if ($hotspotIP) {
    Write-Host ""
    Write-Host "  📱 MOBILE HOTSPOT (recommended):" -ForegroundColor Cyan
    Write-Host "     http://$hotspotIP`:8000" -ForegroundColor White
}
if ($wifiIP) {
    Write-Host ""
    Write-Host "  📶 WIFI (if same network):" -ForegroundColor Cyan
    Write-Host "     http://$wifiIP`:8000" -ForegroundColor White
}
if ($corpIP) {
    Write-Host ""
    Write-Host "  🏢 Corporate WiFi:" -ForegroundColor Cyan
    Write-Host "     http://$corpIP`:8000" -ForegroundColor White
}

Write-Host ""
Write-Host "  💻 Local PC:" -ForegroundColor Cyan
Write-Host "     http://localhost:8000" -ForegroundColor White
Write-Host ""

# 4. Start server
Write-Host "[4/4] Starting Laravel server..." -ForegroundColor Yellow
Write-Host "      Press Ctrl+C to stop" -ForegroundColor Gray
Write-Host ""
Write-Host "════════════════════════════════════════" -ForegroundColor DarkGray
Write-Host ""

php artisan serve --host=0.0.0.0 --port=8000
