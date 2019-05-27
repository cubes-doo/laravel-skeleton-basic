<?php

namespace App\Http\Resources\Select2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionGroup extends ResourceCollection
{
    public function toArray($request)
    {
        self::wrap('results');
        $group = explode(':', $this->first()->slug)[0];

        return [
            'id'       => $group,
            'text'     => ucwords(str_replace('_', ' ', $group)),
            'children' => $this->map(function ($value) {
                return [
                    'id'       => $value->id,
                    'text'     => $value->name,
                ];
            })
        ];
    }
}
