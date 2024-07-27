<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreatePostTagTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS PostTag (
                postID INT,
                tagID INT,
                PRIMARY KEY (postID,
                tagID),
                FOREIGN KEY (postID) REFERENCES Post(postID),
                FOREIGN KEY (tagID) REFERENCES Tag(tagID)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS 08_PostTag;"
        ];
    }
}