<?php
namespace Commands\Programs;
use Commands\AbstractCommand;
use Commands\Argument;

class SetupCronJob extends AbstractCommand
{
    protected static ?string $alias = 'setup:cron';

    public static function getArguments(): array
    {
        return [
            (new Argument('time'))
                ->description('Cron job execution time (format: HH:MM)')
                ->required(true)
                ->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        $executionTime = $this->getArgumentValue('time');

        if (!$this->isValidExecutionTime($executionTime)) {
            $this->log("Invalid time format. Please use HH:MM.");
            return 1;
        }

        [$hour, $minute] = explode(':', $executionTime);
        $command = $this->getCommandPath();
        $cronJob = "$minute $hour * * * $command";
        $tmpFile = $this->createTemporaryFile();

        file_put_contents($tmpFile, $cronJob . PHP_EOL);

        $returnCode = $this->installCronJob($tmpFile);

        $this->cleanup($tmpFile);

        return $returnCode;
    }

    private function isValidExecutionTime(string $executionTime): bool
    {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $executionTime);
    }

    private function getCommandPath(): string
    {
        return '/usr/bin/php ' . __DIR__ . '/../../console cleanup:old-images';
    }

    private function createTemporaryFile(): string
    {
        return tempnam(sys_get_temp_dir(), 'mycron');
    }

    private function installCronJob(string $tmpFile): int
    {
        exec("crontab $tmpFile", $output, $returnVar);

        if ($returnVar !== 0) {
            $this->log("Failed to set up crontab. Error output: " . implode("\n", $output));
        }

        return $returnVar;
    }

    private function cleanup(string $tmpFile): void
    {
        unlink($tmpFile);
    }
}