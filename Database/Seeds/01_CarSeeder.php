<?php
namespace Database\Seeds;

if (class_exists('Database\Seeds\CarSeeder01')) {
    return;
}

use Database\AbstractSeeder;
require_once 'vendor/autoload.php';
use Faker\Factory;

class CarSeeder01 extends AbstractSeeder {
    protected ?string $tableName = 'Car';
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'make'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'year'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'color'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'mileage'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'transmission'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'engine'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'status'
        ]
    ];

    public function createRowData(): array
    {
        $faker = Factory::create();
        
        $makes = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes-Benz'];
        $transmissions = ['Automatic', 'Manual', 'CVT'];
        $statuses = ['Available', 'Sold', 'In Repair', 'Reserved'];
        
        $data = [];
        for ($i = 0; $i < 10000; $i++) {
            $data[] = [
                $faker->randomElement($makes),
                $faker->word,
                $faker->numberBetween(2000, 2024),
                $faker->colorName,
                $faker->randomFloat(2, 5000, 100000),
                $faker->randomFloat(1, 0, 200000),
                $faker->randomElement($transmissions),
                $faker->word . ' ' . $faker->numberBetween(1, 8) . '.0L',
                $faker->randomElement($statuses)
            ];
        }
        
        return $data;
    }
}