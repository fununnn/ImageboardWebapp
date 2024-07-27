<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreateTagTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Tag (
                tagID INT PRIMARY KEY,
                tagName VARCHAR(255)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Tag;"
        ];
    }
}