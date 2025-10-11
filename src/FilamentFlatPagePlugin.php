<?php

declare(strict_types=1);

namespace Panakour\FilamentFlatPage;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class FilamentFlatPagePlugin implements Plugin
{
    public static function make(): static
    {
        return app(self::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-flat-page';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
