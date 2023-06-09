<?php

namespace App\Migrations;

use Config\DB;

class GeneralMigration extends DB
{
    public static function up(): void
    {
        self::run('
            CREATE TABLE test_tbl (
                lc_id int NOT NULL AUTO_INCREMENT,
                lc_title varchar(255) NOT NULL,
                PRIMARY KEY (lc_id)
            )
        ');
    }

    public static function down(): void
    {
        //
    }
}