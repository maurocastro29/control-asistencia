using System.Diagnostics;
using System.Net;
using System.Net.Sockets;
using System.Windows.Forms;

const string appName = "Pangea";
var appDirectory = AppContext.BaseDirectory;
var phpPath = Path.Combine(appDirectory, "php", "php.exe");
var artisanPath = Path.Combine(appDirectory, "artisan");

if (!File.Exists(phpPath) || !File.Exists(artisanPath))
{
    System.Windows.Forms.MessageBox.Show(
        "No se encontró la instalación de Pangea. Reinstala la aplicación o contacta al administrador.",
        appName,
        MessageBoxButtons.OK,
        MessageBoxIcon.Error);
    return;
}

var port = GetAvailablePort();
var server = new Process
{
    StartInfo = new ProcessStartInfo
    {
        FileName = phpPath,
        WorkingDirectory = appDirectory,
        Arguments = $"artisan serve --host=127.0.0.1 --port={port}",
        UseShellExecute = false,
        CreateNoWindow = true,
        WindowStyle = ProcessWindowStyle.Hidden,
        RedirectStandardOutput = true,
        RedirectStandardError = true,
    },
    EnableRaisingEvents = true,
};

try
{
    server.Start();
    await WaitForServerAsync(port, TimeSpan.FromSeconds(15));
    Process.Start(new ProcessStartInfo
    {
        FileName = $"http://127.0.0.1:{port}",
        UseShellExecute = true,
    });

    ApplicationConfiguration.Initialize();
    Application.Run(new TrayContext(server, appName, $"http://127.0.0.1:{port}"));
}
catch (Exception exception)
{
    System.Windows.Forms.MessageBox.Show(
        $"No fue posible iniciar {appName}.\n\n{exception.Message}",
        appName,
        MessageBoxButtons.OK,
        MessageBoxIcon.Error);
}
finally
{
    StopServer(server);
}

static int GetAvailablePort()
{
    var listener = new TcpListener(IPAddress.Loopback, 0);
    listener.Start();
    var port = ((IPEndPoint)listener.LocalEndpoint).Port;
    listener.Stop();
    return port;
}

static async Task WaitForServerAsync(int port, TimeSpan timeout)
{
    var deadline = DateTime.UtcNow.Add(timeout);

    while (DateTime.UtcNow < deadline)
    {
        try
        {
            using var client = new TcpClient();
            await client.ConnectAsync(IPAddress.Loopback, port);
            return;
        }
        catch (SocketException)
        {
            await Task.Delay(150);
        }
    }

    throw new InvalidOperationException("El servidor local no respondió dentro del tiempo esperado.");
}

static void StopServer(Process server)
{
    if (server.HasExited)
    {
        return;
    }

    try
    {
        server.Kill(entireProcessTree: true);
        server.WaitForExit(3000);
    }
    catch
    {
        // The process may already have exited during shutdown.
    }
}

internal sealed class TrayContext : ApplicationContext
{
    private readonly Process server;
    private readonly string applicationUrl;
    private readonly NotifyIcon trayIcon;

    public TrayContext(Process server, string appName, string applicationUrl)
    {
        this.server = server;
        this.applicationUrl = applicationUrl;
        trayIcon = new NotifyIcon
        {
            Icon = SystemIcons.Application,
            Text = appName,
            Visible = true,
            ContextMenuStrip = new ContextMenuStrip(),
        };
        trayIcon.ContextMenuStrip.Items.Add("Abrir Pangea", null, (_, _) => OpenApplication());
        trayIcon.ContextMenuStrip.Items.Add("Cerrar", null, (_, _) => ExitThread());
        trayIcon.DoubleClick += (_, _) => OpenApplication();
    }

    protected override void ExitThreadCore()
    {
        trayIcon.Visible = false;
        trayIcon.Dispose();
        base.ExitThreadCore();
    }

    private void OpenApplication()
    {
        Process.Start(new ProcessStartInfo
        {
            FileName = applicationUrl,
            UseShellExecute = true,
        });
    }
}
