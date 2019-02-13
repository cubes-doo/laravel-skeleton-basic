<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    protected $table = 'users';

    protected $defaultUsers = [
        [
            'first_name' => 'Name',
            'last_name' => 'Surname',
        ],
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Truncating {$this->table} table.");
        
        \DB::table($this->table)->truncate();
        
        $this->command->info('Creating default users.');
        
        foreach($this->defaultUsers as $user) {
            factory(App\Models\User::class)->create([
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => 
                    strtolower($user['first_name']) 
                    . '.' 
                    . strtolower($user['last_name']) 
                    . '@company.com'
            ]);
        }

        // $count = (int) $this->command->ask('How many additional users do you need?', 0);
        
        // $this->command->info("Creating {$count} more users.");

        // factory(App\Models\User::class, $count)->create();
    }
}
