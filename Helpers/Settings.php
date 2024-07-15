<?php

namespace Helpers;

use Exceptions\ReadAndParseEnvException;

class Settings{
    private const ENV_PATH = '.env';

    public static function env(string $pair): string{
        $config = parse_ini_file(dirname(__DIR__, 2) . '/' . self::ENV_PATH);
        if($config === false){
            throw new ReadAndParseEnvException();
        } 
        return $config[$pair];
    }
}