<?php

namespace Database;

use Database\MySQLWrapper;

abstract class AbstractSeeder implements Seeder {
    protected MySQLWrapper $conn;
    protected ?string $tableName = null;
    protected array $tableColumns = [];
    const AVAILABLE_TYPES = [
        'int' => 'i',
        'float' => 'd',
        'string' => 's',
    ];

    public function __construct(MySQLWrapper $conn) {
        $this->conn = $conn;
    }

    public function seed(): void {
        $this->conn->begin_transaction();
        try {
            $data = $this->createRowData();
            if (!$this->tableName || empty($this->tableColumns)) {
                throw new \Exception('Table name or columns are missing');
            }
            foreach ($data as $row) {
                $this->validateRow($row);
                $this->insertRow($row);
            }
            $this->conn->commit();
            echo "Successfully seeded table: {$this->tableName}\n";
        } catch (\Exception $e) {
            $this->conn->rollback();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    protected function validateRow(array $row): void {
        if (count($row) !== count($this->tableColumns)) {
            throw new \Exception('Row does not match column count');
        }
        foreach ($row as $i => $value) {
            $column = $this->tableColumns[$i];
            if (!isset(self::AVAILABLE_TYPES[$column['data_type']])) {
                throw new \InvalidArgumentException("Invalid data type: {$column['data_type']}");
            }
            if (get_debug_type($value) !== $column['data_type']) {
                throw new \InvalidArgumentException("Invalid type for {$column['column_name']}");
            }
        }
    }

    protected function insertRow(array $row): void {
        try {
            $columns = implode(',', array_column($this->tableColumns, 'column_name'));
            $placeholders = str_repeat('?, ', count($row) - 1) . '?';
            $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $dataTypes = implode('', array_map(fn($c) => self::AVAILABLE_TYPES[$c['data_type']], $this->tableColumns));
            $stmt->bind_param($dataTypes, ...array_values($row));
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to execute statement: " . $stmt->error);
            }

            $stmt->close();
        } catch (\Exception $e) {
            echo "Error inserting row: " . $e->getMessage() . "\n";
        }
    }
}
