<?php

namespace Panakour\FilamentFlatPage\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Panakour\FilamentFlatPage\FlatFile;

abstract class FlatPage extends Page
{
    protected static string $view = 'filament-flat-page::flat-page';

    public ?array $data = [];

    protected FlatFile $flatFile;

    public string $activeLocale;

    public $switchLocale = null;

    abstract public function getFileName(): string;

    public function __construct()
    {
        $this->flatFile = new FlatFile($this->getFileName(), $this->getTranslatableFields());
        $this->activeLocale = app()->getLocale();
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $data = $this->flatFile->all();
        $translatableFields = $this->getTranslatableFields();

        foreach ($translatableFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = $data[$field][$this->activeLocale] ?? '';
            }
        }

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema($this->getFlatFilePageForm());
    }

    abstract protected function getFlatFilePageForm(): array;

    protected function getLocaleFromSpatieIfAvailable(): array
    {
        $locales = [];

        if (! filament()->hasPlugin('spatie-laravel-translatable')) {
            return $locales;
        }

        $plugin = filament('spatie-laravel-translatable');
        foreach ($plugin->getDefaultLocales() as $locale) {
            $locales[$locale] = $plugin->getLocaleLabel($locale) ?? $locale;
        }

        return $locales;
    }

    protected function getLocaleOptions(): array
    {
        $locales = $this->getLocaleFromSpatieIfAvailable();
        if (! empty($locales)) {
            return $locales;
        }
        $locales = $this->getTranslatableLocales();

        return array_combine($locales, array_map('strtoupper', $locales));
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('Save')
                ->label(__('filament-flat-page::flat-page.save'))
                ->color('primary')
                ->submit('Update'),
        ];
    }

    protected function getHeaderActions(): array
    {
        if (! $this->hasTranslatableFields()) {
            return [];
        }

        return [
            Actions\SelectAction::make('switchLocale')
                ->label(fn () => strtoupper($this->activeLocale))
                ->options($this->getLocaleOptions()),
        ];
    }

    public function updatedSwitchLocale()
    {
        $this->activeLocale = $this->switchLocale;
        $this->fillForm();
    }

    public function update()
    {
        $state = $this->form->getState();
        $translatableFields = $this->getTranslatableFields();

        foreach ($translatableFields as $field) {
            if (isset($state[$field])) {
                $existingTranslations = $this->flatFile->get($field) ?? [];
                $existingTranslations[$this->activeLocale] = $state[$field];
                $state[$field] = $existingTranslations;
            }
        }

        $this->flatFile->put($state);

        Notification::make()
            ->title('Page updated successfully')
            ->success()
            ->send();

        return redirect()->back();
    }

    public static function getTranslatableLocales(): array
    {
        return config('filament-flat-page.locales', ['en']);
    }

    protected function getTranslatableFields(): array
    {
        return [];
    }

    protected function hasTranslatableFields(): bool
    {
        return ! empty($this->getTranslatableFields());
    }
}
