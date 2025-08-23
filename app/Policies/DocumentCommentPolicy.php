<?php

namespace App\Policies;

use App\Models\DocumentComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentCommentPolicy
{
    use HandlesAuthorization;

    public function update(User $user, DocumentComment $comment)
    {
        // Admin can update any comment
        if ($user->isAdmin()) {
            return true;
        }

        // Users can only update their own comments within 24 hours (extended from 1 hour)
        return $comment->user_id === $user->id && 
               $comment->created_at->diffInHours(now()) < 24;
    }

    public function delete(User $user, DocumentComment $comment)
    {
        // Admin can delete any comment
        if ($user->isAdmin()) {
            return true;
        }

        // Users can delete their own comments
        return $comment->user_id === $user->id;
    }
}
