<?php

namespace App\Models\Utils;

/**
 * Trait depending on CropImagesModelTrait
 */
trait CropImageSingleModelTrait 
{
    /*
     * method signature as in StoreFilesModel (Trait)
     */
    public function processFileBeforeStore($originalImage, $column)
    {
        if(!isset($this->imageResizeRecepies[$column])) {
            return FALSE;
        }
        $resizedImage = $this->imageManipulate($originalImage, 
                              $this->imageResizeRecepies[$column]);
        $resizedImage->save();
        
        return $resizedImage;
    }
}
