<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class TaxonomyTermMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS TaxonomyTerm (
                taxonomyTermID INT PRIMARY KEY AUTO_INCREMENT,
                taxonomyTermName VARCHAR(255) NOT NULL,
                taxonomyTypeID INT,
                description TEXT,
                parentTaxonomyTerm INT,
                FOREIGN KEY (taxonomyTypeID) REFERENCES Taxonomy(taxonomyID)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS TaxonomyTerm;"
        ];
    }
}