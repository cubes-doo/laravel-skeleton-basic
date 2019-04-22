<?php

/**
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 *
 * @copyright  2015-2018 Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * @version    GIT: 1.0.0
 */

namespace App\Http\Resources;

/**
 * Description of Example
 *
 * @category   Class
 *
 * @copyright  2015-2018 Cubes d.o.o.
 *
 * @version    GIT: 1.0.0
 */
class Entity extends Json
{
    public function toArray($request)
    {
        return [
            'id' => 'test',
            'title' => 'test',
            'description' => 'test',
            'created_at' => 'test',
        ];
    }
}
