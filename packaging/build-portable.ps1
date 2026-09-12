param(
    [string]$OutputDirectory = "dist\Pangea"
)

$ErrorActionPreference = "Stop"
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..\")).Path
$outputPath = Join-Path $projectRoot $OutputDirectory
$electronRoot = Join-Path $projectRoot "nativephp\electron"
$electronBuildPath = Join-Path $electronRoot "dist\win-unpacked"
$nativeBuildAppPath = Join-Path $projectRoot "vendor\nativephp\desktop\resources\build\app"

Write-Host "Construyendo Pangea con NativePHP Desktop"
Push-Location $projectRoot
try {
    php artisan optimize:clear
    if ($LASTEXITCODE -ne 0) {
        throw "No fue posible limpiar la configuración de Laravel."
    }

    if (Test-Path $nativeBuildAppPath) {
        $cleanupPath = Join-Path $env:TEMP ("pangea-native-build-cleanup-" + [guid]::NewGuid().ToString("N"))
        New-Item $cleanupPath -ItemType Directory -Force | Out-Null
        & robocopy.exe $cleanupPath $nativeBuildAppPath /MIR /R:2 /W:1 /NFL /NDL /NJH /NJS /NP
        if ($LASTEXITCODE -gt 7) {
            throw "No fue posible limpiar el recurso temporal de NativePHP. Robocopy devolvió el código $LASTEXITCODE."
        }
        Remove-Item $nativeBuildAppPath -Recurse -Force -ErrorAction SilentlyContinue
        Remove-Item $cleanupPath -Recurse -Force -ErrorAction SilentlyContinue
    }

    php artisan native:build win x64
    if ($LASTEXITCODE -ne 0) {
        throw "NativePHP no pudo construir la aplicación Electron."
    }
}
finally {
    Pop-Location
}

if (-not (Test-Path (Join-Path $electronBuildPath "pangea.exe"))) {
    throw "No se encontró el ejecutable Electron en $electronBuildPath."
}

Get-ChildItem (Join-Path $projectRoot "dist") -Filter "Pangea-*-setup.exe" -File -ErrorAction SilentlyContinue |
    Remove-Item -Force
Get-ChildItem (Join-Path $projectRoot "dist") -Filter "Pangea-*-setup.exe.blockmap" -File -ErrorAction SilentlyContinue |
    Remove-Item -Force

if (Test-Path $outputPath) {
    Remove-Item $outputPath -Recurse -Force
}

New-Item $outputPath -ItemType Directory -Force | Out-Null
$robocopySource = (Resolve-Path $electronBuildPath).Path
$robocopyTarget = (Resolve-Path $outputPath).Path
& robocopy.exe $robocopySource $robocopyTarget /E /COPY:DAT /DCOPY:DAT /R:2 /W:1 /NFL /NDL /NJH /NJS /NP
$robocopyExitCode = $LASTEXITCODE
if ($robocopyExitCode -gt 7) {
    throw "No fue posible copiar el paquete portable. Robocopy devolvió el código $robocopyExitCode."
}
$LASTEXITCODE = 0

$readme = @"
PANGEA - CONTROL DE ASISTENCIA

Uso:
1. Ejecuta Pangea.exe.
2. La aplicación se abrirá en su ventana de escritorio.
3. Para cerrar Pangea, utiliza la opción de salida de la aplicación.

La aplicación utiliza la base de datos administrada por NativePHP.
No elimines la carpeta resources ni los archivos de la aplicación.
"@
Set-Content (Join-Path $outputPath "LEEME.txt") $readme -Encoding UTF8

Write-Host "Paquete NativePHP listo: $outputPath"
