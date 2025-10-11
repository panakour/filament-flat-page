<?php

declare(strict_types=1);

namespace Panakour\FilamentFlatPage\Facades;

use Illuminate\Support\Facades\Facade;
use Panakour\FilamentFlatPage\FlatFilePageManager;

final class FilamentFlatPage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FlatFilePageManager::class;
    }
}
