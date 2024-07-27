<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreateCommentLikeTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS CommentLike (
                userID INT,
                commentID INT,
                PRIMARY KEY (userID,
                commentID)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS CommentLike;"
        ];
    }
}