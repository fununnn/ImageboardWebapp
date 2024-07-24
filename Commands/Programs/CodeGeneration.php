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

private function getMigrationContent(string $migrationName): string
{
    $className = $this->pascalCase($migrationName);
    return <<<MIGRATION
<?php

namespace Database\\Migrations;

use Database\\SchemaMigration;

class {$className} implements SchemaMigration
{
    public function up(): array
    {
        return [
            "CREATE TABLE posts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                user_id BIGINT,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        return [
            "DROP TABLE posts"
        ];
    }
}
MIGRATION;
}

private function pascalCase(string $string): string{
    return str_replace(' ', '', ucwords(str_replace('_', ' ', 
$string)));
}
}