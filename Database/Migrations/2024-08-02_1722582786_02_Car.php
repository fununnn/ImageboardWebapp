<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class CarMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Car (
                id INT PRIMARY KEY AUTO_INCREMENT,
                make VARCHAR(255),
                model VARCHAR(255),
                year INT,
                color VARCHAR(255),
                price FLOAT,
                mileage FLOAT,
                transmission VARCHAR(255),
                engine VARCHAR(255),
                status VARCHAR(255)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS Car;"
        ];
    }
}