# Filament Flat Page Plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/panakour/filament-flat-page.svg?style=flat-square)](https://packagist.org/packages/panakour/filament-flat-page)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/panakour/filament-flat-page/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/panakour/filament-flat-page/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/panakour/filament-flat-page/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/panakour/filament-flat-page/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/panakour/filament-flat-page.svg?style=flat-square)](https://packagist.org/packages/panakour/filament-flat-page)

FilamentFlatPage is a plugin for [Filament](https://filamentphp.com/) that allows you to easily create and manage flat file pages with support for translations. It provides a simple way to add configurable, translatable pages to your Filament admin panel without the need for a database.

## Features

- Easily create flat file pages with customizable forms
- Requires no database; data is stored in JSON files
- Built-in multilingual support for creating translatable content
- Seamless integration with Filament admin panel
- Language switcher in the admin page header (integrates with Spatie Translatable, if available)

## Installation

You can install the package via composer:

```bash
composer require panakour/filament-flat-page
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-flat-page-config"
```

This will create a `config/filament-flat-page.php` file where you can set your preferred options:

```php
return [
    'locales' => ['en', 'fr', 'el'],
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-flat-page-views"
```

## Usage

1. Create a new page that extends `FlatPage`:

```php
use Panakour\FilamentFlatPage\Pages\FlatPage;

class SettingsPage extends FlatPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Settings';

    public function getFileName(): string
    {
        return 'settings.json';
    }

    protected function getTranslatableFields(): array
    {
        return ['site_name', 'site_description'];
    }

    protected function getFlatFilePageForm(): array
    {
        return [
            // Your form schema here
        ];
    }
}
```

2. Define your form schema in the `getFlatFilePageForm()` method.

3. Specify the translatable fields in the `getTranslatableFields()` method.

4. Set the filename for storing the flat file data in the `getFileName()` method.

## Using the FlatPage Facade

You can easily access your flat file data from anywhere in your application using the `FlatPage` facade:

```php
use \Panakour\FilamentFlatPage\Facades\FilamentFlatPage;

// Get a non-translatable field
$contactEmail = FilamentFlatPage::get('settings.json', 'contact_email');

// Get a translatable field (uses current locale)
$siteName = FilamentFlatPage::get('settings.json', 'site_name');

// Get a translatable field in a specific locale
$siteNameGreek = FilamentFlatPage::get('settings.json', 'site_name', 'el');

// Get all
$allSettings = FilamentFlatPage::all('settings.json');

```

In Blade templates, you can use it like this:

```blade
<h1>{{ \Panakour\FilamentFlatPage\Facades\FilamentFlatPage::get("page.json", "title") }}</h1>
<p>Contact Email: {{ Panakour\FilamentFlatPage\Facades\FilamentFlatPage::get('settings.json', 'contact_email') }}</p>
<p>Site Name: {{ Panakour\FilamentFlatPage\Facades\FilamentFlatPage::get('settings.json', 'site_name', 'en') }}</p>
```

## Testing
There is a [FlatPageTest.php](tests/FlatPageTest.php) file where you can run to check if tests are passing
```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Panagiotis Koursaris](https://github.com/panakour)
- [All Contributors](../../contributors)

## Show your support

Give a ⭐️ if you like this project or find it helpful!
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
