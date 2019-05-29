<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Policies and models that they apply to should be included
 * (using the keyword <b>"use"</b>) like so:
 * <code>
 * |use    App\Policies\SomePolicyName,
 * |       App\Models\SomeModelName,
 * |       App\Models\SomeOtherModelName,
 * |       ...
 * |       App\Models\SomeFinalModelToWhomThePolicyShouldBeAppliedTo;
 * </code>
 *
 * Other rules:
 * #1 Keep the <$policies> array clean!
 * #2 No long-term code commenting, please!
 * #3 Remove inclusions that are no longer necessary!
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
