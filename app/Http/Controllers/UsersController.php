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

namespace App\Http\Controllers;

//change the request class if needed
use Illuminate\Http\Request as Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Carbon;

/**
 * - Model <use> statements: 
 *      When you have a Controller tailored towards a certain Model entity, 
 *      this Model should be <use>-ed as "Entity". For example, 
 *      in a BuildingsController class, the Building Model should be included 
 *      like so: 
 *      <code>
 *          use App\Models\Building as Entity;
 *      </code>
 *      Likewise, if you want to instantiate an Entity, the variable which holds 
 *      the instance should be named $entity.
 *      This should be AVOIDED on Controllers that are NOT tailored to a 
 *      Model CRUD.
 * 
 * Method order should stay the same as in routes.
 * 
 */
use App\Models\User as Entity;
use App\Http\Resources\Json as JsonResource;

/**
 * Example Controller for describing standards
 * 
 * @category   Class
 * @package    Cubes
 * @copyright  Cubes d.o.o.
 */
class UsersController extends Controller 
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
        
		/* The "can" (policy) middlewares */
		
		// $this->middleware('can:access,' . Entity::class);
        
		// $this
        //     ->middleware('can:change,entity') // 'entity' = route variable name 
        //     ->only(['info', 'activate', 'lock', 'unlock', 'setPin', 'getPin']) // names of methods in this Controller
        // ;
		
		/* 
		 * Middlewares for global scopes on models
		 * Each Model should have its own separate middleware!!!
	     */
		
		$this->middleware(function ($request, $next) {
			//Model 1
			
			Entity::addGlobalScope(function ($query) {
				
			});
			
			return $next($request);
			
		});
	}

    /**
     * Public methods: always exposed via routes. The first arguments MUST be 
     * services resolved by dependency injection, then any entities passed via 
     * route parameters.
     *          
     * Any other public method would repeat most of the steps, if needed, of 
     * course, but would do its own thing like Job queuing, 
     * Event dispatching, or any other business logic.
     */
	
	
	public function all()
	{
		//initiate entity query
		// $query = Entity::query();
		
		// $query->join();
		//!!! OBLIGATORY IF JOIN IS USED!!!
        // $query->select('entities.*');
        
        return view('users.all');
    }
    
    public function datatable()
    {
        return 
            datatables(Entity::query())
                ->filter(function ($query) {
                    if (!request()->has('search')) {
                        return $query;
                    }
                    return $query->where(function($q) {
                        $condition = ['like', '%' . request()['search']['value'] . '%'];

                        return $q
                            ->orWhere('first_name', ...$condition)
                            ->orWhere('last_name', ...$condition)
                            ->orWhere('email', ...$condition)
                        ;
                    });
                })
                ->addColumn('images', function ($entity) {
                    return view('users.partials.table.images', compact('entity'));
                })
                ->addColumn('actions', function ($entity) {
                    return view('users.partials.table.actions', compact('entity'));
                })
                ->rawColumns(['images', 'actions'])
                ->setRowAttr([
                    'data-id' => function($entity) {
                        return $entity->id;
                    }
                ])
                ->make(true)
        ;
    }
    
    public function create()
    {
        // Primary goal: page rendering or retuning JSON
        #1 fetching needed data
        
        #2 normalization
        
        #3 business logic
        
        #4 retuning response
		
		return view('users.create', [
            'entity'   => new Entity(), // passed to avoid existence check on view script
        ]);
    }
    
    public function store()
    {
        $request = $this->request;
//        dd($request->file('images'));
//        dd($request->all());
        
        #1 validation
        $data = $request->validate([
            // validation rules:
            // 1. required or nullable
            // 2. modifier (string, int, date, numeric, file, etc)
            // 3. validation rules specific to modifier
            'first_name' => 'required|string|min:2|max:100',
            'last_name'  => 'required|string|min:2|max:100',
            'email'      => 'required|string|email',
            'images.*.*' => 'required|file|image'
            // 'due_date'     => 'required|date',
            // 'status'       => 'required|string|in:' . implode(',', Entity::STATUSES),
            // 'tag_ids'      => 'nullable|array|exists:tags,id', // many to many relationship
        ]);
        //dd($request->all());
        //(new \App\Models\Image)->storeImageWithActions($request->file('image'), 'test');
        //(new \App\Models\Image)->storeImageWithActions($request->file('image'), 'test');
        
        
        #2 normalization = remove keys from $data that are files, and filter/normalize some values
        // always unset file keys, it will be processed on request object directly
        // unset($data['photo']);
        // always use \Illuminate\Support\Carbon for this, because it is tied to the Time Zone of the application
        // $data['due_date'] = Carbon::parse($data['due_date']);
        // always bcrypt passwords
        // $data['password'] = bcrypt($data['password']);
        
        #3 business logic check and throw ValidationException
        $data['password'] = bcrypt('psst!@#');
        // if (auth()->user()->role != 'janitor') {
        //     throw \Illuminate\Validation\ValidationException::withMessages([
        //         'cards' => 'Your role can\'t create this entity for some reason or another'
        //     ]);
        // }
        
        #4 model population
        $entity = new Entity();
        
        $entity->fill($data);
        
        #5 saving data
        $entity->save();
        
        $entity->storeImages('images');
        // sync many to many relationships
        // $entity->tags()->sync($data['tag_ids']);
        
        // if there is a file being uploaded (ex. photo)
        // if($request->hasFile('photo') && $request->file('photo')->isValid()) {
        //     // entites should use the App\Models\Utils\StoreFilesModel trait
        //     $entity->storeFile('photo');
        // }
        
		#6 Return propper response
		
		// if ajax call is in place return JsonResource with message
        if($request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('User has been saved!'));
        }
        
        //redirection with a message
        return redirect()->route('users.list')->withSystemSuccess(__('User has been saved!'));
    }
    
	/**
	 * 
	 * @param Entity $entity
	 * @return type
	 */
    public function edit(Entity $entity)
    {
        // Primary goal: page rendering or retuning JSON
        #1 fetching needed data
        
        #2 normalization
        
        #3 business logic
        
        #4 retuning response
		
		return view('users.edit', [
			'entity' => $entity,
		]);
    }
	
    public function update(Entity $entity)
    {
        $request = $this->request;
        $required = 'required';
        
        if($entity->id === auth()->user()->id) {
            $required = 'nullable';
        }

        #1 validation
        $data = $request->validate([
            // validation rules:
            // 1. required or nullable
            // 2. modifier (string, int, date, numeric, file, etc)
            // 3. validation rules specific to modifier
            'first_name' => 'required|string|min:2|max:100',
            'last_name'  => 'required|string|min:2|max:100',
            'email'      => $required . '|string|email',
            // 'due_date'     => 'required|date',
            // 'status'       => 'required|string|in:' . implode(',', Entity::STATUSES),
            // 'tag_ids'      => 'nullable|array|exists:tags,id', // many to many relationship
        ]);
        
        #2 normalization = remove keys from $data that are files, and filter/normalize some values
        if($entity->id === auth()->user()->id) {
            unset($data['email']);
        }
        // always unset file keys, it will be processed on request object directly
        // unset($data['photo']);
        // always use \Illuminate\Support\Carbon for this, because it is tied to the Time Zone of the application
        // $data['due_date'] = Carbon::parse($data['due_date']);
        // always bcrypt passwords
        // $data['password'] = bcrypt($data['password']);
        
        #3 business logic check and throw ValidationException
        // if (auth()->user()->role != 'janitor') {
        //     throw \Illuminate\Validation\ValidationException::withMessages([
        //         'cards' => 'Your role can\'t create this entity for some reason or another'
        //     ]);
        // }
        
        #4 model population
        $entity->fill($data);
        
        #5 saving data
        $entity->save();
        
        $entity->storeImages('images');
        // sync many to many relationships
        // $entity->tags()->sync($data['tag_ids']);
        
        // if there is a file being uploaded (ex. photo)
        // if($request->hasFile('photo') && $request->file('photo')->isValid()) {
        //     // entites should use the App\Models\Utils\StoreFilesModel trait
        //     $entity->storeFile('photo');
        // }
        
		#6 Return propper response
		
		// if ajax call is in place return JsonResource with message
        if($request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('User has been saved!'));
        }
        
        //redirection with a message
        return redirect()->route('users.list')->withSystemSuccess(__('User has been saved!'));
    }
    
    /**
     * Handles deletion of the Entity around which this controller revolves.
     * Important issues:
     *      #1 only expose this method via routes with the POST or DELETE method
     *      #2 $entity->delete(); is the only appropriate way to delete a model; 
     *          Whether its soft- or hard- delete, should be defined 
     *          in the model itself
     */
    public function delete(Entity $entity)
    {
        $entity->delete();
        
        // if ajax call is in place return JsonResource with message
        if($this->request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('User has been deleted!'));
        }
        //redirection with a message
        return redirect()->route('users.list')->withSystemSuccess(__('User has been deleted!'));
    }
    
    /**
     * Also abides by the rules used for the delete() method
     */
    public function deletePhoto(Request $request, Entity $entity)
    {
        $request->validate([
            "imageId" => ["nullable", "integer", "min:500", "exists:images,id"],
            "deleteChildren" => ["nullable", "integer", "in:0,1"]
        ]);
//        $validator = Validator::make($request->all(), [
//            "imageId" => ["required", "integer", "exists:images,id"]
//        ]);
//        
//        
//        if ( ! $validator->passes() ) {   
//            return  JsonResource::make(["status" => "FAIL", "errors" => $validator->errors()]);
//        }
  
        // DELTE ALL IMAGES BOUND TO ENTITY
        //$entity = Entity::find($request->id);
//        $entity->deleteFile($request->column);
//        $entity->{$request->column} = NULL;
//        $entity->save();
        
        // DELETE SINGLE IMAGE
        \App\Models\Image::where('id', $request->imageId)->delete();
        
        // DELETE AN IMAGE AND ALL ITS CHILDREN
        // IMPLEMENT
        
        return json_encode(["status" => "OK"]);
        
        // if ajax call is in place return JsonResource with message
        if($this->request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('User photo deleted!'));
        }
        //redirection with a message
        return redirect()->route('users.list')->withSystemSuccess(__('User photo deleted!'));
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
