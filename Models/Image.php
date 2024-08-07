<?php
namespace Models;
use Database\MySQLWrapper;
use Helpers\ValidationHelper;
use Helpers\UploadLimiter;

class Image {
    public function upload($file) {
    try {
        // ファイルのバリデーション
        $validationResult = ValidationHelper::validateImage($file);
        if (!$validationResult['valid']) {
            return ['success' => false, 'error' => $validationResult['error']];
        }

        // 一意なURLの生成
        $uniqueId = $this->generateUniqueUrl();
        $url = 'media/image/' . $uniqueId;

        // ファイルの保存
        $uploadDir = 'uploads/' . date('Y/m/d/');
        $fullUploadDir = __DIR__ . '/../' . $uploadDir;
        if (!is_dir($fullUploadDir)) {
            if (!mkdir($fullUploadDir, 0777, true)) {
                throw new \Exception('Failed to create upload directory');
            }
        }

        $filename = $this->generateUniqueUrl() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $filename;
        error_log("Saved file path: " . $filePath);
        $fullFilePath = $fullUploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $fullFilePath)) {
            throw new \Exception('Failed to save file');
        }

        $limiter = new UploadLimiter();
        if (!$limiter->canUpload($_SERVER['REMOTE_ADDR'])) {
            throw new \Exception('Upload limit exceeded');
        }

        $limiter->logUpload($_SERVER['REMOTE_ADDR']);

        // データベースへの登録
        $db = new MySQLWrapper();
        $stmt = $db->prepare("INSERT INTO Images (url, file_path, upload_ip) VALUES (?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("sss", $url, $filePath, $ip);
        error_log("Inserting image with URL: $url, File path: $filePath, IP: $ip");

        if (!$stmt->execute()) {
            throw new \Exception('Failed to save to database');
        }

        return ['success' => true, 'url' => $url];
    } catch (\Exception $e) {
        error_log('Upload error: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
    }

    public static function getByUrl($url) {
    $db = new MySQLWrapper();
    $query = "SELECT * FROM Images WHERE url = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $url);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();
    error_log("getByUrl query for $url: $query");
    error_log("getByUrl result for $url: " . print_r($image, true));
    return $image;
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
    public static function incrementViewCount($url) {
    $db = new MySQLWrapper();
    $stmt = $db->prepare("UPDATE Images SET view_count = view_count + 1, last_accessed = CURRENT_TIMESTAMP WHERE url = ?");
    $stmt->bind_param("s", $url);
    $stmt->execute();
    }

    // アップロード制限をチェックする新しいメソッド
    public static function canUpload($ip) {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT COUNT(*) FROM Uploads WHERE ip = ? AND timestamp > NOW() - INTERVAL 1 HOUR");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_row()[0];
        $limit = 10; // 1時間あたりの制限

        if ($count < $limit) {
            $stmt = $db->prepare("INSERT INTO Uploads (ip) VALUES (?)");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            return true;
        }
        return false;
    }
}