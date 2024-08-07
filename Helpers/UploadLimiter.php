<?php

namespace Helpers;
use Database\MySQLWrapper;
use Exceptions\ReadAndParseEnvException;

class UploadLimiter {
    private $db;
    private $limit = 1000; // 1時間あたりの制限
    private $timeWindow = 3600; // 1時間（秒）

    public function __construct() {
        $this->db = new MySQLWrapper();
    }

    public function canUpload($ip) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Uploads WHERE ip = ? AND timestamp > NOW() - INTERVAL 1 HOUR");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_row()[0];
        return $count < $this->limit;
    }

    public function logUpload($ip) {
        $stmt = $this->db->prepare("INSERT INTO Uploads (ip, timestamp) VALUES (?, NOW())");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
    }
    public function getRateLimit($ip) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM uploads WHERE ip = ? AND timestamp > NOW() - INTERVAL 1 MINUTE");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    return $stmt->get_result()->fetch_row()[0];
    }
}