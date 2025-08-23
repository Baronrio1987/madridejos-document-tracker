<div class="card border-0 shadow-sm qr-code-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-qr-code me-2"></i>
            <span class="d-none d-sm-inline">QR Code</span>
            <span class="d-sm-none">QR</span>
        </h6>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-label="QR Code actions">
                <i class="bi bi-qr-code me-1"></i>
                <span class="d-none d-md-inline">Actions</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('qr-codes.document', $document) }}">
                    <i class="bi bi-gear me-2"></i>Generate/Customize
                </a></li>
                <li><a class="dropdown-item" href="{{ route('qr-codes.download', $document) }}">
                    <i class="bi bi-download me-2"></i>Download PNG
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="generateQuickQr({{ $document->id }})">
                    <i class="bi bi-lightning me-2"></i>Quick Generate
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="shareQrCode({{ $document->id }})">
                    <i class="bi bi-share me-2"></i>Share QR Code
                </a></li>
            </ul>
        </div>
    </div>
    <div class="card-body text-center">
        <div id="qr-display-{{ $document->id }}" class="qr-display-container">
            @if($document->hasQrCode())
                <div class="qr-code-container mb-3">
                    <img src="{{ $document->getQrCodePath() }}" 
                         alt="QR Code for {{ $document->tracking_number }}" 
                         class="img-fluid qr-image" 
                         onclick="showQrModal({{ $document->id }})"
                         style="cursor: pointer;">
                </div>
                <p class="small text-muted mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    <span class="d-none d-sm-inline">Scan to track this document</span>
                    <span class="d-sm-none">Tap to view larger</span>
                </p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <a href="{{ $document->tracking_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>
                        <span class="d-none d-sm-inline">Open Tracking Page</span>
                        <span class="d-sm-none">Track</span>
                    </a>
                    <button class="btn btn-sm btn-outline-primary" onclick="shareQrCode({{ $document->id }})">
                        <i class="bi bi-share me-1"></i>
                        <span class="d-none d-sm-inline">Share</span>
                    </button>
                </div>
            @else
                <div class="qr-placeholder mb-3">
                    <i class="bi bi-qr-code text-muted qr-placeholder-icon"></i>
                    <p class="text-muted mt-2 mb-0">No QR code generated</p>
                </div>
                <button class="btn btn-primary btn-sm w-100" onclick="generateQuickQr({{ $document->id }})">
                    <i class="bi bi-qr-code me-2"></i>Generate QR Code
                </button>
            @endif
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal-{{ $document->id }}" tabindex="-1" aria-labelledby="qrModalLabel-{{ $document->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel-{{ $document->id }}">
                    <i class="bi bi-qr-code me-2"></i>QR Code - {{ $document->tracking_number }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="qr-modal-container">
                    @if($document->hasQrCode())
                        <img src="{{ $document->getQrCodePath() }}" 
                             alt="QR Code for {{ $document->tracking_number }}" 
                             class="img-fluid mb-3"
                             style="max-width: 300px;">
                        <p class="text-muted mb-3">{{ $document->tracking_number }}</p>
                        <p class="small text-muted">Scan this code with your camera to track the document</p>
                    @endif
                </div>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ $document->tracking_url }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-2"></i>Open Tracking Page
                    </a>
                    <button class="btn btn-outline-secondary" onclick="downloadQrCode({{ $document->id }})">
                        <i class="bi bi-download me-2"></i>Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateQuickQr(documentId) {
    const container = document.getElementById(`qr-display-${documentId}`);
    const originalContent = container.innerHTML;
    
    // Show loading state with mobile-friendly design
    container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mb-0">Generating QR code...</p>
            <small class="text-muted">This may take a moment</small>
        </div>
    `;
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('size', '200');
    formData.append('label', '{{ $document->tracking_number }}');
    
    fetch(`/qr-codes/generate/${documentId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            container.innerHTML = `
                <div class="qr-code-container mb-3">
                    <img src="${data.qr_url}" 
                         alt="QR Code for {{ $document->tracking_number }}" 
                         class="img-fluid qr-image" 
                         onclick="showQrModal(${documentId})"
                         style="cursor: pointer;">
                </div>
                <p class="small text-muted mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    <span class="d-none d-sm-inline">Scan to track this document</span>
                    <span class="d-sm-none">Tap to view larger</span>
                </p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <a href="${data.tracking_url}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>
                        <span class="d-none d-sm-inline">Open Tracking Page</span>
                        <span class="d-sm-none">Track</span>
                    </a>
                    <button class="btn btn-sm btn-outline-primary" onclick="shareQrCode(${documentId})">
                        <i class="bi bi-share me-1"></i>
                        <span class="d-none d-sm-inline">Share</span>
                    </button>
                </div>
            `;
            
            // Show success toast
            showToast('success', 'QR code generated successfully!');
            
            // Add haptic feedback on mobile
            if ('vibrate' in navigator) {
                navigator.vibrate(50);
            }
        } else {
            container.innerHTML = originalContent;
            showToast('error', data.message || 'Failed to generate QR code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = originalContent;
        showToast('error', 'An error occurred while generating the QR code');
    });
}

function showQrModal(documentId) {
    const modal = new bootstrap.Modal(document.getElementById(`qrModal-${documentId}`));
    modal.show();
}

function shareQrCode(documentId) {
    const qrImage = document.querySelector(`#qr-display-${documentId} .qr-image`);
    const trackingUrl = '{{ $document->tracking_url }}';
    
    if (navigator.share && qrImage) {
        // Convert image to blob for sharing
        fetch(qrImage.src)
            .then(response => response.blob())
            .then(blob => {
                const file = new File([blob], 'qr-code-{{ $document->tracking_number }}.png', { type: 'image/png' });
                
                navigator.share({
                    title: 'Document QR Code - {{ $document->tracking_number }}',
                    text: 'Track this document using the QR code or link below:',
                    url: trackingUrl,
                    files: [file]
                }).catch(err => {
                    console.log('Error sharing:', err);
                    fallbackShare(trackingUrl);
                });
            })
            .catch(err => {
                console.log('Error preparing share:', err);
                fallbackShare(trackingUrl);
            });
    } else {
        fallbackShare(trackingUrl);
    }
}

function fallbackShare(trackingUrl) {
    // Fallback sharing options
    const shareData = {
        title: 'Document QR Code - {{ $document->tracking_number }}',
        text: 'Track this document: ' + trackingUrl
    };
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(trackingUrl).then(() => {
            showToast('info', 'Tracking URL copied to clipboard!');
        });
    } else {
        // Show share modal with options
        showShareModal(shareData);
    }
}

function showShareModal(shareData) {
    const modalHtml = `
        <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Share QR Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="shareUrl" class="form-label">Tracking URL:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="shareUrl" value="${shareData.text}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" onclick="shareViaWhatsApp()">
                                <i class="bi bi-whatsapp me-2"></i>Share via WhatsApp
                            </button>
                            <button type="button" class="btn btn-primary" onclick="shareViaEmail()">
                                <i class="bi bi-envelope me-2"></i>Share via Email
                            </button>
                            <button type="button" class="btn btn-info" onclick="shareViaSMS()">
                                <i class="bi bi-phone me-2"></i>Share via SMS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('shareModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
    modal.show();
    
    // Clean up modal after it's hidden
    document.getElementById('shareModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function copyToClipboard() {
    const urlInput = document.getElementById('shareUrl');
    urlInput.select();
    urlInput.setSelectionRange(0, 99999);
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(urlInput.value).then(() => {
            showToast('success', 'URL copied to clipboard!');
        });
    } else {
        document.execCommand('copy');
        showToast('success', 'URL copied to clipboard!');
    }
}

function shareViaWhatsApp() {
    const text = document.getElementById('shareUrl').value;
    const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
}

function shareViaEmail() {
    const text = document.getElementById('shareUrl').value;
    const subject = 'Document Tracking - {{ $document->tracking_number }}';
    const body = `Please use this link to track the document:\n\n${text}`;
    const url = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = url;
}

function shareViaSMS() {
    const text = document.getElementById('shareUrl').value;
    const url = `sms:?body=${encodeURIComponent(text)}`;
    window.location.href = url;
}

function downloadQrCode(documentId) {
    const qrImage = document.querySelector(`#qr-display-${documentId} .qr-image`);
    if (qrImage) {
        const link = document.createElement('a');
        link.href = qrImage.src;
        link.download = 'qr-code-{{ $document->tracking_number }}.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showToast('success', 'QR code downloaded!');
    }
}

function showToast(type, message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: type === 'error' ? 6000 : 3000
    });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}
</script>

<style>
/* Mobile-optimized QR code styles */
.qr-code-card {
    transition: all 0.3s ease;
}

.qr-code-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.qr-code-container {
    display: inline-block;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.qr-code-container:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.qr-image {
    max-width: 150px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.qr-image:hover {
    transform: scale(1.02);
}

.qr-placeholder {
    padding: 2rem 1rem;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: all 0.3s ease;
}

.qr-placeholder:hover {
    border-color: var(--bs-primary);
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

.qr-placeholder-icon {
    font-size: 3rem;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.qr-placeholder:hover .qr-placeholder-icon {
    opacity: 0.8;
    color: var(--bs-primary) !important;
}

/* Mobile specific adjustments */
@media (max-width: 768px) {
    .qr-code-container {
        padding: 0.75rem;
    }
    
    .qr-image {
        max-width: 120px;
    }
    
    .qr-placeholder {
        padding: 1.5rem 0.75rem;
    }
    
    .qr-placeholder-icon {
        font-size: 2.5rem;
    }
    
    .dropdown-menu {
        font-size: 0.9rem;
    }
    
    .modal-dialog {
        margin: 1rem;
    }
    
    .modal-body .qr-modal-container img {
        max-width: 250px;
    }
}

@media (max-width: 576px) {
    .qr-code-container {
        padding: 0.5rem;
    }
    
    .qr-image {
        max-width: 100px;
    }
    
    .qr-placeholder {
        padding: 1rem 0.5rem;
    }
    
    .qr-placeholder-icon {
        font-size: 2rem;
    }
    
    .card-header h6 {
        font-size: 0.9rem;
    }
    
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }
    
    .modal-body .qr-modal-container img {
        max-width: 200px;
    }
}

/* Toast container positioning for mobile */
@media (max-width: 576px) {
    .toast-container {
        top: 0.5rem !important;
        right: 0.5rem !important;
        left: 0.5rem !important;
    }
    
    .toast {
        width: 100%;
    }
}

/* Touch-friendly interactions */
@media (hover: none) and (pointer: coarse) {
    .qr-code-container:hover {
        transform: none;
    }
    
    .qr-image:hover {
        transform: none;
    }
    
    .qr-placeholder:hover {
        border-color: #dee2e6;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
}

/* Loading animation */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.qr-display-container .spinner-border {
    animation: pulse 1.5s ease-in-out infinite;
}

/* Accessibility improvements */
.qr-image:focus {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
}

.btn:focus {
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .qr-code-container {
        border: 2px solid #000;
    }
    
    .qr-placeholder {
        border-color: #000;
        border-width: 3px;
    }
}
</style>