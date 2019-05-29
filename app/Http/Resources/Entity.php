<?php

/**
 * Class
 *
 * PHP version 7.2
 */
namespace App\Http\Resources;

/**
 * Description of Example
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
