@extends('layouts.app')

@section('title', 'Create Document Type')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.document-types.index') }}">Document Types</a></li>
    <li class="breadcrumb-item active">Create Document Type</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Create New Document Type</h1>
            <p class="text-muted mb-0">Add a new document type to the system</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Document Types
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Type Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.document-types.store') }}" id="documentTypeForm">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Document Type Name -->
                            <div class="col-md-8">
                                <label for="name" class="form-label">Document Type Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Enter document type name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">e.g., Memorandum, Ordinance, Purchase Request</small>
                            </div>
                            
                            <!-- Document Type Code -->
                            <div class="col-md-4">
                                <label for="code" class="form-label">Type Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="e.g., MEMO" required maxlength="20" style="text-transform: uppercase;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique abbreviation</small>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Enter document type description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Provide a clear description of what this document type is used for</small>
                            </div>
                            
                            <!-- Retention Period -->
                            <div class="col-md-6">
                                <label for="retention_period" class="form-label">Retention Period <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('retention_period') is-invalid @enderror" 
                                           id="retention_period" name="retention_period" 
                                           value="{{ old('retention_period', 365) }}" 
                                           min="1" max="9999" required>
                                    <span class="input-group-text">days</span>
                                </div>
                                @error('retention_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How long documents of this type should be kept</small>
                            </div>
                            
                            <!-- Quick Retention Presets -->
                            <div class="col-md-6">
                                <label class="form-label">Quick Presets</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setRetention(365)">1 Year</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setRetention(730)">2 Years</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setRetention(1095)">3 Years</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setRetention(1825)">5 Years</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setRetention(3650)">10 Years</button>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Document Type
                                    </label>
                                </div>
                                <small class="text-muted">Inactive types cannot be used for new documents</small>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Create Document Type
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar with guidelines -->
        <div class="col-xl-4">
            <!-- Guidelines -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use descriptive, official names
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Keep codes short and memorable
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Set appropriate retention periods
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Include detailed descriptions
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Follow municipal standards
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Retention Period Guide -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Retention Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Routine Documents</span>
                            <span class="badge bg-light text-dark">1-2 years</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Financial Records</span>
                            <span class="badge bg-light text-dark">3-5 years</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Legal Documents</span>
                            <span class="badge bg-light text-dark">5-10 years</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Ordinances/Resolutions</span>
                            <span class="badge bg-light text-dark">Permanent</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span>Historical Records</span>
                            <span class="badge bg-light text-dark">Permanent</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Existing Document Types -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-tags me-2"></i>Existing Document Types</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        @forelse(\App\Models\DocumentType::active()->limit(8)->get() as $type)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $type->name }}</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark">{{ $type->code }}</span>
                                <small class="text-muted">{{ $type->retention_period }}d</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted mb-0">No document types yet</p>
                        @endforelse
                        
                        @if(\App\Models\DocumentType::active()->count() > 8)
                        <small class="text-muted">...and {{ \App\Models\DocumentType::active()->count() - 8 }} more</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-generate code from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const codeField = document.getElementById('code');
        
        if (!codeField.value || codeField.dataset.autoGenerated) {
            // Generate code from name (first letters or abbreviation)
            let code = '';
            
            // Common abbreviations for document types
            const abbreviations = {
                'memorandum': 'MEMO',
                'ordinance': 'ORD',
                'resolution': 'RES',
                'letter': 'LTR',
                'purchase request': 'PR',
                'purchase order': 'PO',
                'voucher': 'VOUCH',
                'report': 'RPT',
                'application': 'APP',
                'notice': 'NOT',
                'contract': 'CON',
                'permit': 'PER',
                'certificate': 'CERT',
                'endorsement': 'END',
                'authorization': 'AUTH'
            };
            
            const lowerName = name.toLowerCase();
            
            // Check for known abbreviations first
            for (const [word, abbrev] of Object.entries(abbreviations)) {
                if (lowerName.includes(word)) {
                    code = abbrev;
                    break;
                }
            }
            
            // If no known abbreviation, generate from first letters
            if (!code) {
                const words = name.split(' ');
                words.forEach(word => {
                    if (word.length > 0) {
                        code += word.charAt(0).toUpperCase();
                    }
                });
            }
            
            // Limit to 20 characters
            code = code.substring(0, 20);
            codeField.value = code;
            codeField.dataset.autoGenerated = 'true';
        }
    });
    
    // Remove auto-generated flag when user manually edits code
    document.getElementById('code').addEventListener('input', function() {
        delete this.dataset.autoGenerated;
        this.value = this.value.toUpperCase();
    });
    
    // Set retention period presets
    function setRetention(days) {
        document.getElementById('retention_period').value = days;
    }
    
    // Form validation
    document.getElementById('documentTypeForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const code = document.getElementById('code').value.trim();
        const retention = parseInt(document.getElementById('retention_period').value);
        
        if (name.length < 3) {
            e.preventDefault();
            alert('Document type name must be at least 3 characters long.');
            return false;
        }
        
        if (code.length < 2) {
            e.preventDefault();
            alert('Document type code must be at least 2 characters long.');
            return false;
        }
        
        if (retention < 1 || retention > 9999) {
            e.preventDefault();
            alert('Retention period must be between 1 and 9999 days.');
            return false;
        }
    });
    
    // Auto-suggest retention periods based on document type
    document.getElementById('name').addEventListener('blur', function() {
        const name = this.value.toLowerCase();
        const retentionField = document.getElementById('retention_period');
        
        if (!retentionField.value || retentionField.value == 365) {
            let suggestedRetention = 365; // Default 1 year
            
            // Suggest retention periods based on document type
            if (name.includes('ordinance') || name.includes('resolution') || name.includes('contract')) {
                suggestedRetention = 3650; // 10 years for legal documents
            } else if (name.includes('financial') || name.includes('voucher') || name.includes('purchase')) {
                suggestedRetention = 1825; // 5 years for financial documents
            } else if (name.includes('permit') || name.includes('license') || name.includes('certificate')) {
                suggestedRetention = 1095; // 3 years for permits
            } else if (name.includes('memo') || name.includes('notice') || name.includes('letter')) {
                suggestedRetention = 730; // 2 years for correspondence
            }
            
            retentionField.value = suggestedRetention;
        }
    });
    
    // Retention period converter
    document.getElementById('retention_period').addEventListener('input', function() {
        const days = parseInt(this.value);
        let helpText = '';
        
        if (days >= 3650) {
            helpText = `${Math.round(days / 365)} years`;
        } else if (days >= 365) {
            helpText = `${Math.round(days / 365 * 10) / 10} years`;
        } else if (days >= 30) {
            helpText = `${Math.round(days / 30)} months`;
        } else {
            helpText = `${days} days`;
        }
        
        // Update help text if exists
        const helpElement = this.parentNode.nextElementSibling;
        if (helpElement && helpElement.classList.contains('text-muted')) {
            helpElement.textContent = `Approximately ${helpText}`;
        }
    });
</script>
@endpush