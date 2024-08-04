<?php
namespace Database;

use mysqli;
use Helpers\Settings;

class MySQLWrapper extends mysqli {
    public function __construct(
        ?string $hostname = null,
        ?string $username = null,
        ?string $password = null,
        ?string $database = null,
        ?int $port = null,
        ?string $socket = null
    ) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $hostname = $hostname ?? Settings::env('DATABASE_HOST');
        $username = $username ?? Settings::env('DATABASE_USER');
        $password = $password ?? Settings::env('DATABASE_USER_PASSWORD');
        $database = $database ?? Settings::env('DATABASE_NAME');
        
        error_log("Attempting to connect: Host: $hostname, User: $username, Database: $database, Password: " . ($password ? 'set' : 'not set'));

        try {
            parent::__construct($hostname, $username, $password, $database, $port, $socket);
        } catch (\mysqli_sql_exception $e) {
            error_log("MySQL connection error: " . $e->getMessage());
            throw $e;
        }

        if ($this->connect_errno) {
            error_log("Failed to connect: (" . $this->connect_errno . ") " . $this->connect_error);
            throw new \Exception("Failed to connect: (" . $this->connect_errno . ") " . $this->connect_error);
        }
        
        error_log("Connected successfully to database: " . $this->getDatabaseName());
    }

    public function getDatabaseName(): string {
        $result = $this->query("SELECT database() AS the_db");
        if (!$result) {
            throw new \Exception("Failed to get database name: " . $this->error);
        }
        return $result->fetch_row()[0];
    }
}
