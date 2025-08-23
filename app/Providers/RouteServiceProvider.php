<?php
// app/Providers/RouteServiceProvider.php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     */
    public const HOME = '/dashboard';

    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        $this->configureRouteModelBinding();
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email . $request->ip());
        });
    }   

    /**
     * Configure route model binding for the application.
     */
    protected function configureRouteModelBinding()
    {
        Route::bind('document', function ($value) {
            return \App\Models\Document::where('id', $value)
                                     ->orWhere('tracking_number', $value)
                                     ->firstOrFail();
        });

        Route::bind('documentType', function ($value) {
            return \App\Models\DocumentType::findOrFail($value);
        });

        Route::bind('department', function ($value) {
            return \App\Models\Department::where('id', $value)
                                        ->orWhere('code', $value)
                                        ->firstOrFail();
        });

        Route::bind('user', function ($value) {
            return \App\Models\User::where('id', $value)
                                  ->orWhere('employee_id', $value)
                                  ->firstOrFail();
        });

        Route::bind('route', function ($value) {
            return \App\Models\DocumentRoute::findOrFail($value);
        });

        Route::bind('attachment', function ($value) {
            return \App\Models\DocumentAttachment::findOrFail($value);
        });

        Route::bind('comment', function ($value) {
            return \App\Models\DocumentComment::findOrFail($value);
        });

        Route::bind('notification', function ($value) {
            return \App\Models\Notification::findOrFail($value);
        });

        Route::bind('setting', function ($value) {
            return \App\Models\SystemSetting::where('id', $value)
                                           ->orWhere('key', $value)
                                           ->firstOrFail();
        });

        Route::bind('template', function ($value) {
            return \App\Models\RoutingTemplate::findOrFail($value);
        });
    }
}