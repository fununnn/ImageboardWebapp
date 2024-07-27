<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreatePostLikeTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS PostLike (
                userID INT,
                postID INT,
                PRIMARY KEY (userID,
                postID),
                FOREIGN KEY (userID) REFERENCES User(userID),
                FOREIGN KEY (postID) REFERENCES Post(postID)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS 06_PostLike;"
        ];
    }
}