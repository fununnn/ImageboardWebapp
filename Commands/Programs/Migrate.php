<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;

class Migrate extends AbstractCommand
{
    // コマンド名を設定します
    protected static ?string $alias = 'migrate';

    // 引数を割り当てます
    public static function getArguments(): array
    {
        return [
            (new Argument('rollback'))->description('Roll backwards. An integer n may also be provided to rollback n times.')->required(false)->allowAsShort(true),
            (new Argument('init'))->description("Create the migrations table if it doesn't exist.")->required(false)->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        $rollback = $this->getArgumentValue('rollback');

        if ($this->getArgumentValue('init')) $this->createMigrationsTable();

        if ($rollback === false) {
            $this->log("Starting migration......");
            $this->migrate();
        } else {
            // rollbackはtrueに設定されているか、それに関連付けられた値が
            // 整数として存在するかのいずれかです。
            $rollbackN = $rollback === true ? 1 : (int)$rollback;
            $this->log("Running rollback....");
            $this->rollback($rollbackN);
        }
        return 0;
    }

    private function createMigrationsTable(): void
    {
        $this->log("Creating migrations table if necessary...");

        $mysqli = new MySQLWrapper();

        $result = $mysqli->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL
            );
        ");

        if ($result === false) throw new \Exception("Failed to create migration table.");

        $this->log("Done setting up migration tables.");
    }

    private function migrate(): void
    {
        $this->log("Running migrations...");

        $mysqli = new MySQLWrapper();
        $mysqli->begin_transaction();

        try {
            $lastMigration = $this->getLastMigration();
            $allMigrations = $this->getAllMigrationFiles();
            $startIndex = ($lastMigration) ? array_search($lastMigration, $allMigrations) + 1 : 0;

            for ($i = $startIndex; $i < count($allMigrations); $i++) {
                $filename = $allMigrations[$i];

                $this->log("Processing file: " . $filename);

                include_once($filename);

                $migrationClass = $this->getClassnameFromMigrationFilename($filename);

                $this->log("Migration class: " . $migrationClass);

                if (!class_exists($migrationClass)) {
                    throw new \Exception("Migration class {$migrationClass} not found in file {$filename}");
                }

                $migration = new $migrationClass();

                $this->log(sprintf("Processing up migration for %s", $migrationClass));
                $queries = $migration->up();

                $this->log("Queries: " . print_r($queries, true));

                if (empty($queries)) {
                    throw new \Exception("No queries to run for " . $migrationClass);
                }

                $this->processQueries($queries);
                $this->insertMigration($filename);
            }

            $mysqli->commit();
            $this->log("Migration ended successfully.\n");
        } catch (\Exception $e) {
            $mysqli->rollback();
            $this->log("Migration failed: " . $e->getMessage());
            $this->log("Rolling back all changes.");
            throw $e;
        }
    }

private function getClassnameFromMigrationFilename(string $filename): string
{
    $baseName = basename($filename, '.php');
    $parts = explode('_', $baseName);
    
    // タイムスタンプと数字のプレフィックスを除去
    array_shift($parts); // 日付を削除
    array_shift($parts); // 時間を削除
    if (is_numeric($parts[0])) {
        array_shift($parts); // 数字のプレフィックスを削除
    }
    
    $className = implode('', array_map('ucfirst', $parts)) . 'Migration';
    return sprintf("%s\%s", 'Database\Migrations', $className);
}

    private function getLastMigration(): ?string
    {
        $mysqli = new MySQLWrapper();

        $query = "SELECT filename FROM migrations ORDER BY id DESC LIMIT 1";

        $result = $mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['filename'];
        }

        return null;
    }

private function getAllMigrationFiles(string $order = 'asc'): array
{
    $directory = sprintf("%s/../../Database/Migrations", __DIR__);
    $this->log($directory);
    $allFiles = glob($directory . "/*.php");

    // ファイル名全体で自然順ソート
    sort($allFiles, SORT_NATURAL | SORT_FLAG_CASE);

    return $order === 'desc' ? array_reverse($allFiles) : $allFiles;
}

private function getFilePrefix(string $filename): string
{
    $parts = explode('_', basename($filename));
    return $parts[2] ?? '';
}


private function processQueries(array $queries): void
{
    $mysqli = new MySQLWrapper();
    foreach ($queries as $query) {
        try {
            $result = $mysqli->query($query);
            if ($result === false) {
                throw new \Exception($mysqli->error);
            }
            $this->log('Ran query: ' . $query);
        } catch (\Exception $e) {
            throw new \Exception(sprintf("Query failed: %s\nError: %s", $query, $e->getMessage()));
        }
    }
}

    private function insertMigration(string $filename): void
    {
        $mysqli = new MySQLWrapper();

        $statement = $mysqli->prepare("INSERT INTO migrations (filename) VALUES (?)");
        if (!$statement) {
            throw new \Exception("Prepare failed: (" . $mysqli->errno . ")" . $mysqli->error);
        }

        // クエリが準備されたので、準備されたクエリに文字列値を挿入します。
        $statement->bind_param("s", $filename);

        // ステートメントを実行します
        if (!$statement->execute()) {
            throw new \Exception("Execute failed: (" . $statement->errno . ")" . $statement->error);
        }

        // ステートメントを閉じます
        $statement->close();
    }

private function rollback(int $n = 1): void {
    $this->log("Rolling back {$n} migration(s)...");

    $mysqli = new MySQLWrapper();
    $mysqli->begin_transaction();

    try {
        $lastMigration = $this->getLastMigration();
        $allMigrations = $this->getAllMigrationFiles('desc');  // 降順で取得

        // ソートされたリストで最後のマイグレーションのインデックスを探します
        $lastMigrationIndex = array_search($lastMigration, $allMigrations);

        // 最後のマイグレーションが見つかったことを確認します
        if ($lastMigrationIndex === false) {
            throw new \Exception("Could not find the last migration ran: " . $lastMigration);
        }

        $count = 0;
        // 毎回、マイグレーションのダウン関数を実行します。
        for ($i = $lastMigrationIndex; $count < $n && $i < count($allMigrations); $i++) {
            $filename = $allMigrations[$i];

            $this->log("Rolling back: {$filename}");

            include_once($filename);

            $migrationClass = $this->getClassnameFromMigrationFilename($filename);
            
            if (!class_exists($migrationClass)) {
                throw new \Exception("Migration class {$migrationClass} not found in file {$filename}");
            }

            $migration = new $migrationClass();

            $queries = $migration->down();
            if (empty($queries)) {
                throw new \Exception("No queries to run for rollback in " . $migrationClass);
            }

            $this->processQueries($queries);
            $this->removeMigration($filename);
            $count++;
        }

        $mysqli->commit();
        $this->log("Rollback completed successfully.\n");
    } catch (\Exception $e) {
        $mysqli->rollback();
        $this->log("Rollback failed: " . $e->getMessage());
        $this->log("Rolling back all changes.");
        throw $e;
    }
}
    private function removeMigration(string $filename): void {
    $mysqli = new MySQLWrapper();
    
    $statement = $mysqli->prepare("DELETE FROM migrations WHERE filename = ?");
    
    if (!$statement) throw new \Exception("Prepare failed: (" . 
    $mysqli->errno . ") " . $mysqli->error);
    
    $statement->bind_param("s", $filename);
    
    if (!$statement->execute()) throw new \Exception("Execute failed: (" . $statement->errno . ") " . $statement->error);
    
    $statement->close();
    }

}