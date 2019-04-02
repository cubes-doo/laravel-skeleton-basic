<?php

namespace App\Models\Utils;
use App\Model\Image;

/**
 * Trait for models which are using 'images' table with Image model.
 * This trait is coupled with Image model.
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
     * Delete one image from 'images' table
     * 
     * @param string $class
     * 
     * @return void
     */
    public function deleteImage($class=NULL)
    {
        if($class) {
            $this->images()->where('class', $class)->first()->delete();
        }
        else {
            $this->images()->first()->delete();
        }
    }
    
    /**
     * Delete all images bound to the model using this trait, from 'images' table.
     * 
     * @return void
     */
    public function deleteImages($class=NULL)
    {
        $images = $this->images();
        if($class) {
            $images = $images->where('class', $class);
        }
        
        $images->get()->map(function($item) {
            $item->delete();
        });
    }
    
    /**
     * @param string $class
     * 
     * @return string
     */
    public function getImageUrl($class)
    {
        $image = $this->images()->where('class', $class)->first();
        return $image->getUrl();
    }
    
    /**
     * Get first image of a class
     * 
     * @param string $class
     * 
     * @return Image
     */
    public function getImage($class=FALSE) 
    {
        if($class) {
            return $this->images()
                        ->where('class', $class)
                        ->first();
        }
        return $this->images()->first();
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
    
    
    /**
     * mixed $file (null|string|UploadedFile)
     */
    public function storeImage($class, $file, $newFilename=False)
    {
        $fileObj = null;
        
        if(is_null($file)) {
            $fileObj = request()->file($class);
        }
        else if (is_string($file)) {
            $fileObj = request()->file($file);
        }
        else {
            $fileObj = $file;
        }
        
        if(!$fileObj instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            throw new \InvalidArgumentException;
        }
        
        $stat = Image::storeImage($fileObj, $class, $newFilename);
        return $stat;
    }
}
