@extends('layouts.app')

@section('title', 'QR Code Generator')

@section('breadcrumbs')
    <li class="breadcrumb-item">QR Codes</li>
    @if($document)
        <li class="breadcrumb-item"><a href="{{ route('documents.show', $document) }}">{{ $document->tracking_number }}</a></li>
        <li class="breadcrumb-item active">Generate QR Code</li>
    @else
        <li class="breadcrumb-item active">Generator</li>
    @endif
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">
                @if($document)
                    Generate QR Code for {{ $document->tracking_number }}
                @else
                    QR Code Generator
                @endif
            </h1>
            <p class="text-muted mb-0">
                @if($document)
                    Create a QR code for quick document tracking access
                @else
                    Generate QR codes for document tracking
                @endif
            </p>
        </div>
        @if(!$document)
        <div class="col-auto">
            <button class="btn btn-primary" onclick="showBulkGenerator()">
                <i class="bi bi-qr-code me-2"></i>Bulk Generate
            </button>
        </div>
        @endif
    </div>
@endsection

@section('content')
    @if($document)
        {{-- Single Document QR Generator --}}
        <div class="row">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">QR Code Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="qrGeneratorForm">
                            @csrf
                            <div class="mb-3">
                                <label for="label" class="form-label">Label Text</label>
                                <input type="text" class="form-control" id="label" name="label" 
                                       value="{{ $document->tracking_number }}" 
                                       placeholder="Enter label text">
                                <small class="text-muted">Text to display below the QR code</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="size" class="form-label">Size</label>
                                <select class="form-select" id="size" name="size">
                                    <option value="200">Small (200x200)</option>
                                    <option value="300" selected>Medium (300x300)</option>
                                    <option value="400">Large (400x400)</option>
                                    <option value="500">Extra Large (500x500)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Preview URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           value="{{ route('public.track.show', $document->tracking_number) }}" 
                                           readonly>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="copyToClipboard('{{ route('public.track.show', $document->tracking_number) }}')">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                                <small class="text-muted">This URL will be encoded in the QR code</small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-qr-code me-2"></i>Generate QR Code
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="downloadBtn" style="display: none;">
                                    <i class="bi bi-download me-2"></i>Download QR Code
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                {{-- Document Info --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Document Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 small">
                            <div class="col-4 text-muted">Title:</div>
                            <div class="col-8">{{ $document->title }}</div>
                            
                            <div class="col-4 text-muted">Type:</div>
                            <div class="col-8">{{ $document->documentType->name }}</div>
                            
                            <div class="col-4 text-muted">Status:</div>
                            <div class="col-8">
                                <span class="status-badge status-{{ $document->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                </span>
                            </div>
                            
                            <div class="col-4 text-muted">Department:</div>
                            <div class="col-8">{{ $document->currentDepartment->name }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">QR Code Preview</h5>
                    </div>
                    <div class="card-body text-center">
                        <div id="qrPreview" class="mb-3">
                            <div class="qr-placeholder">
                                <i class="bi bi-qr-code" style="font-size: 4rem; color: #dee2e6;"></i>
                                <p class="text-muted mt-2">QR Code will appear here</p>
                            </div>
                        </div>
                        
                        <div id="qrInfo" style="display: none;">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-2">Usage Instructions</h6>
                                <ul class="list-unstyled mb-0 small text-start">
                                    <li><i class="bi bi-check text-success me-2"></i>Scan with any QR code reader</li>
                                    <li><i class="bi bi-check text-success me-2"></i>Automatically opens tracking page</li>
                                    <li><i class="bi bi-check text-success me-2"></i>No login required for tracking</li>
                                    <li><i class="bi bi-check text-success me-2"></i>Works on mobile devices</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Bulk QR Generator --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Select Documents for QR Generation</h5>
            </div>
            <div class="card-body">
                @if($documents && $documents->count() > 0)
                    <form id="bulkQrForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bulk_size" class="form-label">QR Code Size</label>
                                <select class="form-select" id="bulk_size" name="size">
                                    <option value="200">Small (200x200)</option>
                                    <option value="300" selected>Medium (300x300)</option>
                                    <option value="400">Large (400x400)</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        Select All Documents
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="headerCheckbox" class="form-check-input">
                                        </th>
                                        <th>Tracking Number</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>QR Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $doc)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}" 
                                                   class="form-check-input document-checkbox">
                                        </td>
                                        <td>
                                            <a href="{{ route('documents.show', $doc) }}" class="text-decoration-none">
                                                {{ $doc->tracking_number }}
                                            </a>
                                        </td>
                                        <td>{{ Str::limit($doc->title, 40) }}</td>
                                        <td class="text-muted">{{ $doc->documentType->name }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $doc->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($doc->hasQrCode())
                                                <span class="badge bg-success">Generated</span>
                                            @else
                                                <span class="badge bg-secondary">Not Generated</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span id="selectedCount">0</span> documents selected
                            </div>
                            <button type="submit" class="btn btn-primary" id="bulkGenerateBtn" disabled>
                                <i class="bi bi-qr-code me-2"></i>Generate QR Codes
                            </button>
                        </div>
                    </form>
                    
                    {{ $documents->links() }}
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No documents found</h5>
                        <p class="text-muted">Create some documents first to generate QR codes.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Progress Modal -->
    <div class="modal fade" id="progressModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generating QR Codes</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p id="progressText">Please wait while we generate your QR codes...</p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%" id="progressBar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .qr-placeholder {
        padding: 2rem;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    
    .qr-code-container {
        display: inline-block;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-in_progress { background-color: #cce7ff; color: #0f5132; }
    .status-completed { background-color: #d1e7dd; color: #0f5132; }
    .status-cancelled { background-color: #f8d7da; color: #721c24; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($document)
        initSingleQrGenerator();
    @else
        initBulkQrGenerator();
    @endif
});

function initSingleQrGenerator() {
    const form = document.getElementById('qrGeneratorForm');
    const downloadBtn = document.getElementById('downloadBtn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        generateQrCode();
    });
}

function initBulkQrGenerator() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const documentCheckboxes = document.querySelectorAll('.document-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const bulkGenerateBtn = document.getElementById('bulkGenerateBtn');
    
    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        documentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });
    
    headerCheckbox.addEventListener('change', function() {
        documentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });
    
    // Individual checkbox changes
    documentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.document-checkbox:checked').length;
        selectedCount.textContent = checked;
        bulkGenerateBtn.disabled = checked === 0;
        
        // Update select all checkbox state
        const total = documentCheckboxes.length;
        selectAllCheckbox.checked = checked === total;
        selectAllCheckbox.indeterminate = checked > 0 && checked < total;
        headerCheckbox.checked = checked === total;
        headerCheckbox.indeterminate = checked > 0 && checked < total;
    }
    
    // Bulk form submission
    document.getElementById('bulkQrForm').addEventListener('submit', function(e) {
        e.preventDefault();
        generateBulkQrCodes();
    });
}

function generateQrCode() {
    const form = document.getElementById('qrGeneratorForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const downloadBtn = document.getElementById('downloadBtn');
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating...';
    
    fetch('{{ $document ? route("qr-codes.generate", $document) : "" }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayQrCode(data.qr_url);
            setupDownloadButton(data.qr_url);
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while generating the QR code.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-qr-code me-2"></i>Generate QR Code';
    });
}

function generateBulkQrCodes() {
    const form = document.getElementById('bulkQrForm');
    const formData = new FormData(form);
    const modal = new bootstrap.Modal(document.getElementById('progressModal'));
    
    modal.show();
    
    fetch('{{ route("qr-codes.bulk-generate") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Network response was not ok');
    })
    .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'qr_codes_' + new Date().toISOString().slice(0,10) + '.zip';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        
        modal.hide();
        showAlert('success', 'QR codes generated and downloaded successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        modal.hide();
        showAlert('error', 'An error occurred while generating QR codes.');
    });
}

function displayQrCode(qrUrl) {
    const preview = document.getElementById('qrPreview');
    const info = document.getElementById('qrInfo');
    
    preview.innerHTML = `
        <div class="qr-code-container">
            <img src="${qrUrl}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
        </div>
    `;
    
    info.style.display = 'block';
}

function setupDownloadButton(qrUrl) {
    const downloadBtn = document.getElementById('downloadBtn');
    downloadBtn.style.display = 'block';
    
    downloadBtn.onclick = function() {
        const link = document.createElement('a');
        link.href = '{{ $document ? route("qr-codes.download", $document) : "" }}?' + new URLSearchParams({
            size: document.getElementById('size').value,
            label: document.getElementById('label').value
        });
        link.download = 'QR_{{ $document ? $document->tracking_number : "" }}.png';
        link.click();
    };
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showAlert('success', 'URL copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        showAlert('error', 'Failed to copy URL to clipboard.');
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush