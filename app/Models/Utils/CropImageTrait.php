<?php

namespace App\Models\Utils;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

/**
 * Manipulate images with the help of Intervention image library
 * Classes using this trait should set '$this->imageResizeRecepies' to
 * wanted image resize sizes.
 */
trait CropImageTrait
{
    /*
     * Name of this trait's config file
     */
    private $CONFIG_NAME = 'crop_image_recipes';
    
    /**
     * @var int resize image according to specified width and height
     */
    private $ACTION_RESIZE = 'resize';
    
    /**
     * @var int crop image according to specified position, width and height
     */
    private $ACTION_CROP = 'crop';
    
    /**
     * @var int fit image according to specified width and height
     */
    private $ACTION_FIT = 'fit';
    
    /**
     * @var int JPEG encode image
     */
    private $ACTION_JPG_ENCODE = 'jpg-encode';
    
    /**
     * @var string perform autocrop (width/2, height/2, posX/2, posY/2)
     */
    protected $DO_AUTO_CROP = 'autocrop';
    
    protected $ACTIONS = [
        'resize',
        'crop',
        'resize',
        'jpg-encode',
    ];

    
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
        
        $cropData['crop_width'] = $orig_width / 2;
        $cropData['crop_height'] = $orig_height / 2;
            
        $cropData['w'] = $orig_width;
        $cropData['h'] = $orig_height;
        
        $cropData['pos_x'] = $orig_width / 4;
        $cropData['pos_y'] = $orig_height / 4;
        
        return $cropData;
    }
    
    
    /**
     * Crop image if $cropData has valid members
     *
     * @param object $image    | image object
     * @param array  $cropData | crop parameters in the following format:
     *                         [w, h, crop_width, crop_height, pos_x, pos_y];
     */
    private function cropImage($image, $cropData)
    {
        $stat = false;

        if (array_has($cropData, $this->DO_AUTO_CROP)) {
            $cropData = $this->calculateAutocropBox($image);
        }

        if (! empty($cropData) && count($cropData) == 6) {
            $orig_width = $image->width();
            $orig_height = $image->height();
            
            $crop_width = $cropData['crop_width'];
            $crop_height = $cropData['crop_height'];
            
            $viewport_width = $cropData['w'];
            $viewport_height = $cropData['h'];
            
            $crop_pos_x = $cropData['pos_x'];
            $crop_pos_y = $cropData['pos_y'];
            
            if (areNumeric(
                $crop_width,
                $crop_height,
                $crop_pos_x,
                $crop_pos_y,
                $orig_width,
                $viewport_width
            )) {
                
                // calculate posX and posY
                $ratio = ($orig_width / $viewport_width);
                
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
                
                $image->crop((int)$crop_width, (int)$crop_height, $calc_posX, $calc_posY);
                
                $stat = true;
            } else {
                Log::error('Invalid crop image data received (invalid types) in: "'
                            . __FILE__ . '". Crop data var_export: ' . var_export($cropData, true));
            }
        } else {
            Log::error('Invalid crop image crop position array received (invalid member count) in: "'
                        . __FILE__ . '". ');
        }
        
        return $stat;
    }
    
    
    /**
     * Resize the image and constrain aspect ratio (auto if w or h is NULL)
     *
     * @param object $image                | Intervention-image object
     * @param int    $w
     * @param int    $h
     * @param bool   $constrainAspectRatio
     *
     * @return bool
     */
    public function resizeImage($image, $w, $h, $constrainAspectRatio = false)
    {
        $stat = false;
        
        if (! ($w == null && $h == null)) {
            
            //Log::debug('resizeImage(): resizing image to: ' . $w . "x" . $h);
            
            $image->resize($w, $h, function ($constraint) use ($constrainAspectRatio) {
                if ($constrainAspectRatio) {
                    $constraint->aspectRatio();
                }
                $constraint->upsize(); // keep image from being upsized
            });
            
            $stat = true;
        }
        
        return $stat;
    }
    
    
    /**
     * @link https://stackoverflow.com/questions/26890539/intervention-image-aspect-ratio
     *
     * @param object $image     | Intervention-image object
     * @param type   $w
     * @param type   $h
     * @param mixed  $position
     * @param mixed  $conUpsize
     */
    public function fitImage($image, $w, $h, $position = 'center', $conUpsize = true)
    {
        $image->fit($w, $h, function ($constraint) use ($conUpsize) {
            if ($conUpsize) {
                $constraint->upsize();
            }
        }, $position);
        
        return true;
    }
    
    /**
     * Read default config actions from config file
     *
     * Config example:
     *      [
     *           'avatar' => [
     *               "type" => "fit",
     *               "w" => 120,     // key can also be 'width'
     *               "h" => 60       // key can also be 'height'
     *           ],
     *           'second_size' => [              // can contain multiple actions
     *               [                           // in one recipe. In this example
     *                  "type" => "resize",      // we first resize an image and
     *                  "w" => 800               // then crop it.
     *               ],
     *               [
     *                   "type" => "crop",
     *                   "width" => 400
     *                   "height" => 400
     *               ]
     *           ],
     *      ]
     *
     * @param mixed $sizeName
     */
    private function getConfigActionRecipe($sizeName)
    {
        $defaults = [
            'type' => 'fit',
            'w' => 200,
        ];
        
        $recipes = config("{$this->CONFIG_NAME}.$sizeName", $defaults);
        return $recipes;
    }

    
    /**
     * Resize or crop image with the help of intervention library
     *
     * @link http://image.intervention.io
     * Intervention image library MUST be set as provider and given an
     * alias 'Image' in config/app
     *
     * @param mixed        $imageOrig file path or file object from request
     *                                (see Intervention\Image\AbstractDecoder\init() for
     *                                all possible input types)
     * @param string|array $actions   | actions to perform on image
     *
     * @return Image
     */
    private function imageManipulate($imageOrig, $actions)
    {
        
        // init new image instance of intervention.image.
        $image = Image::make($imageOrig);

        if (is_string($actions)) {
            $actions = $this->getConfigActionRecipe($actions);
        }
        
        // accept array of arrays or only a single array
        if (isset($actions['type'])) {
            $actions = [$actions];
        }
        
        /* perform specified actions in order in which they are specified */
        foreach ($actions as $action) {
            $action['w'] = $action['width'] ?? ($action['w'] ?? null);
            $action['h'] = $action['height'] ?? ($action['h'] ?? null);

            if ($action['type'] == $this->ACTION_CROP) {
                $this->cropImage($image, $action);
            }
            if ($action['type'] == $this->ACTION_RESIZE) {
                $this->resizeImage(
                    $image,
                    $action['w'],
                    $action['h'],
                    $action['car'] ?? true
                );
            }
            if ($action['type'] == $this->ACTION_FIT) {
                $this->fitImage($image, $action['w'], $action['h']);
            }
            if ($action['type'] == $this->ACTION_JPG_ENCODE) {
                $image->encode('jpg', 75);
                $extension = 'jpeg'; // caller should set new extension suffix to the filename
            }
        }

        return $image;
    }
}
