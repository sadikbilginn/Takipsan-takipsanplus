<?php

namespace App\Providers;

use App\Menu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        view()->composer('*', function($view) {

            $menus = Cache::remember('glb_menus', 3600, function () {
                return Menu::where('parent_id', 0)
                    ->with(['children', 'roles'])
                    ->orderBy('sort')
                    ->get();
            });

            $view->with('glb_menus', $menus);
        });

    }
}
