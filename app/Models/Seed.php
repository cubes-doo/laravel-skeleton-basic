<?php

/**
 * Class
 *
 * PHP version 7.2
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seed extends Model
{
    /**
     * specifying table names is recommended
     */
    protected $table = 'seeds';
    
    /**
     * used to check which columns may be updated using mass-assignment
     */
    protected $fillable = ['class'];
}
