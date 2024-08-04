<?php
header("Access-Control-Allow-Origin: *");

spl_autoload_register(function($className) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$routes = include('Routing/routes.php');
$path = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if (isset($routes[$path])) {
    try {
        $renderer = $routes[$path]();
        
        foreach ($renderer->getFields() as $name => $value) {
            $sanitized_value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if ($sanitized_value !== $value) {
                throw new Exception("Invalid header value");
            }
            header("{$name}: {$sanitized_value}");
        }
        
        echo $renderer->getContent();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Not Found']);
}
