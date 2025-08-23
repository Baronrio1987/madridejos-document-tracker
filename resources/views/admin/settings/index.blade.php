@extends('layouts.app')

@section('title', 'System Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item active">System Settings</li>
@endsection

@section('page-header')
    <div class="row align-items-start">
        <div class="col-12 col-lg-8">
            <h1 class="h3 mb-2 mb-md-0">System Settings</h1>
            <p class="text-muted mb-3 mb-md-0">Configure system parameters and preferences</p>
        </div>
        <div class="col-12 col-lg-4">
            <!-- Mobile-first button layout -->
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                <div class="btn-group flex-fill flex-sm-auto" role="group">
                    <button type="button" class="btn btn-outline-info" onclick="loadDefaultSettings()" data-bs-toggle="tooltip" title="Load default system settings">
                        <i class="bi bi-download me-1 d-sm-none"></i>
                        <i class="bi bi-download me-2 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">Load Defaults</span>
                        <span class="d-sm-none">Defaults</span>
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="resetAllSettings()" data-bs-toggle="tooltip" title="Reset all settings to defaults">
                        <i class="bi bi-arrow-clockwise me-1 d-sm-none"></i>
                        <i class="bi bi-arrow-clockwise me-2 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">Reset All</span>
                        <span class="d-sm-none">Reset</span>
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary flex-fill flex-sm-auto" onclick="generateCss()" data-bs-toggle="tooltip" title="Apply current theme settings">
                        <i class="bi bi-palette me-1 d-sm-none"></i>
                        <i class="bi bi-palette me-2 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">Apply Theme</span>
                        <span class="d-sm-none">Theme</span>
                    </button>
                    <a href="{{ route('admin.settings.create') }}" class="btn btn-primary flex-fill flex-sm-auto">
                        <i class="bi bi-plus-circle me-1 d-sm-none"></i>
                        <i class="bi bi-plus-circle me-2 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">Add Setting</span>
                        <span class="d-sm-none">Add</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @if(empty($settings))
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4 py-md-5">
                <i class="bi bi-gear text-muted mb-3" style="font-size: 2.5rem;"></i>
                <h5 class="text-muted mb-3">No Settings Found</h5>
                <p class="text-muted mb-4 px-2">It looks like no system settings have been configured yet.</p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 gap-sm-3">
                    <button type="button" class="btn btn-primary" onclick="seedSettings()">
                        <i class="bi bi-download me-2"></i>Load Default Settings
                    </button>
                    <a href="{{ route('admin.settings.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add First Setting
                    </a>
                </div>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @foreach($settings as $group => $groupSettings)
                <div class="card border-0 shadow-sm mb-3 mb-md-4">
                    <!-- Mobile-optimized card header -->
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-{{ $group == 'general' ? 'gear' : ($group == 'notification' ? 'bell' : ($group == 'security' ? 'shield-lock' : ($group == 'document' ? 'file-earmark' : ($group == 'appearance' ? 'palette' : ($group == 'theme' ? 'brush' : 'building'))))) }} me-2 text-primary"></i>
                            <h5 class="mb-0 flex-grow-1">{{ ucfirst($group) }} Settings</h5>
                            <!-- Collapsible toggle for mobile -->
                            <button class="btn btn-sm btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ ucfirst($group) }}" aria-expanded="true" aria-controls="collapse{{ ucfirst($group) }}">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Collapsible card body for mobile -->
                    <div class="collapse show" id="collapse{{ ucfirst($group) }}">
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($groupSettings as $setting)
                                    <div class="col-12 col-lg-6">
                                        <div class="setting-item">
                                            <label for="setting_{{ $setting->key }}" class="form-label d-flex align-items-center">
                                                <span class="flex-grow-1">{{ ucwords(str_replace(['.', '_'], ' ', $setting->key)) }}</span>
                                                @if($setting->description)
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $setting->description }}">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                @endif
                                            </label>
                                            
                                            @if($setting->type === 'boolean')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="setting_{{ $setting->key }}" 
                                                           name="settings[{{ $setting->key }}]" 
                                                           value="1" {{ $setting->getProcessedValue() ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="setting_{{ $setting->key }}">
                                                        <span class="status-text">{{ $setting->getProcessedValue() ? 'Enabled' : 'Disabled' }}</span>
                                                    </label>
                                                </div>
                                            @elseif($setting->type === 'color')
                                                <div class="color-input-group">
                                                    <div class="input-group">
                                                        <input type="color" class="form-control form-control-color color-picker" 
                                                               id="setting_{{ $setting->key }}" 
                                                               name="settings[{{ $setting->key }}]" 
                                                               value="{{ $setting->getProcessedValue() }}">
                                                        <input type="text" class="form-control color-display" 
                                                               value="{{ $setting->getProcessedValue() }}" readonly>
                                                        <button class="btn btn-outline-secondary" type="button" onclick="copyColorValue(this)">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif($setting->type === 'file')
                                                <div class="file-upload-container">
                                                    @if($setting->getProcessedValue())
                                                        <div class="current-file mb-3">
                                                            <div class="card border-0 bg-light">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-center">
                                                                        @if(in_array(pathinfo($setting->getProcessedValue(), PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                                            <img src="{{ $setting->getProcessedValue() }}" alt="Current" class="img-thumbnail me-3" style="max-height: 60px; max-width: 60px;">
                                                                        @else
                                                                            <i class="bi bi-file-earmark text-primary me-3" style="font-size: 2rem;"></i>
                                                                        @endif
                                                                        <div class="flex-grow-1">
                                                                            <small class="text-muted">Current file:</small>
                                                                            <div class="fw-medium">{{ basename($setting->getProcessedValue()) }}</div>
                                                                        </div>
                                                                        <a href="{{ $setting->getProcessedValue() }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                            <i class="bi bi-eye"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control" 
                                                           id="setting_{{ $setting->key }}" 
                                                           name="files[{{ $setting->key }}]"
                                                           accept="image/*,.pdf,.doc,.docx"
                                                           onchange="previewFile(this)">
                                                    <div class="file-preview mt-2" style="display: none;"></div>
                                                    <small class="text-muted d-block mt-1">Choose a new file to replace the current one</small>
                                                </div>
                                            @elseif($setting->type === 'json')
                                                <div class="json-input-container">
                                                    <textarea class="form-control font-monospace" 
                                                              id="setting_{{ $setting->key }}" 
                                                              name="settings[{{ $setting->key }}]" 
                                                              rows="4">{{ is_array($setting->getProcessedValue()) ? json_encode($setting->getProcessedValue(), JSON_PRETTY_PRINT) : $setting->raw_value }}</textarea>
                                                    <div class="d-flex gap-2 mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatJson(this)">
                                                            <i class="bi bi-code"></i> Format
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="validateJson(this)">
                                                            <i class="bi bi-check-circle"></i> Validate
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif($setting->type === 'integer' || $setting->type === 'float')
                                                <input type="number" class="form-control" 
                                                       id="setting_{{ $setting->key }}" 
                                                       name="settings[{{ $setting->key }}]" 
                                                       value="{{ $setting->getProcessedValue() }}"
                                                       {{ $setting->type === 'float' ? 'step=0.01' : '' }}>
                                            @else
                                                <input type="text" class="form-control" 
                                                       id="setting_{{ $setting->key }}" 
                                                       name="settings[{{ $setting->key }}]" 
                                                       value="{{ $setting->getProcessedValue() }}">
                                            @endif
                                            
                                            @if($setting->description)
                                                <small class="text-muted d-none d-md-block mt-1">{{ $setting->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Sticky save button for mobile -->
            <div class="settings-save-container">
                <div class="d-grid d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary btn-lg" id="saveSettingsBtn">
                        <i class="bi bi-check-circle me-2"></i>Save Settings
                    </button>
                </div>
            </div>
        </form>
    @endif
@endsection

@push('styles')
<style>
    /* Mobile-specific styles for settings page */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .setting-item {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .setting-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .form-label {
            font-size: 0.95rem;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            min-height: 48px;
            font-size: 16px; /* Prevent zoom on iOS */
        }
        
        .btn {
            min-height: 44px;
        }
        
        /* Color picker mobile optimization */
        .color-input-group .form-control-color {
            width: 60px;
            height: 48px;
            padding: 0.25rem;
        }
        
        .color-display {
            font-family: monospace;
            font-size: 0.875rem;
        }
        
        /* File upload mobile styling */
        .current-file .card {
            border-radius: 0.5rem;
        }
        
        .file-preview {
            max-width: 100%;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        
        .file-preview img {
            max-width: 100%;
            height: auto;
        }
        
        /* JSON editor mobile styling */
        .json-input-container textarea {
            font-size: 0.875rem;
            line-height: 1.4;
        }
        
        /* Form switch mobile styling */
        .form-check-input {
            width: 2.5rem;
            height: 1.25rem;
        }
        
        .status-text {
            font-weight: 500;
            margin-left: 0.5rem;
        }
        
        /* Button group responsive */
        .btn-group .btn {
            border-radius: 0.375rem !important;
            margin-bottom: 0.5rem;
        }
        
        /* Sticky save button */
        .settings-save-container {
            position: sticky;
            bottom: 1rem;
            z-index: 10;
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    }
    
    /* Desktop styles */
    @media (min-width: 769px) {
        .settings-save-container {
            margin-top: 2rem;
        }
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
    }
    
    .loading-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        max-width: 90%;
        width: 300px;
    }
    
    /* Toast notifications for mobile */
    .toast-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1100;
    }
    
    @media (max-width: 576px) {
        .toast-container {
            top: 0.5rem;
            right: 0.5rem;
            left: 0.5rem;
        }
        
        .toast {
            width: 100%;
        }
    }
    
    /* Form validation feedback */
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .invalid-feedback {
        display: block;
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 0.25rem;
    }
    
    /* Animation for collapsible sections */
    .collapse {
        transition: all 0.3s ease;
    }
    
    /* Better touch targets */
    .btn-sm {
        min-height: 36px;
        min-width: 36px;
    }
    
    /* Responsive tooltips */
    @media (max-width: 768px) {
        .tooltip {
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Enhanced mobile-first JavaScript for settings page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Form change detection
        let formChanged = false;
        const formInputs = document.querySelectorAll('#settingsForm input, #settingsForm textarea, #settingsForm select');
        
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                formChanged = true;
                updateSaveButtonState();
            });
            
            // Real-time validation for text inputs
            if (input.type === 'text' || input.type === 'number') {
                input.addEventListener('input', function() {
                    if (this.checkValidity()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            }
        });
        
        // Update save button state
        function updateSaveButtonState() {
            const saveBtn = document.getElementById('saveSettingsBtn');
            if (formChanged) {
                saveBtn.classList.add('btn-success');
                saveBtn.classList.remove('btn-primary');
                saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Save Changes';
            }
        }
        
        // Color picker synchronization
        document.querySelectorAll('input[type="color"]').forEach(colorInput => {
            const textInput = colorInput.nextElementSibling;
            
            colorInput.addEventListener('input', function() {
                textInput.value = this.value;
                formChanged = true;
                updateSaveButtonState();
            });
            
            textInput.addEventListener('input', function() {
                if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                    colorInput.value = this.value;
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                }
            });
        });
        
        // Boolean switch text update
        document.querySelectorAll('.form-check-input[type="checkbox"]').forEach(checkbox => {
            const statusText = checkbox.parentElement.querySelector('.status-text');
            if (statusText) {
                checkbox.addEventListener('change', function() {
                    statusText.textContent = this.checked ? 'Enabled' : 'Disabled';
                });
            }
        });
        
        // Form submission with loading state
        document.getElementById('settingsForm')?.addEventListener('submit', function() {
            const saveBtn = document.getElementById('saveSettingsBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            
            // Show loading overlay
            showLoadingState('Saving settings...');
        });
        
        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });
        
        // Mobile-specific enhancements
        if (window.innerWidth <= 768) {
            // Auto-collapse sections except the first one
            const collapses = document.querySelectorAll('.collapse');
            collapses.forEach((collapse, index) => {
                if (index > 0) {
                    const bsCollapse = new bootstrap.Collapse(collapse, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            });
            
            // Update collapse button icons
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    setTimeout(() => {
                        const target = document.querySelector(this.getAttribute('data-bs-target'));
                        if (target.classList.contains('show')) {
                            icon.className = 'bi bi-chevron-up';
                        } else {
                            icon.className = 'bi bi-chevron-down';
                        }
                    }, 350);
                });
            });
        }
        
        // Add touch feedback for mobile
        if ('ontouchstart' in window) {
            document.querySelectorAll('.btn, .form-control, .form-check-input').forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                }, { passive: true });
                
                element.addEventListener('touchend', function() {
                    this.style.transform = '';
                }, { passive: true });
            });
        }
    });
    
    // Utility functions
    function copyColorValue(button) {
        const colorDisplay = button.previousElementSibling;
        const colorValue = colorDisplay.value;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(colorValue).then(() => {
                showToast('Color value copied to clipboard!', 'success');
            });
        } else {
            // Fallback for older browsers
            colorDisplay.select();
            document.execCommand('copy');
            showToast('Color value copied to clipboard!', 'success');
        }
    }
    
    function previewFile(input) {
        const file = input.files[0];
        const preview = input.parentElement.querySelector('.file-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px;">`;
                } else {
                    preview.innerHTML = `<div class="alert alert-info"><i class="bi bi-file-earmark me-2"></i>${file.name}</div>`;
                }
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }
    
    function formatJson(button) {
        const textarea = button.closest('.json-input-container').querySelector('textarea');
        try {
            const json = JSON.parse(textarea.value);
            textarea.value = JSON.stringify(json, null, 2);
            textarea.classList.remove('is-invalid');
            showToast('JSON formatted successfully!', 'success');
        } catch (e) {
            textarea.classList.add('is-invalid');
            showToast('Invalid JSON format!', 'danger');
        }
    }
    
    function validateJson(button) {
        const textarea = button.closest('.json-input-container').querySelector('textarea');
        try {
            JSON.parse(textarea.value);
            textarea.classList.remove('is-invalid');
            showToast('JSON is valid!', 'success');
        } catch (e) {
            textarea.classList.add('is-invalid');
            showToast('Invalid JSON: ' + e.message, 'danger');
        }
    }
    
    function generateCss() {
        showLoadingState('Generating CSS...');
        
        fetch('{{ route("admin.settings.generate-css") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.text())
        .then(css => {
            hideLoadingState();
            let styleElement = document.getElementById('dynamic-styles');
            if (!styleElement) {
                styleElement = document.createElement('style');
                styleElement.id = 'dynamic-styles';
                document.head.appendChild(styleElement);
            }
            styleElement.textContent = css;
            
            showToast('Theme applied successfully! Changes will be visible on page refresh.', 'success');
        })
        .catch(error => {
            hideLoadingState();
            console.error('Error:', error);
            showToast('Error applying theme', 'danger');
        });
    }
    
    function loadDefaultSettings() {
        if (confirm('This will load default system settings. Any existing settings with the same keys will be overwritten. Continue?')) {
            showLoadingState('Loading default settings...');
            
            fetch('{{ route("admin.settings.load-defaults") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingState();
                if (data.success) {
                    showToast(`Default settings loaded successfully! (${data.count} settings added/updated)`, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showToast(data.message || 'Error loading default settings', 'danger');
                }
            })
            .catch(error => {
                hideLoadingState();
                console.error('Error:', error);
                showToast('Error loading default settings', 'danger');
            });
        }
    }
    
    function resetAllSettings() {
        if (confirm('WARNING: This will delete ALL custom settings and load only the default settings. This action cannot be undone. Continue?')) {
            showLoadingState('Resetting all settings...');
            
            fetch('{{ route("admin.settings.reset-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingState();
                if (data.success) {
                    showToast('All settings have been reset to defaults!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showToast(data.message || 'Error resetting settings', 'danger');
                }
            })
            .catch(error => {
                hideLoadingState();
                console.error('Error:', error);
                showToast('Error resetting settings', 'danger');
            });
        }
    }
    
    function seedSettings() {
        loadDefaultSettings();
    }
    
    function showLoadingState(message) {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-card">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mb-2">${message}</h5>
                <p class="text-muted mb-0">Please wait...</p>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    
    function hideLoadingState() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    }
    
    function showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        container.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: type === 'danger' ? 8000 : 5000
        });
        bsToast.show();
        
        // Remove toast element after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
</script>
@endpush