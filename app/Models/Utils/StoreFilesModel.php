<?php

namespace App\Models\Utils;

trait StoreFilesModel
{
    /**
     * The name of the storage disk to store files to
     * @param string
     */
    //protected static $defaultStorageDiskName = 'public';

    /**
     * The map of storage disk names by column names
     */
    //protected static $columnStorageDiskMap = [];
    
    /**
     * 
     * @return string The storage disk name in filesystem config
     */
    public static function getDefaultStorageDiskName()
    {
        return property_exists(static::class, 'defaultStorageDiskName') ? static::$defaultStorageDiskName : 'public';
    }
    
    /**
     * 
     * @return array Mapping of column vs disk name
     */
    public static function getColumnStorageDiskMap()
    {
        return property_exists(static::class, 'columnStorageDiskMap') ? static::$columnStorageDiskMap : [];
    }

    /**
     * @return string
     */
    public static function storageDiskName(string $column = null)
    {
        $storageDiskName = static::getDefaultStorageDiskName();
        $columnStorageDiskMap = static::getColumnStorageDiskMap();

        if (!empty($column) && isset($columnStorageDiskMap[$column])) {
            $storageDiskName = $columnStorageDiskMap[$column];
        }

        return $storageDiskName;
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public static function storageDisk(string $column = null)
    {
        $storageDiskName = static::storageDiskName($column);

        return \Storage::disk($storageDiskName);
    }

    /**
     * @return string
     */
    public static function storageBaseDir()
    {
        return str_slug((new static())->getTable());
    }

    /**
     * @return string
     */
    public function storageBasePath($column)
    {
        $fileName = $this->fileName($column);
        
        if ($fileName) {
            return static::storageBaseDir() . '/' . $fileName;
        }
        
        return null; 
    }
    
    /**
     * @return string
     */
    public function fileName($column)
    {
        if ($this->getAttribute($column)) {
            return $this[$this->primaryKey] . '_' . snake_case($column) . '_' . $this->getAttribute($column);
        }
        
        return null;
    }
    
    /**
     * @return string
     */
	public function fileUrl($column)
    {
        
        $storageBasePath = $this->storageBasePath($column);
        
        if ($storageBasePath) {

            return static::storageDisk($column)->url($storageBasePath);
        }
        
        return null;
	}
    
    /**
     * @return string
     */
    public function filePath($column)
    {
        $storageBasePath = $this->storageBasePath($column);
        
        if ($storageBasePath) {

            return static::storageDisk($column)->path($storageBasePath);
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

        //delete old file
        $this->deleteFile($column);
        
        $this->setAttribute($column, $this->filterFileOriginalName($file, $column));

        if (empty($this->exists)) {
            //save model if it is a new record not saved in database to obtain autoincrement keys
            $this->save();
        }

        if (method_exists($this, 'processFileBeforeStore')) {
            //class implements processFileBeforeStore try to execute it
            $this->processFileBeforeStore($file, $column);
        }
        
        $file->storeAs(static::storageBaseDir(), $this->fileName($column), [
            'disk' => static::storageDiskName($column)
        ]);
        
        $this->save();
        
        if (method_exists($this, 'processFileAfterStore')) {
            //class implements processFileAfterStore try to execute it
            $this->processFileAfterStore($file, $column);
        }
        
        return $this;
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Model Fluent interface
     */
    public function deleteFile($column)
    {
        $storageBasePath = $this->storageBasePath($column);

        if ($storageBasePath && static::storageDisk($column)->exists($storageBasePath)) {
            static::storageDisk($column)->delete($storageBasePath);
        }

        return $this;
    }
    
    /**
     * Left as an example for processFileBeforeStore
    protected function processFileBeforeStore(\Illuminate\Http\UploadedFile $file, $column)
    {
        
    }
    */
    
    /**
     * Left as an example for processFileBeforeStore
    protected function processFileAfterStore(\Illuminate\Http\UploadedFile $file, $column)
    {
        $newPath = $this->filePath($column);
    }
    */

    /**
     * @return string
     */
    protected function filterFileOriginalName(\Illuminate\Http\UploadedFile $file, $column)
    {
        if (preg_match('/(.*)\.([^\.]+)$/', $file->getClientOriginalName(), $matches)) {
            //if file has an extension
            $fileName = str_slug($matches[1]) . '.' . strtolower($matches[2]);
        } else {
            $fileName = str_slug($file->getClientOriginalName());
        }

        return $fileName;
    }
}