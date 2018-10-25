<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\User' => 'App\Policies\UserPolicy',
        'App\Customer' => 'App\Policies\CustomerPolicy',
        'App\Table' => 'App\Policies\TablePolicy',
        'App\DishCategory' => 'App\Policies\DishCategoryPolicy',
        'App\Dish' => 'App\Policies\DishPolicy',
        'App\Cart' => 'App\Policies\CartPolicy',
        'App\Order' => 'App\Policies\OrderPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
