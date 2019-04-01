<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models\Utils;

/**
 * Trait for models which are using 'images' table with Image model.
 */
trait ImageableModelTrait {
    
    
    /*
     * Polymorphic 'one-to-many' relationship with 'images' table.
     */
    public function images()
    {
        return $this->morphMany(get_class(), 'imageable');
    }
    
    /**
     * Store image into 'images' table
     * 
     * @param string $filename
     * @param string $class
     * 
     * @return Object | class object using this trait (for method chaining)
     */
    public function storeImage($filename, $column, $class) 
    {
        $this->images()->create([
            "name" => $filename,
            "class" => $class
        ]);
        
        $entity->storeFile($column);
        
        return $this;
    }
    
    /**
     * Delete image from 'images' table
     * 
     * @param string $class
     * 
     * @return void
     */
    public function deleteImage($class)
    {
        $this->images()->where('class', $class)->delete();
    }
    
    /**
     * Delete all images bound to the model using this trait, from 'images' table.
     * 
     * @return void
     */
    public function deleteImages()
    {
        $this->images()->delete();
    }
    
    
    /**
     * @param string $class
     * 
     * @return string
     */
    public function getImageUrl($class)
    {
        return $this->images()->where('class', $class)->first();
    }
    
    
    /**
     * Get first image of a class
     * 
     * @param string $class
     * 
     * @return Image
     */
    public function getImage($class) 
    {
        return $this->images()
                    ->where('class', $class)
                    ->first();
    }
    
    /**
     * Get all images or images of a class
     * 
     * @param string $class
     * 
     * @return Collection
     */
    public function getImages($class=FALSE) 
    {
        if($class) {
            return $this->images()
                        ->where('class', $class)
                        ->get();
        }
        return $this->images()->get();
    }
}
