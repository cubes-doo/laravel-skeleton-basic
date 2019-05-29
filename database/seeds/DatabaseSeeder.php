<?php

/**
 * Class
 *
 * PHP version 7.2
 */
use Illuminate\Database\Seeder;

/**
 * Main seeder class
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Runs the DB seeds
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(AclSeeder::class);
        $this->call(ExamplesTableSeeder::class);
        $this->call(DatatablesTableSeeder::class);
    }
}
