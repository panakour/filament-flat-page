<?php

namespace Panakour\FilamentFlatPage;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentFlatPageServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-flat-page';

    public static string $viewNamespace = 'filament-flat-page';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('panakour/filament-flat-page');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {
        $this->app->singleton(FlatFilePageManager::class, function () {
            return new FlatFilePageManager(new FlatFile());
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'panakour/filament-flat-page';
    }

}
