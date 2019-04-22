<?php

/**
 * Class
 *
 * PHP version 7.2
 *
 * @category   class
 *
 * @copyright  Cubes d.o.o.
 * @license    GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers;

//change the request class if needed
use Illuminate\Support\Carbon;

use App\Models\Example as Entity;

/*
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
use Illuminate\Http\Request as Request;
use App\Http\Resources\Json as JsonResource;

/**
 * Example Controller for describing standards
 *
 * @category   Class
 *
 * @copyright  Cubes d.o.o.
 */
class EntitesController extends Controller
{
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * The Controller constructor is primarily used for dependency injection...
     *
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
                
                //limit field on logged in user id for example
                $query->my();
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
        
        return view('entities.all');
    }
    
    public function datatable()
    {
        return
            datatables(Entity::query())
                ->filter(function ($query) {
                    if (request()->has('search')) {
                        $query->where('title', 'like', '%' . request()['search']['value'] . '%');
                    }
                })
                ->editColumn('active', function ($entity) {
                    return view('entities.partials.table.active', compact('entity'));
                })
                ->editColumn('title', '{{str_cut($title, 20)}}')
                ->editColumn('description', '{{str_cut($description, 30)}}')
                ->editColumn('photo', function ($entity) {
                    return view('entities.partials.table.photo', compact('entity'));
                })
                ->addColumn('actions', function ($entity) {
                    return view('entities.partials.table.actions', compact('entity'));
                })
                ->rawColumns(['active', 'photo', 'actions'])
                ->setRowAttr([
                    'data-id' => function ($entity) {
                        return $entity->id;
                    },
                ])
                ->make(true);
    }
    
    public function create()
    {
        $request = $this->request;
        
        // Primary goal: page rendering or retuning JSON
        #1 fetching needed data
        $statuses = Entity::STATUSES;
        
        #2 normalization
        
        #3 business logic
        
        #4 retuning response
        
        return view('entities.create', [
            'entity' => new Entity(), // passed to avoid existence check on view script
            'statuses' => $statuses,
        ]);
    }
    
    public function store()
    {
        $request = $this->request;
        
        #1 validation
        $data = $request->validate([
            // validation rules:
            // 1. required or nullable
            // 2. modifier (string, int, date, numeric, file, etc)
            // 3. validation rules specific to modifier
            'title' => 'required|string|min:10|max:100',
            'description' => 'required|string|min:10|max:655',
            'status' => [
                'nullable', 'string', 'in:' . implode(',', Entity::STATUSES),
                // function ($attribute, $value, $fail) {
                //     if ($value === 'foo') {
                //         $fail($attribute.' is invalid.');
                //     }
                // }
            ],
            'photo' => 'nullable|file|mimes:jpg,png,gif',
            'photo_resize' => 'nullable|file|mimes:jpg,png,gif',
            // 'due_date'     => 'required|date',
            // 'status'       => 'required|string|in:' . implode(',', Entity::STATUSES),
            // 'tag_ids'      => 'nullable|array|exists:tags,id', // many to many relationship
        ]);
        
        #2 normalization = remove keys from $data that are files, and filter/normalize some values
        // always unset file keys, it will be processed on request object directly
        unset($data['photo']);
        unset($data['photo_resize']);
        // always use \Illuminate\Support\Carbon for this, because it is tied to the Time Zone of the application
        // $data['due_date'] = Carbon::parse($data['due_date']);
        // always bcrypt passwords
        // $data['password'] = bcrypt($data['password']);
        
        #3 business logic check and throw ValidationException
        $data['created_by'] = auth()->user()->id;
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
        
        // sync many to many relationships
        // $entity->tags()->sync($data['tag_ids']);
        
        // if there is a file being uploaded (ex. photo)
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // entites should use the App\Models\Utils\StoreFilesTrait trait
            $entity->storeFile('photo');
        }
        if ($request->hasFile('photo_resize') && $request->file('photo_resize')->isValid()) {
            // entites should use the App\Models\Utils\StoreFilesTrait trait
            $entity->storeFile('photo_resize');
        }
        
        #6 Return propper response
        
        // if ajax call is in place return JsonResource with message
        if ($request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Entity has been saved!'));
        }
        
        //redirection with a message
        return redirect()->route('entities.list')->withSystemSuccess(__('Entity has been saved!'));
    }
    
    /**
     * @param Entity $entity
     *
     * @return type
     */
    public function edit(Entity $entity)
    {
        $request = $this->request;
        
        // Primary goal: page rendering or retuning JSON
        #1 fetching needed data
        $statuses = Entity::STATUSES;
        
        #2 normalization
        
        #3 business logic
        
        #4 retuning response
        
        return view('entities.edit', [
            'entity' => $entity,
            'statuses' => $statuses,
        ]);
    }
    
    public function update(Entity $entity)
    {
        $request = $this->request;
        
        #1 validation
        $data = $request->validate([
            // validation rules:
            // 1. required or nullable
            // 2. modifier (string, int, date, numeric, file, etc)
            // 3. validation rules specific to modifier
            'title' => 'required|string|min:10|max:100',
            'description' => 'required|string|min:10|max:655',
            'status' => [
                'nullable', 'string', 'in:' . implode(',', Entity::STATUSES),
                // function ($attribute, $value, $fail) {
                //     if ($value === 'foo') {
                //         $fail($attribute.' is invalid.');
                //     }
                // }
            ],
            'photo' => 'nullable|file|mimes:jpg,png,gif',
            // 'due_date'     => 'required|date',
            // 'status'       => 'required|string|in:' . implode(',', Entity::STATUSES),
            // 'tag_ids'      => 'nullable|array|exists:tags,id', // many to many relationship
        ]);
        
        #2 normalization = remove keys from $data that are files, and filter/normalize some values
        // always unset file keys, it will be processed on request object directly
        unset($data['photo']);
        // always use \Illuminate\Support\Carbon for this, because it is tied to the Time Zone of the application
        // $data['due_date'] = Carbon::parse($data['due_date']);
        // always bcrypt passwords
        // $data['password'] = bcrypt($data['password']);
        
        #3 business logic check and throw ValidationException
        $data['created_by'] = auth()->user()->id;
        // if (auth()->user()->role != 'janitor') {
        //     throw \Illuminate\Validation\ValidationException::withMessages([
        //         'cards' => 'Your role can\'t create this entity for some reason or another'
        //     ]);
        // }
        
        #4 model population
        $entity->fill($data);
        
        #5 saving data
        $entity->save();
        
        // sync many to many relationships
        // $entity->tags()->sync($data['tag_ids']);
        
        // if there is a file being uploaded (ex. photo)
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // entites should use the App\Models\Utils\StoreFilesTrait trait
            $entity->storeFile('photo');
        }
        
        #6 Return propper response
        
        // if ajax call is in place return JsonResource with message
        if ($request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Entity has been saved!'));
        }
        
        //redirection with a message
        return redirect()->route('entities.list')->withSystemSuccess(__('Entity has been saved!'));
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
        if ($this->request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Entity has been deleted!'));
        }
        //redirection with a message
        return redirect()->route('entities.list')->withSystemSuccess(__('Entity has been deleted!'));
    }
    
    /**
     * Handles change in any one column. In this case it is a column that
     * denotes entity activity, and will be appropriately called 'active'.
     * Important rules:
     *      #1 only expose this method via routes with the POST or PATCH method
     *      #2 this method only changes the specified column and returns
     *          an appropriate response
     *      #3 other business logic associated with this change must be
     *          delegated to Event Listeners and/or Jobs
     */
    public function changeActive(Entity $entity)
    {
        $entity->update([
            'active' => ! $entity->active,
        ]);
        
        // if ajax call is in place return JsonResource with message
        if ($this->request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Entity status has been changed!'));
        }
        //redirection with a message
        return redirect()->route('entities.list')->withSystemSuccess(__('Entity status has been changed!'));
    }
    
    /**
     * Also abides by the rules used for the delete() method
     */
    public function deletePhoto(Entity $entity)
    {
        $entity->deleteFile('photo');
        
        // if ajax call is in place return JsonResource with message
        if ($this->request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Entity photo deleted!'));
        }
        //redirection with a message
        return redirect()->route('entities.list')->withSystemSuccess(__('Entity photo deleted!'));
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
