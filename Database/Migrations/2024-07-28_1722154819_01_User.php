<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class UserMigration implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS User (
                userID INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(255) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
                subscription_status VARCHAR(255),
                subscriptionCreatedAt DATETIME,
                subscriptionEndAt DATETIME
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS User;"
        ];
    }
}