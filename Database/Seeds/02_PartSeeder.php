<?php
namespace Database\Seeds;

if (class_exists('Database\Seeds\PartSeeder02')) {
    return;
}

use Database\AbstractSeeder;
require_once 'vendor/autoload.php';
use Faker\Factory;

class PartSeeder02 extends AbstractSeeder {
    protected ?string $tableName = 'Part';
    protected array $tableColumns = [
        [
            'data_type' => 'int',
            'column_name' => 'carID'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'quantityInStock'
        ]
    ];

public function createRowData(): array
{
    $faker = Factory::create();
    
    // Car テーブルの最大 ID を取得
    $maxCarId = $this->getMaxCarId();
    
    $data = [];
    for ($i = 0; $i < 10000; $i++) {
        $data[] = [
            $faker->numberBetween(1, $maxCarId), // 実際に存在する Car の id 範囲内で生成
            $faker->word,
            $faker->sentence,
            $faker->randomFloat(2, 10, 1000),
            $faker->numberBetween(0, 100)
        ];
    }
    
    return $data;
}

private function getMaxCarId(): int
{
    $result = $this->conn->query("SELECT MAX(id) as max_id FROM Car");
    $row = $result->fetch_assoc();
    return (int)($row['max_id'] ?? 0);
}
}