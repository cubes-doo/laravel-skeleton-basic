<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


/*
 * Image model
 * 
 * Relation::morphMap() je setovano u AppServiceProvider-u.
 */
class Image extends Model
{
    
    const CLASS_ORIGINAL_PHOTO= 1;
    const CLASS_RESIZED_PHOTO = 2;
    
    const CLASSES = [
        self::CLASS_ORIGINAL_PHOTO,
        self::CLASS_RESIZED_PHOTO
    ];
    
    public $timestamps = FALSE;
    
    protected $guarded = [];
    
    public $imageDisk;
    

    public function imageable()
    {
        return $this->morphTo();
    }
    
    
    /**
     * Read images specifications which overrides those from cropImageXTrait
     */
    private function readImagesSpecs()
    {
        if(isset($this->imagableClasses)) {
            return $this->imagableClasses;
        }
        if(isset($this->imageResizeRecepies)) {
            return array_keys($this->imageResizeRecepies);
        }
        if(isset($this->multiImageResizeRecepies)) {
            return array_keys($this->multiImageResizeRecepies);
        }
        return [];
    }
    
    
    
    /**
     * Store image into 'images' table
     * 
     * @param string $filename
     * @param string $class
     * 
     * @return Object | class object using this trait (for method chaining)
     */
    public function storeImages() 
    {
        $imagableClasses = $this->readImagesSpecs();
        
        
        foreach($imagableClasses as $class) {
            $entity->storeFile($column);
            
            $this->images()->create([
                "name" => $this->fileName($class),
                "class" => $class
            ]);
        }
        
        
        return $this;
    }
    
    
    /**
     * Construct image filename
     * 
     * @param type $origFilename
     * @param type $class
     */
    private function constructImageFilename($base, $class)
    {
        $filenameNoExt =  $seoPrefix . str_slug($fnameBase, '-') . $fnameSugar;
        $fullFileName =  $filenameNoExt . "." . $extension;   
        return $fullFileName;
    }
    
    /**
     * 
     * @param UploadedFile $file
     * @param string $class
     * 
     * @return boolean
     */
    public static function storeImage($file, $class, $newFilename=FALSE) 
    {
        if($newFilename) {
            $fullFilename = $this->constructImageFilename($newFilename, '');
        }               
        else {
            $fullFilename = $this->constructImageFilename($newFilename, $class);
        }
        
        $storagePath = storage_path() . "/app/public/images/"; // test path
        
        // save image to filesystem '$storagePath' and return image filename
        try {
            // NOTE: problems with public_path(). Raises \Intervention\Image\Exception\NotWritableException
            $image->save($storagePath . $fullFilename);
            $savedImageName = $fullFilename;
        } catch (\Throwable $e) {
            // NOTE: Couldn't succeed to catch only \Intervention\Image\Exception\ImageException
            //       cannot get $e->message - protected property ??!!
            // PERHAPS SOLUTION: $e->getMessage();
            $savedImageName = FALSE;
            Log::error(__METHOD__ . ' -> Save image error in: "' . __FILE__ 
                     . '". Intervention-Image error message: ' . $e->getMessage());
        }
    }
    
    public function getUrl()
    {
        
    }
    
    /*
     * Izbrisi sliku sa hard-diska i izbrisi red iz baze podataka
     */
    public function delete() 
    {
        $stat = $this->deleteImageFromHdd();
        
        if(!$stat) {
            Log::error(__METHOD__ . ": Image entry was deleted from the database in spite of previous error.");
        }
        
        parent::delete();
    }
    
    
    /**
     * Return common image folder server path
     * 
     * @return string
     */
    private function getCommonImageFolderPath()
    {
        return Storage::disk($this->imageDisk ?? config('filesystems.default'));
    }
    
    
    private function getAbsoluteServerPath()
    {
        $diskPath = $this->getCommonImageFolderPath();
        
        dd($diskPath);
    }
    
    
    /*
     * Izbrisi fajl slike sa hard-diska
     */
    public function deleteImageFromHdd()
    {
        $f = $this->getAbsoluteServerPath();
        Log::debug("deleting '$f' image form HDD");

        if(file_exists($f) && is_file($f)) {
            try {
                unlink($f);
            }
            catch (\Throwable $e) {
                Log::error(__METHOD__ . "Cannot delete image '$f'. Exception encountered. Not enough permissions?");
                return FALSE;
            }
        }
        else {
            Log::error(__METHOD__ . ": Image constructed path '$f' from columns in "
                     . "'images' table row is nonexistant to the server. "
                     . "Cannot delete that image file.");
            return FALSE;
        }
        
        return TRUE;
    }
}
