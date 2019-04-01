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
        
        $newPath = $this->filePath($column);
        
        dd($newPath);
        
        foreach($this->multiImageResizeRecepies[$column] as $imageResizeRecipe) {

            $resizedImage = $this->imageManipulate($newPath, 
                                  $imageResizeRecipe);
            $resizedImage->save($this->storageBaseDir() . DIRECTORY_SEPARATOR . Str::random()); 
            
            $resizedFilename = $this->storageBaseDir() . DIRECTORY_SEPARATOR . Str::random();
            
            dd($resizedImage);
            logger($resizedFilename);
            logger(var_export($resizedImage));
            // Instead of Intervention/Image->save()
            \Storage::disk('public')->put( $resizedFilename, $resizedImage);
        }
    }
}
