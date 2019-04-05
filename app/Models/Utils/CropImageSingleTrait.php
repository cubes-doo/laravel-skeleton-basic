<?php

namespace App\Models\Utils;

/**
 * Trait depending on CropImagesModelTrait
 */
trait CropImageSingleTrait 
{
    /*
     * method signature as in StoreFilesTrait
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
