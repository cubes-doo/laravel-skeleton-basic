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
class DatatablesTableSeeder extends Seeder
{
    /**
     * Name of the table being seeded
     *
     * @var string
     */
    protected $tables = ['dt_primary', 'dt_child', 'dt_parent'];
    
    protected $primary = [
        ['title' => '01 primary 1'],
        ['title' => '02 primary 2'],
        ['title' => '03 primary 3', 'parent_id' => 1],
        ['title' => '04 primary 4', 'parent_id' => 2],
        ['title' => '05 primary 5', 'parent_id' => 3],
        ['title' => '06 primary 6', 'parent_id' => 4],
        ['title' => '07 primary 7', 'parent_id' => 5],
        ['title' => '08 primary 8', 'parent_id' => 1],
        ['title' => '09 primary 9', 'parent_id' => 2],
        ['title' => '10 primary 10', 'parent_id' => 3],
        ['title' => '11 primary 11', 'parent_id' => 4],
        ['title' => '12 primary 12', 'parent_id' => 5],
    ];
    
    protected $child = [
        ['title' => 'child 1', 'parent_id' => 1],
        ['title' => 'child 2', 'parent_id' => 2],
        ['title' => 'child 3', 'parent_id' => 3],
        ['title' => 'child 4', 'parent_id' => 4],
        ['title' => 'child 5', 'parent_id' => 5],
        ['title' => 'child 6', 'parent_id' => 6],
        ['title' => 'child 7', 'parent_id' => 7],
        ['title' => 'child 8', 'parent_id' => 8],
        ['title' => 'child 9', 'parent_id' => 9],
        ['title' => 'child 10', 'parent_id' => 10],
        ['title' => 'child 11', 'parent_id' => 11],
        ['title' => 'child 12', 'parent_id' => 12],
        ['title' => 'child 13', 'parent_id' => 12],
        ['title' => 'child 14', 'parent_id' => 12],
        ['title' => 'child 15', 'parent_id' => 12],
        ['title' => 'child 16', 'parent_id' => 12],
        ['title' => 'child 17', 'parent_id' => 12],
    ];
    
    protected $parent = [
        ['title' => 'parent 1'],
        ['title' => 'parent 2'],
        ['title' => 'parent 3'],
        ['title' => 'parent 4'],
        ['title' => 'parent 5'],
        
    ];
    
    /**
     * Runs the DB seed
     *
     * @return void
     */
    public function run()
    {
        
        foreach($this->tables as $table){
            $this->command->info("Truncating {$table} table.");
            
            \DB::table($table)->truncate();

            $this->command->info("Creating default {$table}.");
            
            if($table == 'dt_primary'){
                foreach($this->primary as $row){
                    DB::table($table)->insert($row);
                }
            }
            if($table == 'dt_child'){
                foreach($this->child as $row){
                    DB::table($table)->insert($row);
                }
            }
            if($table == 'dt_parent'){
                foreach($this->parent as $row){
                    DB::table($table)->insert($row);
                } 
            }
        }
        
    }
}
