<?php

use Filament\Forms\Components\TextInput;
use Panakour\FilamentFlatPage\FlatFile;
use Panakour\FilamentFlatPage\FlatFilePageManager;

function createTestPage()
{
    return new class extends \Panakour\FilamentFlatPage\Pages\FlatPage
    {
        public function getFileName(): string
        {
            return 'test-page.json';
        }

        protected function getFlatFilePageForm(): array
        {
            return [
                TextInput::make('title')->required(),
                TextInput::make('content')->required(),
                TextInput::make('translatable_text'),
            ];
        }

        protected function getTranslatableFields(): array
        {
            return ['translatable_text'];
        }

        public static function getTranslatableLocales(): array
        {
            return ['en', 'fr', 'el'];
        }
    };
}

it('can create a page, save it, and retrieve its contents', function () {
    $page = createTestPage();

    $page->form->fill([
        'title' => 'Test Title',
        'content' => 'Test Content',
    ]);

    $page->update();

    $filePath = storage_path('app/flat-pages/test-page.json');
    expect(file_exists($filePath))->toBeTrue();

    $manager = new FlatFilePageManager(new FlatFile);
    $savedTitle = $manager->get('test-page.json', 'title');
    $savedContent = $manager->get('test-page.json', 'content');

    expect($savedTitle)->toBe('Test Title');
    expect($savedContent)->toBe('Test Content');

    unlink($filePath);
});

it('can handle updates to existing pages', function () {
    $page = createTestPage();

    $page->form->fill([
        'title' => 'Initial Title',
        'content' => 'Initial Content',
    ]);
    $page->update();

    $page->form->fill([
        'title' => 'Updated Title',
        'content' => 'Updated Content',
    ]);

    $page->update();

    $manager = new FlatFilePageManager(new FlatFile);
    $updatedTitle = $manager->get('test-page.json', 'title');
    $updatedContent = $manager->get('test-page.json', 'content');

    expect($updatedTitle)->toBe('Updated Title');
    expect($updatedContent)->toBe('Updated Content');

    unlink(storage_path('app/flat-pages/test-page.json'));
});

it('can create a page with multilingual fields, save it, and retrieve its contents', function () {
    $page = createTestPage();

    $page->activeLocale = 'en';

    $page->form->fill([
        'title' => 'English Title',
        'content' => 'English Content',
        'translatable_text' => 'English Translatable Text',
    ]);
    $page->update();

    $page->activeLocale = 'fr';
    $page->form->fill([
        'title' => 'Titre Français',
        'content' => 'Contenu Français',
        'translatable_text' => 'Texte Traduisible Français',
    ]);
    $page->update();

    $page->activeLocale = 'el';
    $page->form->fill([
        'title' => 'Ελληνικός Τίτλος',
        'content' => 'Ελληνικό Περιεχόμενο',
        'translatable_text' => 'Ελληνικό Μεταφράσιμο Κείμενο',
    ]);
    $page->update();

    $filePath = storage_path('app/flat-pages/test-page.json');
    expect(file_exists($filePath))->toBeTrue();

    $manager = new FlatFilePageManager(new FlatFile);

    expect($manager->get('test-page.json', 'translatable_text', 'en'))->toBe('English Translatable Text');
    expect($manager->get('test-page.json', 'translatable_text', 'fr'))->toBe('Texte Traduisible Français');
    expect($manager->get('test-page.json', 'translatable_text', 'el'))->toBe('Ελληνικό Μεταφράσιμο Κείμενο');

    unlink($filePath);
});

it('returns null for non-existent translations', function () {
    $page = createTestPage();

    $page->activeLocale = 'en';
    $page->form->fill(['title' => 'English Title', 'content' => 'English Content', 'translatable_text' => 'English Text']);
    $page->update();

    $manager = new FlatFilePageManager(new FlatFile);

    expect($manager->get('test-page.json', 'translatable_text', 'en'))->toBe('English Text');

    expect($manager->get('test-page.json', 'translatable_text', 'fr'))->toBeNull();
    expect($manager->get('test-page.json', 'translatable_text', 'el'))->toBeNull();

    unlink(storage_path('app/flat-pages/test-page.json'));
});
