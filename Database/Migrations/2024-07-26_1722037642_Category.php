<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreateCategoryTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Category (
                category INT PRIMARY KEY,
                categoryName VARCHAR(255)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Category;"
        ];
    }
}