<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\Facades\DB;
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
        Filament::serving(function () {

            Filament::registerUserMenuItems([
                'account' => UserMenuItem::make()->url(route('filament.pages.profile')),

            ]);

            // Using Vite
            Filament::registerViteTheme('resources/css/filament.css');
            Filament::registerNavigationGroups([
                'Association Management',
                'Business Management',
                'Settings',
                'Old Data'
            ]);
        });

        if (config('app.enable_query_log')) {
            DB::listen(function ($query) {
                $log = self::getEloquentSqlWithBindings($query);
                logger($log);
            });
        }
    }




    public static function getEloquentSqlWithBindings($query)
    {
        return vsprintf(str_replace('?', '%s', $query->sql), collect($query->bindings)->map(function ($binding) {
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }
}
