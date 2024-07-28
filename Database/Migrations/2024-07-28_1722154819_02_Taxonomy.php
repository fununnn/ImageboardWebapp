<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class TaxonomyMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Taxonomy (
                taxonomyID INT PRIMARY KEY AUTO_INCREMENT,
                taxonomyName VARCHAR(255) NOT NULL,
                description TEXT
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Taxonomy;"
        ];
    }
}