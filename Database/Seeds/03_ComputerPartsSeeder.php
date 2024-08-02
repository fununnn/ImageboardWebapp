<?php
namespace Database\Seeds;

if (class_exists('Database\Seeds\ComputerPartsSeeder03')) {
    return;
}

use Database\AbstractSeeder;
require_once 'vendor/autoload.php';
use Faker\Factory;

class ComputerPartsSeeder03 extends AbstractSeeder {
    protected ?string $tableName = 'Computerparts';
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'type'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'brand'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model_number'
        ],
                [
            'data_type' => 'string',
            'column_name' => 'release_date'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'performance_score'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'market_price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'rsm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'power_consumptionw'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'lengthm'
        ],
                [
            'data_type' => 'float',
            'column_name' => 'widthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'heightm'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'lifespan'
        ]
    ];

    public function createRowData(): array
    {
        $faker = Factory::create();
        
        $types = ['CPU', 'GPU', 'SSD', 'RAM'];
        $brands = ['AMD', 'NVIDIA', 'Intel', 'Samsung', 'Corsair', 'Western Digital'];
        
        $data = [];
        for ($i = 0; $i < 10000; $i++) {
            $data[] = [
                $faker->word,
                $faker->randomElement($types),
                $faker->randomElement($brands),
                $faker->bothify('??-#####'),
                $faker->date(),
                $faker->sentence,
                $faker->numberBetween(70, 100),
                $faker->randomFloat(2, 50, 1000),
                $faker->randomFloat(2, 0.01, 0.1),
                $faker->randomFloat(1, 1, 500),
                $faker->randomFloat(3, 0.01, 0.5),
                $faker->randomFloat(3, 0.01, 0.5),
                $faker->randomFloat(3, 0.001, 0.1),
                $faker->numberBetween(3, 10)
            ];
        }
        
        return $data;
    }
}