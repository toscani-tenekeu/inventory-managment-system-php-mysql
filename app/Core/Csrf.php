<?php

declare(strict_types=1);

namespace IMS\Core;

final class Csrf
{
    public function token(): string
    {
        if (!isset($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public function validate(?string $token): bool
    {
        return is_string($token) && hash_equals($this->token(), $token);
    }

    public function regenerate(): void
    {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
}
