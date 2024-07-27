<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定します
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当てます
    public static function getArguments(): array
    {
        return [
            (new Argument('name'))->description('Name of the file that is to be generated.')->required(false),
        ];
    }

    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for......' . $codeGenType);

        if ($codeGenType === 'migration') {
            $migrationName = $this->getArgumentValue('name');
            $this->generateMigrationFile($migrationName);
        }

        return 0;
    }
private function generateMigrationFile(string $migrationName): void
{
    $filename = sprintf(
        '%s_%s_%s.php',
        date('Y-m-d'),
        time(),
        $migrationName
    );

    $migrationContent = $this->getMigrationContent($migrationName);

    // 移行ファイルを保存するパスを指定します
    $path = sprintf("%s/../../Database/Migrations/%s",
    __DIR__, $filename);

    file_put_contents($path, $migrationContent);
    $this->log("Migration file {$filename} has been generated!");
}

private function getMigrationContent(string $tableName): string
{
    $className = $this->pascalCase($tableName);
    $tableStructure = $this->getTableStructure($tableName);

    return <<<MIGRATION
<?php
namespace Database\\Migrations;
use Database\\SchemaMigration;

class Create{$className}Table implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS {$tableName} (
                {$tableStructure}
            );"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE IF EXISTS {$tableName};"
        ];
    }
}
MIGRATION;
}

    private function getTableStructure(string $tableName): string
{
    $sqlFilePath = sprintf("%s/../../Database/Examples/%s.sql", __DIR__, $tableName);
    if (!file_exists($sqlFilePath)) {
        throw new \Exception("SQL file for table {$tableName} not found.");
    }

    $sqlContent = file_get_contents($sqlFilePath);
    preg_match('/CREATE TABLE.*?\((.*?)\);/s', $sqlContent, $matches);
    if (empty($matches[1])) {
        throw new \Exception("Could not parse SQL file for table {$tableName}.");
    }

    $tableStructure = trim($matches[1]);
    $lines = explode(",", $tableStructure);
    $formattedLines = array_map(function($line) {
        return trim($line);
    }, $lines);

    return implode(",\n                ", $formattedLines);
    }
}