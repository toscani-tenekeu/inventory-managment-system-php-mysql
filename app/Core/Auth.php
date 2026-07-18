<?php

declare(strict_types=1);

namespace IMS\Core;

use PDO;

final class Auth
{
    private ?array $cachedUser = null;
    private bool $resolved = false;

    public function __construct(private readonly PDO $pdo)
    {
    }

    public function user(): ?array
    {
        if ($this->resolved) {
            return $this->cachedUser;
        }
        $this->resolved = true;

        $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            return null;
        }

        $statement = $this->pdo->prepare(
            'SELECT id, name, email, role, locale, theme, active, last_login_at
             FROM users WHERE id = :id AND active = 1 LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        if (!$user) {
            unset($_SESSION['user_id']);
            return null;
        }

        return $this->cachedUser = $user;
    }

    public function attempt(string $email, string $password): bool
    {
        $this->guardRateLimit();
        $email = mb_strtolower(trim($email));

        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email AND active = 1 LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $this->recordFailure();
            return false;
        }

        if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
            $update = $this->pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
            $update->execute(['hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $user['id']]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['locale'] = in_array($user['locale'], ['fr', 'en'], true) ? $user['locale'] : 'fr';
        unset($_SESSION['_login_attempts']);

        $this->pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')
            ->execute(['id' => $user['id']]);
        $this->resolved = false;
        $this->cachedUser = null;
        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['_csrf']);
        $this->resolved = true;
        $this->cachedUser = null;
        session_regenerate_id(true);
    }

    public function requireLogin(): array
    {
        $user = $this->user();
        if (!$user) {
            throw new AuthorizationException('authentication_required');
        }
        return $user;
    }

    public function isAdmin(): bool
    {
        return ($this->user()['role'] ?? null) === 'admin';
    }

    public function authorize(string $permission): void
    {
        $user = $this->requireLogin();
        $adminOnly = ['users.manage', 'catalog.archive', 'movements.reverse', 'audit.view'];
        if (in_array($permission, $adminOnly, true) && $user['role'] !== 'admin') {
            throw new AuthorizationException('forbidden');
        }
    }

    private function guardRateLimit(): void
    {
        $attempts = $_SESSION['_login_attempts'] ?? ['count' => 0, 'started_at' => time()];
        if ((time() - (int) $attempts['started_at']) > 900) {
            $_SESSION['_login_attempts'] = ['count' => 0, 'started_at' => time()];
            return;
        }
        if ((int) $attempts['count'] >= 8) {
            throw new ValidationException('too_many_login_attempts');
        }
    }

    private function recordFailure(): void
    {
        $attempts = $_SESSION['_login_attempts'] ?? ['count' => 0, 'started_at' => time()];
        if ((time() - (int) $attempts['started_at']) > 900) {
            $attempts = ['count' => 0, 'started_at' => time()];
        }
        $attempts['count']++;
        $_SESSION['_login_attempts'] = $attempts;
    }
}
