<?php

namespace App\Http\Resources\Select2;

use Illuminate\Http\Resources\Json\JsonResource;

class Tag extends JsonResource
{
    public function toArray($request)
    {
        self::wrap('results');
        
        return [
            'id' => $this->id,
            'text' => $this->title,
        ];
    }
}
