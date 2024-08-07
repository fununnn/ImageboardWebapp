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
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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