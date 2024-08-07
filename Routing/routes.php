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

    '/' => function(): HTTPRenderer {
        return new HTMLRenderer('home', []);  // 新しいホームページビューを作成
    },

    'upload' => function(): HTTPRenderer {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $image = new Image();
        $result = $image->upload($_FILES['image']);
        return new JSONRenderer($result);
    }
    return new HTMLRenderer('imagehosting/upload');
    },

    'media/image/{url}' => function(string $url): HTTPRenderer {
    error_log("Requested URL: $url");
    $image = Image::getByUrl('media/image/' . $url);
    error_log("Image data: " . print_r($image, true));
    if ($image) {
        $filePath = realpath(__DIR__ . '/../' . $image['file_path']);
        error_log("Resolved file path: " . $filePath);
        if ($filePath && file_exists($filePath)) {
            $mimeType = mime_content_type($filePath);
            error_log("MIME type: " . $mimeType);
            header("Content-Type: " . $mimeType);
            readfile($filePath);
            Image::incrementViewCount('media/image/' . $url);
            exit;
        } else {
            error_log("File does not exist: " . $filePath);
        }
    } else {
        error_log("Image not found in database for URL: media/image/" . $url);
    }
    return new HTMLRenderer('error/404');
    },

    'delete/{url}' => function(string $url): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = Image::delete($url);
            return new JSONRenderer($result);
        }
        return new HTMLRenderer('error/405');
    },
        // routes.php の末尾に以下を追加
    '404' => function(): HTTPRenderer {
        return new HTMLRenderer('error/404');
    },
    '500' => function(): HTTPRenderer {
        return new HTMLRenderer('error/500');
    }
];