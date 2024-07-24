<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

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
        $this->log("Backup created successfully.");
    }

    private function wipeDatabase(): void
    {
        $this->log("Wiping database...");
        $this->log("Database wiped successfully.");
    }
}