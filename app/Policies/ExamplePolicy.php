<?php

/**
 * Class
 *
 * PHP version 7.2
 */
namespace App\Policies;

use App\Models\User;

use App\Models\Example as Entity;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Example Policy for describing standards
 *
 * Policies are generally written for each Model separately, but if this is
 * not necessary an abstraction could be made that would cover multiple Models.
 * A fact that you need to hear right away: a Policy can be assigned to multiple
 * Models in the AuthServiceProvider, but a Model cannot use more than one Policy;
 * The last assignment of a Policy to a Model will override all previous ones.
 *
 * All public methods in a Policy must return a boolean value.
 *
 * - before method: used exclusively when there is a recurring check
 * - other (public) method: the user entity is always the first argument,
 *      any other arguments are passed after it.
 *      If a Policy is tailored to a Model CRUD, which is usually the case:
 *          #1 the Entity keyword should be used, if its instance is necessary for a check, @see ExampleController (ctrl + f + "Entity")
 *          #2 each method's name should correspond with the name of the action that was attempted on the Entity instance
 */
class ExamplePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        // Common check for all policy actions
        // executed BEFORE any other action
        // ideally to enable super users

        return true;//remove this after copy!!!
    }
    
    public function access(User $user)
    {
        // Access policy only checks if user has right to do anything with the Entity
    }

    public function change(User $user, Entity $entity)
    {
        // Change policy checks if user has right to change the Entity
    }
}
