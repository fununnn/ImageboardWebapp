<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreateCommentTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Comment (
                commentID INT PRIMARY KEY AUTO_INCREMENT,
                commentText VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
                userID INT,
                postID INT
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Comment;"
        ];
    }
}