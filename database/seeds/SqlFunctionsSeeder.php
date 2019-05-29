<?php

/**
 * Class
 *
 * PHP version 7.2
 */
use Illuminate\Database\Seeder;

/**
 * Seeder for Users model
 */
class SqlFunctionsSeeder extends Seeder
{
    /**
     * Runs the DB seed
     */
    public function run()
    {
        $this->command->info('Adding custom SQL functions');

        \DB::unprepared(
            <<<SQL
        -- splits a string (str) by a delimeter (delim) and returns back the part on position (pos - 0 based)
        -- usage example:
        /*
            SELECT SPLIT_STRING(slug, ':', 1) as `group`, name, slug, id
                FROM acl_permissions
                WHERE deleted_at IS NULL
                GROUP BY SPLIT_STRING(slug, ':', 1), name, slug, id
        */
        DROP FUNCTION IF EXISTS SPLIT_STRING;
        CREATE FUNCTION SPLIT_STRING(str VARCHAR(255), delim VARCHAR(12), pos INT)
        RETURNS VARCHAR(255) COMMENT 'splits a string (str) by a delimeter (delim) and returns back the part on position (pos - 0 based)'
        RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos),
            LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1),
            delim, '');
SQL
        );
    }
}
