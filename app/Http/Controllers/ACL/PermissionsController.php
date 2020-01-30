<?php

/**
 * Class
 *
 * PHP version 7.2
 */
namespace App\Http\Controllers\ACL;

//change the request class if needed
use Illuminate\Support\Carbon;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request as Request;

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
use App\Http\Resources\Json as JsonResource;
use App\Http\Resources\Select2\PermissionGroup;
use Junges\ACL\Http\Models\Permission as Entity;

/**
 * Example Controller for describing standards
 */
class PermissionsController extends Controller
{
    const ACTIONS = [
        'create',
        'read',
        'update',
        'delete',
    ];

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
        
        $this->middleware(['groups:admin']);
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
        // $query->select('permissions.*');
        
        return view('acl.permissions.all');
    }
    
    public function datatable()
    {
        return
            datatables(Entity::query())
                ->filter(function ($query) {
                    if (request()->has('search')) {
                        $query->where('name', 'like', '%' . request()['search']['value'] . '%');
                    }
                })
                ->addColumn('actions', function ($entity) {
                    return view('acl.permissions.partials.table.actions', ['entity' => $entity]);
                })
                ->rawColumns(['actions'])
                ->setRowAttr([
                    'data-id' => function ($entity) {
                        return $entity->id;
                    },
                ])
                ->make(true);
    }
    
    public function create()
    {
        // Primary goal: page rendering or retuning JSON
        #1 fetching needed data
        
        #2 normalization
        
        #3 business logic
        
        #4 retuning response
        
        return view('acl.permissions.create', [
            'entity' => new Entity(), // passed to avoid existence check on view script
            'models' => get_models(),
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
            'name' => 'required|string|min:3|max:100',
            'model' => 'required|string|min:3|max:100',
            'action' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|min:10|max:655',
        ]);
        
        #2 normalization = remove keys from $data that are files, and filter/normalize some values
        // always unset file keys, it will be processed on request object directly
        $data['slug'] = snake_case($data['model']) . ':' . snake_case($data['action']);
        // always use \Illuminate\Support\Carbon for this, because it is tied to the Time Zone of the application
        // $data['due_date'] = Carbon::parse($data['due_date']);
        // always bcrypt passwords
        // $data['password'] = bcrypt($data['password']);
        
        #3 business logic check and throw ValidationException
        
        #4 model population
        $entity = new Entity();
        $entity->fill($data);
        
        #5 saving data
        $entity->save();
        
        // sync many to many relationships
        // $entity->tags()->sync($data['tag_ids']);
        
        // if there is a file being uploaded (ex. photo)
        
        #6 Return propper response
        
        // if ajax call is in place return JsonResource with message
        if ($request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Permission has been saved!'));
        }
        
        //redirection with a message
        return redirect()->route('acl.permissions.list')->withSystemSuccess(__('Permission has been saved!'));
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
        
        #2 normalization
        
        #3 business logic
        
        #4 retuning response
        
        return view('acl.permissions.edit', [
            'entity' => $entity,
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
            'name' => 'required|string|min:3|max:100',
            'model' => 'required|string|min:3|max:100',
            'action' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|min:10|max:655',
        ]);
        
        #2 normalization = remove keys from $data that are files, and filter/normalize some values
        // always unset file keys, it will be processed on request object directly
        $data['slug'] = snake_case($data['model']) . ':' . snake_case($data['action']);
        // always use \Illuminate\Support\Carbon for this, because it is tied to the Time Zone of the application
        // $data['due_date'] = Carbon::parse($data['due_date']);
        // always bcrypt passwords
        // $data['password'] = bcrypt($data['password']);
        
        #3 business logic check and throw ValidationException
        
        #4 model population
        $entity->fill($data);
        
        #5 saving data
        $entity->save();
        
        // sync many to many relationships
        
        // if there is a file being uploaded (ex. photo)
        
        #6 Return propper response
        
        // if ajax call is in place return JsonResource with message
        if ($request->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Permission has been saved!'));
        }
        
        //redirection with a message
        return redirect()->route('acl.permissions.list')->withSystemSuccess(__('Permission has been saved!'));
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
            return JsonResource::make()->withSuccess(__('Permission has been deleted!'));
        }
        //redirection with a message
        return redirect()->route('acl.permissions.list')->withSystemSuccess(__('Permission has been deleted!'));
    }

    public function selection()
    {
        $permissions = Entity::query();
        $data = $this->request->validate([
            'q' => 'nullable|string',
        ]);
        
        if (! empty($data['q'])) {
            $permissions->where(function ($q) use ($data) {
                $s = ['LIKE', '%' . $data['q'] . '%'];
                $q->orWhere('name', ...$s);
                $q->orWhere('description', ...$s);
            });
        }

        $permissions = $permissions->get();

        // return EntityResource::make($permissions);
        return PermissionGroup::collection(
            $permissions->groupBy(function ($item, $key) {
                return explode(':', $item->slug)[0];
            })->values()
        );
    }
}
