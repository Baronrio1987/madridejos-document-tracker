@extends('layouts.app')

@section('title', 'Document Types')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Document Types</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Document Types</h1>
            <p class="text-muted mb-0">View all available document types</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        @forelse($documentTypes as $documentType)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-tags" style="font-size: 1.5rem;"></i>
                        </div>
                        <span class="badge bg-{{ $documentType->is_active ? 'success' : 'secondary' }}">
                            {{ $documentType->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <h5 class="card-title mb-2">{{ $documentType->name }}</h5>
                    <p class="text-muted mb-3">
                        <span class="badge bg-light text-dark me-2">{{ $documentType->code }}</span>
                    </p>
                    
                    @if($documentType->description)
                        <p class="card-text small text-muted mb-3">{{ Str::limit($documentType->description, 100) }}</p>
                    @endif
                    
                    <div class="mb-3">
                        <small class="text-muted">Retention Period:</small>
                        <div class="fw-semibold">{{ $documentType->retention_period }} days</div>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-primary">{{ $documentType->documents_count ?? 0 }}</div>
                                <small class="text-muted">Documents</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-success">{{ $documentType->routing_templates_count ?? 0 }}</div>
                                <small class="text-muted">Templates</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-tags text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No document types found</h5>
                <p class="text-muted">No document types are currently available.</p>
            </div>
        </div>
        @endforelse
    </div>
@endsection