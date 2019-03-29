<?php

namespace App\Lib\Traits;

use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Manipulate images with the help of Intervention image library
 * Classes using this trait should set '$this->imageResizeRecepies' to 
 * wanted image resize sizes.
 */
trait CropImagesModelTrait  
{
    /*
     * Image driver names
     */
    protected $IMAGICK = "imagick";
    protected $GD = "gd";
    
    // 'webP' is supported by both drivers since PHP >= 5.5.0 
    // (TODO: append 'webP' to following arrays if PHP version is >= 5.5.0)
    private $IMAGICK_SUPPORT = ['jpeg', 'png', 'gif'];
    private $GD_SUPPORT = ['jpeg', 'png', 'gif', 'tiff', 'bsd', 'ico', 'psd'];
    
    /**
     * @var integer resize image according to specified width and height
     */
    private $ACTION_RESIZE = 0;
    /**
     * @var integer crop image according to specified position, width and height
     */
    private $ACTION_CROP = 1;
    /**
     * @var integer fit image according to specified width and height
     */
    private $ACTION_FIT = 2;
    /**
     * @var integer JPEG encode image 
     */
    private $ACTION_JPG_ENCODE = 3;
    /**
     * @var string perform autocrop (width/2, height/2, posX/2, posY/2)
     */
    protected $DO_AUTO_CROP = 'autocrop';

            
    /**
     *  @var array  |  array of arrays where inner array has 4 members 
     */
    protected $imageResizeRecepies;
    
    
    public function __construct()
    {
        /*
         * Set recepies array for image sizes 
         * name, width, height, imageNameSugar
         */
        $this->imageResizeRecepies = [
            ["thumbnail", 200, NULL, "_thumbnail"],
            ["float", 600, NULL, "_float"],
            ["original", NULL, NULL, ""],
        ];  
    }
    
    /*
     * Calculate auto crop box values
     * 
     * @param Object $image | Intervention-Image object
     * 
     * @return array
     */
    private function calculateAutocropBox($image)
    {
        $cropData = [];
        
        $orig_width = $image->width();
        $orig_height = $image->height();
        
        $cropData['crop_width'] = $orig_width/2;
        $cropData['crop_height'] = $orig_height/2;
            
        $cropData['w'] = $orig_width;
        $cropData['h'] = $orig_height;
        
        $cropData['pos_x'] = $orig_width/4;
        $cropData['pos_y'] = $orig_height/4;
        
        return $cropData;
    }
    
    
    /**
     * Crop image if $cropData has valid members
     * 
     * @param object $image    | image object
     * @param array  $cropData | crop parameters in the following format: 
     *                          [w, h, crop_width, crop_height, pos_x, pos_y];
     * @return void
     */
    private function cropImage($image, $cropData)
    {
        $stat = FALSE;

        if (array_has($cropData, $this->DO_AUTO_CROP)) {
            $cropData = $this->calculateAutocropBox($image);
        }

        if(!empty($cropData) && count($cropData) == 6) {
            
            $orig_width = $image->width();
            $orig_height = $image->height();
            
            $crop_width = $cropData['crop_width'];
            $crop_height = $cropData['crop_height'];
            
            $viewport_width = $cropData['w'];
            $viewport_height = $cropData['h'];
            
            $crop_pos_x = $cropData['pos_x'];
            $crop_pos_y = $cropData['pos_y'];
            
            if( areNumeric($crop_width, $crop_height, $crop_pos_x , $crop_pos_y, 
                           $orig_width, $viewport_width) ) {
                
                // calculate posX and posY
                $ratio = ( $orig_width/$viewport_width );
                
                $calc_posX = (int) ($crop_pos_x * $ratio);
                $calc_posY = (int) ($crop_pos_y * $ratio);
                
            
                // DEBUG
                //------
                /*
                dd('ratio: ' . $ratio, 
                   'crop width:' . $crop_width, 
                   'crop heigth:' . $crop_height,
                   'crop pos_x:' . $crop_pos_x, 
                   'crop pos_y:' . $crop_pos_y,
                   'calculated crop pos_x:' . $calc_posX,
                   'calculated crop pos_y:' . $calc_posY,
                   'original width: ' . $orig_width, 
                   'original height: ' . $orig_height
                   );
                */
                
                $image->crop((int)$crop_width, (int)$crop_height,  $calc_posX,  $calc_posY);
                
                $stat = TRUE;
            }
            else {
                Log::error('Invalid crop image data received (invalid types) in: "' 
                            . __FILE__ . '". Crop data var_export: ' . var_export($cropData, TRUE) );
            }
        }
        else {
            Log::error('Invalid crop image crop position array received (invalid member count) in: "' 
                        . __FILE__ . '". ');
        }
        
        return $stat;
    }
    
    
    /**
     * Find out if PHP image driver is GD or IMAGICK
     * Return FALSE if none of them is loaded. 
     * 
     * @return string|boolean
     */
    protected function findImageDriverName()
    {
        if(extension_loaded('gd')) {
            return $this->GD;
        }
        else if(extension_loaded('imagick')) {
            return $this->IMAGICK;
        }
        return FALSE;
    }
    
    
    /**
     * Get image name with appended 'sugar' before extension 
     * 
     * @param string $imageName
     * @param string $sugar
     * 
     * @return string|boolean 
     */
    public function imgNameAppendSugar($imageName, $sugar)
    {
        $newName = FALSE;

        if(is_string($imageName) && is_string($sugar)) {
            $basename = pathinfo($imageName, PATHINFO_FILENAME);
            $extension = pathinfo($imageName, PATHINFO_EXTENSION);
            $newName = $basename . $sugar . '.' . $extension;
        }
        return $newName;
    }
    
    
    /**
     * Resize the image and constrain aspect ratio (auto if w or h is NULL)
     * 
     * @param object  $image | Intervention-image object
     * @param int     $w
     * @param int     $h
     * @param boolean $constrainAspectRatio
     * 
     * @return boolean
     */
    public function resizeImage($image, $w, $h, $constrainAspectRatio=FALSE)
    {
        $stat = FALSE;
        
        if( ! ($w == NULL && $h == NULL) ) {
            
            //Log::debug('resizeImage(): resizing image to: ' . $w . "x" . $h);
            
            $image->resize($w, $h, function ($constraint) use($constrainAspectRatio) {
                
                if($constrainAspectRatio) {
                    $constraint->aspectRatio();
                } 
                $constraint->upsize(); // keep image from being upsized
            });
            
            $stat = TRUE;
        }
        
        return $stat;
    }
    
    
    /**
     * @link https://stackoverflow.com/questions/26890539/intervention-image-aspect-ratio
     * 
     * @param object $image | Intervention-image object
     * @param type $w
     * @param type $h
     */
    public function fitImage($image, $w, $h, $position='center', $conUpsize=TRUE)
    {
        $image->fit($w, $h, function($constraint) use($conUpsize) {
            if($conUpsize) {
                $constraint->upsize();
            }
        }, $position);
        
        return TRUE;
    }
    
    
    /**
     * Resize or crop image with the help of intervention library 
     * @link http://image.intervention.io
     * Intervention image library MUST be set as provider and given an 
     * alias 'Image' in config/app
     * 
     * @param string|UploadedFile $imageOrig file path or file object from request
     * @param string       $fnameBase   | base part of filename
     * @param string       $extension   | filename extension
     * @param string       $storagePath | location where to store the file
     * @param array        $actions     | actions to perform on image
     * @param int          $w           | saved image new(resized) width
     * @param int          $h           | saved image new(resized) height
     * @param string       $fnameSugar  | string to be appended to the end of a
     *                                    filename before extension part
     * @param string       $seoPrefix   | string which to prepend to image name
     * @param array        $cropArr     | crop parameters in the following format:
     *                                    [w, h, posX, posY];
     * @param boolean      $car         | constrain aspect ratio
     * 
     * @return mixed  | if success -> string filename of saved image
     *                  if fail -> FALSE
     */
    private function imageManipulateAndSave($imageOrig, $fnameBase, $extension, 
            $storagePath, Array $actions, $w=NULL, $h=NULL, $fnameSugar='', 
            $seoPrefix='', $cropData=NULL, $car=FALSE)
    {
        
        // init new image instance of intervention.image.
        $image = Image::make($imageOrig);
        
        /* perform specified actions in order in which they are specified */
        foreach($actions as $action) {
            
            if($action == $this->ACTION_CROP) {
                $this->cropImage($image, $cropData);
            }
            if($action == $this->ACTION_RESIZE) {
                $this->resizeImage($image, $w, $h, $car);
            }
            if($action == $this->ACTION_FIT) {
                $this->fitImage($image, $w, $h);
            }
            if($action == $this->ACTION_JPG_ENCODE) {
                $image->encode('jpg', 75);
                $extension = 'jpeg';
            }
        }
        
        // construct image filename
        $filenameNoExt =  $seoPrefix . str_slug($fnameBase, '-') . $fnameSugar;
        $fullFileName =  $filenameNoExt . "." . $extension;
                         
        
        // save image to filesystem '$storagePath' and return image filename
        try {
            // NOTE: problems with public_path(). Raises \Intervention\Image\Exception\NotWritableException
            $image->save($storagePath . $fullFileName);
            $savedImageName = $fullFileName;
        } catch (\Throwable $e) {
            // NOTE: Couldn't succeed to catch only \Intervention\Image\Exception\ImageException
            //       cannot get $e->message - protected property ??!!
            // PERHAPS SOLUTION: $e->getMessage();
            $savedImageName = FALSE;
            Log::error(__METHOD__ . ' -> Save image error in: "' . __FILE__ 
                     . '". Intervention-Image error message: ' . $e->getMessage());
        }

        return $savedImageName;
    }
    
    
    /**
     * Resize or crop image received from the request
     * 
     * @param UploadedFile $requestFile | file object from request
     * @param string       $storagePath | location where to store the file
     * @param array        $actions     | actions to perform on the image
     * @param array        $options     | see underlying implementation in
     *                                    imageManipulateAndSave()
     * 
     * @return mixed  | if success -> string filename of saved image
     *                  if fail -> FALSE
     */
    protected function uploadedImageManipulateAndSave(UploadedFile $requestFile, 
                 $storagePath, Array $actions, Array $kwargs) {
        
        $w = $kwargs['width'] ?? NULL;
        $h = $kwargs['height'] ?? NULL;
        $fnameBase = $kwargs['fname_base'] ?? NULL;
        $fnameSugar = $kwargs['fname_sugar'] ?? '';
        $seoPrefix = $kwargs['seoPrefix'] ?? '';
        $cropData = $kwargs['cropData'] ?? NULL;   
        $car = $kwargs['car'] ?? FALSE;   
        
        $extension = $requestFile->getClientOriginalExtension();
        
        if(is_null($fnameBase)) {
            $fnameOrig = $requestFile->getClientOriginalName();
            $fnameBase = pathinfo($fnameOrig, PATHINFO_FILENAME);
        }
        
        return $this->imageManipulateAndSave($requestFile, $fnameBase, $extension,
                        $storagePath, $actions, $w, $h, $fnameSugar, 
                        $seoPrefix, $cropData, $car);

    }
    
    
    /**
     * Resize or crop image already present on local storage
     * 
     * @param string       $localImagePath  | path to local image
     * @param string       $storagePath     | location where to store the file
     * @param array        $actions         | actions to perform on the image
     * @param array        $options         | see underlying implementation
     *                                      | imageManipulateAndSave()
     * 
     * @return mixed  | if success -> string filename of saved image
     *                  if fail -> FALSE
     */
    protected function savedImageManipulate($localImagePath, 
                 $storagePath, Array $actions, Array $kwargs) {
        
        $w = $kwargs['width'] ?? NULL;
        $h = $kwargs['height'] ?? NULL;
        $fnameBase = $kwargs['fname_base'] ?? NULL;
        $fnameSugar = $kwargs['fname_sugar'] ?? '';
        $seoPrefix = $kwargs['seoPrefix'] ?? '';
        $cropData = $kwargs['cropData'] ?? NULL;   
        $car = $kwargs['car'] ?? FALSE;   
        
        $extension = pathinfo($localImagePath, PATHINFO_EXTENSION);
        
        // DEBUG
        Log::debug("savedImageManipulate()");
        Log::debug("local image path = '$localImagePath'");
        Log::debug("storage path = '$storagePath'");
        Log::debug($actions);
        Log::debug($kwargs);
        
        if(is_null($fnameBase)) {
            $fnameBase = pathinfo($localImagePath, PATHINFO_FILENAME);
        }
        
        return $this->imageManipulateAndSave($localImagePath, $fnameBase, $extension,
                        $storagePath, $actions, $w, $h, $fnameSugar, 
                        $seoPrefix, $cropData, $car);

    }
    
    
    /**
     * Delete image from storage
     * 
     * @param string $image_path | full system image path
     * 
     * @return BOOLEAN
     */
    protected function deleteImage($image_path)
    {
        $stat = NULL;
        
        // delete previous image from storage if it exists
        if($image_path) {
            try {
                unlink($image_path);
                $stat = TRUE;
            } catch(\Throwable $e) {
                // cannot delete image from filepath
                Log::error('Delete image error in: "' . __FILE__ . '". ' . 
                           'Cannot delete from the storage path. ' . $e->message);
                $stat = FALSE;
            }
        }
        
        return $stat;
    }
    
    
}