<?php

/**
 * Class
 *
 * PHP version 7.2
 */
use Illuminate\Database\Seeder;

use App\Models\Seed;

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

    public function call($class, $silent = false)
    {
        if(Seed::where('class', $class)->get()->isEmpty()) {
            parent::call($class, $silent);
            Seed::create(['class' => $class]);
            return $this;
        }
    }
}
