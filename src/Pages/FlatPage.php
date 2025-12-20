<?php

declare(strict_types=1);

namespace Panakour\FilamentFlatPage\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Panakour\FilamentFlatPage\FlatFile;

/**
 * @property-read Schema $form
 */
abstract class FlatPage extends Page
{
    public ?array $data = [];

    public string $activeLocale;

    protected string $view = 'filament-flat-page::flat-page';

    protected FlatFile $flatFile;

    public function __construct()
    {
        $this->flatFile = new FlatFile($this->getFileName(), $this->getTranslatableFields());
        $this->activeLocale = $this->getDefaultLocale();
    }

    abstract public function getFileName(): string;

    abstract protected function getFlatFilePageForm(): array;

    public static function getTranslatableLocales(): array
    {
        return config('filament-flat-page.locales', ['en']);
    }

    final public function mount(): void
    {
        $this->fillForm();
    }

    final public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components($this->applyTranslatable($this->getFlatFilePageForm()));
    }

    final public function update()
    {
        $state = $this->form->getState();
        $translatableFields = $this->getTranslatableFields();
        $locale = $this->activeLocale;

        foreach ($translatableFields as $field) {
            if (isset($state[$field])) {
                $existing = $this->flatFile->get($field) ?? [];
                $value = $state[$field];
                $translations = is_array($existing) ? $existing : [];
                if (is_array($value)) {
                    $translations = [...$translations, ...$value];
                } else {
                    $translations[$locale] = $value;
                }
                $state[$field] = $translations;
            }
        }

        $this->flatFile->put($state);

        Notification::make()
            ->title(__('filament-flat-page::flat-page.updated_successfully'))
            ->success()
            ->send();

        return redirect()->back();
    }

    protected function fillForm(): void
    {
        $data = $this->flatFile->all();
        $translatableFields = $this->getTranslatableFields();
        $defaultLocale = $this->getDefaultLocale();

        foreach ($translatableFields as $field) {
            if (! isset($data[$field])) {
                continue;
            }

            if (! is_array($data[$field])) {
                $data[$field] = [$defaultLocale => $data[$field]];
            }
        }

        $this->form->fill($data);
    }

    protected function applyTranslatable(array $components): array
    {
        if (! $this->hasTranslatableFields()) {
            return $components;
        }

        return collect($components)
            ->map(fn (Component $component) => $this->applyTranslatableToComponent($component))
            ->all();
    }

    protected function applyTranslatableToComponent(Component $component): Component
    {
        if ($component instanceof Field && in_array($component->getName(), $this->getTranslatableFields(), true)) {
            try {
                $component = $component->translatable();
            } catch (\BadMethodCallException) {
                // translatable macro not available; leave as-is
            }
        }

        if (method_exists($component, 'getDefaultChildComponents')) {
            /** @var array<Component> $children */
            $children = $component->getDefaultChildComponents() ?? [];
            if (! empty($children)) {
                $children = array_map(
                    fn (Component $child) => $this->applyTranslatableToComponent($child),
                    $children,
                );
                // Re-assign mapped children without forcing schema/container creation.
                if (method_exists($component, 'childComponents')) {
                    $component->childComponents($children);
                }
            }
        }

        return $component;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Save')
                ->label(__('filament-flat-page::flat-page.save'))
                ->color('primary')
                ->submit('Update'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTranslatableFields(): array
    {
        return [];
    }

    protected function hasTranslatableFields(): bool
    {
        return ! empty($this->getTranslatableFields());
    }

    protected function getDefaultLocale(): string
    {
        $locales = static::getTranslatableLocales();

        return reset($locales) ?: app()->getLocale();
    }
}
