<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class PostsMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Posts (
                post_id INT AUTO_INCREMENT PRIMARY KEY,
                reply_to_id INT,
                subject VARCHAR(255),
                content TEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (reply_to_id) REFERENCES Posts(post_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Posts;"
        ];
    }
}