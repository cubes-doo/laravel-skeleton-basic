<?php

namespace App\Http\Controllers\Traits;

use App\Models\Tag as Entity;
use App\Http\Resources\Select2\Tag as Resource;
use App\Http\Resources\Json as JsonResource;


/**
 * Trait is intended to use with 'tagsSelect' jQuery Plugin
 */
trait TagsSelectTrait
{   
    /**
     * This ajax method returns filtered records from 'tags' table
     * 
     * @return JSON
     */
    public function selectTag()
    {   
        $selections = Entity::query();
        
        $request = $this->request;
        
        $data = $request->validate([
            'term' => 'nullable|string',
        ]);
            
        if (! empty($data['term'])) {
            $selections->where(function ($q) use ($data) {
                $s = ['LIKE', '%' . $data['term'] . '%'];
                $q->where(function ($query) use ($s) {
                    $query->where('title', ...$s);
                });
            });
        }

        $selections = $selections->get();
        if ($selections->isEmpty()) {
            return response()->json(['results' => []]);
        }
        return Resource::collection($selections);
    }
    
    /**
     * This ajax method create new record in 'tags' table
     * 
     * @return JSON
     */
    public function createTag()
    {
        $request = $this->request;
        
        $data = request()->validate([
                'title' => 'required|string|max:121'
        ]);
        
        $entity = new Entity();
        $entity->fill($data);

        $entity->save();
            
        return JsonResource::make([
                'id' => $entity->id,
                'title' => $entity->title,
            ])->withSuccess(__('Tag has been saved!'));
    }
}

