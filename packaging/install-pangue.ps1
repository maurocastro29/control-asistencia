Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

$ErrorActionPreference = "Stop"
$payloadArchive = Join-Path $PSScriptRoot "PanguePayload.zip"
$defaultPath = Join-Path $env:LOCALAPPDATA "Pangue"

$dialog = New-Object System.Windows.Forms.FolderBrowserDialog
$dialog.Description = "Selecciona dónde instalar Pangue"
$dialog.SelectedPath = $defaultPath
$dialog.ShowNewFolderButton = $true

if ($dialog.ShowDialog() -ne [System.Windows.Forms.DialogResult]::OK) {
    exit 0
}

$installPath = $dialog.SelectedPath
$tempPath = Join-Path ([System.IO.Path]::GetTempPath()) ("PangueSetup-" + [guid]::NewGuid().ToString("N"))
$payloadPath = Join-Path $tempPath "payload"

try {
    New-Item $payloadPath -ItemType Directory -Force | Out-Null
    Expand-Archive -Path $payloadArchive -DestinationPath $payloadPath -Force

    if (Test-Path (Join-Path $installPath "database\database.sqlite")) {
        $backupPath = Join-Path $installPath "database\backups"
        New-Item $backupPath -ItemType Directory -Force | Out-Null
        Copy-Item (Join-Path $installPath "database\database.sqlite") (Join-Path $backupPath ("database-" + (Get-Date -Format "yyyyMMdd-HHmmss") + ".sqlite")) -Force
    }

    New-Item $installPath -ItemType Directory -Force | Out-Null
    Copy-Item (Join-Path $payloadPath "*") $installPath -Recurse -Force

    $shortcutPath = Join-Path ([Environment]::GetFolderPath("Desktop")) "Pangue.lnk"
    $shell = New-Object -ComObject WScript.Shell
    $shortcut = $shell.CreateShortcut($shortcutPath)
    $shortcut.TargetPath = Join-Path $installPath "Pangue.exe"
    $shortcut.WorkingDirectory = $installPath
    $shortcut.Description = "Pangue - Control de asistencia"
    $shortcut.IconLocation = Join-Path $installPath "Pangue.exe"
    $shortcut.Save()

    [System.Windows.Forms.MessageBox]::Show(
        "Pangue se instaló correctamente en:`n`n$installPath`n`nSe creó un acceso directo en el escritorio.",
        "Pangue",
        [System.Windows.Forms.MessageBoxButtons]::OK,
        [System.Windows.Forms.MessageBoxIcon]::Information
    )

    Start-Process (Join-Path $installPath "Pangue.exe")
}
catch {
    [System.Windows.Forms.MessageBox]::Show(
        "No fue posible instalar Pangue:`n`n$($_.Exception.Message)",
        "Pangue",
        [System.Windows.Forms.MessageBoxButtons]::OK,
        [System.Windows.Forms.MessageBoxIcon]::Error
    )
    exit 1
}
finally {
    if (Test-Path $tempPath) {
        Remove-Item $tempPath -Recurse -Force -ErrorAction SilentlyContinue
    }
}
