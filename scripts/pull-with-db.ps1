# Pull latest code + sync production (Aiven) DB into local DB.
# Requires: .env.production (git-ignored) with REMOTE_DB_* vars, and local .env with DB_* vars.
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not (Test-Path ".env.production")) {
    Write-Host "Missing .env.production. Copy .env.production.example and fill in Railway/Aiven credentials."
    exit 1
}

function Load-EnvFile($path) {
    Get-Content $path | ForEach-Object {
        if ($_ -match '^\s*([^#=]+)=(.*)$') {
            [System.Environment]::SetEnvironmentVariable($matches[1].Trim(), $matches[2].Trim())
        }
    }
}

Load-EnvFile ".env.production"
Load-EnvFile ".env"

Write-Host "==> git pull origin master"
git pull origin master

$dumpPath = "storage/app/production_dump.sql"
Write-Host "==> Dumping remote DB ($env:REMOTE_DB_DATABASE)"
mysqldump -h $env:REMOTE_DB_HOST -P $env:REMOTE_DB_PORT -u $env:REMOTE_DB_USERNAME "-p$env:REMOTE_DB_PASSWORD" --ssl-mode=REQUIRED --no-tablespaces $env:REMOTE_DB_DATABASE > $dumpPath

$localHost = if ($env:DB_HOST) { $env:DB_HOST } else { "127.0.0.1" }
$localPort = if ($env:DB_PORT) { $env:DB_PORT } else { "3306" }
$localPassArg = if ($env:DB_PASSWORD) { "-p$env:DB_PASSWORD" } else { $null }
Write-Host "==> Importing into local DB ($env:DB_DATABASE)"
if ($localPassArg) {
    Get-Content $dumpPath | mysql -h $localHost -P $localPort -u $env:DB_USERNAME $localPassArg $env:DB_DATABASE
} else {
    Get-Content $dumpPath | mysql -h $localHost -P $localPort -u $env:DB_USERNAME $env:DB_DATABASE
}

Remove-Item $dumpPath -Force

Write-Host "==> Clearing cache"
php artisan cache:clear

Write-Host "Done. Code + DB are in sync with production."
