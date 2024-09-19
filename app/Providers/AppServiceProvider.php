<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('settings', Setting::firstOrCreate());

        Blade::if('canAdminAny', function (...$permissions) {
            $user = auth('admin')->user();
            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }
            return false;
        });

    }
}
