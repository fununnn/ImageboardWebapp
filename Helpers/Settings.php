<?php
namespace Helpers;

use Exceptions\ReadAndParseEnvException;

class Settings
{
    private static $env = [];

    public static function env($key = null, $default = null)
    {
        if (empty(self::$env)) {
            $path = __DIR__ . '/../.env';
            error_log("Reading .env file from: $path");

            if (!file_exists($path)) {
                error_log(".env file not found at: $path");
                throw new ReadAndParseEnvException(".env file not found at: $path");
            }

            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                list($name, $value) = explode('=', $line, 2);
                self::$env[trim($name)] = trim($value, '"');
                error_log("Loaded env variable: $name = " . ($name === 'DATABASE_USER_PASSWORD' ? '********' : self::$env[trim($name)]));
            }
        }

        if ($key === null) {
            return self::$env;
        }

        $value = self::$env[$key] ?? $default;
        error_log("Retrieving env variable: $key = " . ($key === 'DATABASE_USER_PASSWORD' ? '********' : $value));
        return $value;
    }
}
