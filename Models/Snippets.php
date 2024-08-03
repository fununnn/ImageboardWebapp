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
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT * FROM Snippets WHERE unique_url = ? AND (expiration IS NULL OR expiration > NOW())");
        $stmt->bind_param("s", $url);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    private static function generateUniqueUrl(): string
    {
        return bin2hex(random_bytes(8));
    }
}