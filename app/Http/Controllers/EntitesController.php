<?php

/**
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 * @copyright  2015-2018 Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: 1.0.0
 */

namespace App\Http\Controllers;

//change the request class if needed
use Illuminate\Http\Request as Request;

use Illuminate\Support\Carbon;

/**
 * - Model <use> statements: 
 *      When you have a Controller tailored towards a certain Model entity, 
 *      this Model should be <use>-ed as "Entity". Fore example, 
 *      in a BuildingsController class, the Building Model should be included 
 *      like so: 
 *      <code>
 *          use App\Models\Building as Entity;
 *      </code>
 *      Likewise, if you want to instantiate an Entity, the variable which holds 
 *      the instance should be named $entity.
 *      This should be AVOIDED on Controllers that are NOT tailored to a 
 *      Model CRUD.
 */
use App\Models\Example as Entity;
use App\Http\Resources\Entity as EntityResource;

/**
 * Example Controller for describing standards
 * 
 * @category   Class
 * @package    Cubes
 * @copyright  2015-2018 Cubes d.o.o.
 * @version    GIT: 1.0.0
 */
class EntitesController extends Controller 
{
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * The Controller constructor is primarily used for dependency injection...
     * @link https://laravel.com/docs/5.7/controllers#dependency-injection-and-controllers
     * 
     * and for registering middlewares.
     * @link https://laravel.com/docs/5.7/controllers#controller-middleware
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        
		$this->middleware('can:view,' . Entity::class);
        
		$this
            ->middleware('can:edit,entity') // 'entity' = route variable name 
            ->only(['info', 'activate', 'lock', 'unlock', 'setPin', 'getPin']) // names of methods in this Controller
        ;
	}

    /**
     * Public methods: always exposed via routes. The first arguments MUST be 
     * services resolved by dependency injection, than any entities passed via 
     * route parameters.
     *          
     * Any other public method would repeat most of the steps, if needed, of 
     * course, but would do its own thing on steps #5 & #6. Be it Job queuing, 
     * Event dispatching, or any other business logic.
     * If needed, steps #5 & #6 could be replaced with a service call, that 
     * would deal with saving data, API calls and any other mumbo-jumbo.
     */
    public function postAction(Entity $entity)
    {
        $request = $this->request;
        
        #1 validation
        $data = $request->validate([
            'title'        => 'required|string|max:255|min:2',
            'category_id'  => 'required|int|exists:categories,id',
            'phone_number' => [
                'required', 'string', 
                function ($attribute, $value, $fail) {
                    if ($value === 'foo') {
                        $fail($attribute.' is invalid.');
                    }
                }
            ],
            'due_date'     => 'required|date',
            'status'       => 'required|in:' . implode(',', Entity::STATUSES),
            'image'        => 'nullable|file|mimes:jpg,png,gif',
            // validation rules:
            // 1. required or nullable
            // 2. modifier (string or int or date or numeric or file etc)
            // 3. validation rules specific to modifier
        ]);
        
        
        #2 normalization = remove keys from $data that are files, and filter/normalize some values
        // always unset file keys, it will be processed on request object directly
        unset($data['image']);
        // always use \Illuminate\Support\Carbon for this, because it is tied to the Time Zone of the application
        $data['due_date'] = Carbon::parse($data['due_date']);
        // always bcrypt passwords
        $data['password'] = bcrypt($data['password']);
        
        #3 business logic check and throw ValidationException
        if ($entity->cards_count > 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cards' => 'You exceed max card count'
            ]);
        }
        
        if($entity->status > $data['status']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => 'You can\'t return to previous status'
            ]);
        }
        
        #4 model population
        $entity->fill($data);
        
        #5 saving data
        $entity->save();
        
        // if there is a file being uploaded (ex. image)
        if($request->hasFile('image') && $request->file('image')->isValid()) {
            $imageFile = $request->file('image');
            
            // entites should use the App\Models\Utils\StoreFilesModel trait
            $entity->storeFile($imageFile, 'image');
        }
        
        if($request->wantsJson()) {
            return new EntityResource(Entity::find(1));
            //return EntityResource::collection(Entity::all());
        }
        
        #6 redirection with a message or json response if "wants json" (i.e. ajax call)
        return redirect()->route('entities.list')->withSystemSuccess(__('Entity has been saved!'));
    }
    
    public function getAction(Request $request, Entity $entity)
    {
        // Primary goal: page rendering or retuning JSON
        #1 fetching needed data
        
        #2 normalization
        
        #3 business logic
        
        #4 retuning response
    }
    
    /**
     * Protected/Private methods: used to uphold the single responsibility 
     * principle, for bits and pieces of code that are repeated throughout the 
     * same Controller or any other Controller which extends this one. 
     * If there are pieces of logic that reoccur on the project all the time, 
     * use of traits is encouraged.
     */
    protected function helperFunction()
    {
        
    }
}
