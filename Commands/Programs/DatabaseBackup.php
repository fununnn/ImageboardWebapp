<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Helpers\Settings;

class DatabaseBackup extends AbstractCommand
{
    protected static ?string $alias = 'db-backup';

    public static function getArguments(): array
    {
        return [
            (new Argument('output'))
                ->description('Output file path for the backup')
                ->required(false)
                ->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        $outputPath = $this->getArgumentValue('output') ?: $this->getDefaultBackupPath();

        $this->log("Starting database backup...");

        try {
            $this->performBackup($outputPath);
            $this->log("Database backup completed successfully: $outputPath");
            return 0;
        } catch (\Exception $e) {
            $this->log("Backup failed: " . $e->getMessage());
            return 1;
        }
    }

private function performBackup(string $outputPath): void
{
    $mysqli = new MySQLWrapper();
    $dbName = Settings::env('DATABASE_NAME');
    $host = Settings::env('DATABASE_HOST', 'localhost'); // DATABASE_HOSTが設定されていない場合は'localhost'を使用

    // テーブル構造のダンプ
    $command = sprintf(
        'mysqldump --no-data --routines --events --triggers ' .
        '--host=%s --user=%s --password=%s %s > %s',
        escapeshellarg($host),
        escapeshellarg(Settings::env('DATABASE_USER')),
        escapeshellarg(Settings::env('DATABASE_USER_PASSWORD')),
        escapeshellarg($dbName),
        escapeshellarg($outputPath)
    );
    exec($command . ' 2>&1', $output, $returnVar);

    if ($returnVar !== 0) {
        throw new \Exception("Failed to dump database structure: " . implode("\n", $output));
    }

    // データのダンプ（大きなテーブルは除外）
    $command = sprintf(
        'mysqldump --no-create-info --skip-triggers ' .
        '--host=%s --user=%s --password=%s %s >> %s',
        escapeshellarg($host),
        escapeshellarg(Settings::env('DATABASE_USER')),
        escapeshellarg(Settings::env('DATABASE_USER_PASSWORD')),
        escapeshellarg($dbName),
        escapeshellarg($outputPath)
    );
    exec($command . ' 2>&1', $output, $returnVar);

    if ($returnVar !== 0) {
        throw new \Exception("Failed to dump database data: " . implode("\n", $output));
    }
}

    private function getDefaultBackupPath(): string
    {
        $backupDir = dirname(__DIR__, 2) . '/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        return $backupDir . '/backup_' . date('Y-m-d_His') . '.sql';
    }
}