<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class TaxonomyTerm implements SchemaMigration
{
    public function up(): array
    {
        return [
            "taxonomyTermID INT PRIMARY KEY AUTO_INCREMENT,
                taxonomyTermName VARCHAR(255) NOT NULL,
                taxonomyTypeID INT,
                description TEXT,
                parentTaxonomyTerm INT,
                FOREIGN KEY (taxonomyTypeID) REFERENCES Taxonomy(taxonomyID),
        ];
    }

    public function down(): array
    {
        // Implement the down migration if necessary
        return [
            // Add down migration SQL here
        ];
    }
}