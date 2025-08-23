@extends('layouts.app')

@section('title', 'Notifications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Notifications</h1>
            <p class="text-muted mb-0">Stay updated with document activities</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                <i class="bi bi-check-all me-2"></i>Mark All as Read
            </button>
        </div>
    </div>
@endsection

@section("content")
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('notifications.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                
                <div class="col-md-4"></div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i>
                        </button>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }} p-3 border-bottom" 
                     onclick="markAsReadAndNavigate({{ $notification->id }}, '{{ $notification->document_id ? route('documents.show', $notification->document_id) : '#' }}')">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <div class="bg-{{ $notification->type }} bg-opacity-10 text-{{ $notification->type }} rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-{{ $notification->type == 'info' ? 'info-circle' : ($notification->type == 'warning' ? 'exclamation-triangle' : ($notification->type == 'success' ? 'check-circle' : 'x-circle')) }}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                        {{ $notification->title }}
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary ms-2">New</span>
                                        @endif
                                    </h6>
                                    <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" onclick="event.stopPropagation()">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(!$notification->is_read)
                                        <li><button class="dropdown-item" onclick="event.stopPropagation(); markAsRead({{ $notification->id }})">
                                            <i class="bi bi-check me-2"></i>Mark as Read
                                        </button></li>
                                        @endif
                                        <li><button class="dropdown-item text-danger" onclick="event.stopPropagation(); deleteNotification({{ $notification->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Pagination -->
                <div class="card-footer bg-white border-0">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bell text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">No notifications</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['type', 'status']))
                            Try adjusting your filters or <a href="{{ route('notifications.index') }}">clear all filters</a>.
                        @else
                            You're all caught up! New notifications will appear here.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .notification-item {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
    }
    
    .notification-item.unread {
        background-color: #f0f9ff;
        border-left: 4px solid #1e40af;
    }
</style>
@endpush

@push('scripts')
<script>
    function markAsRead(notificationId) {
        fetch(`/api/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    function markAllAsRead() {
        fetch('/api/notifications/mark-all-read', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    function deleteNotification(notificationId) {
        if (confirm('Are you sure you want to delete this notification?')) {
            fetch(`/api/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }
    
    function markAsReadAndNavigate(notificationId, url) {
        fetch(`/api/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (url !== '#') {
                window.location.href = url;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (url !== '#') {
                window.location.href = url;
            }
        });
    }
</script>
@endpush