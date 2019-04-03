<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Utils\CropImageModelTrait;

/*
 * Image model
 * 
 * Relation::morphMap() is set in AppServiceProvider.
 */
class Image extends Model
{
    use CropImageModelTrait;
    
    const IMAGE_FOLDER_NAME = "images";
    
    public $timestamps = FALSE;
    protected $guarded = [];
    public static $imageDisk;
    
    
    /*
     * Morph relation with imagable entities
     */
    public function imageable()
    {
        return $this->morphTo();
    }
    
    /**
     * NOT IMPLEMENTED !
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
     * Construct image filename
     * 
     * @param UploadedFile $file
     * @param string $class
     * 
     * @return string
     */
    private static function constructImageFilename($file, $class)
    {
        $filename = $file->getClientOriginalName();
        $fileinfo = pathinfo($filename);
        return str_slug($fileinfo['filename']) . '_' . $class . '.' . ($fileinfo['extension'] ?? '');
    }
    
    /**
     * Store image to hard-drive
     * 
     * @param UploadedFile $file
     * @param string       $class
     * @param mixed        $constructFilname (\Closure or string)
     * 
     * @return string|FALSE
     */
    public static function storeImageToHdd($file, $class, \Closure $constructFilname=NULL) 
    {
        if(is_string($constructFilname)) {
            $fullFilename = $constructFilname;
        }               
        else if(is_callable($constructFilname)) {
            $fullFilename = $constructFilname($file, $class);
        }               
        else {
            $fullFilename = self::constructImageFilename($file, $class);
        }
        
        try {
            $file->storeAs(self::IMAGE_FOLDER_NAME, $fullFilename, [
                'disk' => "public"
            ]);
        } catch (\Throwable $e) {
            $fullFilename = FALSE;
            Log::error(__METHOD__ . ' -> Save image error in: "' . __FILE__ 
                     . '". Intervention-Image error message: ' . $e->getMessage());
        }
        return $fullFilename;
    }
    
    /*
     * Store image with processBefore and processAfter calls
     */
    public function storeImageWithActions($entity, $file, $class, $imageResizeRecepies=[], 
                                          $multiImageResizeRecepies=[], $constructFilnameFunc=NULL)
    {
        if (count($imageResizeRecepies) > 0) {
            $this->processFileBeforeStore($file, $class, $imageResizeRecepies);
        }

        $origImgName = self::storeImageToHdd($file, $class, $constructFilnameFunc);
                
        $origImgObj = $entity->images()->create([
            "name" => $origImgName,
            "class" => $class
        ]);
        
        $origImgSavePath = $this->getImagesStoragePath() . $origImgName;
        
        if (count($multiImageResizeRecepies) > 0) { 
            $this->processFileAfterStore($entity, $origImgObj->id, $origImgSavePath, $class, 
                                         $multiImageResizeRecepies, $origImgName);
        }
        
        return $origImgName;
    }
    
    /*
     * method signature as in StoreFilesModel (Trait)
     */
    protected function processFileBeforeStore($originalImage, $class, $imageResizeRecepies)
    {
        if(!isset($imageResizeRecepies[$class])) {
            return FALSE;
        }
        $resizedImage = $this->imageManipulate($originalImage, 
                              $imageResizeRecepies[$class]);
        $resizedImage->save();
        
        return $resizedImage;
    }
    
    /*
     * method signature as in StoreFilesModel (Trait)
     */
    public function processFileAfterStore($entity, $origImgId, $origImagePath, $class, 
                                          $multiImageResizeRecepies, $origImgName )
    {
        if(!isset($multiImageResizeRecepies[$class])) {
            return FALSE;
        }
        
        $origImgInfo = pathinfo($origImagePath);
        $basePath = $origImgInfo['dirname'];
        
        foreach($multiImageResizeRecepies[$class] as $key => $imageResizeRecipe) {
            
            $resizedImage = $this->imageManipulate($origImagePath, 
                                  $imageResizeRecipe);
            $resizedImage->save(
                $basePath 
                . DIRECTORY_SEPARATOR 
                . $origImgInfo['filename'] . '_' . $key . "." . ($origImgInfo['extension'] ?? "") ); // TODO: insert common func for name construction
            
            $entity->images()->create([
                "name" => $origImgInfo['filename'] . '_' . $key . "." . ($origImgInfo['extension'] ?? ""), // TODO: insert common func for name construction
                "class" => $key,
                "parent_id" => $origImgId
            ]);
        }
    }
    
    /**
     * Return image URL
     * 
     * @return string
     */
    public function getUrl()
    {
        return "/storage/" . self::IMAGE_FOLDER_NAME . "/" . $this->name;  
    }
    
    /*
     * Get application's absolute storage path
     */
    protected function getStoragePath()
    {
        return "/opt/public/storage/";
    }
    
    /**
     * Return absolute storage path for images
     * 
     * @return string
     */
    protected function getImagesStoragePath()
    {
        return $this->getStoragePath() . self::IMAGE_FOLDER_NAME . '/';
    }
    
    /**
     * Return absolute file path from this image instance
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->getImagesStoragePath() . $this->name;
    }
    
    /**
     * Izbrisi sliku sa hard-diska i izbrisi red iz baze podataka
     * 
     * @return void
     */
    public function delete() 
    {
        $stat = $this->deleteFileFromHdd();
        
        if(!$stat) {
            Log::error(__METHOD__ . ": Image entry was deleted from the database in spite of previous error.");
        }
        
        parent::delete();
    }
    
    /**
     * Erase image file from the Hard-drive
     * 
     * @return boolean
     */
    public function deleteFileFromHdd()
    {
        $f = $this->getPath();
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
