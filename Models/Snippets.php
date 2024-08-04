<?php
namespace Models;

use Database\MySQLWrapper;

class Snippets
{
    public static function create(string $content, string $language, ?string $expiration): ?string
    {
    $db = new MySQLWrapper();
    $uniqueUrl = self::generateUniqueUrl();
    
    // expiration が NULL または空文字列の場合、NULL を使用
    $expirationValue = ($expiration === '' || $expiration === null) ? null : $expiration;
    
    $stmt = $db->prepare("INSERT INTO Snippets (content, language, expiration, unique_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $content, $language, $expirationValue, $uniqueUrl);
    
    if ($stmt->execute()) {
        return $uniqueUrl;
    }
    
    return null;
    }

    public static function getByUrl(string $url): ?array
    {
    error_log("Attempting to get snippet with URL: $url");
    $db = new MySQLWrapper();
    $stmt = $db->prepare("SELECT * FROM Snippets WHERE unique_url = ?");
    $stmt->bind_param("s", $url);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $snippet = $result->fetch_assoc();
    
    if ($snippet) {
        error_log("Snippet found: " . print_r($snippet, true));
    } else {
        error_log("No snippet found for URL: $url");
    }
    
    return $snippet;
    }

    private static function generateUniqueUrl(): string
    {
        return bin2hex(random_bytes(8));
    }
}