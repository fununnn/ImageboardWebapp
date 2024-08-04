<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");

spl_autoload_register(function($className) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        error_log("Class file not found: $file");
    }
});

$routes = include('Routing/routes.php');
$path = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
error_log("Requested path: $path");

$matchedRoute = null;
$params = [];

foreach ($routes as $routePattern => $handler) {
    $pattern = preg_replace('/{([^}]+)}/', '(?P<$1>[^/]+)', $routePattern);
    if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
        $matchedRoute = $routePattern;
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        break;
    }
}

if ($matchedRoute) {
    error_log("Route found: $matchedRoute");
    try {
        $renderer = $routes[$matchedRoute](...$params);
        
        foreach ($renderer->getFields() as $name => $value) {
            $sanitized_value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            if ($sanitized_value !== $value) {
                throw new Exception("Invalid header value: $name");
            }
            header("{$name}: {$sanitized_value}");
        }
        
        $content = $renderer->getContent();
        error_log("Response content: " . substr($content, 0, 100) . "..."); // Log first 100 characters
        echo $content;
    } catch (Exception $e) {
        error_log("Error in route execution: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    error_log("No route found for path: $path");
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Not Found']);
}