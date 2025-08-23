<?php

namespace App\Policies;

use App\Models\DocumentRoute;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentRoutePolicy
{
    use HandlesAuthorization;

    public function receive(User $user, DocumentRoute $route)
    {
        // Admin can receive any route
        if ($user->isAdmin()) {
            return true;
        }

        // Users can receive routes destined for their department
        return $route->to_department_id === $user->department_id &&
               $route->status === 'pending';
    }

    public function process(User $user, DocumentRoute $route)
    {
        // Admin can process any route
        if ($user->isAdmin()) {
            return true;
        }

        // Department heads and encoders can process routes in their department
        if (in_array($user->role, ['department_head', 'encoder'])) {
            return $route->to_department_id === $user->department_id &&
                   $route->status === 'received';
        }

        return false;
    }

    public function bulkRoute(User $user)
    {
        return in_array($user->role, ['admin', 'department_head']);
    }
}
