<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $this->initializeApplication();

        Window::open();
    }

    /**
     * Initialize the application with its default data.
     */
    private function initializeApplication(): void
    {
        $applicationStoragePath = storage_path('app');
        if (! is_dir($applicationStoragePath) && ! mkdir($applicationStoragePath, 0755, true) && ! is_dir($applicationStoragePath)) {
            throw new \RuntimeException("Unable to create application storage directory: {$applicationStoragePath}");
        }

        $markerFile = $applicationStoragePath.'/.initialized';

        // The application has already been initialized.
        if (file_exists($markerFile)) {
            return;
        }

        try {
            Artisan::call('migrate', [
                '--force' => true,
            ]);

            Artisan::call('db:seed', [
                '--force' => true,
            ]);

            file_put_contents($markerFile, now()->toIso8601String());

            Log::info('Application initialized successfully.');
        } catch (\Throwable $e) {
            Log::error('Application initialization failed.', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            throw $e;
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}
