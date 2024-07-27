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
                commentID),
                FOREIGN KEY (userID) REFERENCES User(userID),
                FOREIGN KEY (commentID) REFERENCES Comment(commentID)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS 07_CommentLike;"
        ];
    }
}