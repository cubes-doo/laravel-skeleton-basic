<?php

/**
 * Class
 *
 * @category   class
 * @copyright  Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Utils;

trait StoreFilesModel
{
    public static function storageDir()
    {
        return str_slug((new static())->getTable());
    }
    
    public function columnFileName($column)
    {
        if ($this->$column) {
            return $this[$this->primaryKey] . '_' . $column . '_' . $this->$column;
        }
        
        return null;
    }
    
	public function columnFileUrl($column)
    {
        
        $columnFileName = $this->columnFileName($column);
        
        if ($columnFileName) {
            return url('/storage/' . static::storageDir() . '/' . $columnFileName);
        }
        
        return null;
	}
    
    public function columnFilePath($column)
    {
        $columnFileName = $this->columnFileName($column);
        
        if ($columnFileName) {
            return public_path('/storage/' . static::storageDir() . '/' . $columnFileName);
        }
        
        return null; 
    }
    
    public function storeFile(\Illuminate\Http\UploadedFile $file, $column)
    {
        $oldColumnFilePath = $this->columnFilePath($column);
        if ($oldColumnFilePath && is_file($oldColumnFilePath)) {
            //remove old file
            @unlink($oldColumnFilePath);
        }
        
        if (preg_match('/(.*)\.([^\.]+)$/', $file->getClientOriginalName(), $matches)) {
                
            $this->$column = str_slug($matches[1]) . '.' . strtolower($matches[2]);
        } else {
            $this->$column = str_slug($file->getClientOriginalName());
        }
        
        if (empty($this[$this->primaryKey])) {
            $this->save();
        }
        
        $this->processFileBeforeStore($file, $column);
        
        $file->storeAs(self::storageDir(), $this->columnFileName($column));
        
        $this->save();
        
        return $this;
    }
    
    protected function processFileBeforeStore(\Illuminate\Http\UploadedFile $file, $column)
    {
        
    }
}