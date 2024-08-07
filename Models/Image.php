<?php
namespace Models;
use Database\MySQLWrapper;
use Helpers\ValidationHelper;

class Image {
    public function upload($file) {
    // ファイルのバリデーション
    $validationResult = ValidationHelper::validateImage($file);
    if (!$validationResult['valid']) {
        return ['success' => false, 'error' => $validationResult['error']];
    }

    // 一意なURLの生成
    $uniqueId = $this->generateUniqueUrl();
    $url = 'media/image/' . $uniqueId;

    // ファイルの保存
    $uploadDir = __DIR__ . '/../uploads/media/image/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = $uniqueId . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        return ['success' => false, 'error' => 'Failed to save file'];
    }

    // データベースへの登録
    $db = new MySQLWrapper();
    $stmt = $db->prepare("INSERT INTO Images (url, file_path) VALUES (?, ?)");
    $stmt->bind_param("ss", $url, $filePath);

    if ($stmt->execute()) {
        return ['success' => true, 'url' => $url];
    } else {
        return ['success' => false, 'error' => 'Failed to save to database'];
    }
    }

    public static function getByUrl($url) {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT * FROM Images WHERE url = ?");
        $stmt->bind_param("s", $url);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function delete($url) {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT file_path FROM Images WHERE url = ?");
        $stmt->bind_param("s", $url);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            unlink($result['file_path']);
            $stmt = $db->prepare("DELETE FROM Images WHERE url = ?");
            $stmt->bind_param("s", $url);
            if ($stmt->execute()) {
                return ['success' => true];
            }
        }
        
        return ['success' => false, 'error' => 'Image not found'];
    }

    private function generateUniqueUrl() {
        return bin2hex(random_bytes(8));
    }
}