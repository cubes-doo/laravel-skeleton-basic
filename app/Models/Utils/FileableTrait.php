<?php

namespace App\Models\Utils;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Trait is checking following attributes
 *
 * - fileClassStorageDiskMap
 *      Is used to associate file class to a storage disk from config/filesystem
 *      Default storage disk is 'public' (\App\Models\File::DEFAULT_STORAGE_DISK)
 *      ex:
 *      [
 *          'contract' => 'private', //Put file with class 'contract' into the private storage disk
 *          'logo' => 'public', //Put file with class 'logo' into the public (default) storage disk
 *      ]
 * - fileClassStorageDirectoryMap
 *      Is used to associate class to a storage directory under storage disk
 *      Default storage directory is table name
 *      ex
 *      [
 *          'contract' => 'contracts', //Put file with class 'contract' into directory 'contracts'
 *          'logo' => 'users/logos', //Put file with class 'logo' into directory 'users/logos'
 *      ]
 * - filesDefaultStorageDiskName
 *      String, it is used to determine default storage disk name for model,
 *      if not set then default storage disk of File model i sused
 *
 * - filesDefaultStorageDirectory
 *      If is set as STRING this will be used as default directory for all file classes
 *      If is set to boolean TRUE the str_slug-ed table name of the model will be used as default directory
 *      Otherwise the default directory of the Files model will be used
 */
trait FileableTrait
{
    /**
     * @param int|string|null $class If int is passed than file is accessed over id
     *
     * @return string|null
     */
    public function getFilePath($class = null)
    {
        $file = $this->getFile($class);

        if (! $file) {
            return null;
        }

        return $file->filePath();
    }

    /**
     * @param int|string|null $class If int is passed than file is accessed over id
     *
     * @return string|null
     */
    public function getFileUrl($class = null)
    {
        $file = $this->getFile($class);

        if (! $file) {
            return null;
        }

        return $file->fileUrl();
    }

    /**
     * @param int|string|null $class If int is passed than file is accessed over id
     *
     * @return File|null
     */
    public function getFile($class = null)
    {
        $files = $this->files;

        if (! $class) {
            return $files->first();
        }

        if (is_int($class)) {
            return $files->where('id', $class)->first();
        }

        return $files->where('class', $class)->first();
    }

    /**
     * @param int|string|null $class If int is passed than file is accessed over id
     *
     * @return bool
     */
    public function hasFile($class = null)
    {
        $files = $this->files;

        if (! $class) {
            return $files->count() > 0;
        }

        if (is_int($class)) {
            return $files->where('id', $class)->count() > 0;
        }
        
        return $files->where('class', $class)->count() > 0;
    }

    /**
     * @param string|null $class
     *
     * @return File[]
     */
    public function getFiles($class = null)
    {
        $files = $this->files;

        if (! $class) {
            return $files;
        }

        return $files->where('class', $class)->all();
    }

    /**
     * @param int|string|null $class If int is passed than file is accessed over id
     *
     * @return self
     */
    public function deleteFile($class = null)
    {
        $file = $this->getFile($class);

        if ($file) {
            $file->delete();
        }

        return $this;
    }

    /**
     * @param string|null $class
     *
     * @return self
     */
    public function deleteFiles($class = null)
    {
        foreach ($this->getFiles($class) as $file) {
            $file->delete();
        }

        return $this;
    }

    /**
     * @param string|\Illuminate\Http\UploadedFile $class
     * @param string|\Illuminate\Http\UploadedFile $file
     * @param array                                $attributes The attributes for new file
     *
     * @return File
     */
    public function storeFile($class, $file = null, $attributes = null)
    {
        if ($class instanceof \Illuminate\Http\UploadedFile) {
            $file = $class;
            $class = null;
        }

        if (! is_string($class)) {
            $class = File::DEFAULT_CLASS;
        }

        if (! ($file instanceof \Illuminate\Http\UploadedFile)) {
            if (! is_string($file)) {
                $file = $class;
            }

            if (! request()->file($file)) {
                //unable to resolve file just return
                logger()->debug('File ' . $file . ' has not been uploaded while trying to store it');
                return;
            }

            $file = request()->file($file);
        }

        if (! $file->isValid()) {
            //file has not been uploaded just return
            logger()->debug('File ' . $class . ' is not valid');
            return;
        }

        if (! is_array($attributes)) {
            $attributes = [];
        }
        
        $attributes['class'] = $class;
        $attributes['disk'] = $this->getFileClassStorageDiskName($class);
        $attributes['directory'] = $this->getFileClassStorageDirectory($class);
        logger()->debug('Storing file', $attributes);
        $fileModel = $this->files()->create($attributes);

        $fileModel->storeFile($file, $attributes);

        return $fileModel;
    }

    /**
     * @param string|\Illuminate\Http\UploadedFile[] $class
     * @param string|\Illuminate\Http\UploadedFile[] $files
     * @param array                                  $attributes The attributes for each of the new file
     */
    public function storeFiles($class, $files = null, $attributes = null)
    {
        if (is_array($class)) {
            $file = $class;
            $class = null;
        }

        if (! is_string($class)) {
            $class = File::DEFAULT_CLASS;
        }

        if (is_null($files)) {
            $files = collect(request()->file($class))->flatten()->toArray();
        }
        
        if (is_string($files)) {
            $files = collect(request()->file($files))->flatten()->toArray();
        }

        if (! is_array($files) || empty($files)) {
            //no files has been uploaded just return
            return;
        }

        foreach ($files as $file) {
            $this->storeFile($class, $file, $attributes);
        }

        return;
    }

    // OTHER NON API METHODS

    
    public static function bootFileableTrait()
    {
        /* Make morph map relation ( string(morphKey) pointing to class namespace ) */
        Relation::morphMap([
            static::getFileableMorphKey() => static::class,
        ]);

        /* Remove related files on model delete*/
        static::deleting(function ($fileableModel) {
            $fileableModel->deleteFiles();
        });
    }
    
    /*
     * Get model's protected property $table as morph key.
     */
    public static function getFileableMorphKey()
    {
        return (new static())->getTable();
    }
    
    /*
     * Establishes a polymorphic 'one-to-many' relationship with 'files' table
     */
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @param mixed $class
     *
     * @return string The directory on storage for file class
     */
    public function getFileClassStorageDirectory($class)
    {
        if (
            ! isset($this->fileClassStorageDirectoryMap)
            || ! is_array($this->fileClassStorageDirectoryMap)
            || empty($this->fileClassStorageDirectoryMap[$class])
            || ! is_string($this->fileClassStorageDirectoryMap[$class])
        ) {
            return $this->getFilesDefaultStorageDirectory();
        }

        return $this->fileClassStorageDirectoryMap[$class];
    }

    /**
     * @param mixed $class
     *
     * @return string Storage disk for file class if is mapped in 'fileClassStorageDiskMap' property
     */
    public function getFileClassStorageDiskName($class)
    {
        if (
            ! isset($this->fileClassStorageDiskMap)
            || ! is_array($this->fileClassStorageDiskMap)
            || empty($this->fileClassStorageDiskMap[$class])
            || ! is_string($this->fileClassStorageDiskMap[$class])
        ) {
            return $this->getFilesDefaultStorageDiskName();
        }

        return $this->fileClassStorageDiskMap[$class];
    }

    /**
     * @return string Default storage disk key
     */
    public function getFilesDefaultStorageDiskName()
    {
        return $this->filesDefaultStorageDiskName ?? File::DEFAULT_STORAGE_DISK;
    }

    /**
     * @return string Default storage directory
     */
    public function getFilesDefaultStorageDirectory()
    {
        if (empty($this->filesDefaultStorageDirectory)) {
            return File::DEFAULT_DIRECTORY;
        }

        if ($this->filesDefaultStorageDirectory === true) {
            return Str::slug($this->getTable());
        }

        return (string) $this->filesDefaultStorageDirectory;
    }
}
