<div class="comment mb-3" id="comment-{{ $comment->id }}">
    <div class="d-flex">
        <div class="me-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-person"></i>
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="bg-light rounded p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-0">{{ $comment->user->name }}</h6>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        @if($comment->type !== 'general')
                            <span class="badge bg-secondary ms-2">{{ ucfirst($comment->type) }}</span>
                        @endif
                    </div>
                    @if(Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->isAdmin()))
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            @can('update', $comment)
                            <li><button class="dropdown-item" onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->comment) }}')">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </button></li>
                            @endcan
                            @can('delete', $comment)
                            <li><button class="dropdown-item text-danger" onclick="deleteComment({{ $comment->id }})">
                                <i class="bi bi-trash me-2"></i>Delete
                            </button></li>
                            @endcan
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="comment-content" id="comment-content-{{ $comment->id }}">
                    <p class="mb-0">{{ $comment->comment }}</p>
                </div>
                <div class="comment-edit-form" id="comment-edit-form-{{ $comment->id }}" style="display: none;">
                    <form onsubmit="updateComment(event, {{ $comment->id }})">
                        @csrf
                        @method('PUT')
                        <div class="mb-2">
                            <textarea class="form-control" id="edit-comment-{{ $comment->id }}" rows="3" required>{{ $comment->comment }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit({{ $comment->id }})">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($comment->replies->count() > 0)
                <div class="ms-4 mt-3">
                    @foreach($comment->replies as $reply)
                        @include('documents.partials.comment', ['comment' => $reply])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>