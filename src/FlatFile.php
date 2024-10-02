<?php

namespace Panakour\FilamentFlatPage;

use Spatie\Valuestore\Valuestore;

class FlatFile
{
    protected Valuestore $store;

    protected array $translatableFields;

    protected string $path;

    public function __construct(string $fileName = '', array $translatableFields = [])
    {
        $this->path = storage_path('app/flat-pages/');
        if (! file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }
        $this->setStore($fileName);
        $this->translatableFields = $translatableFields;
    }

    public function setStore(string $fileName): self
    {
        if (! empty($fileName)) {
            $this->store = Valuestore::make($this->path . $fileName);
        }

        return $this;
    }

    public function all(): array
    {
        return $this->store->all();
    }

    public function put(array $data): void
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->translatableFields)) {
                $this->store->put($key, $this->formatTranslatable($value));
            } else {
                $this->store->put($key, $value);
            }
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->store->get($key, $default);
    }

    protected function formatTranslatable($value): array
    {
        if (! is_array($value)) {
            return [app()->getLocale() => $value];
        }

        return $value;
    }
}
