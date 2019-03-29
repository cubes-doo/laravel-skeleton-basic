<?php

namespace App\Models\Utils;

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
    
    /**
     * @var integer resize image according to specified width and height
     */
    private $ACTION_RESIZE = 'resize';
    /**
     * @var integer crop image according to specified position, width and height
     */
    private $ACTION_CROP = "crop";
    /**
     * @var integer fit image according to specified width and height
     */
    private $ACTION_FIT = "fit";
    /**
     * @var integer JPEG encode image 
     */
    private $ACTION_JPG_ENCODE = "jpg-encode";
    /**
     * @var string perform autocrop (width/2, height/2, posX/2, posY/2)
     */
    protected $DO_AUTO_CROP = 'autocrop';
    
    protected $ACTIONS = [
        "resize",
        "crop",
        "resize",
        "jpg-encode",
    ];

            
    /**
     *  @var array  |  array of arrays where inner array has 2 members with dimensions 
     *  $this->imageResizeRecepies = [
            ["type => "fit", "w" => 200, "h" => NULL],
            ["type => "fit", "w" => 600, "h" => NULL],
            ["type => "fit", "w" => NULL, "h" => NULL]
        ];  
     */

    
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
     * @param mixed $imageOrig file path or file object from request 
     *              (see Intervention\Image\AbstractDecoder\init() for 
     *               all possible input types)
     * @param array        $actions     | actions to perform on image
     * 
     * @return Image
     */
    private function imageManipulate($imageOrig, Array $actions)
    {
        
        // init new image instance of intervention.image.
        $image = Image::make($imageOrig);
        
        //dd($actions);
        // allow:
        // [
        //  'type' => ,
        //  'width' => 'fit',
        //  'height' => 'fit',
        // ]
        // 
        // 
        // 
        if (isset($actions['type'])) {
            $actions = [$actions];
        }
        
        /* perform specified actions in order in which they are specified */
        foreach($actions as $action) {

            if($action['type'] == $this->ACTION_CROP) {
                $this->cropImage($image, $action);
            }
            if($action['type'] == $this->ACTION_RESIZE) {
                $this->resizeImage($image, $action['w'], $action['h'], $action['car']);
            }
            if($action['type'] == $this->ACTION_FIT) {
                $this->fitImage($image, $action['w'], $action['h']);
            }
            if($action['type'] == $this->ACTION_JPG_ENCODE) {
                $image->encode('jpg', 75);
                $extension = 'jpeg';
            }
        }

        return $image;
    }
    
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
    }
    
}