<?php

declare(strict_types=1);

namespace IMS\Core;

final class Translator
{
    private array $messages;

    public function __construct(private readonly string $directory, private readonly string $locale)
    {
        $file = rtrim($directory, '/') . '/' . $locale . '.php';
        $this->messages = is_file($file) ? (array) require $file : [];
    }

    public function get(string $key, array $replace = []): string
    {
        $message = (string) ($this->messages[$key] ?? $key);
        foreach ($replace as $name => $value) {
            $message = str_replace(':' . $name, (string) $value, $message);
        }
        return $message;
    }

    public function locale(): string
    {
        return $this->locale;
    }
}
