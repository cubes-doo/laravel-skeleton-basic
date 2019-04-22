<?php

/**
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 *
 * @copyright  Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 */

use App\Models\{Example, User};

use Illuminate\Database\Seeder;

/**
 * Seeder for Users model
 *
 * @category   Class
 *
 * @copyright  Cubes d.o.o.
 */
class ExamplesTableSeeder extends Seeder
{
    /**
     * Name of the table being seeded
     *
     * @var string
     */
    protected $table = 'examples';
    
    /**
     * Runs the DB seed
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Truncating {$this->table} table.");
        
        \DB::table($this->table)->truncate();
        
        $this->command->info("Creating default {$this->table}.");

        User::all()->each(function ($item, $key) {
            $forThisUser = [
                'created_by' => $item->id,
            ];
            $factory = factory(Example::class, 10);
            // active
            $factory->create($forThisUser + [
                'active' => Example::ACTIVE,
            ]);
            // in-active
            $factory->create($forThisUser + [
                'active' => Example::INACTIVE,
            ]);
            // by-status
            foreach (Example::STATUSES as $status) {
                $factory->create($forThisUser + [
                    'status' => $status,
                ]);
            }
            // deleted
            $factory->create($forThisUser + [
                'deleted_at' => now()->format('Y-m-d H:i:s'),
            ]);
        });
        

        // optionally for testing...
        // $count = (int) $this->command->ask('How many additional examples do you need?', 0);
        
        // $this->command->info("Creating {$count} more examples.");

        // factory(Example::class, $count)->create();
    }
}
