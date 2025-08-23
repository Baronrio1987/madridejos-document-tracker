@extends('layouts.app')

@section('title', 'Create Setting')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">Create Setting</li>
@endsection

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">Create New Setting</h1>
            <p class="text-muted mb-0">Add a new system configuration setting</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Settings
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Setting Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="key" class="form-label">Setting Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('key') is-invalid @enderror" 
                                       id="key" name="key" value="{{ old('key') }}" 
                                       placeholder="e.g., theme.primary_color" required>
                                @error('key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Use dot notation for grouping (e.g., group.setting_name)</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="type" class="form-label">Data Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="string" {{ old('type') == 'string' ? 'selected' : '' }}>String</option>
                                    <option value="integer" {{ old('type') == 'integer' ? 'selected' : '' }}>Integer</option>
                                    <option value="float" {{ old('type') == 'float' ? 'selected' : '' }}>Float</option>
                                    <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
                                    <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Color</option>
                                    <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>File</option>
                                    <option value="json" {{ old('type') == 'json' ? 'selected' : '' }}>JSON</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="group" class="form-label">Group <span class="text-danger">*</span></label>
                                <select class="form-select @error('group') is-invalid @enderror" 
                                        id="group" name="group" required>
                                    <option value="">Select Group</option>
                                    <option value="general" {{ old('group') == 'general' ? 'selected' : '' }}>General</option>
                                    <option value="appearance" {{ old('group') == 'appearance' ? 'selected' : '' }}>Appearance</option>
                                    <option value="theme" {{ old('group') == 'theme' ? 'selected' : '' }}>Theme</option>
                                    <option value="document" {{ old('group') == 'document' ? 'selected' : '' }}>Document</option>
                                    <option value="notification" {{ old('group') == 'notification' ? 'selected' : '' }}>Notification</option>
                                    <option value="security" {{ old('group') == 'security' ? 'selected' : '' }}>Security</option>
                                    <option value="municipality" {{ old('group') == 'municipality' ? 'selected' : '' }}>Municipality</option>
                                </select>
                                @error('group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_public" name="is_public" value="1"
                                           {{ old('is_public') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        Public Setting
                                    </label>
                                </div>
                                <small class="text-muted">Public settings can be viewed by non-admin users</small>
                            </div>
                            
                            <div class="col-12" id="value-container">
                                <!-- This will be populated by JavaScript -->
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Describe what this setting controls">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Create Setting
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Examples</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <strong>Color Setting:</strong>
                            <div class="text-muted">Key: theme.primary_color</div>
                            <div class="text-muted">Type: color</div>
                            <div class="text-muted">Value: #1e40af</div>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Logo Setting:</strong>
                            <div class="text-muted">Key: appearance.logo</div>
                            <div class="text-muted">Type: file</div>
                            <div class="text-muted">Value: Upload image file</div>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Font Setting:</strong>
                            <div class="text-muted">Key: theme.font_family</div>
                            <div class="text-muted">Type: string</div>
                            <div class="text-muted">Value: "Inter, sans-serif"</div>
                        </div>
                        
                        <div class="mb-0">
                            <strong>Background Opacity:</strong>
                            <div class="text-muted">Key: appearance.background_opacity</div>
                            <div class="text-muted">Type: float</div>
                            <div class="text-muted">Value: 0.1</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('key').addEventListener('input', function() {
        const key = this.value;
        const groupField = document.getElementById('group');
        
        if (key.includes('.') && !groupField.value) {
            const parts = key.split('.');
            groupField.value = parts[0];
        }
    });
    
    document.getElementById('type').addEventListener('change', function() {
        updateValueField(this.value);
    });
    
    function updateValueField(type) {
        const container = document.getElementById('value-container');
        let html = '';
        
        switch(type) {
            case 'boolean':
                html = `
                    <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                    <select class="form-control" id="value" name="value" required>
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                `;
                break;
            case 'color':
                html = `
                    <label for="value" class="form-label">Color Value <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color" id="value" name="value" value="#1e40af" required>
                        <input type="text" class="form-control" value="#1e40af" readonly>
                    </div>
                `;
                break;
            case 'file':
                html = `
                    <label for="file_value" class="form-label">File Upload <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="file_value" name="file_value" required>
                    <small class="text-muted">Supported formats: Images, PDF, DOC, DOCX</small>
                `;
                break;
            case 'json':
                html = `
                    <label for="value" class="form-label">JSON Value <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="value" name="value" rows="4" 
                              placeholder='{"key": "value"}' required></textarea>
                `;
                break;
            case 'integer':
                html = `
                    <label for="value" class="form-label">Integer Value <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="value" name="value" placeholder="123" required>
                `;
                break;
            case 'float':
                html = `
                    <label for="value" class="form-label">Float Value <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="value" name="value" placeholder="1.5" required>
                `;
                break;
            default:
                html = `
                    <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="value" name="value" placeholder="Enter setting value" required>
                `;
        }
        
        container.innerHTML = html;
        
        if (type === 'color') {
            const colorInput = container.querySelector('input[type="color"]');
            const textInput = container.querySelector('input[type="text"]');
            
            colorInput.addEventListener('input', function() {
                textInput.value = this.value;
            });
            
            textInput.addEventListener('input', function() {
                colorInput.value = this.value;
            });
        }
    }
    
    updateValueField('string');
</script>
@endpush