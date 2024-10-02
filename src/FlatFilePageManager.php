<?php

namespace Panakour\FilamentFlatPage;

class FlatFilePageManager
{
    public function __construct(private readonly FlatFile $flatFile) {}

    public function get(string $fileName, string $key, ?string $locale = null)
    {
        $data = $this->flatFile->setStore($fileName)->all();

        if (! isset($data[$key])) {
            return null;
        }

        if (is_array($data[$key])) {
            $locale = $locale ?? app()->getLocale();

            return $data[$key][$locale] ?? null;
        }

        return $data[$key];
    }

    public function all(string $fileName)
    {
        return $this->flatFile->setStore($fileName)->all();
    }
}
