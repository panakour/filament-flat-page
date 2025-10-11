<?php

namespace Panakour\FilamentFlatPage\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Panakour\FilamentFlatPage\FilamentFlatPageServiceProvider;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Panakour\\FilamentFlatPage\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        // Ensure storage directory exists
        $storagePath = storage_path('app/flat-pages');
        if (! file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentFlatPageServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        // Set up Filament configuration
        config()->set('filament.default_filesystem_disk', 'local');

        // Configure flat page settings
        config()->set('filament-flat-page.locales', ['en', 'fr', 'el']);

        // Disable error handler interference in CI
        config()->set('app.debug', false);
    }

    protected function defineDatabaseMigrations()
    {
        // No migrations needed for flat file storage
    }

    protected function tearDown(): void
    {
        // Clean up any test files
        $testFiles = glob(storage_path('app/flat-pages/test-*.json'));
        foreach ($testFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }
}
