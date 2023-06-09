<?php

namespace App\Migrations;

use Config\DB;

class Migration extends DB {
    public static function handle(string $state = 'up'): void
    {
        $migrations = [
            GeneralMigration::class
        ];
        foreach ($migrations as $migration) {
            $migration::$state();
        }
    }
}





