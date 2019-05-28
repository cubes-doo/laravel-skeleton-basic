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

use App\Lib\HelperPack;
use Illuminate\Database\Seeder;
use Junges\ACL\Http\Models\Permission;
use Junges\ACL\Http\Models\Group;
use App\Models\User;

/**
 * Seeder for Users model
 *
 * @category   Class
 *
 * @copyright  Cubes d.o.o.
 */
class AclSeeder extends Seeder
{
    /**
     * Users to be seeded by default
     *
     * @var array
     */
    protected $defaultGroups = [
        'admin' => [
            '*'
        ],
        'moderator' => [
            'user:*'
        ],
        'editor' => [
            'example:*',
            'user:read'
        ],
    ];
    
    /**
     * Runs the DB seed
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Truncating acl tables.");
        
        foreach(config('acl.tables') as $key => $value) {
            if($key == 'users') {
                // there's another seeder that takes care of truncating this table
                continue;
            }

            \DB::raw("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE table `$value`; SET FOREIGN_KEY_CHECKS = 1;");
        }
        
        $this->command->info('Creating default permisssions.');
        foreach (get_models() as $model) {
            foreach(HelperPack::generateCrudPermissionsForModel($model) as $value) {
                Permission::create($value);
            }
        }

        $this->command->info('Creating default groups.');
        
        foreach ($this->defaultGroups as $group => $permissions) {
            $groupObj = Group::create([
                'name' => ucfirst($group),
                'slug' => $group,
            ]);

            foreach($permissions as $permission) {
                switch ($permission) {
                    case '*':
                        $groupObj->assignAllPermissions();
                        break;
                    case preg_match('/.+\:\*/', $permission) ? true : false:
                        $model = explode(':', $permission)[0];
                        $modelCrud = [
                            $model . ':create',
                            $model . ':read',
                            $model . ':update',
                            $model . ':delete',
                        ];
                        $groupObj->assignPermissions($modelCrud);
                        break;
                    default:
                        $groupObj->assignPermissions([$permission]);
                        break;
                }
            }
        }

        $this->command->info('Everyone\'s an admin!');

        foreach(User::get() as $user) {
            $user->assignGroup(['admin']);
        }
    }
}
