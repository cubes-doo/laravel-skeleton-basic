<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


/*
 * Image model
 * 
 * Relation::morphMap() je setovano u AppServiceProvider-u.
 */
class Image extends Model
{
    
    const CLASS_ORIGINAL_PHOTO= 1;
    const CLASS_RESIZED_PHOTO = 2;
    
    const CLASSES = [
        self::CLASS_ORIGINAL_PHOTO,
        self::CLASS_RESIZED_PHOTO
    ];
    
    public $timestamps = FALSE;
    
    protected $guarded = [];
    

    public function imageable()
    {
        return $this->morphTo();
    }
    
    
    /*
     * Izbrisi sliku sa hard-diska i izbrisi red iz baze podataka
     */
    public function delete() 
    {
        $stat = $this->deleteImageFromHdd();
        
        if(! $stat) {
            Log::error(__METHOD__ . ": Image entry was deleted from the database in spite of previous error.");
        }
        
        parent::delete();
    }
    
    
    /*
     * Izbrisi fajl slike sa hard-diska
     */
    public function deleteImageFromHdd()
    {
        $f = $this->getAbsoluteServerPath();
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
