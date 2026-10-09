# ============================================
# Coffee Beans — Auto Launch (Server + Browser)
# ============================================
Clear-Host
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   Coffee Beans — Auto Launching...    ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Change to project directory
Set-Location "C:\laragon\www\coffee_beans"

# 1. Kill existing servers
Write-Host "[1/5] Stopping old servers..." -ForegroundColor Yellow
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Seconds 1
Write-Host "      ✅ Done" -ForegroundColor Green

# 2. Clear caches
Write-Host "[2/5] Clearing caches..." -ForegroundColor Yellow
php artisan config:clear 2>&1 | Out-Null
php artisan cache:clear 2>&1 | Out-Null
php artisan view:clear 2>&1 | Out-Null
php artisan route:clear 2>&1 | Out-Null
Write-Host "      ✅ Done" -ForegroundColor Green

# 3. Start Laravel server in a minimized window
Write-Host "[3/5] Starting Laravel server..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd 'C:\laragon\www\coffee_beans'; php artisan serve --host=0.0.0.0 --port=8000"
Start-Sleep -Seconds 3
Write-Host "      ✅ Server started" -ForegroundColor Green

# 4. Detect best URL (prefer hotspot para sa mobile)
Write-Host "[4/5] Detecting URLs..." -ForegroundColor Yellow
$ips = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {
    $_.IPAddress -notlike "127.*" -and
    $_.IPAddress -notlike "169.254.*" -and
    $_.PrefixOrigin -ne "WellKnown"
}).IPAddress

$hotspotIP = $ips | Where-Object { $_ -like "192.168.137.*" } | Select-Object -First 1
$wifiIP = $ips | Where-Object { $_ -like "192.168.*" -and $_ -notlike "192.168.137.*" -and $_ -notlike "192.168.56.*" } | Select-Object -First 1
$corpIP = $ips | Where-Object { $_ -like "172.*" } | Select-Object -First 1

Write-Host ""
Write-Host "════════════════════════════════════════" -ForegroundColor Green
Write-Host "  ACCESS URLs" -ForegroundColor Green
Write-Host "════════════════════════════════════════" -ForegroundColor Green
if ($hotspotIP) { Write-Host "  📱 Hotspot:  http://$hotspotIP`:8000" -ForegroundColor Cyan }
if ($wifiIP)    { Write-Host "  📶 WiFi:     http://$wifiIP`:8000" -ForegroundColor Cyan }
if ($corpIP)    { Write-Host "  🏢 Corp:     http://$corpIP`:8000" -ForegroundColor Cyan }
Write-Host "  💻 Local:    http://localhost:8000" -ForegroundColor Cyan
Write-Host "════════════════════════════════════════" -ForegroundColor Green
Write-Host ""

# 5. Auto-open browser
Write-Host "[5/5] Opening browser..." -ForegroundColor Yellow
Start-Process "http://localhost:8000/consignment/reports"
Write-Host "      ✅ Browser opened" -ForegroundColor Green

Write-Host ""
Write-Host "✅ System ready! Press any key to close this window (server continues running)" -ForegroundColor Green
Write-Host ""
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
