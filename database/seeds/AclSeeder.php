<?php

/**
 * Class
 *
 * PHP version 7.2
 */
use App\Models\User;
use App\Lib\HelperPack;
use Illuminate\Database\Seeder;
use Junges\ACL\Http\Models\Group;
use Junges\ACL\Http\Models\Permission;

/**
 * Seeder for Users model
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
            '*',
        ],
        'moderator' => [
            'user:*',
        ],
        'editor' => [
            'example:*',
            'user:read',
        ],
    ];

    protected $entityActions = [
        'datatable_child' => [
            'name' => 'Datatable Child',
            'actions' => ['create', 'read', 'update', 'delete'],
        ],
        'datatable_parent' => [
            'name' => 'Datatable Parent',
            'actions' => ['create', 'read', 'update', 'delete'],
        ],
        'datatable_primary' => [
            'name' => 'Datatable Primary',
            'actions' => ['create', 'read', 'update', 'delete'],
        ],
        'example' => [
            'name' => 'Example',
            'actions' => ['create', 'read', 'update', 'delete'],
        ],
        'file' => [
            'name' => 'File',
            'actions' => ['create', 'read', 'update', 'delete'],
        ],
        'collection' => [
            'name' => 'Collection',
            'actions' => ['create', 'read', 'update', 'delete'],
        ],
        'collection.image' => [
            'name' => 'Collection Image',
            'actions' => ['create', 'read', 'update', 'delete', 'crop'],
        ],
        'user' => [
            'name' => 'User',
            'actions' => ['create', 'read', 'update', 'delete'],
        ],
    ];
    
    /**
     * Runs the DB seed
     */
    public function run()
    {
        $this->command->info('Truncating acl tables.');
        
        foreach (config('acl.tables') as $key => $value) {
            if ($key == 'users') {
                // there's another seeder that takes care of truncating this table
                continue;
            }

            \DB::raw("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE table `$value`; SET FOREIGN_KEY_CHECKS = 1;");
        }
        
        $this->command->info('Creating default permisssions.');
        foreach ($this->entityActions as $slug => $entity) {
            foreach (HelperPack::generateCrudPermissionsForModel($slug, $entity['name'], $entity['actions']) as $value) {
                Permission::create($value);
            }
        }

        $this->command->info('Creating default groups.');
        
        foreach ($this->defaultGroups as $group => $permissions) {
            $groupObj = Group::create([
                'name' => ucfirst($group),
                'slug' => $group,
            ]);

            foreach ($permissions as $permission) {
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

        foreach (User::get() as $user) {
            $user->assignGroup(['admin']);
        }
    }
}
