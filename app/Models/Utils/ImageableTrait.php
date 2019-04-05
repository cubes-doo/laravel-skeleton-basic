<?php

namespace App\Models\Utils;
use App\Models\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Trait for models which are using 'images' table with Image model.
 * This trait is coupled with Image model.
 */
trait ImageableTrait
{
    /**
     * Make morph map relation ( string(morphKey) pointing to class namespace )
     */
    public static function bootImageableTrait()
    {
        /* morphMap for Image model */
        Relation::morphMap([
            static::getImagableMorphKey() => static::class
        ]);
    }
    
    /*
     * Get model's protected property $table as morph key.
     */
    public static function getImagableMorphKey()
    {
        return (new static())->getTable();
    }
    
    
    /*
     * Establishes a polymorphic 'one-to-many' relationship with 'images' table
     */
    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
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
     * Return image resize recepies (single and multi) stack of arrays.
     * 
     * @return Array
     */
    private function getImgRecepiesStack()
    {
        $imgRecepiesStack = [
            Image::RR_SINGLE => [],
            Image::RR_MULTI => []
        ];
        
        if(isset($this->imageResizeRecepies)) {
            $imgRecepiesStack[Image::RR_SINGLE] = $this->imageResizeRecepies;
        }
        
        if(isset($this->multiImageResizeRecepies)) {
            $imgRecepiesStack[Image::RR_MULTI] = $this->multiImageResizeRecepies;
        }
        
        return $imgRecepiesStack;
    }
    
    /**
     * @param string $class - !!!NOT A PHP CLASS
     *                        examples:
     *                          "avatar", "icon" ...
     *                        i.e. an image (or image size) associated to the model 
     *                        where this trait is used
     * @param mixed $file (null|string|UploadedFile)
     * @param $newFilename 
     * 
     * @return boolean
     */
    public function storeImage($class, $file = NULL, $newFilename = NULL)
    {
        if(is_null($file)) {
            $file = request()->file($class);
        }
        
        if (is_string($file)) {
            $file = request()->file($file);
        }
        
        if(!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException;
        }
        
        $imgRecepiesStack = $this->getImgRecepiesStack();

        $imgObj = new Image();
        $imgObj->storeImageWithActions($this, $file, $class, $imgRecepiesStack, 
                                       $newFilename);
        
        return TRUE;
    }
    
    
    /**
     * Store multiple images in a single call
     * 
     * @param type $class
     * @param type $files
     * @param type $newFilename
     * 
     * @throws \InvalidArgumentException
     * 
     * @return boolean
     */
    public function storeImages($class, $files = NULL, $newFilename = NULL)
    {
        if(is_null($files)) {
            $files = collect(request()->file($class))->flatten()->toArray();
        }
        
        if (is_string($files)) {
            $files = collect(request()->file($files))->flatten()->toArray();
        }
        
        foreach($files as $file) {
            if(!$file instanceof UploadedFile) {
                throw new \InvalidArgumentException;
            }
        }
        
        $imgRecepiesStack = $this->getImgRecepiesStack();
        
        $imgObj = new Image();
        $imgObj->storeImagesWithActions($this, $files, $class, $imgRecepiesStack, 
                                        $newFilename);
        return TRUE;
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
    
}
