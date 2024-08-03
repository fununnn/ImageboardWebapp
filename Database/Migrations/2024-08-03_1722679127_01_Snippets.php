<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class SnippetsMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Snippets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                content TEXT NOT NULL,
                language VARCHAR(50),
                expiration DATETIME,
                unique_url VARCHAR(255) UNIQUE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Snippets;"
        ];
    }
}