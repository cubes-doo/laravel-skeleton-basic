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
use App\Lib\HelperPack;

/**
 * Seeder for Users model
 * 
 * @category   Class
 * @package    Cubes
 * @copyright  Cubes d.o.o.
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Name of the table being seeded
     * 
     * @var string
     */
    protected $table = 'users';

    /**
     * Users to be seeded by default
     * 
     * @var array
     */
    protected $defaultUsers = [
        [
            'first_name' => 'Name',
            'last_name' => 'Surname',
        ],
    ];
    
    /**
     * Runs the DB seed
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Truncating {$this->table} table.");
        
        \DB::table($this->table)->truncate();
        
        $this->command->info('Creating default users.');
        
        foreach($this->defaultUsers as $user) {
            $email = HelperPack::generateEmailStr('cubes.rs', '.', $user);
            
            factory(App\Models\User::class)->create([
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => $email
            ]);
        }

        // optionally for testing...
        // $count = (int) $this->command->ask('How many additional users do you need?', 0);
        
        // $this->command->info("Creating {$count} more users.");

        // factory(App\Models\User::class, $count)->create();
    }
}
