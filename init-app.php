<?php
spl_autoload_register(function($className) {
    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Database\MySQLWrapper;

try {
    $opts = getopt('', ['migrate']);
    if (isset($opts['migrate'])) {
        printf("Database migration started.\n");
        include(__DIR__ . '/Database/setup.php');
        printf("Database migration completed.\n");
    }

    $mysqli = new MySQLWrapper();
    $charset = $mysqli->get_charset();
    if ($charset === null) {
        throw new \Exception('Charset could not be read');
    }
    printf("%s's charset: %s%s", $mysqli->getDatabaseName(), $charset->charset, PHP_EOL);
    printf("collation: %s%s", $charset->collation, PHP_EOL);
} catch (\Exception $e) {
    printf("An error occurred: %s%s", $e->getMessage(), PHP_EOL);
} finally {
    if (isset($mysqli) && $mysqli instanceof MySQLWrapper) {
        $mysqli->close();
    }
}