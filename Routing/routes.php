<?php

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    'random/part' => function(): HTTPRenderer {
        $part = DatabaseHelper::getRandomComputerPart();
        return new HTMLRenderer('component/random-part', ['part' => $part]);
    },

    'parts' => function(): HTTPRenderer {
        // IDの検証
        $id = ValidationHelper::integer($_GET['id'] ?? null);
        $part = DatabaseHelper::getComputerPartById($id);
        return new HTMLRenderer('component/parts', ['part' => $part]);
    },

    'api/random/part' => function(): HTTPRenderer {
        $part = DatabaseHelper::getRandomComputerPart();
        return new JSONRenderer(['part' => $part]);
    },

    'api/parts' => function() {
        $id = ValidationHelper::integer($_GET['id'] ?? null);
        $part = DatabaseHelper::getComputerPartById($id);
        return new JSONRenderer(['part' => $part]);
    },

    'types' => function(): HTTPRenderer {
        $type = $_GET['type'] ?? null;
        $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
        
        $parts = DatabaseHelper::getComputerPartsByType($type, $page, $perpage);
        return new JSONRenderer($parts);
    },
    
    'random/computer' => function(): HTTPRenderer {
        $computer = DatabaseHelper::getRandomComputer();
        return new JSONRenderer($computer);
    },
    
    'parts/newest' => function(): HTTPRenderer {
        $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
        
        $parts = DatabaseHelper::getNewestComputerParts($page, $perpage);
        return new JSONRenderer($parts);
    },
    
    'parts/performance' => function(): HTTPRenderer {
        $order = $_GET['order'] ?? 'desc';
        $type = $_GET['type'] ?? null;
        
        $parts = DatabaseHelper::getComputerPartsByPerformance($order, $type);
        return new JSONRenderer($parts);
    },
];