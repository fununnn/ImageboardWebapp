<?php
return [
    Commands\Programs\Migrate::class,
    Commands\Programs\CodeGeneration::class,
    Commands\Programs\DbWipe::class,
    Commands\Programs\BookSearch::class,
    Commands\Programs\GenerateCommands::class, 
    Commands\Programs\DatabaseBackup::class,
    Commands\Programs\Seed::class,
    Commands\Programs\CleanupOldImages::class, 
    Commands\Programs\SetupCronJob::class, 
    
];