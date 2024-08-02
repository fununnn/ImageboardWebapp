<?php
namespace Commands\Programs;
use Commands\AbstractCommand;
use Database\MySQLWrapper;
use Database\Seeder;

class Seed extends AbstractCommand
{
    protected static ?string $alias = 'seed';

    public static function getArguments(): array
    {
        return [];
    }

    public function execute(): int
    {
        $this->runAllSeeds();
        return 0;
    }

function runAllSeeds(): void {
    $directoryPath = __DIR__ . '/../../Database/Seeds';
    $files = scandir($directoryPath);
    
    $seedFiles = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'php';
    });
    sort($seedFiles);

    foreach ($seedFiles as $file) {
        echo "Processing file: " . $file . PHP_EOL;
        $baseClassName = pathinfo($file, PATHINFO_FILENAME);
        $className = 'Database\\Seeds\\' . ucfirst(preg_replace('/^\d+_/', '', $baseClassName)) . substr($baseClassName, 0, 2);
        echo "Class name: " . $className . PHP_EOL;
        
        if (!class_exists($className)) {
            require_once $directoryPath . '/' . $file;
            echo "File loaded: " . $file . PHP_EOL;
        } else {
            echo "Class already exists: " . $className . PHP_EOL;
        }

        if (class_exists($className) && is_subclass_of($className, 'Database\\Seeder')) {
            echo "Seeding: " . $file . PHP_EOL;
            $seeder = new $className(new \Database\MySQLWrapper());
            $seeder->seed();
        } else {
            echo "Skipping: " . $file . " (not a valid seeder)" . PHP_EOL;
        }
    }
}
}