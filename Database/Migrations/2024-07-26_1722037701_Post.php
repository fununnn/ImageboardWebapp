<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreatePostTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Post (
                postID INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255),
                content TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
                userID INT,
                CategoryID INT
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Post;"
        ];
    }
}