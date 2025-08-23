@extends('layouts.app')

@section('title', $documentType->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.document-types.index') }}">Document Types</a></li>
    <li class="breadcrumb-item active">{{ $documentType->name }}</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">{{ $documentType->name }}</h1>
            <p class="text-muted mb-0">Document Type Details</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.document-types.edit', $documentType) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Document Type Information -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Type Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Name</label>
                            <p class="fw-semibold">{{ $documentType->name }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Code</label>
                            <p><span class="badge bg-secondary">{{ $documentType->code }}</span></p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <p>
                                <span class="badge bg-{{ $documentType->is_active ? 'success' : 'danger' }}">
                                    {{ $documentType->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Retention Period</label>
                            <p class="fw-semibold">{{ $documentType->retention_period }} days</p>
                        </div>
                        
                        @if($documentType->description)
                        <div class="col-12">
                            <label class="form-label text-muted">Description</label>
                            <p>{{ $documentType->description }}</p>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Created Date</label>
                            <p>{{ $documentType->created_at->format('F d, Y') }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted">Last Updated</label>
                            <p>{{ $documentType->updated_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 mb-0 text-primary">{{ $stats['total_documents'] }}</div>
                                <small class="text-muted">Total Documents</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 mb-0 text-warning">{{ $stats['pending_documents'] }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 mb-0 text-success">{{ $stats['completed_documents'] }}</div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="h4 mb-0 text-info">{{ $stats['routing_templates'] }}</div>
                                <small class="text-muted">Templates</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection