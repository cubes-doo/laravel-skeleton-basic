<?php

/**
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 * @copyright  Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

use App\Models\Utils\{
    ActiveInactive, 
    ActiveInactiveModel,
    StoreFilesModel
};

/**
 * Example Model for describing standards
 * 
 * @category   Class
 * @package    Cubes
 * @copyright  Cubes d.o.o.
 */
class Example extends Model implements ActiveInactive 
{
    use ActiveInactiveModel, StoreFilesModel, SoftDeletes;
    
    /**
     * Constants: must be declared for non-arbitrary values, that will always correspond to an attribute in Entity
     */
    const STATUSES = [
        'status1',
        'status2',
        'status3',
        'status4'
    ];
    
    const STATUS_1 = 'status1';
    const STATUS_2 = 'status2';
    const STATUS_3 = 'status3';
    const STATUS_4 = 'status4';
    
    /**
     * specifying table names is recommended
     */
    protected $table = 'examples';
    
    /**
     * used to check which columns may be updated using mass-assignment
     */
    protected $fillable = ['id', 'title', 'description', 'active', 'status', 'created_by'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['active', 'status', 'title', 'description', 'photo'];
    
    /**
     * used to fetch certain attributes as Date objects
     */
    protected $dates = ['created_at', 'updated_at'];
    
    /**
     * https://laravel.com/docs/5.5/eloquent-mutators#attribute-casting
     */
    // protected $casts = ['data' => 'array'];
    
    /**
     * https://laravel.com/docs/5.5/eloquent-relationships#touching-parent-timestamps
     */
    // protected $touches = ['exampleParent'];
    
    
    /**
     * Relationships: must be declared for all related models, even if they will never be used
     */
    public function exampleParent() 
    {
        return $this->belongsTo('App\Models\ExampleParent');
    }
    
    public function tags()
    {
        return $this->hasMany('App\Models\Tag');
    }
    
    public function exampleChild() 
    {
        return $this->hasOne('App\Models\ExampleChild');
    }
    
    public function exampleChildren() 
    {
        return $this->hasMany('App\Models\ExampleChild');
    }
    
    /**
     * Local Scopes: must be declared to avoid code repetition when querying the entity against its own table or any related table
     */
    public function scopeMy($query)
    {
        return $query->where('created_by', auth()->user()->id);
    }
    
    /**
     * Overriding delete method if delete logic is complicated & is a HARD delete.
     * 
     * DO NOT DO THIS IF SOFT DELETE!!!
     */
    public function delete() 
    {
        // deleting many-to-many relationships
        // $this->tags()->sync([]);
        // delete children if needed
        //$this->exampleChildren()->delete();
        // delete all related files by columns
        $this->deleteFile('photo');
        // delete this instance
        return parent::delete();
    }
}
