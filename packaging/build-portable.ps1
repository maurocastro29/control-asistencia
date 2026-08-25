param(
    [string]$OutputDirectory = "dist\Pangea",
    [switch]$SeedDatabase
)

$ErrorActionPreference = "Stop"
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..\")).Path
$outputPath = Join-Path $projectRoot $OutputDirectory
$appPath = $outputPath
$phpPath = Join-Path $appPath "php"
$launcherPublishPath = Join-Path $projectRoot "packaging\.launcher-publish"
$phpExecutable = (Get-Command php -ErrorAction Stop).Source
$phpSourcePath = Split-Path $phpExecutable -Parent
$composer = (Get-Command composer -ErrorAction Stop).Source

Write-Host "Preparando paquete Pangea en $outputPath"

if (Test-Path $outputPath) {
    Remove-Item $outputPath -Recurse -Force
}
New-Item $appPath -ItemType Directory -Force | Out-Null

$excludeDirectories = @(
    ".git",
    ".github",
    ".idea",
    ".vscode",
    "node_modules",
    "vendor",
    "storage\framework\cache",
    "storage\framework\sessions",
    "storage\framework\testing",
    "storage\framework\views",
    "storage\logs",
    "tests",
    "packaging",
    "dist"
)

$robocopyArgs = @($projectRoot, $appPath, "/E", "/COPY:DAT", "/DCOPY:DAT", "/R:1", "/W:1", "/NFL", "/NDL", "/NJH", "/NJS")
foreach ($directory in $excludeDirectories) {
    $robocopyArgs += "/XD"
    $robocopyArgs += (Join-Path $projectRoot $directory)
}
$robocopyArgs += "/XF"
$robocopyArgs += (Join-Path $projectRoot ".env")
& robocopy @robocopyArgs | Out-Null
if ($LASTEXITCODE -gt 7) {
    throw "No fue posible copiar los archivos de la aplicación. Código Robocopy: $LASTEXITCODE"
}

New-Item (Join-Path $appPath "storage\framework\cache") -ItemType Directory -Force | Out-Null
New-Item (Join-Path $appPath "storage\framework\sessions") -ItemType Directory -Force | Out-Null
New-Item (Join-Path $appPath "storage\framework\views") -ItemType Directory -Force | Out-Null
New-Item (Join-Path $appPath "storage\logs") -ItemType Directory -Force | Out-Null
New-Item (Join-Path $appPath "bootstrap\cache") -ItemType Directory -Force | Out-Null

$envTemplate = Get-Content (Join-Path $projectRoot ".env.example") -Raw
$envTemplate = $envTemplate -replace "APP_ENV=local", "APP_ENV=production"
$envTemplate = $envTemplate -replace "APP_DEBUG=true", "APP_DEBUG=false"
$envTemplate = $envTemplate -replace "APP_URL=http://localhost", "APP_URL=http://127.0.0.1"
$envTemplate = $envTemplate -replace "APP_KEY=", "APP_KEY="
$envTemplate = $envTemplate -replace "DB_DATABASE=laravel", "DB_DATABASE=database/database.sqlite"
Set-Content (Join-Path $appPath ".env") $envTemplate -Encoding UTF8

Write-Host "Copiando PHP y extensiones SQLite"
New-Item $phpPath -ItemType Directory -Force | Out-Null
$phpCopyArgs = @($phpSourcePath, $phpPath, "/E", "/COPY:DAT", "/DCOPY:DAT", "/R:1", "/W:1", "/NFL", "/NDL", "/NJH", "/NJS")
& robocopy @phpCopyArgs | Out-Null
if ($LASTEXITCODE -gt 7) {
    throw "No fue posible copiar PHP. Código Robocopy: $LASTEXITCODE"
}

$phpIni = Join-Path $phpPath "php.ini"
if (Test-Path (Join-Path $phpPath "php.ini-production")) {
    Copy-Item (Join-Path $phpPath "php.ini-production") $phpIni -Force
}

$phpIniContent = Get-Content $phpIni -Raw
$phpIniContent = $phpIniContent -replace ';extension_dir = "ext"', 'extension_dir = "ext"'
$phpIniContent = $phpIniContent -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite'
$phpIniContent = $phpIniContent -replace ';extension=sqlite3', 'extension=sqlite3'
$phpIniContent = $phpIniContent -replace ';extension=mbstring', 'extension=mbstring'
$phpIniContent = $phpIniContent -replace ';extension=fileinfo', 'extension=fileinfo'
$phpIniContent = $phpIniContent -replace ';extension=openssl', 'extension=openssl'
$phpIniContent = $phpIniContent -replace ';extension=zip', 'extension=zip'
Set-Content $phpIni $phpIniContent -Encoding ASCII

Push-Location $appPath
try {
    Write-Host "Instalando dependencias de producción"
    & $composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    if ($LASTEXITCODE -ne 0) {
        throw "Composer no pudo instalar las dependencias de producción."
    }

    & (Join-Path $phpPath "php.exe") artisan key:generate --force
    & (Join-Path $phpPath "php.exe") artisan config:cache
    & (Join-Path $phpPath "php.exe") artisan route:cache
    & (Join-Path $phpPath "php.exe") artisan view:cache

    $databasePath = Join-Path $appPath "database\database.sqlite"
    if (-not (Test-Path $databasePath)) {
        New-Item $databasePath -ItemType File -Force | Out-Null
    }

    & (Join-Path $phpPath "php.exe") artisan migrate --force
    if ($SeedDatabase) {
        & (Join-Path $phpPath "php.exe") artisan db:seed --force
    }
}
finally {
    Pop-Location
}

Write-Host "Publicando Pangea.exe"
if (Test-Path $launcherPublishPath) {
    Remove-Item $launcherPublishPath -Recurse -Force
}
New-Item $launcherPublishPath -ItemType Directory -Force | Out-Null
& dotnet publish (Join-Path $projectRoot "packaging\PangeaLauncher.csproj") -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -o $launcherPublishPath
if ($LASTEXITCODE -ne 0) {
    throw "No fue posible compilar Pangea.exe."
}
Copy-Item (Join-Path $launcherPublishPath "Pangea.exe") (Join-Path $appPath "Pangea.exe") -Force

$readme = @"
PANGEA - CONTROL DE ASISTENCIA

Uso:
1. Copia toda esta carpeta en el equipo Windows.
2. Ejecuta Pangea.exe.
3. Se abrirá automáticamente en el navegador.
4. Para cerrar Pangea, usa el icono de la bandeja del sistema y selecciona Cerrar.

La base de datos local se encuentra en database\database.sqlite.
No elimines la carpeta php, vendor, storage ni database.
"@
Set-Content (Join-Path $appPath "LEEME.txt") $readme -Encoding UTF8

Remove-Item $launcherPublishPath -Recurse -Force
Write-Host "Paquete listo: $appPath"
