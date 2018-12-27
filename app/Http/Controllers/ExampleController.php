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

/**
 * Example Controller for describing standards
 * 
 * @category   Class
 * @package    Cubes
 * @copyright  2015-2018 Cubes d.o.o.
 * @version    GIT: 1.0.0
 */
class ExampleController extends Controller 
{
    /**
     * 
     */
    public function __construct()
    {
		$this->middleware('can:view,' . Entity::class);
        // 'entity' - route variable name 
		$this->middleware('can:edit,entity')->only(['info', 'activate', 'lock', 'unlock', 'setPin', 'getPin']);
	}

    /**
     * Public methods: always exposed via routes. Their first argument is always 
     * the \Illuminate\Http\Request object, followed by any entities resolved 
     * by dependency injection, than any entities passed via route parameters.
     *          
     * Any other public method would repeat most of the steps, if needed, of 
     * course, but would do its own thing on steps #5 & #6. Be it Job queuing, 
     * Event dispatching, or any other business logic.
     * If needed, steps #5 & #6 could be replaced with a service call, that 
     * would deal with saving data, API calls and any other mumbo-jumbo.
     */
    public function postAction(Request $request, Entity $entity)
    {
        //The order of operations in a BASIC CRUD public method:
        #1 (optional) additional policy check that is specific to postAction
        $this->authorize('policyAction', $entity);
    
        
        #2 validation
        $data = $request->validate([
            // validation rules
            // 1. required or nullable
            // 2. modifier (string or int or date or numeric or file etc)
            // 3. validation rules specific to modifier
        ]);
        
        
        #3 normalization = remove keys from $data that are files, and filter/normalize some values
        unset($data['profile_photo']);
        $data['password'] = bcrypt($data['password']);
        
        #4 business logic check and throw ValidationException
        if ($entity->cards_count > 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cards' => 'You exceed max card count'
            ]);
        }
        
        
        #5 model population
         
        #6 saving data
        
        #7 redirection with a message or json response if "wants json" aka ajax
        
    }
    
    public function getAction(Request $request, Entity $entity)
    {
        
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
