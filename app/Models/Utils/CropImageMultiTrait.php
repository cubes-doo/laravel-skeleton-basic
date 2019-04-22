<?php

namespace App\Models\Utils;

/**
 * Trait depending on CropImagesModelTrait
 */
trait CropImageMultiTrait
{
    
    
    /*
     * method signature as in StoreFilesTrait
     */
    public function processFileAfterStore($originalImage, $column)
    {
        if (! isset($this->multiImageResizeRecepies[$column])) {
            return false;
        }
        
        $origImagePath = $this->filePath($column);
        
        $origImgInfo = pathinfo($origImagePath);
        $basePath = $origImgInfo['dirname'];
        
        foreach ($this->multiImageResizeRecepies[$column] as $key => $imageResizeRecipe) {
            $resizedImage = $this->imageManipulate(
                $origImagePath,
                $imageResizeRecipe
            );
            $resizedImage->save(
                $basePath
                . DIRECTORY_SEPARATOR
                . $origImgInfo['filename'] . '_' . $key . '.' . $origImgInfo['extension'] ?? ''
            );
        }
    }
}
