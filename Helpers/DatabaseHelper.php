<?php

namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper
{
    private static function getDB(): MySQLWrapper {
        return new MySQLWrapper();
    }

    public static function getRandomComputerPart(): array {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM Computerparts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $part = $stmt->get_result()->fetch_assoc();
        if (!$part) throw new Exception('No part found');
        return $part;
    }

    public static function getComputerPartById(int $id): array {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM Computerparts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $part = $stmt->get_result()->fetch_assoc();
        if (!$part) throw new Exception('Part not found');
        return $part;
    }

    public static function getComputerPartsByType(string $type = null, int $page, int $perpage): array {
        $db = self::getDB();
        $offset = ($page - 1) * $perpage;
        $query = "SELECT * FROM Computerparts";
        $types = '';
        $params = [];
        if ($type) {
            $query .= " WHERE type = ?";
            $types .= 's';
            $params[] = $type;
        }
        $query .= " LIMIT ? OFFSET ?";
        $types .= 'ii';
        $params[] = $perpage;
        $params[] = $offset;
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getRandomComputer(): array {
        $db = self::getDB();
        $types = ['CPU', 'GPU', 'Motherboard', 'RAM', 'Storage', 'PowerSupply'];
        $computer = [];
        foreach ($types as $type) {
            $stmt = $db->prepare("SELECT * FROM Computerparts WHERE type = ? ORDER BY RAND() LIMIT 1");
            $stmt->bind_param('s', $type);
            $stmt->execute();
            if ($part = $stmt->get_result()->fetch_assoc()) {
                $computer[$type] = $part;
            }
        }
        return $computer;
    }

    public static function getNewestComputerParts(int $page, int $perpage): array {
        $db = self::getDB();
        $offset = ($page - 1) * $perpage;
        $stmt = $db->prepare("SELECT * FROM Computerparts ORDER BY release_date DESC LIMIT ? OFFSET ?");
        $stmt->bind_param('ii', $perpage, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getComputerPartsByPerformance(string $order = 'desc', string $type = null): array {
        $db = self::getDB();
        $query = "SELECT * FROM Computerparts";
        $types = '';
        $params = [];
        if ($type) {
            $query .= " WHERE type = ?";
            $types .= 's';
            $params[] = $type;
        }
        $query .= " ORDER BY performance_score " . ($order === 'asc' ? 'ASC' : 'DESC') . " LIMIT 50";
        $stmt = $db->prepare($query);
        if ($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
