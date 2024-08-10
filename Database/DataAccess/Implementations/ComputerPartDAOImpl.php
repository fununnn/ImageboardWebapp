<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\ComputerPartDAO;
use Database\DatabaseManager;
use Models\ComputerPart;
use Models\DataTimeStamp;

class ComputerPartDAOImpl implements ComputerPartDAO
{
    public function create(ComputerPart $partData): bool
    {
        if ($partData->getId() !== null) {
            throw new \Exception('Cannot create a computer part with an existing ID. id: ' . $partData->getId());
        }
        return $this->createOrUpdate($partData);
    }

    public function getById(int $id): ?ComputerPart
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $computerPart = $mysqli->prepareAndFetchAll("SELECT * FROM Computerparts WHERE id = ?", 'i', [$id])[0] ?? null;

        return $computerPart === null ? null : $this->resultToComputerPart($computerPart);
    }

    public function update(ComputerPart $partData): bool
    {
        if ($partData->getId() === null) {
            throw new \Exception('Computer part specified has no ID.');
        }

        $current = $this->getById($partData->getId());
        if ($current === null) {
            throw new \Exception(sprintf("Computer part %s does not exist.", $partData->getId()));
        }

        return $this->createOrUpdate($partData);
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM Computerparts WHERE id = ?", 'i', [$id]);
    }

    public function getRandom(): ?ComputerPart
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $computerPart = $mysqli->prepareAndFetchAll("SELECT * FROM Computerparts ORDER BY RAND() LIMIT 1", '', [])[0] ?? null;

        return $computerPart === null ? null : $this->resultToComputerPart($computerPart);
    }

    public function getAll(int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM Computerparts LIMIT ?, ?";
        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$offset, $limit]);

        return $results === null ? [] : $this->resultsToComputerParts($results);
    }

    public function getAllByType(string $type, int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM Computerparts WHERE type = ? LIMIT ?, ?";
        $results = $mysqli->prepareAndFetchAll($query, 'sii', [$type, $offset, $limit]);

        return $results === null ? [] : $this->resultsToComputerParts($results);
    }

    public function createOrUpdate(ComputerPart $partData): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "INSERT INTO Computerparts (id, name, type, brand, model_number, release_date, description, performance_score, " .
                 "market_price, rsm, power_consumptionw, lengthm, widthm, heightm, lifespan) " .
                 "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) " .
                 "ON DUPLICATE KEY UPDATE id = ?, name = VALUES(name), type = VALUES(type), brand = VALUES(brand), " .
                 "model_number = VALUES(model_number), release_date = VALUES(release_date), description = VALUES(description), " .
                 "performance_score = VALUES(performance_score), market_price = VALUES(market_price), rsm = VALUES(rsm), " .
                 "power_consumptionw = VALUES(power_consumptionw), lengthm = VALUES(lengthm), widthm = VALUES(widthm), " .
                 "heightm = VALUES(heightm), lifespan = VALUES(lifespan)";

        $result = $mysqli->prepareAndExecute(
            $query,
            'isssssidddddddi',
            [
                $partData->getId(), $partData->getName(), $partData->getType(), $partData->getBrand(),
                $partData->getModelNumber(), $partData->getReleaseDate(), $partData->getDescription(),
                $partData->getPerformanceScore(), $partData->getMarketPrice(), $partData->getRsm(),
                $partData->getPowerConsumptionW(), $partData->getLengthM(), $partData->getWidthM(),
                $partData->getHeightM(), $partData->getLifespan(), $partData->getId()
            ]
        );

        if (!$result) return false;

        if ($partData->getId() === null) {
            $partData->setId($mysqli->insert_id);
            $createdAt = $partData->getReleaseDate() ?? date('Y-m-d');  // デフォルト値を設定
            $updatedAt = date('Y-m-d');  // 現在の日付を設定
            $timeStamp = $partData->getTimeStamp() ?? new DataTimeStamp($createdAt, $updatedAt);
            $partData->setTimeStamp($timeStamp);
        }

        return true;
    }

    private function resultToComputerPart(array $data): ComputerPart
    {
        $createdAt = $data['created_at'] ?? 'default_created_at';  // デフォルト値を設定
        $updatedAt = $data['updated_at'] ?? 'default_updated_at';  // デフォルト値を設定

        return new ComputerPart(
            name: $data['name'],
            type: $data['type'],
            brand: $data['brand'],
            id: $data['id'],
            modelNumber: $data['model_number'],
            releaseDate: $data['release_date'],
            description: $data['description'],
            performanceScore: $data['performance_score'],
            marketPrice: $data['market_price'],
            rsm: $data['rsm'],
            powerConsumptionW: $data['power_consumptionw'],
            lengthM: $data['lengthm'],
            widthM: $data['widthm'],
            heightM: $data['heightm'],
            lifespan: $data['lifespan'],
            timeStamp: new DataTimeStamp($createdAt, $updatedAt)
        );
    }

    private function resultsToComputerParts(array $results): array
    {
        return array_map([$this, 'resultToComputerPart'], $results);
    }
}
