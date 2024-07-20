<?php
namespace Helpers;
use Exceptions\ReadAndParseEnvException;

class Settings {
    private const ENV_PATH = '.env';

    public static function env(string $pair): string {
        $envPath = dirname(__DIR__) . '/' . self::ENV_PATH;
        echo "Attempting to read .env file from: $envPath\n";  // デバッグ情報

        if (!file_exists($envPath)) {
            throw new ReadAndParseEnvException(".env file not found at: $envPath");
        }

        $config = parse_ini_file($envPath);
        if ($config === false) {
            throw new ReadAndParseEnvException("Failed to read or parse .env file");
        }
        if (!array_key_exists($pair, $config)) {
            throw new ReadAndParseEnvException("Environment variable '$pair' not found");
        }
        return $config[$pair];
    }
}