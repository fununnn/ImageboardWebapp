<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class PostMigration implements SchemaMigration
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
                CategoryID INT,
                FOREIGN KEY (userID) REFERENCES User(userID),
                FOREIGN KEY (CategoryID) REFERENCES TaxonomyTerm(taxonomyTermID)
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