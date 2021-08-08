<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Config;
use App\Services\GeoIp\GeoIp;
use App\Services\GeoIp\IpStack;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    // add () => UrlGenerator $url
    public function boot(UrlGenerator $url)
    {
        Paginator::useBootstrap();

        Schema::defaultStringLength(191);
        
        if(env('REDIRECT_HTTPS')) {
            $url->formatScheme('https');
        }

        // $this->app->bind('path.public', function() {
        //     return base_path().'/../public_html';
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(GeoIp::class, IpStack::class);
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
        if(env('REDIRECT_HTTPS')) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
