<?php

return [

    /*
    |--------------------------------------------------------------------------
    |  Models
    |--------------------------------------------------------------------------
    |
    | When using this package, we need to know which Eloquent Model should be used
    | to retrieve your groups and permissions. Of course, it is just the basics models
    | needed, but you can use whatever you like.
    |
     */
    'models' => [
        /*
         | The model you want to use as User Model must use MateusJunges\ACL\Traits\UsersTrait
         */
        'user' => \App\Models\User::class,

        /*
         | The model you want to use as Permission model must use the MateusJunges\ACL\Traits\PermissionsTrait
         */
        'permission' => Junges\ACL\Http\Models\Permission::class,

        /*
         | The model you want to use as Group model must use the MateusJunges\ACL\Traits\GroupsTrait
         */
        'group' => Junges\ACL\Http\Models\Group::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    | Specify the basics authentication tables that you are using.
    | Once you required this package, the following tables are
    | created by default when you run the command
    |
    | php artisan migrate
    |
    | If you want to change this tables, please keep the basic structure unchanged.
    |
     */
    'tables' => [
        'users' => 'users',
        'groups' => 'acl_groups',
        'permissions' => 'acl_permissions',
        'group_has_permissions' => 'acl_group_has_permissions',
        'user_has_permissions' => 'acl_user_has_permissions',
        'user_has_groups' => 'acl_user_has_groups',
    ],

];
