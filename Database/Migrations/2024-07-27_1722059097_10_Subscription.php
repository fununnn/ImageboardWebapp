<?php
namespace Database\Migrations;
use Database\SchemaMigration;

class CreateSubscriptionTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS Subscription (
                subscriptionID INT PRIMARY KEY AUTO_INCREMENT,
                userID INT,
                subscription VARCHAR(255),
                subscription_status VARCHAR(255),
                subscriptionCreatedAt DATETIME,
                subscriptionEndAt DATETIME,
                FOREIGN KEY (userID) REFERENCES User(userID)
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS 10_Subscription;"
        ];
    }
}