<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'department_head']);
    }

    public function view(User $user, User $model)
    {
        // Admin can view all users
        if ($user->isAdmin()) {
            return true;
        }

        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Department heads can view users in their department
        if ($user->isDepartmentHead()) {
            return $user->department_id === $model->department_id;
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model)
    {
        // Admin can update all users
        if ($user->isAdmin()) {
            return true;
        }

        // Users can update their own profile (limited fields)
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        // Only admin can delete users, but not themselves
        return $user->isAdmin() && $user->id !== $model->id;
    }
}
