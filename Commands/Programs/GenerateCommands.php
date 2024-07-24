<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class GenerateCommands extends AbstractCommand
{
    protected static ?string $alias = 'generate-commands';

    public static function getArguments(): array
    {
        return [
            (new Argument('name'))
                ->description('The name of the new command')
                ->required(true),
        ];
    }

    public function execute(): int
    {
        $commandName = $this->getArgumentValue('name');

        if (!$commandName) {
            $this->log("Error: Command name is required.");
            return 1;
        }

        $className = $this->formatClassName($commandName);
        $filePath = $this->generateCommandFile($className);

        if ($filePath) {
            $this->log("Command file generated successfully: $filePath");
            $this->updateRegistry($className);
            return 0;
        } else {
            $this->log("Error: Failed to generate command file.");
            return 1;
        }
    }

    private function formatClassName(string $commandName): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $commandName)));
    }

    private function generateCommandFile(string $className): ?string
    {
        $namespace = 'Commands\\Programs';
        $filePath = __DIR__ . "/$className.php";

        $template = <<<EOT
<?php

namespace $namespace;

use Commands\AbstractCommand;
use Commands\Argument;

class $className extends AbstractCommand
{
    protected static ?string \$alias = '{command-alias}';

public static function getArguments(): array
{
    return [
        (new Argument('name'))
            ->description('The name of the new command')
            ->required(true)
            ->allowAsShort(true),  // オプションで短い形式も許可
    ];
}

    public function execute(): int
    {
        // TODO: Implement command logic here
        \$this->log("$className command executed.");
        return 0;
    }
}
EOT;

        $template = str_replace('{command-alias}', strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $className)), $template);

        if (file_put_contents($filePath, $template) !== false) {
            return $filePath;
        }

        return null;
    }

    private function updateRegistry(string $className): void
    {
        $registryFile = __DIR__ . '/../registry.php';
        $content = file_get_contents($registryFile);

        $newCommand = "    Commands\\Programs\\$className::class,\n];";
        $content = str_replace('];', $newCommand, $content);

        file_put_contents($registryFile, $content);
        $this->log("Registry updated successfully.");
    }
}