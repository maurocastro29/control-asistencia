using System.IO.Compression;
using System.Diagnostics;
using System.Reflection;
using System.Windows.Forms;

ApplicationConfiguration.Initialize();

const string appName = "Pangea";
var defaultPath = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), appName);
using var dialog = new FolderBrowserDialog
{
    Description = "Selecciona dónde instalar Pangea",
    SelectedPath = defaultPath,
    ShowNewFolderButton = true,
};

if (dialog.ShowDialog() != DialogResult.OK)
{
    return;
}

var installPath = dialog.SelectedPath;
var tempPath = Path.Combine(Path.GetTempPath(), "PangeaSetup-" + Guid.NewGuid().ToString("N"));
var payloadPath = Path.Combine(tempPath, "payload.zip");
var extractPath = Path.Combine(tempPath, "payload");

try
{
    Directory.CreateDirectory(extractPath);
    using (var resource = Assembly.GetExecutingAssembly().GetManifestResourceStream("PangeaPayload.zip")
        ?? throw new InvalidOperationException("No se encontró el paquete de Pangea dentro del instalador."))
    using (var payload = File.Create(payloadPath))
    {
        resource.CopyTo(payload);
    }

    ZipFile.ExtractToDirectory(payloadPath, extractPath, overwriteFiles: true);

    var databasePath = Path.Combine(installPath, "database", "database.sqlite");
    if (File.Exists(databasePath))
    {
        var backupDirectory = Path.Combine(installPath, "database", "backups");
        Directory.CreateDirectory(backupDirectory);
        File.Copy(databasePath, Path.Combine(backupDirectory, $"database-{DateTime.Now:yyyyMMdd-HHmmss}.sqlite"), overwrite: true);
    }

    Directory.CreateDirectory(installPath);
    CopyDirectory(extractPath, installPath);
    CreateShortcut(installPath);

    MessageBox.Show(
        $"Pangea se instaló correctamente en:\n\n{installPath}\n\nSe creó un acceso directo en el escritorio.",
        appName,
        MessageBoxButtons.OK,
        MessageBoxIcon.Information);

    Process.Start(new ProcessStartInfo
    {
        FileName = Path.Combine(installPath, "Pangea.exe"),
        WorkingDirectory = installPath,
        UseShellExecute = true,
    });
}
catch (Exception exception)
{
    MessageBox.Show(
        $"No fue posible instalar Pangea:\n\n{exception.Message}",
        appName,
        MessageBoxButtons.OK,
        MessageBoxIcon.Error);
    Environment.ExitCode = 1;
}
finally
{
    try
    {
        if (Directory.Exists(tempPath))
        {
            Directory.Delete(tempPath, recursive: true);
        }
    }
    catch
    {
        // Temporary files can be removed by Windows after the installer exits.
    }
}

static void CopyDirectory(string sourceDirectory, string destinationDirectory)
{
    Directory.CreateDirectory(destinationDirectory);

    foreach (var file in Directory.GetFiles(sourceDirectory))
    {
        File.Copy(file, Path.Combine(destinationDirectory, Path.GetFileName(file)), overwrite: true);
    }

    foreach (var directory in Directory.GetDirectories(sourceDirectory))
    {
        CopyDirectory(directory, Path.Combine(destinationDirectory, Path.GetFileName(directory)));
    }
}

static void CreateShortcut(string installPath)
{
    var desktopPath = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
    var shortcutPath = Path.Combine(desktopPath, "Pangea.lnk");
    var shellType = Type.GetTypeFromProgID("WScript.Shell")
        ?? throw new InvalidOperationException("No fue posible crear el acceso directo.");
    dynamic shell = Activator.CreateInstance(shellType)!;
    dynamic shortcut = shell.CreateShortcut(shortcutPath);
    shortcut.TargetPath = Path.Combine(installPath, "Pangea.exe");
    shortcut.WorkingDirectory = installPath;
    shortcut.Description = "Pangea - Control de asistencia";
    shortcut.IconLocation = Path.Combine(installPath, "Pangea.exe");
    shortcut.Save();
}
