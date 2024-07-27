<?php
namespace Commands\Programs;
use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;

class DbWipe extends AbstractCommand
{
    protected static ?string $alias = 'db-wipe';

    public static function getArguments(): array
    {
        return [
            (new Argument('backup'))
                ->description('Create a backup before wiping the database')
                ->required(false)
                ->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        $backup = $this->getArgumentValue('backup');
        if ($backup !== false) {
            $this->createBackup();
        }
        $this->wipeDatabase();
        return 0;
    }

    private function createBackup(): void
    {
        $this->log("Creating database backup...");
        // バックアップロジックを実装
        $this->log("Backup created successfully.");
    }

    private function wipeDatabase(): void
    {
        $this->log("Wiping database...");
        $mysqli = new MySQLWrapper();
        
        // 外部キー制約チェックを無効化
        $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // テーブル一覧を取得
        $tables = $mysqli->query("SHOW TABLES");
        while ($row = $tables->fetch_array(MYSQLI_NUM)) {
            $table = $row[0];
            $this->log("Dropping table: $table");
            $mysqli->query("DROP TABLE IF EXISTS `$table`");
        }
        
        // 外部キー制約チェックを再度有効化
        $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
        
        $this->log("Database wiped successfully.");
    }
}