<?php
namespace Commands\Programs;
use Commands\AbstractCommand;
use Commands\Argument;

class CodeGeneration extends AbstractCommand
{
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    public static function getArguments(): array
    {
        return [
            (new Argument('name'))
                ->description('Name of the file that is to be generated, or "all" to generate for all SQL files.')
                ->required(true),
        ];
    }

    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for......' . $codeGenType);
        if ($codeGenType === 'migration') {
            $migrationName = $this->getArgumentValue('name');
            if ($migrationName === 'all') {
                $this->generateAllMigrationFiles();
            } else {
                $this->generateMigrationFile($migrationName);
            }
        }
        return 0;
    }

    private function generateAllMigrationFiles(): void
    {
        $sqlDir = sprintf("%s/../../Database/Examples", __DIR__);
        $files = glob($sqlDir . "/*.sql");
        
        foreach ($files as $file) {
            $tableName = pathinfo($file, PATHINFO_FILENAME);
            $this->generateMigrationFile($tableName);
        }
        
        $this->log("All migration files have been generated!");
    }

    private function generateMigrationFile(string $migrationName): void
    {
        $sqlFilePath = $this->findSqlFile($migrationName);
        if (!$sqlFilePath) {
            throw new \Exception("SQL file for {$migrationName} not found.");
        }

        $prefix = basename($sqlFilePath, '.sql');
        $filename = sprintf(
            '%s_%s_%s.php',
            date('Y-m-d'),
            time(),
            $prefix
        );
        $migrationContent = $this->getMigrationContent($migrationName, $prefix);
        $path = sprintf("%s/../../Database/Migrations/%s", __DIR__, $filename);
        file_put_contents($path, $migrationContent);
        $this->log("Migration file {$filename} has been generated!");
    }

    private function findSqlFile(string $migrationName): ?string
    {
        $sqlDir = sprintf("%s/../../Database/Examples", __DIR__);
        $files = glob($sqlDir . "/*.sql");
        foreach ($files as $file) {
            if (stripos(basename($file), $migrationName) !== false) {
                return $file;
            }
        }
        return null;
    }

private function getMigrationContent(string $tableName, string $prefix): string
{
    $className = $this->formatClassName($prefix);
    $tableStructure = $this->getTableStructure($tableName);
    
    // テーブル名から数字のプレフィックスを削除
    $cleanTableName = preg_replace('/^\d+_/', '', $tableName);

    return <<<MIGRATION
<?php
namespace Database\\Migrations;

use Database\\SchemaMigration;

class {$className} implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS {$cleanTableName} (
                {$tableStructure}
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS {$cleanTableName};"
        ];
    }
}
MIGRATION;
}

    private function formatClassName(string $prefix): string
    {
        // プレフィックスから数字を削除し、残りの部分をパスカルケースに変換
        $withoutNumbers = preg_replace('/^\d+_/', '', $prefix);
        return $this->pascalCase($withoutNumbers) . 'Migration';
    }

private function getTableStructure(string $tableName): string
{
    $sqlFilePath = $this->findSqlFile($tableName);
    if (!$sqlFilePath) {
        throw new \Exception("SQL file for table {$tableName} not found.");
    }
    
    $sqlContent = file_get_contents($sqlFilePath);
    
    if (preg_match('/CREATE TABLE.*?\((.*?)\);/s', $sqlContent, $matches)) {
        $tableStructure = trim($matches[1]);
        $lines = explode(",", $tableStructure);
        $formattedLines = array_map(function($line) {
            return trim($line);
        }, $lines);
        return implode(",\n                ", $formattedLines);
    }
    
    throw new \Exception("Could not parse SQL content for table {$tableName}.");
}
}