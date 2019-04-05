<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Utils\CropImageModelTrait;

/*
 * Image model
 * 
 * Relation::morphMap() is set in ImageableTrait or AppServiceProvider.
 * 
 * TODO: Get paths for 'server-storage' and 'public-storage' form \Storage.
 */
class Image extends Model
{
    use CropImageModelTrait;
    
    const IMAGE_FOLDER_NAME = "images";
    
    /*
     * Constants used for merging recepies arrays ( see mergeRecepies() and 
     * storeImageWithActions() )
     * RR - Resize Recipe
     */
    const RR_SINGLE = "single";
    const RR_MULTI = "multi";
    
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
     * Read image resize recepies from this model and merge them with 
     * $recipeArrStack.
     * 
     * @param Array $recipeArrStack
     * 
     * @return void
     */
    private function mergeRecepies(&$recipeArrStack)
    {
        if(isset($this->multiImageResizeRecepies)) {
            $recipeArrStack[self::RR_MULTI] = array_merge($recipeArrStack[self::RR_MULTI], 
                                                          $this->multiImageResizeRecepies);
        }
        if(isset($this->imageResizeRecepies)) {
            $recipeArrStack[self::RR_SINGLE] = array_merge($recipeArrStack[self::RR_SINGLE],
                                                           $this->imageResizeRecepies);
        }
    }
    
    /**
     * Construct image filename from received filepath or file object.
     * 
     * @param string|UplodedFile $fileSrc
     * @param integer $imageId  | Image ID in 'images' table
     * @param string  $addendum | insert this string into filename
     * @param booled  $path     | return absolute path containing new filename
     * 
     * @return string
     */
    public static function constructImageFilename($fileSrc, $imgId, $addendum, $path=FALSE)
    {
        $templateStr = "%d-%s-%s.%s";
        
        if($fileSrc instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $file = $fileSrc;
            $filepath = $file->getClientOriginalName();
            $fileinfo = pathinfo($filepath);
        }
        elseif(is_string($fileSrc)) {
            $filepath = $fileSrc;
            $fileinfo = pathinfo($filepath);
        }
        else {
            throw new \InvalidArgumentException("'\$addendum' parameter must be"
                                              . "of type string or UploadedFile");
        }
        
        $fnameBase = $fileinfo['filename'];
        $ext = $fileinfo['extension'] ?? '';
        
        $newFilename = sprintf($templateStr, $imgId, $fnameBase, $addendum, $ext);
        
        if($path) {
            return $fileinfo['dirname'] . DIRECTORY_SEPARATOR . $newFilename;
        }
        return $newFilename;
    }
    
    /**
     * Store image to hard-drive
     * 
     * @param UploadedFile $file
     * @param integer      $imageId       | Image ID in 'images' table
     * @param string       $nameAddendum  | insert this string in filename
     * @param mixed        $constructFilname (\Closure or string)
     * 
     * @return string|FALSE
     */
    public static function storeImageToHdd($file, $imgId, $nameAddendum='', 
                                           $constructFilname=NULL) 
    {
        if(is_string($constructFilname)) {
            $fullFilename = $imgId . $constructFilname;
        }               
        else if(is_callable($constructFilname)) {
            $fullFilename = $constructFilname($file, $imgId, $nameAddendum);
        }               
        else {
            $fullFilename = self::constructImageFilename($file, $imgId, 
                                                         $nameAddendum, FALSE);
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
    public function storeImageWithActions($entity, $file, $class, $imgRecepiesStack,  
                                          $constructFilnameFunc=NULL)
    {
        //$this->mergeRecepies($imgRecepiesStack); // UNTESTED
        
        $origImgObj = $entity->images()->create([
            "name" => "temporary",
            "class" => $class
        ]);
        
        if (count($imgRecepiesStack[self::RR_SINGLE]) > 0) {
            $this->processFileBeforeStore($file, $class, $imgRecepiesStack[self::RR_SINGLE]);
        }

        $origImgName = self::storeImageToHdd($file, $origImgObj->id, $class,
                                             $constructFilnameFunc);
        
        $origImgObj->name = $origImgName;
        $origImgObj->save();
        
        $origImgSavePath = $this->getImagesStoragePath() . $origImgName;
        
        if (count($imgRecepiesStack[self::RR_MULTI]) > 0) { 
            $this->processFileAfterStore($entity, $origImgObj->id, $origImgSavePath, 
                                         $class, $imgRecepiesStack[self::RR_MULTI]);
        }
        
        return $origImgName;
    }
    
    /*
     * Store image with processBefore and processAfter calls
     */
    public function storeImagesWithActions($entity, $files, $class, $imgRecepiesStack, 
                                           $constructFilnameFunc=NULL)
    {
        foreach ($files as $file) {
            $this->storeImageWithActions($entity, $file, $class, $imgRecepiesStack, 
                                         $constructFilnameFunc);
        }
        return;
    }
    
    /**
     * Process an image with image resize recepies using CropImageModelTrait
     * 
     * @return boolean
     */
    protected function processFileBeforeStore($originalImage, $class, $imageResizeRecepies)
    {
        if(!isset($imageResizeRecepies[$class])) {
            return FALSE;
        }
        $resizedImage = $this->imageManipulate($originalImage, 
                                               $imageResizeRecepies[$class]);
        $resizedImage->save();
        
        return TRUE;
    }
    
    /**
     * Process an image with multiple resize recepies using CropImageModelTrait
     * 
     * @return boolean
     */
    public function processFileAfterStore($entity, $origImgId, $origImagePath, $class, 
                                          $multiImageResizeRecepies)
    {
        if(!isset($multiImageResizeRecepies[$class])) {
            return FALSE;
        }
        
        $origImgInfo = pathinfo($origImagePath);
        $basePath = $origImgInfo['dirname'];
        
        foreach($multiImageResizeRecepies[$class] as $key => $imageResizeRecipe) {
            
            $imgObj = $entity->images()->create([
                        "name" => "temporary",
                        "class" => $key,
                        "parent_id" => $origImgId
                    ]);
            
            $resizedImage = $this->imageManipulate($origImagePath, 
                                                   $imageResizeRecipe);
            $newFilepath = self::constructImageFilename($origImagePath, $imgObj->id, 
                                                        $key, TRUE);
            $resizedImage->save($newFilepath);

            $imgObj->name = basename($newFilepath);
            $imgObj->save();
        }
        
        return TRUE;
    }
    
    /**
     * Return image URL
     * 
     * @return string
     */
    public function getUrl()
    {
        // TODO: get "/storage/" folder from laravel's \Storage facade
        return "/storage/" . self::IMAGE_FOLDER_NAME . DIRECTORY_SEPARATOR . $this->name;  
    }
    
    /**
     * Get application's absolute storage path
     * 
     * @return string
     */
    protected function getStoragePath()
    {
        return "/opt/public/storage/"; // TODO: get path from laravel's \Storage facade
    }
    
    /**
     * Return absolute storage path for images
     * 
     * @return string
     */
    protected function getImagesStoragePath()
    {
        return $this->getStoragePath() . self::IMAGE_FOLDER_NAME . DIRECTORY_SEPARATOR;
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
     * Erase image from the hard-drive and erase image's entry from the database 
     * table
     * 
     * @return void
     */
    public function delete() 
    {
        $stat = $this->deleteFileFromHdd();
        
        if(!$stat) {
            Log::error(__METHOD__ . ": Image entry was deleted from the database " 
                                  . " in spite of previous error.");
        }
        
        parent::delete();
    }
    
    /**
     * Erase image file from the hard-drive
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
                Log::error(__METHOD__ . "Cannot delete image '$f'. Exception "
                         . "encountered. Not enough permissions?");
                return FALSE;
            }
        }
        else {
            Log::error(__METHOD__ . ": Image constructed path '$f' from columns "
                     . "in 'images' table row is nonexistant to the server. "
                     . "Cannot delete that image file.");
            return FALSE;
        }
        
        return TRUE;
    }
}
