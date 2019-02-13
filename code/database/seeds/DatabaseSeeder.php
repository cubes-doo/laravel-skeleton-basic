<?php

/**
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 * @copyright  Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 */

use Illuminate\Database\Seeder;

/**
 * Main seeder class
 * 
 * @category   Class
 * @package    Cubes
 * @copyright  Cubes d.o.o.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Runs the DB seeds
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
    }
}
