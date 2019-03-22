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
    
    public function fileName($column)
    {
        if ($this->$column) {
            return $this[$this->primaryKey] . '_' . $column . '_' . $this->$column;
        }
        
        return null;
    }
    
	public function fileUrl($column)
    {
        
        $fileName = $this->fileName($column);
        
        if ($fileName) {
            return url('/storage/' . static::storageDir() . '/' . $fileName);
        }
        
        return null;
	}
    
    public function filePath($column)
    {
        $fileName = $this->fileName($column);
        
        if ($fileName) {
            return public_path('/storage/' . static::storageDir() . '/' . $fileName);
        }
        
        return null; 
    }
    
    /**
     * @param string $column
     * @param string|\Illuminate\Http\UploadedFile $file
     * @return mixed $this fluent interface
     */
    public function storeFile($column, $file = null)
    {
        if (is_string($file) && !empty($file)) {

            $file = request()->file($file);

        } else if ($file === null) {

            $file = request()->file($column);
        }

        if (!($file instanceof \Illuminate\Http\UploadedFile)) {
            throw new \InvalidArgumentException('Unable to resolve file from request');
        }

        $oldColumnFilePath = $this->filePath($column);
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
        
        $file->storeAs(self::storageDir(), $this->fileName($column));
        
        $this->save();
        
        return $this;
    }
    
    protected function processFileBeforeStore(\Illuminate\Http\UploadedFile $file, $column)
    {
        
    }
    
    public function deleteFile($column)
    {
        $columnFilePath = $this->filePath($column);
        
        if(is_file($columnFilePath)) {
            unlink($columnFilePath);
            $this->$column = null;
            $this->save();
            return $columnFilePath;
        }
        
        return false;
    }
}