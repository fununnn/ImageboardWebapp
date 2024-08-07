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
        $time = $this->getArgumentValue('time');
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            $this->log("Invalid time format. Please use HH:MM.");
            return 1;
        }

        list($hour, $minute) = explode(':', $time);
        $command = '/usr/bin/php ' . __DIR__ . '/../../console cleanup:old-images';
        $cronJob = "{$minute} {$hour} * * * $command";
        $tmpFile = '/tmp/mycron';

        file_put_contents($tmpFile, $cronJob . PHP_EOL);

        exec("crontab $tmpFile", $output, $returnVar);

        if ($returnVar !== 0) {
            $this->log("Crontabの設定に失敗しました。エラー出力: " . implode("\n", $output));
            return 1;
        } else {
            $this->log("Crontabが正常に設定されました。実行時間: {$time}");
        }

        unlink($tmpFile);
        return 0;
    }
}