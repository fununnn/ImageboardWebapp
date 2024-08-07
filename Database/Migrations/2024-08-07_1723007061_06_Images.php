<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class ImagesMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                url VARCHAR(255) NOT NULL UNIQUE,
                file_path VARCHAR(255) NOT NULL,
                upload_ip VARCHAR(45) NOT NULL,
                upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                last_accessed DATETIME DEFAULT CURRENT_TIMESTAMP,
                view_count INT DEFAULT 0,
                INDEX (upload_ip),
                INDEX (last_accessed)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Images;"
        ];
    }
}