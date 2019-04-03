<?php

namespace App\Models\Utils;
use App\Model\Image;

/**
 * Trait for models which are using 'images' table with Image model.
 * This trait is coupled with Image model.
 */
trait ImageableTrait
{
    /*
     * Establishes a polymorphic 'one-to-many' relationship with 'images' table
     */
    public function images()
    {
        return $this->morphMany(get_class(), 'imageable');
    }
    
    /**
     * Delete one image from 'images' table
     * 
     * @param string $class - !!!NOT A PHP CLASS
     *                        examples:
     *                          "avatar", "icon" ...
     *                        i.e. an image (or image size) associated to the model 
     *                        where this trait is used
     * @return void
     */
    public function deleteImage($class = NULL, $deleteChildren = TRUE)
    {
        $images = $this->images();
        
        if(!empty($class)) {
            $images = $images->where('class', $class);
        }

        $first = $images()->first();

        if($deleteChildren) {
            $images->where('parent_id', $first->id)->delete();
        }

        $first->delete();
    }
    
    /**
     * Delete all images bound to the model using this trait, from 'images' table.
     * 
     * @param string $class - !!!NOT A PHP CLASS
     *                        examples:
     *                          "avatar", "icon" ...
     *                        i.e. an image (or image size) associated to the model 
     *                        where this trait is used
     * @return void
     */
    public function deleteImages($class = NULL, $deleteChildren = true)
    {
        $images = $this->images();
        
        if(!empty($class)) {
            $images = $images->where('class', $class);
        }
        
        $images->get()->map(function($item, $key) use($deleteChildren, $images) {
            if($deleteChildren) {
                $images->where('parent_id', $item->id)->delete();
            }
            $item->delete();
        });
    }
    
    /**
     * @param string $class - !!!NOT A PHP CLASS
     *                        examples:
     *                          "avatar", "icon" ...
     *                        i.e. an image (or image size) associated to the model 
     *                        where this trait is used
     * @return string
     */
    public function getImageUrl($class)
    {
        return $this->images()->where('class', $class)->first()->getUrl();
    }
    
    /**
     * Get first image of a class
     * 
     * @param string $class - !!!NOT A PHP CLASS
     *                        examples:
     *                          "avatar", "icon" ...
     *                        i.e. an image (or image size) associated to the model 
     *                        where this trait is used
     * @return Image
     */
    public function getImage($class = NULL) 
    {
        $images = $this->images();

        if(!empty($class)) {
            $images = $images->where('class', $class);
        }
        
        return $images->first();
    }
    
    /**
     * Get all images or images of a class
     * 
     * @param string $class - !!!NOT A PHP CLASS
     *                        examples:
     *                          "avatar", "icon" ...
     *                        i.e. an image (or image size) associated to the model 
     *                        where this trait is used
     * @return Collection
     */
    public function getImages($class = NULL) 
    {
        $images = $this->images();

        if(!empty($class)) {
            $images = $images->where('class', $class);
        }

        return $images->get();
    }
    
    
    /**
     * @param string $class - !!!NOT A PHP CLASS
     *                        examples:
     *                          "avatar", "icon" ...
     *                        i.e. an image (or image size) associated to the model 
     *                        where this trait is used
     * @param mixed $file (null|string|UploadedFile)
     * @param $newFilename 
     */
    public function storeImage($class, $file = NULL, $newFilename = FALSE)
    {
        if(is_null($file)) {
            $file = request()->file($class);
        }
        
        if (is_string($file)) {
            $file = request()->file($file);
        }
        
        if(!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            throw new \InvalidArgumentException;
        }
        
        return Image::storeImage($file, $class, $newFilename);
    }
}
