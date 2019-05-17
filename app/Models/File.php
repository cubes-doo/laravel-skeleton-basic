<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/*
 * File model
 */
class File extends Model
{
    const DEFAULT_STORAGE_DISK = 'public';
    const DEFAULT_DIRECTORY = 'files';
    const DEFAULT_CLASS = 'file';

    protected $attributes = [
        'disk' => self::DEFAULT_STORAGE_DISK,
        'directory' => self::DEFAULT_DIRECTORY,
        'class' => self::DEFAULT_CLASS,
    ];

    protected $fillable = ['id', 'disk', 'directory', 'name', 'class', 'size', 'mime', 'description'];
    /*
     * Morph relation with imagable entities
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    public function scopeOfClass($query, $class)
    {
        $query->where($this->getTable() . '.class', $class);
    }

    public function scopeOfMime($query, $mime)
    {
        $query->where($this->getTable() . '.mime', $mime);
    }

    public function scopeInDisk($query, $disk)
    {
        $query->where($this->getTable() . '.disk', $disk);
    }

    public function scopeInDirectory($query, $directory, $includeSubdirectories = false)
    {
        if ($includeSubdirectories) {
            $query->where(function ($query) use ($directory) {
                $query->orWhere($this->getTable() . '.directory', $directory)
                    ->orWhere($this->getTable() . '.directory', 'LIKE', $directory . '/%');
            });

            return;
        }

        $query->where($this->getTable() . '.directory', $directory);
    }
    
    /**
     * @return string|null The file name on disk
     */
    public function fileName()
    {
        if (!$this->id || !$this->name) {
            return null;
        }

        $class = $this->class;
        if (!$class) {
            $class = static::DEFAULT_CLASS;
        }

        return $this->id . '_' . \Str::slug($class) . '_' . $this->name;
    }

    /**
     * @return string|null The file url for download
     */
    public function fileUrl()
    {
        $storageRelativePath = $this->storageRelativePath();
        if (!$storageRelativePath) {
            return null;
        }

        return $this->storageDisk()->url($storageRelativePath);
    }

    /**
     * @return string|null The file path on disk
     */
    public function filePath()
    {
        $storageRelativePath = $this->storageRelativePath();
        if (!$storageRelativePath) {
            return null;
        }

        return $this->storageDisk()->path($storageRelativePath);
    }

    /**
     * @return int|string If not pretty int is returned as number of bytes, otherwise the size in kB, MB, GB or TB is returned
     */
    public function fileSize($pretty = false)
    {
        if (!$pretty) {
            return $this->size;
        }

        if ($this->size < 1024) {
            return $this->size . ' B';
        }

        $size = (int) $this->size;
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];

        $unitIndex = 0;
        while ($size >= 1024 && $unitIndex < count($units)) {
            $size /= 1024;
            $unitIndex ++;
        }

        $unit = $units[$unitIndex];
        
        if ($size < 10) {
            return sprintf('%.2f %s', $size, $unit);
        }

        return sprintf('%d %s', $size, $unit);
    }

    /**
     * Delete associated file from storage
     * @return self
     */
    public function deleteFile()
    {
        $storageRelativePath = $this->storageRelativePath();
        if (!$storageRelativePath) {
            return null;
        }

        $this->storageDisk()->delete($storageRelativePath);

        return $this;
    }

    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function storageDisk()
    {
        return Storage::disk($this->storageDiskName());
    }

    /**
     * @return string
     */
    public function storageDiskName()
    {
        if (!$this->disk) {
            return static::DEFAULT_STORAGE_DISK;
        }

        return $this->disk;
    }

    /**
     * @return string
     */
    public function storageDirectory()
    {
        $directory = $this->directory;
        if (!$directory) {
            $directory = static::DEFAULT_DIRECTORY;
        }

        return $directory;
    }

    /**
     * @return string|null Relative path on storage disk
     */
    public function storageRelativePath()
    {
        $fileName = $this->fileName();
        if (!$fileName) {
            return null;
        }

        $directory = $this->storageDirectory();

        return trim($directory, '/') . '/' . $fileName;
    }


    /**
     * @override When deleted the associated file should be deleted also
     */
    public function delete()
    {
        parent::delete();
        $this->deleteFile();
    }

    /**
     * Utility function for sanitizing uploaded file name
     * @return string
     */
    public static function filterFileOriginalName(\Illuminate\Http\UploadedFile $file)
    {
        if (preg_match('/(.*)\.([^\.]+)$/', $file->getClientOriginalName(), $matches)) {
            //if file has an extension
            $fileName = str_slug($matches[1]) . '.' . strtolower($matches[2]);
        } else {
            $fileName = str_slug($file->getClientOriginalName());
        }

        return $fileName;
    }

    /**
     * Stores uploaded file and deletes previous one
     * @param \Illuminate\Http\UploadedFile $file The uploaded file
     * @param string|array $attributes Attributes to set for model. If string is passed then it is considered to be the 'class' field
     * @return self
     */
    public function storeFile(\Illuminate\Http\UploadedFile $file, $attributes = null)
    {
        if (is_string($attributes)) {
            $attributes = [
                'class' => $attributes,
            ];
        }

        if (!is_array($attributes)) {
            $attributes = [];
        }

        $attributes['name'] = static::filterFileOriginalName($file);
        $attributes['size'] = $file->getSize();
        $attributes['mime'] = $file->getMimeType();

        if ($this->exists) {
            //delete previously saved file
            $this->deleteFile();
        }

        //fill attributes and save
        $this->fill($attributes);
        $this->save();

        $directory = $this->storageDirectory();
        $disk = $this->storageDiskName();

        $file->storeAs($directory, $this->fileName(), [
            'disk' => $disk
        ]);

        return $this;
    }
}
