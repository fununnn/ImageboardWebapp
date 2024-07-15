<?php
use Database\MySQLWrapper;

echo "Starting database setup...\n";

$mysqli = new MySQLWrapper();
echo "Attempting to create cars table...\n";

$sqlFiles = glob(__DIR__ . '/Examples/*.sql');

foreach ($sqlFiles as $sqlFile){
    $filename = basename($sqlFile);
    echo "Attempting to execute $fileName...\n";
    $sql = file_get_contents($sqlFile);
    $result = $mysqli->query($sql);
    if ($result === false) {throw new Exception('Could not execute query: ' . $mysqli->error);
    }else {
        echo "Successfully created or updated cars table\n";
    }
}

$mysqli->close();
?>