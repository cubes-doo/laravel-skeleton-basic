<?php

namespace App\Models\Utils;

use Illuminate\Support\Str;

/**
 * Trait depending on CropImagesModelTrait
 */
trait CropImageMultiModelTrait
{
    
    
    /*
     * method signature as in StoreFilesModel (Trait)
     */
    public function processFileAfterStore($originalImage, $column)
    {
        if(!isset($this->multiImageResizeRecepies[$column])) {
            return FALSE;
        }
        
        $origImagePath =  $this->filePath($column);
        
        $origImgInfo = pathinfo($origImagePath);
        $basePath = $origImgInfo['dirname'];
        
        foreach($this->multiImageResizeRecepies[$column] as $key => $imageResizeRecipe) {
            
            $resizedImage = $this->imageManipulate($origImagePath, 
                                  $imageResizeRecipe);
            $resizedImage->save(
                $basePath 
                . DIRECTORY_SEPARATOR 
                . $origImgInfo['filename'] . '_' . $key . "." . $origImgInfo['extension'] ?? ""); 
        }
    }
}
