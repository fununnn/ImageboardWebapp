<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class PostTaxonomyMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS PostTaxonomy (
                postTaxonomyID INT PRIMARY KEY AUTO_INCREMENT,
                postID INT,
                taxonomyID INT,
                FOREIGN KEY (postID) REFERENCES Post(postID),
                FOREIGN KEY (taxonomyID) REFERENCES Taxonomy(taxonomyID)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS PostTaxonomy;"
        ];
    }
}