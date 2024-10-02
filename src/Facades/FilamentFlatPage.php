<?php

namespace Panakour\FilamentFlatPage\Facades;

use Illuminate\Support\Facades\Facade;
use Panakour\FilamentFlatPage\FlatFilePageManager;

class FilamentFlatPage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FlatFilePageManager::class;
    }
}
