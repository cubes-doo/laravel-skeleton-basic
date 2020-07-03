<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
 * Tag model
 */
class Tag extends Model
{
    protected $table = 'tags';
    
    protected $fillable = ['title'];
}
