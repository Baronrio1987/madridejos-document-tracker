<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'department_head', 'encoder', 'viewer']);
    }

    public function view(User $user, Document $document)
    {
        // Admin can view all documents
        if ($user->isAdmin()) {
            return true;
        }

        // Users can view documents they created
        if ($document->created_by === $user->id) {
            return true;
        }

        // Department heads can view documents in their department
        if ($user->isDepartmentHead()) {
            return $document->current_department_id === $user->department_id ||
                   $document->origin_department_id === $user->department_id;
        }

        // Users in the same department as current or origin department
        return $document->current_department_id === $user->department_id ||
               $document->origin_department_id === $user->department_id;
    }

    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'department_head', 'encoder']);
    }

    public function update(User $user, Document $document)
    {
        // Admin can update all documents
        if ($user->isAdmin()) {
            return true;
        }

        // Users can update documents they created (if not completed)
        if ($document->created_by === $user->id && $document->status !== 'completed') {
            return true;
        }

        // Department heads can update documents in their department
        if ($user->isDepartmentHead()) {
            return $document->current_department_id === $user->department_id;
        }

        return false;
    }

    public function delete(User $user, Document $document)
    {
        // Only admin and document creator can delete (if not routed yet)
        if ($user->isAdmin()) {
            return true;
        }

        return $document->created_by === $user->id && 
               $document->routes()->count() === 0 &&
               $document->status === 'pending';
    }

    public function route(User $user, Document $document)
    {
        // Admin can route any document
        if ($user->isAdmin()) {
            return true;
        }

        // Department heads and encoders can route documents in their current department
        if (in_array($user->role, ['department_head', 'encoder'])) {
            return $document->current_department_id === $user->department_id;
        }

        return false;
    }

    public function generateQrCode(User $user, Document $document)
    {
        return $this->view($user, $document);
    }

    public function downloadQrCode(User $user, Document $document)
    {
        return $this->view($user, $document);
    }

    // Update your Document model to include QR code URL accessor
    public function getQrCodeUrlAttribute()
    {
        return route('qr-codes.generate', $this);
    }

    public function getQrDownloadUrlAttribute()
    {
        return route('qr-codes.download', $this);
    }
}
