<?php

/**
 * Class
 *
 * PHP version 7.2
 */
namespace App\Models;

use Junges\ACL\Traits\UsersTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Model for the appplication user
 */
class User extends Authenticatable
{
    use Notifiable;
    use \App\Models\Utils\ImageableTrait;
    use UsersTrait;

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * Attributes that should be hidden for arrays
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $imageResizeRecepies = [
//        'multiple_images' => [
//            [
//                'type' => 'fit',
//                'w' => 400,
//                'h' => 400,
//            ],
//        ],
        'orig_image_resized_multiple' => [
            [
                'type' => 'fit',
                'w' => 350,
                'h' => 200,
            ],
        ],
        'orig_image_resized' => [
            [
                'type' => 'fit',
                'w' => 100,
                'h' => 500,
            ],
        ],
    ];

    protected $multiImageResizeRecepies = [
        'multiple_images' => [
            'avatar' => 'avatar',
            'icon' => 'thumbnail',
        ],
        'orig_image_multiple' => [
            'avatar' => 'avatar',
            'icon' => 'thumbnail',
        ],
        'orig_image_resized_multiple' => [
            'avatar' => 'avatar',
            'icon' => 'thumbnail',
        ],
    ];
}
