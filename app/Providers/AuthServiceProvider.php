<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\DocumentComment;
use App\Models\DocumentRoute;
use App\Models\Notification;
use App\Models\User;
use App\Policies\DocumentCommentPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\DocumentRoutePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Document::class => DocumentPolicy::class,
        User::class => UserPolicy::class,
        DocumentRoute::class => DocumentRoutePolicy::class,
        DocumentComment::class => DocumentCommentPolicy::class,
        Notification::class => NotificationPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Define gates
        Gate::define('manage-settings', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['admin', 'department_head']);
        });

        Gate::define('manage-users', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-departments', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('view-analytics', function ($user) {
            return in_array($user->role, ['admin', 'department_head']);
        });
    }
}
