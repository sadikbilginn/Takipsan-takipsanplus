<?php

namespace App\Providers;

use App\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Factory;

class SettingServiceProvider extends ServiceProvider
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
    public function boot(Factory $cache, Setting $settings)
    {

        if(DB::connection()->getDatabaseName() && Schema::hasTable('settings'))
        {
            $settings = $cache->remember('db_settings', 3600, function() use ($settings)
            {
                return $settings->pluck('value', 'key')->all();
            });

            config()->set('db_settings', $settings);
        }

    }
}
