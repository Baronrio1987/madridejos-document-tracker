@extends('layouts.app')

@section('title', $document->tracking_number)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
    <li class="breadcrumb-item active">{{ $document->tracking_number }}</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ $document->tracking_number }}</h1>
            <p class="text-muted mb-0">{{ $document->title }}</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                @can('update', $document)
                <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                @endcan
                
                @can('route', $document)
                <a href="{{ route('documents.routing.create', $document) }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right me-2"></i>Route Document
                </a>
                @endcan
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item" onclick="updateStatus()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Update Status
                        </button></li>
                        <li><a class="dropdown-item" href="{{ route('documents.track', $document->tracking_number) }}">
                            <i class="bi bi-search me-2"></i>Track Document
                        </a></li>
                        @can('delete', $document)
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger" onclick="deleteDocument()">
                            <i class="bi bi-trash me-2"></i>Delete
                        </button></li>
                        @endcan
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-xl-8">
            <!-- Document Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tracking Number</label>
                            <p class="fw-semibold">{{ $document->tracking_number }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <p>
                                <span class="status-badge status-{{ $document->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Document Type</label>
                            <p>{{ $document->documentType->name }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Priority</label>
                            <p>
                                <i class="bi bi-flag priority-{{ $document->priority }}"></i>
                                {{ ucfirst($document->priority) }}
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Origin Department</label>
                            <p>{{ $document->originDepartment->name }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Current Department</label>
                            <p>{{ $document->currentDepartment->name }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Date Received</label>
                            <p>{{ $document->date_received->format('M d, Y') }}</p>
                        </div>
                        
                        @if($document->target_completion_date)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Target Completion</label>
                            <p>
                                {{ $document->target_completion_date->format('M d, Y') }}
                                @if($document->isOverdue())
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @endif
                            </p>
                        </div>
                        @endif
                        
                        <div class="col-12">
                            <label class="form-label text-muted">Title</label>
                            <p>{{ $document->title }}</p>
                        </div>
                        
                        @if($document->description)
                        <div class="col-12">
                            <label class="form-label text-muted">Description</label>
                            <p>{{ $document->description }}</p>
                        </div>
                        @endif
                        
                        @if($document->remarks)
                        <div class="col-12">
                            <label class="form-label text-muted">Remarks</label>
                            <p>{{ $document->remarks }}</p>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Created By</label>
                            <p>{{ $document->creator->name }} ({{ $document->creator->department->name }})</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Created Date</label>
                            <p>{{ $document->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Document Routes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Routing History</h5>
                </div>
                <div class="card-body">
                    @if($document->routes && $document->routes->count() > 0)
                        <div class="timeline">
                            @foreach($document->routes->sortBy('routed_at') as $route)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $route->status == 'processed' ? 'success' : ($route->status == 'received' ? 'info' : 'warning') }}"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $route->fromDepartment->name }} → {{ $route->toDepartment->name }}</h6>
                                            <p class="text-muted mb-1">{{ $route->routing_purpose }}</p>
                                            @if($route->instructions)
                                                <p class="text-muted small mb-1"><strong>Instructions:</strong> {{ $route->instructions }}</p>
                                            @endif
                                            <small class="text-muted">
                                                Routed by {{ $route->routedBy->name }} on {{ $route->routed_at->format('M d, Y h:i A') }}
                                                @if($route->received_at)
                                                    • Received on {{ $route->received_at->format('M d, Y h:i A') }}
                                                @endif
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $route->status == 'processed' ? 'success' : ($route->status == 'received' ? 'info' : 'warning') }}">
                                            {{ ucfirst($route->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-arrow-right" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">No routing history yet</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Comments -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Comments</h5>
                </div>
                <div class="card-body">
                    <!-- Add Comment Form -->
                    <form id="commentForm" class="mb-4">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <textarea class="form-control" name="comment" rows="3" placeholder="Add a comment..." required></textarea>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select mb-2" name="type">
                                    <option value="general">General</option>
                                    <option value="instruction">Instruction</option>
                                    <option value="feedback">Feedback</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Add Comment</button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Comments List -->
                    <div id="commentsList">
                        @if($document->comments && $document->comments->count() > 0)
                            @foreach($document->comments->where('parent_id', null)->sortByDesc('created_at') as $comment)
                                @include('documents.partials.comment', ['comment' => $comment])
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">
                                <p class="mb-0">No comments yet. Be the first to add one!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('route', $document)
                        <a href="{{ route('documents.routing.create', $document) }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Route Document
                        </a>
                        @endcan
                        
                        @can('update', $document)
                        <button class="btn btn-outline-primary" onclick="updateStatus()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Update Status
                        </button>
                        @endcan
                        
                        <a href="{{ route('documents.track', $document->tracking_number) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-search me-2"></i>Track Document
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Attachments -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-paperclip me-2"></i>
                        Attachments (<span id="attachmentCount">{{ $document->attachments ? $document->attachments->count() : 0 }}</span>)
                    </h6>
                    @can('update', $document)
                    <button class="btn btn-sm btn-outline-primary" onclick="uploadFiles()">
                        <i class="bi bi-plus me-1"></i>Upload
                    </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div id="attachmentsList">
                        @if($document->attachments && $document->attachments->count() > 0)
                            @foreach($document->attachments as $attachment)
                            <div class="d-flex align-items-center justify-content-between mb-3 attachment-item" data-attachment-id="{{ $attachment->id }}">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-{{ $attachment->file_type === 'pdf' ? 'pdf' : ($attachment->file_type === 'doc' || $attachment->file_type === 'docx' ? 'word' : 'text') }} text-muted me-2" style="font-size: 1.2rem;"></i>
                                    <div>
                                        <a href="javascript:void(0)" class="text-decoration-none fw-semibold" onclick="viewAttachment({{ $attachment->id }}, '{{ $attachment->original_name }}', '{{ $attachment->file_type }}')">
                                            {{ $attachment->original_name }}
                                        </a>
                                        <div class="small text-muted">
                                            {{ $attachment->getFileSizeHumanAttribute() }} • 
                                            Uploaded {{ $attachment->created_at->diffForHumans() }} by {{ $attachment->uploadedBy->name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="downloadAttachment({{ $attachment->id }})" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    @can('update', $document)
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAttachment({{ $attachment->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted" id="noAttachmentsMessage">
                                <i class="bi bi-paperclip" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No attachments</p>
                                @can('update', $document)
                                <small>Click the Upload button to add files</small>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Document Statistics -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $document->routes ? $document->routes->count() : 0 }}</div>
                                <small class="text-muted">Routes</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $document->comments ? $document->comments->count() : 0 }}</div>
                                <small class="text-muted">Comments</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $document->attachments ? $document->attachments->count() : 0 }}</div>
                                <small class="text-muted">Files</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0">{{ $document->created_at->diffInDays() }}</div>
                                <small class="text-muted">Days Old</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('documents.partials.qr-code-section', ['document' => $document])
    
    <!-- File Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="files[]" multiple 
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx" id="fileInput">
                            <small class="text-muted">
                                Allowed types: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX. Max 10MB per file.
                            </small>
                        </div>
                        <div id="uploadProgress" class="progress d-none mb-3" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div id="selectedFiles"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="uploadButton">
                            <i class="bi bi-upload me-1"></i>Upload Files
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="statusForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending" {{ $document->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $document->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $document->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $document->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks (Optional)</label>
                            <textarea class="form-control" name="remarks" rows="3" placeholder="Add remarks about this status change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- File Viewer Modal -->
    <div class="modal fade" id="fileViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl" id="fileViewerModalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileViewerTitle">File Viewer</h5>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="minimizeBtn" title="Minimize">
                            <i class="bi bi-dash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="maximizeBtn" title="Maximize">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-0" id="fileViewerModalBody">
                    <div class="d-flex justify-content-center align-items-center bg-light" style="min-height: 500px;" id="fileViewerContainer">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="fileViewerModalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="printFileBtn">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                    <button type="button" class="btn btn-success" id="downloadFileBtn">
                        <i class="bi bi-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimized Modal Indicator -->
    <div class="position-fixed bottom-0 start-0 m-3 d-none" id="minimizedIndicator" style="z-index: 1060;">
        <div class="card shadow-lg" style="width: 250px;">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark me-2"></i>
                        <span class="small text-truncate" id="minimizedFileName">File Viewer</span>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="restoreBtn" title="Restore">
                            <i class="bi bi-window"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="closeMinimizedBtn" title="Close">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: capitalize;
    }
    
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-in_progress { background-color: #cce7ff; color: #0f5132; }
    .status-completed { background-color: #d1e7dd; color: #0f5132; }
    .status-cancelled { background-color: #f8d7da; color: #721c24; }
    .status-archived { background-color: #e2e3e5; color: #41464b; }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -22px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #e9ecef;
    }
    
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #dee2e6;
    }

    #fileViewerContainer {
        position: relative;
        overflow: auto;
    }

    #fileViewerContainer iframe {
        width: 100%;
        height: 600px;
        border: none;
    }

    #fileViewerContainer img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    .file-preview-error {
        text-align: center;
        padding: 50px;
        color: #6c757d;
    }

    .file-preview-error i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    /* Modal maximize/minimize styles */
    .modal-maximized {
        max-width: 100vw !important;
        width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
    }

    .modal-maximized .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    .modal-maximized .modal-body {
        flex: 1;
        overflow: auto;
    }

    .modal-maximized #fileViewerContainer {
        height: 100%;
        min-height: auto;
    }

    .modal-maximized #fileViewerContainer iframe {
        height: 100%;
    }

    .modal-minimized {
        display: none !important;
    }

    #minimizedIndicator {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #minimizedIndicator:hover {
        transform: translateY(-2px);
    }

    .modal-transition {
        transition: all 0.3s ease;
    }

    /* Office viewer styles */
    .office-viewer-container {
        padding: 20px;
    }

    .viewer-tabs {
        text-align: center;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .viewer-tabs button {
        margin: 0 5px;
        transition: all 0.2s ease;
    }

    .viewer-tabs button.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .viewer-content {
        min-height: 500px;
        position: relative;
    }

    .file-preview-error ul {
        text-align: left;
        display: inline-block;
    }

    .file-preview-error .btn {
        margin: 5px;
    }

    /* Loading animation for office documents */
    .office-viewer-container .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Global variables
    let uploadModal;
    let statusModal;
    let fileViewerModal;
    let currentAttachmentId = null;
    let isModalMaximized = false;
    let isModalMinimized = false;
    let originalModalClass = 'modal-xl';
    
    document.addEventListener('DOMContentLoaded', function() {
        uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        fileViewerModal = new bootstrap.Modal(document.getElementById('fileViewerModal'));
        
        // File input change handler
        document.getElementById('fileInput').addEventListener('change', function() {
            displaySelectedFiles(this.files);
        });

        // Modal control event listeners
        initializeModalControls();
    });

    function initializeModalControls() {
        // Maximize button
        document.getElementById('maximizeBtn').addEventListener('click', function() {
            toggleMaximize();
        });

        // Minimize button
        document.getElementById('minimizeBtn').addEventListener('click', function() {
            minimizeModal();
        });

        // Restore button (from minimized state)
        document.getElementById('restoreBtn').addEventListener('click', function() {
            restoreModal();
        });

        // Close minimized button
        document.getElementById('closeMinimizedBtn').addEventListener('click', function() {
            closeModal();
        });

        // Double-click header to maximize/restore
        document.querySelector('#fileViewerModal .modal-header').addEventListener('dblclick', function() {
            if (!isModalMinimized) {
                toggleMaximize();
            }
        });

        // Prevent modal from closing when clicking on modal dialog in maximized state
        document.getElementById('fileViewerModal').addEventListener('click', function(e) {
            if (isModalMaximized && e.target === this) {
                e.stopPropagation();
            }
        });
    }

    function toggleMaximize() {
        const modal = document.getElementById('fileViewerModal');
        const modalDialog = document.getElementById('fileViewerModalDialog');
        const maximizeBtn = document.getElementById('maximizeBtn');
        const maximizeIcon = maximizeBtn.querySelector('i');

        if (isModalMaximized) {
            // Restore to normal size
            modalDialog.className = `modal-dialog ${originalModalClass} modal-transition`;
            maximizeIcon.className = 'bi bi-arrows-fullscreen';
            maximizeBtn.title = 'Maximize';
            isModalMaximized = false;
        } else {
            // Maximize
            modalDialog.className = 'modal-dialog modal-maximized modal-transition';
            maximizeIcon.className = 'bi bi-fullscreen-exit';
            maximizeBtn.title = 'Restore';
            isModalMaximized = true;
        }

        // Adjust iframe height if needed
        adjustViewerHeight();
    }

    function minimizeModal() {
        const modal = document.getElementById('fileViewerModal');
        const minimizedIndicator = document.getElementById('minimizedIndicator');
        const fileName = document.getElementById('fileViewerTitle').textContent;

        // Hide modal
        modal.classList.add('modal-minimized');
        
        // Show minimized indicator
        minimizedIndicator.classList.remove('d-none');
        document.getElementById('minimizedFileName').textContent = fileName;
        
        isModalMinimized = true;
    }

    function restoreModal() {
        const modal = document.getElementById('fileViewerModal');
        const minimizedIndicator = document.getElementById('minimizedIndicator');

        // Show modal
        modal.classList.remove('modal-minimized');
        
        // Hide minimized indicator
        minimizedIndicator.classList.add('d-none');
        
        isModalMinimized = false;
    }

    function closeModal() {
        const modal = document.getElementById('fileViewerModal');
        const minimizedIndicator = document.getElementById('minimizedIndicator');
        const modalDialog = document.getElementById('fileViewerModalDialog');

        // Reset modal state
        modal.classList.remove('modal-minimized');
        minimizedIndicator.classList.add('d-none');
        
        // Reset maximize state
        if (isModalMaximized) {
            modalDialog.className = `modal-dialog ${originalModalClass}`;
            document.getElementById('maximizeBtn').querySelector('i').className = 'bi bi-arrows-fullscreen';
            document.getElementById('maximizeBtn').title = 'Maximize';
            isModalMaximized = false;
        }
        
        // Close modal
        fileViewerModal.hide();
        
        // Reset states
        isModalMinimized = false;
        currentAttachmentId = null;
    }

    function adjustViewerHeight() {
        const container = document.getElementById('fileViewerContainer');
        const iframe = container.querySelector('iframe');
        
        if (iframe && isModalMaximized) {
            // Calculate available height
            const modalHeader = document.querySelector('#fileViewerModal .modal-header');
            const modalFooter = document.querySelector('#fileViewerModal .modal-footer');
            const headerHeight = modalHeader.offsetHeight;
            const footerHeight = modalFooter.offsetHeight;
            const availableHeight = window.innerHeight - headerHeight - footerHeight - 20; // 20px for padding
            
            iframe.style.height = availableHeight + 'px';
            container.style.height = availableHeight + 'px';
        } else if (iframe && !isModalMaximized) {
            // Reset to default height
            iframe.style.height = '600px';
            container.style.height = 'auto';
            container.style.minHeight = '500px';
        }
    }

    // Listen for window resize to adjust maximized modal
    window.addEventListener('resize', function() {
        if (isModalMaximized) {
            adjustViewerHeight();
        }
    });

    // View attachment function
    function viewAttachment(attachmentId, filename, fileType) {
        currentAttachmentId = attachmentId;
        
        // Reset modal states
        resetModalState();
        
        // Set modal title
        document.getElementById('fileViewerTitle').textContent = filename;
        
        // Show loading spinner
        const container = document.getElementById('fileViewerContainer');
        container.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        
        // Show modal
        fileViewerModal.show();
        
        // Get file viewer URL
        const viewerUrl = `{{ route('attachments.view', '__ATTACHMENT_ID__') }}`.replace('__ATTACHMENT_ID__', attachmentId);
        
        // Load file content based on file type
        loadFileContent(viewerUrl, filename, fileType, container);
    }

    function resetModalState() {
        const modal = document.getElementById('fileViewerModal');
        const modalDialog = document.getElementById('fileViewerModalDialog');
        const minimizedIndicator = document.getElementById('minimizedIndicator');
        const maximizeBtn = document.getElementById('maximizeBtn');

        // Reset classes
        modal.classList.remove('modal-minimized');
        modalDialog.className = `modal-dialog ${originalModalClass}`;
        minimizedIndicator.classList.add('d-none');
        
        // Reset maximize button
        maximizeBtn.querySelector('i').className = 'bi bi-arrows-fullscreen';
        maximizeBtn.title = 'Maximize';
        
        // Reset states
        isModalMaximized = false;
        isModalMinimized = false;
    }

    function loadFileContent(viewerUrl, filename, fileType, container) {
        if (['pdf'].includes(fileType.toLowerCase())) {
            // For PDF files, use iframe with PDF viewer
            container.innerHTML = `
                <iframe src="${viewerUrl}" style="width: 100%; height: 600px; border: none;"></iframe>
            `;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileType.toLowerCase())) {
            // For images, display directly
            container.innerHTML = `
                <img src="${viewerUrl}" alt="${filename}" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
            `;
        } else if (['doc', 'docx'].includes(fileType.toLowerCase())) {
            // For Word documents, try multiple viewers
            loadOfficeDocument(viewerUrl, filename, fileType, container, 'word');
        } else if (['xls', 'xlsx'].includes(fileType.toLowerCase())) {
            // For Excel documents, try multiple viewers
            loadOfficeDocument(viewerUrl, filename, fileType, container, 'excel');
        } else if (['ppt', 'pptx'].includes(fileType.toLowerCase())) {
            // For PowerPoint documents, try multiple viewers
            loadOfficeDocument(viewerUrl, filename, fileType, container, 'powerpoint');
        } else {
            // For other file types, show preview not available
            container.innerHTML = `
                <div class="file-preview-error">
                    <i class="bi bi-file-earmark-text"></i>
                    <h5>Preview not available</h5>
                    <p>This file type cannot be previewed in the browser.</p>
                    <p>Use the download button to view the file.</p>
                </div>
            `;
        }
        
        // Handle iframe load errors
        const iframe = container.querySelector('iframe');
        if (iframe) {
            iframe.onerror = function() {
                handleViewerError(container, filename, fileType);
            };
            
            // Add load timeout
            setTimeout(() => {
                if (iframe.contentDocument && iframe.contentDocument.readyState !== 'complete') {
                    handleViewerError(container, filename, fileType);
                }
            }, 10000); // 10 second timeout
        }
        
        // Handle image load errors
        const img = container.querySelector('img');
        if (img) {
            img.onerror = function() {
                container.innerHTML = `
                    <div class="file-preview-error">
                        <i class="bi bi-exclamation-triangle"></i>
                        <h5>Failed to load image</h5>
                        <p>The image could not be loaded for preview.</p>
                        <p>Use the download button to view the file.</p>
                    </div>
                `;
            };
        }
    }

    function loadOfficeDocument(viewerUrl, filename, fileType, container, docType) {
        const fullUrl = window.location.origin + viewerUrl;
        
        // Create a container for multiple viewer attempts
        container.innerHTML = `
            <div class="office-viewer-container">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="spinner-border text-primary me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span>Loading ${docType} preview...</span>
                </div>
                <div class="viewer-tabs">
                    <button class="btn btn-sm btn-outline-primary active" onclick="switchViewer('google', '${fullUrl}', '${filename}', '${fileType}')">
                        Google Docs Viewer
                    </button>
                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="switchViewer('office', '${fullUrl}', '${filename}', '${fileType}')">
                        Microsoft Office Online
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="switchViewer('download', '${fullUrl}', '${filename}', '${fileType}')">
                        Download to View
                    </button>
                </div>
                <div class="viewer-content mt-3" id="viewerContent">
                    <!-- Viewer will be loaded here -->
                </div>
            </div>
        `;
        
        // Start with Google Docs viewer
        switchViewer('google', fullUrl, filename, fileType);
    }

    function switchViewer(viewerType, fullUrl, filename, fileType) {
        const viewerContent = document.getElementById('viewerContent');
        const buttons = document.querySelectorAll('.viewer-tabs button');
        
        // Update button states
        buttons.forEach(btn => btn.classList.remove('active'));
        event?.target?.classList.add('active');
        
        switch(viewerType) {
            case 'google':
                const googleViewerUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(fullUrl)}&embedded=true`;
                viewerContent.innerHTML = `
                    <iframe src="${googleViewerUrl}" style="width: 100%; height: 600px; border: none;" onload="handleViewerLoad(this)" onerror="handleViewerError(this.parentNode, '${filename}', '${fileType}')"></iframe>
                `;
                break;
                
            case 'office':
                const officeViewerUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fullUrl)}`;
                viewerContent.innerHTML = `
                    <iframe src="${officeViewerUrl}" style="width: 100%; height: 600px; border: none;" onload="handleViewerLoad(this)" onerror="handleViewerError(this.parentNode, '${filename}', '${fileType}')"></iframe>
                `;
                break;
                
            case 'download':
                viewerContent.innerHTML = `
                    <div class="text-center p-4">
                        <i class="bi bi-file-earmark-${fileType.includes('xl') ? 'excel' : (fileType.includes('doc') ? 'word' : 'text')}" style="font-size: 4rem; color: #28a745;"></i>
                        <h5 class="mt-3">${filename}</h5>
                        <p class="text-muted">This file needs to be downloaded to view properly.</p>
                        <button class="btn btn-success btn-lg" onclick="downloadAttachment(${currentAttachmentId})">
                            <i class="bi bi-download me-2"></i>Download File
                        </button>
                        <div class="mt-3 small text-muted">
                            <p>Office files can be viewed in:</p>
                            <ul class="list-unstyled">
                                <li>• Microsoft Office (Word, Excel, PowerPoint)</li>
                                <li>• LibreOffice</li>
                                <li>• Google Workspace</li>
                            </ul>
                        </div>
                    </div>
                `;
                break;
        }
    }

    function handleViewerLoad(iframe) {
        try {
            // Check if iframe loaded successfully
            if (iframe.contentDocument || iframe.contentWindow) {
                console.log('Viewer loaded successfully');
            }
        } catch (e) {
            console.warn('Viewer load check failed:', e);
        }
    }

    function handleViewerError(container, filename, fileType) {
        const isExcelFile = ['xls', 'xlsx'].includes(fileType.toLowerCase());
        const isWordFile = ['doc', 'docx'].includes(fileType.toLowerCase());
        
        let fileTypeLabel = fileType.toUpperCase();
        let appSuggestions = '';
        
        if (isExcelFile) {
            fileTypeLabel = 'Excel';
            appSuggestions = 'Microsoft Excel, LibreOffice Calc, or Google Sheets';
        } else if (isWordFile) {
            fileTypeLabel = 'Word';
            appSuggestions = 'Microsoft Word, LibreOffice Writer, or Google Docs';
        }
        
        container.innerHTML = `
            <div class="file-preview-error">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <h5>Preview Not Available</h5>
                <p>This ${fileTypeLabel} file cannot be previewed in the browser.</p>
                <p class="text-muted small">Online viewers may not support all file formats or may be blocked by security settings.</p>
                
                <div class="mt-4">
                    <button class="btn btn-success btn-lg" onclick="downloadAttachment(${currentAttachmentId})">
                        <i class="bi bi-download me-2"></i>Download File
                    </button>
                </div>
                
                <div class="mt-3 small text-muted">
                    <p><strong>To view this file:</strong></p>
                    <ul class="list-unstyled">
                        <li>1. Download the file using the button above</li>
                        <li>2. Open with: ${appSuggestions}</li>
                    </ul>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-outline-primary btn-sm" onclick="retryViewer()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Retry Preview
                    </button>
                </div>
            </div>
        `;
    }

    function retryViewer() {
        if (currentAttachmentId) {
            const filename = document.getElementById('fileViewerTitle').textContent;
            const fileType = filename.split('.').pop().toLowerCase();
            viewAttachment(currentAttachmentId, filename, fileType);
        }
    }

    // Download attachment function
    function downloadAttachment(attachmentId) {
        const downloadUrl = `{{ route('attachments.download', '__ATTACHMENT_ID__') }}`.replace('__ATTACHMENT_ID__', attachmentId);
        window.open(downloadUrl, '_blank');
    }

    // Print file function
    document.getElementById('printFileBtn').addEventListener('click', function() {
        if (currentAttachmentId) {
            const iframe = document.querySelector('#fileViewerContainer iframe');
            const img = document.querySelector('#fileViewerContainer img');
            
            if (iframe) {
                try {
                    iframe.contentWindow.print();
                } catch (e) {
                    // If iframe printing fails, open in new window for printing
                    const printWindow = window.open(iframe.src, '_blank');
                    printWindow.onload = function() {
                        printWindow.print();
                    };
                }
            } else if (img) {
                // For images, create a new window with just the image for printing
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print</title>
                            <style>
                                body { margin: 0; padding: 0; }
                                img { max-width: 100%; height: auto; }
                            </style>
                        </head>
                        <body>
                            <img src="${img.src}" alt="${img.alt}" onload="window.print()">
                        </body>
                    </html>
                `);
                printWindow.document.close();
            }
        }
    });

    // Download file from viewer
    document.getElementById('downloadFileBtn').addEventListener('click', function() {
        if (currentAttachmentId) {
            downloadAttachment(currentAttachmentId);
        }
    });
    
    // Upload files function
    function uploadFiles() {
        uploadModal.show();
        // Clear previous selections
        document.getElementById('fileInput').value = '';
        document.getElementById('selectedFiles').innerHTML = '';
        document.getElementById('uploadProgress').classList.add('d-none');
    }
    
    // Display selected files
    function displaySelectedFiles(files) {
        const container = document.getElementById('selectedFiles');
        container.innerHTML = '';
        
        if (files.length > 0) {
            const fileList = document.createElement('div');
            fileList.className = 'border rounded p-2 bg-light';
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = document.createElement('div');
                fileItem.className = 'd-flex justify-content-between align-items-center py-1';
                fileItem.innerHTML = `
                    <span class="small">
                        <i class="bi bi-file-earmark me-1"></i>
                        ${file.name} (${formatFileSize(file.size)})
                    </span>
                `;
                fileList.appendChild(fileItem);
            }
            
            container.appendChild(fileList);
        }
    }
    
    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Handle file upload
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        const fileInput = document.getElementById('fileInput');
        const files = fileInput.files;
        
        if (files.length === 0) {
            showAlert('error', 'Please select at least one file to upload.');
            return;
        }
        
        // Add files to form data
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        
        // Add CSRF token
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Show progress
        const progressContainer = document.getElementById('uploadProgress');
        const progressBar = progressContainer.querySelector('.progress-bar');
        const uploadButton = document.getElementById('uploadButton');
        
        progressContainer.classList.remove('d-none');
        progressBar.style.width = '0%';
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Uploading...';
        
        // Create XMLHttpRequest for progress tracking
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressBar.textContent = Math.round(percentComplete) + '%';
            }
        });
        
        xhr.addEventListener('load', function() {
            try {
                const data = JSON.parse(xhr.responseText);
                
                if (data.success) {
                    // Close modal
                    uploadModal.hide();
                    
                    // Update attachments list
                    updateAttachmentsList(data.files);
                    
                    // Update counter
                    document.getElementById('attachmentCount').textContent = data.total_count || (document.querySelectorAll('.attachment-item').length + data.files.length);
                    
                    // Show success message
                    showAlert('success', 'Files uploaded successfully!');
                } else {
                    showAlert('error', 'Error: ' + data.message);
                }
            } catch (error) {
                console.error('Upload response error:', error);
                showAlert('error', 'An error occurred while uploading files.');
            }
            
            // Reset button
            uploadButton.disabled = false;
            uploadButton.innerHTML = '<i class="bi bi-upload me-1"></i>Upload Files';
            progressContainer.classList.add('d-none');
        });
        
        xhr.addEventListener('error', function() {
            showAlert('error', 'Network error occurred while uploading files.');
            uploadButton.disabled = false;
            uploadButton.innerHTML = '<i class="bi bi-upload me-1"></i>Upload Files';
            progressContainer.classList.add('d-none');
        });
        
        // Use the correct route
        xhr.open('POST', `{{ route('documents.attachment.store', $document) }}`);
        xhr.send(formData);
    });
    
    // Update attachments list
    function updateAttachmentsList(newFiles) {
        const attachmentsList = document.getElementById('attachmentsList');
        const noAttachmentsMessage = document.getElementById('noAttachmentsMessage');
        
        // Hide "no attachments" message if it exists
        if (noAttachmentsMessage) {
            noAttachmentsMessage.style.display = 'none';
        }
        
        // Add new files to the list
        newFiles.forEach(file => {
            const attachmentItem = document.createElement('div');
            attachmentItem.className = 'd-flex align-items-center justify-content-between mb-3 attachment-item';
            attachmentItem.setAttribute('data-attachment-id', file.id);
            
            attachmentItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark-text text-muted me-2" style="font-size: 1.2rem;"></i>
                    <div>
                        <a href="javascript:void(0)" class="text-decoration-none fw-semibold" onclick="viewAttachment(${file.id}, '${file.original_name}', '${file.file_type}')">
                            ${file.original_name}
                        </a>
                        <div class="small text-muted">
                            ${file.file_size} • Just uploaded
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary" onclick="downloadAttachment(${file.id})" title="Download">
                        <i class="bi bi-download"></i>
                    </button>
                    @can('update', $document)
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAttachment(${file.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                    @endcan
                </div>
            `;
            
            attachmentsList.appendChild(attachmentItem);
        });
    }
    
    // Delete attachment function
    function deleteAttachment(attachmentId) {
        if (confirm('Are you sure you want to delete this attachment?')) {
            fetch(`{{ route('documents.attachment.destroy', [$document, '__ATTACHMENT_ID__']) }}`.replace('__ATTACHMENT_ID__', attachmentId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the attachment item from the UI
                    const attachmentItem = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
                    if (attachmentItem) {
                        attachmentItem.remove();
                    }
                    
                    // Update counter
                    document.getElementById('attachmentCount').textContent = data.total_count || 0;
                    
                    // Show "no attachments" message if no attachments left
                    if (data.total_count === 0) {
                        document.getElementById('attachmentsList').innerHTML = `
                            <div class="text-center py-3 text-muted" id="noAttachmentsMessage">
                                <i class="bi bi-paperclip" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No attachments</p>
                                @can('update', $document)
                                <small>Click the Upload button to add files</small>
                                @endcan
                            </div>
                        `;
                    }
                    
                    showAlert('success', 'Attachment deleted successfully.');
                } else {
                    showAlert('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showAlert('error', 'An error occurred while deleting the attachment.');
            });
        }
    }
    
    // Show alert function
    function showAlert(type, message) {
        // Remove any existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Update status
    function updateStatus() {
        statusModal.show();
    }
    
    // Delete document
    function deleteDocument() {
        if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("documents.destroy", $document) }}';
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Handle status update
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Disable button and show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Prepare the request data
        const requestData = {
            status: formData.get('status'),
            remarks: formData.get('remarks'),
            _token: csrfToken,
            _method: 'PATCH'
        };
        
        console.log('Sending status update request:', requestData);
        
        fetch('{{ route("documents.update-status", $document) }}', {
            method: 'POST', // Use POST with _method spoofing
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            console.log('Response received:', response.status, response.statusText);
            
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            
            if (data.success) {
                // Close modal
                statusModal.hide();
                
                // Show success message
                showAlert('success', data.message);
                
                // Update status badge in the UI
                function updateStatusBadge(newStatus, statusColor) {
                    const statusBadge = document.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.className = `status-badge status-${newStatus}`;
                        statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1).replace('_', ' ');
                    }
                }
                
                // Optionally reload page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message || 'An error occurred while updating status.');
            }
        })
        .catch(error => {
            console.error('Status update error:', error);
            showAlert('error', error.message || 'An error occurred while updating status. Please try again.');
        })
        .finally(() => {
            // Re-enable button
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });
    
    // Edit comment function
    function editComment(commentId, currentText) {
        // Hide the comment content and show the edit form
        document.getElementById('comment-content-' + commentId).style.display = 'none';
        document.getElementById('comment-edit-form-' + commentId).style.display = 'block';
        
        // Focus on the textarea
        document.getElementById('edit-comment-' + commentId).focus();
    }

    // Cancel edit function
    function cancelEdit(commentId) {
        // Show the comment content and hide the edit form
        document.getElementById('comment-content-' + commentId).style.display = 'block';
        document.getElementById('comment-edit-form-' + commentId).style.display = 'none';
    }

    // Update comment function
    function updateComment(event, commentId) {
        event.preventDefault();
        
        const textarea = document.getElementById('edit-comment-' + commentId);
        const newComment = textarea.value.trim();
        
        if (!newComment) {
            showAlert('error', 'Comment cannot be empty.');
            return;
        }
        
        fetch(`{{ route('documents.comment.update', [$document, '__COMMENT_ID__']) }}`.replace('__COMMENT_ID__', commentId), {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment: newComment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the comment content
                document.querySelector('#comment-content-' + commentId + ' p').textContent = newComment;
                
                // Hide edit form and show content
                cancelEdit(commentId);
                
                // Show success message
                showAlert('success', 'Comment updated successfully!');
            } else {
                showAlert('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while updating the comment.');
        });
    }

    // Delete comment function
    function deleteComment(commentId) {
        if (confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
            fetch(`{{ route('documents.comment.destroy', [$document, '__COMMENT_ID__']) }}`.replace('__COMMENT_ID__', commentId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the comment from the DOM
                    document.getElementById('comment-' + commentId).remove();
                    
                    // Show success message
                    showAlert('success', 'Comment deleted successfully!');
                } else {
                    showAlert('error', 'Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while deleting the comment.');
            });
        }
    }

    // Update the existing comment form handler
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`{{ route('documents.comment.store', $document) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add the new comment to the list
                const commentsList = document.getElementById('commentsList');
                commentsList.insertAdjacentHTML('afterbegin', data.html);
                
                // Reset the form
                this.reset();
                
                // Show success message
                showAlert('success', 'Comment added successfully!');
            } else {
                showAlert('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while adding the comment.');
        });
    });
</script>
@endpush