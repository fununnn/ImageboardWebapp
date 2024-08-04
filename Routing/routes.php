<?php

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Models\Snippets;
use Models\Image;

return [
    'random/part' => function(): HTTPRenderer {
        return new HTMLRenderer('component/random-part', ['part' => DatabaseHelper::getRandomComputerPart()]);
    },

    'parts' => function(): HTTPRenderer {
        $id = ValidationHelper::integer($_GET['id'] ?? null);
        return new HTMLRenderer('component/parts', ['part' => DatabaseHelper::getComputerPartById($id)]);
    },

    'api/random/part' => function(): HTTPRenderer {
        return new JSONRenderer(['part' => DatabaseHelper::getRandomComputerPart()]);
    },

    'api/parts' => function(): HTTPRenderer {
        $id = ValidationHelper::integer($_GET['id'] ?? null);
        return new JSONRenderer(['part' => DatabaseHelper::getComputerPartById($id)]);
    },

    'types' => function(): HTTPRenderer {
        $type = $_GET['type'] ?? null;
        $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
        return new JSONRenderer(DatabaseHelper::getComputerPartsByType($type, $page, $perpage));
    },
    
    'random/computer' => function(): HTTPRenderer {
        return new JSONRenderer(DatabaseHelper::getRandomComputer());
    },
    
    'parts/newest' => function(): HTTPRenderer {
        $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
        $perpage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
        return new JSONRenderer(DatabaseHelper::getNewestComputerParts($page, $perpage));
    },
    
    'parts/performance' => function(): HTTPRenderer {
        $order = $_GET['order'] ?? 'desc';
        $type = $_GET['type'] ?? null;
        return new JSONRenderer(DatabaseHelper::getComputerPartsByPerformance($order, $type));
    },

    'snippet/create' => function(): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'] ?? '';
            $language = $_POST['language'] ?? '';
            $expiration = $_POST['expiration'] ?? null;
            $uniqueUrl = Models\Snippets::create($content, $language, $expiration);
            return new JSONRenderer(['success' => (bool)$uniqueUrl, 'url' => $uniqueUrl ? "/snippet/view/{$uniqueUrl}" : null, 'error' => $uniqueUrl ? null : 'Failed to create snippet']);
        }
        return new HTMLRenderer('snippet/create');
    },

    'snippet/view/{url}' => function(string $url): HTTPRenderer {
        $snippet = Models\Snippets::getByUrl($url);
        
        if ($snippet) {
            return new HTMLRenderer('snippet/view', ['snippet' => $snippet]);
        } else {
            return new HTMLRenderer('snippet/not_found');
        }
    },

    '' => function(): HTTPRenderer {
        return new HTMLRenderer('imagehosting/upload');
    },

    'upload' => function(): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $image = new Image();
            $result = $image->upload($_FILES['image']);
            return new JSONRenderer($result);
        }
        return new HTMLRenderer('error/405');
    },

    'image/{url}' => function(string $url): HTTPRenderer {
        $image = Image::getByUrl($url);
        if ($image) {
            return new HTMLRenderer('imagehosting/view', ['image' => $image]);
        }
        return new HTMLRenderer('error/404');
    },

    'delete/{url}' => function(string $url): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = Image::delete($url);
            return new JSONRenderer($result);
        }
        return new HTMLRenderer('error/405');
    }
];