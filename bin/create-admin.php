<?php

declare(strict_types=1);

use IMS\Core\Database;

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/helpers.php';
load_env(BASE_PATH . '/.env');

spl_autoload_register(static function (string $class): void {
    $prefix = 'IMS\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

try {
    $pdo = Database::connect([
        'host' => env_string('DB_HOST', '127.0.0.1'),
        'port' => env_int('DB_PORT', 3306),
        'name' => env_string('DB_NAME', 'ims'),
        'user' => env_string('DB_USER', 'ims'),
        'pass' => env_string('DB_PASS', ''),
    ]);

    $name = trim((string) readline('Nom de l’administrateur : '));
    $email = mb_strtolower(trim((string) readline('Adresse e-mail : ')));
    $password = hidden_prompt('Mot de passe : ');
    $confirmation = hidden_prompt('Confirmez le mot de passe : ');

    if ($name === '' || mb_strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Nom ou adresse e-mail invalide.');
    }
    if ($password !== $confirmation) {
        throw new RuntimeException('Les mots de passe ne correspondent pas.');
    }
    if (
        strlen($password) < 10
        || preg_match('/[a-z]/', $password) !== 1
        || preg_match('/[A-Z]/', $password) !== 1
        || preg_match('/[0-9]/', $password) !== 1
    ) {
        throw new RuntimeException('Utilisez au moins 10 caractères avec majuscule, minuscule et chiffre.');
    }

    $statement = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash, role, locale)
         VALUES (:name, :email, :password_hash, 'admin', :locale)"
    );
    $statement->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'locale' => in_array(env_string('APP_LOCALE', 'fr'), ['fr', 'en'], true)
            ? env_string('APP_LOCALE', 'fr')
            : 'fr',
    ]);

    fwrite(STDOUT, PHP_EOL . 'Administrateur créé avec succès.' . PHP_EOL);
} catch (Throwable $exception) {
    fwrite(STDERR, PHP_EOL . 'Erreur : ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

function hidden_prompt(string $label): string
{
    if (DIRECTORY_SEPARATOR === '\\') {
        return trim((string) readline($label));
    }

    fwrite(STDOUT, $label);
    $settings = trim((string) shell_exec('stty -g'));
    if ($settings !== '') {
        shell_exec('stty -echo');
    }
    $value = trim((string) fgets(STDIN));
    if ($settings !== '') {
        shell_exec('stty ' . escapeshellarg($settings));
    }
    fwrite(STDOUT, PHP_EOL);
    return $value;
}
