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

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Model for the appplication user
 * 
 * @category   Class
 * @package    Cubes
 * @copyright  Cubes d.o.o.
 */
class User extends Authenticatable
{
    use Notifiable;
    use \App\Models\Utils\ImageableTrait;

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
        
        "test" => [
            [
                "type" => "fit",
                "w" => 400,
                "h" => 400,
            ]
        ] 
    ];

    protected $multiImageResizeRecepies = [
            "test" => [
                'avatar' => "avatar",
                'm' => "thumbnail"
            ]
    ];
}
