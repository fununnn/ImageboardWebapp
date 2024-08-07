<?php
namespace Commands\Programs;
use Commands\AbstractCommand;
use Database\MySQLWrapper;

class CleanupOldImages extends AbstractCommand
{
    protected static ?string $alias = 'cleanup:old-images';

    public static function getArguments(): array
    {
        return [];  
    }

    public function execute(): int
    {
        $this->log("Starting cleanup of old images...");
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT url, file_path FROM Images WHERE last_accessed < NOW() - INTERVAL 30 DAY");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (file_exists($row['file_path'])) {
                unlink($row['file_path']);
                $this->log("Deleted file: {$row['file_path']}");
            }
            $deleteStmt = $db->prepare("DELETE FROM Images WHERE url = ?");
            $deleteStmt->bind_param("s", $row['url']);
            $deleteStmt->execute();
            $this->log("Deleted database record for URL: {$row['url']}");
        }
        $this->log("Cleanup of old images completed.");
        return 0;
    }
}