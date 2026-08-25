$ErrorActionPreference = "Stop"
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..\")).Path
$portablePath = Join-Path $projectRoot "dist\Pangea"
$packagingPath = Join-Path $projectRoot "packaging"
$payloadPath = Join-Path $packagingPath "PangeaPayload.zip"
$installerPath = Join-Path $projectRoot "dist\Pangea-Setup.exe"
$publishPath = Join-Path $packagingPath ".setup-publish"

if (-not (Test-Path (Join-Path $portablePath "Pangea.exe"))) {
    throw "No existe dist\Pangea. Ejecuta primero build-portable.ps1."
}

if (Test-Path $payloadPath) {
    Remove-Item $payloadPath -Force
}
if (Test-Path $installerPath) { Remove-Item $installerPath -Force }
if (Test-Path $publishPath) { Remove-Item $publishPath -Recurse -Force }

Write-Host "Comprimiendo la distribución portable"
& tar.exe -a -c -f $payloadPath -C $portablePath .
if ($LASTEXITCODE -ne 0) {
    throw "No fue posible crear el paquete de la aplicación."
}

Write-Host "Compilando el instalador autocontenido"
& dotnet publish (Join-Path $packagingPath "PangeaSetup.csproj") -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -o $publishPath
if ($LASTEXITCODE -ne 0 -or -not (Test-Path (Join-Path $publishPath "Pangea-Setup.exe"))) {
    throw "No fue posible compilar Pangea-Setup.exe."
}
Copy-Item (Join-Path $publishPath "Pangea-Setup.exe") $installerPath -Force

Remove-Item $payloadPath -Force
Remove-Item $publishPath -Recurse -Force
Write-Host "Instalador listo: $installerPath"
