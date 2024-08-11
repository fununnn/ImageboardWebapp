<?php

use Helpers\DatabaseHelper;
use Models\ComputerPart;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Models\Snippets;
use Models\Image;
use Types\ValueType;
use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Database\DataAccess\Implementations\PostDAOImpl;
use Models\Post;

return [
    'random/part' => function(): HTTPRenderer {
        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getRandom();

        if($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },

    'parts'=>function(): HTTPRenderer{
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getById($id);

        if($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },

    'update/part' => function(): HTMLRenderer {
        $part = null;
        $partDao = new ComputerPartDAOImpl();
        if(isset($_GET['id'])){
            $id = ValidationHelper::integer($_GET['id']);
            $part = $partDao->getById($id);
        }
        return new HTMLRenderer('component/update-computer-part', ['part'=>$part]);
    },
    'form/update/part' => function(): HTTPRenderer {
        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method!');
            }

            $required_fields = [
                'name' => ValueType::STRING,
                'type' => ValueType::STRING,
                'brand' => ValueType::STRING,
                'modelNumber' => ValueType::STRING,
                'releaseDate' => ValueType::DATE,
                'description' => ValueType::STRING,
                'performanceScore' => ValueType::INT,
                'marketPrice' => ValueType::FLOAT,
                'rsm' => ValueType::FLOAT,
                'powerConsumptionW' => ValueType::FLOAT,
                'lengthM' => ValueType::FLOAT,
                'widthM' => ValueType::FLOAT,
                'heightM' => ValueType::FLOAT,
                'lifespan' => ValueType::INT,
            ];

            $partDao = new ComputerPartDAOImpl();

            // 入力に対する単純なバリデーション。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
            if(isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成+アンパッキング
            $part = new ComputerPart(...$validatedData);

            error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

            // 新しい部品情報でデータベースの更新を試みます。
            // 別の方法として、createOrUpdateを実行することもできます。
            if(isset($validatedData['id'])) $success = $partDao->update($part);
            else $success = $partDao->create($part);

            if (!$success) {
                throw new Exception('Database update failed!');
            }

            return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
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
        return new HTMLRenderer('error/404');
    },
        // routes.php の末尾に以下を追加
    '404' => function(): HTTPRenderer {
        return new HTMLRenderer('error/404');
    },
    '500' => function(): HTTPRenderer {
        return new HTMLRenderer('error/500');
    },

    'delete/part' => function(): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method!');
        }

        $id = ValidationHelper::integer($_POST['id'] ?? null);
        if ($id === null) {
            throw new Exception('ID is required!');
        }

        $partDao = new ComputerPartDAOImpl();
        $success = $partDao->delete($id);

        return new JSONRenderer(['success' => $success, 'message' => $success ? 'Part deleted successfully' : 'Failed to delete part']);
    },

    'parts/all' => function(): HTTPRenderer {
        $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
        $perPage = ValidationHelper::integer($_GET['perPage'] ?? 15, 1, 100);
        
        $offset = ($page - 1) * $perPage;
        
        $partDao = new ComputerPartDAOImpl();
        $parts = $partDao->getAll($offset, $perPage);
        
        return new JSONRenderer(['parts' => array_map(function($part) { return $part->toArray(); }, $parts)]);
    },

    'parts/type' => function(): HTTPRenderer {
        $type = $_GET['type'] ?? null;
        if ($type === null) {
            throw new Exception('Type is required!');
        }
        
        $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
        $perPage = ValidationHelper::integer($_GET['perPage'] ?? 15, 1, 100);
        
        $offset = ($page - 1) * $perPage;
        
        $partDao = new ComputerPartDAOImpl();
        $parts = $partDao->getAllByType($type, $offset, $perPage);
        
        return new JSONRenderer(['parts' => array_map(function($part) { return $part->toArray(); }, $parts)]);
    },

    'threads' => function(): HTTPRenderer {
        $postDao = new PostDAOImpl();
        $threads = $postDao->getAllThreads(0, 20); // 最新20件のスレッドを取得
        return new HTMLRenderer('thread/list', ['threads' => $threads]);
    },

    'thread/create' => function(): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postDao = new PostDAOImpl();
            $post = new Post(
                subject: $_POST['subject'] ?? '',
                content: $_POST['content'] ?? ''
            );
            $success = $postDao->create($post);
            return new JSONRenderer(['success' => $success]);
        }
        return new HTMLRenderer('thread/create');
    },

    'thread/view/{id}' => function(int $id): HTTPRenderer {
        $postDao = new PostDAOImpl();
        $thread = $postDao->getById($id);
        if (!$thread) {
            return new HTMLRenderer('error/404');
        }
        $replies = $postDao->getReplies($thread, 0, 100); // 最新100件の返信を取得
        return new HTMLRenderer('thread/view', ['thread' => $thread, 'replies' => $replies]);
    },

    'thread/reply/{id}' => function(int $id): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postDao = new PostDAOImpl();
            $post = new Post(
                replyToId: $id,
                content: $_POST['content'] ?? ''
            );
            $success = $postDao->create($post);
            return new JSONRenderer(['success' => $success]);
        }
        return new HTMLRenderer('error/405'); // Method Not Allowed
    }
];