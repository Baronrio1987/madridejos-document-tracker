{{-- resources/views/partials/navigation.blade.php --}}
<nav class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
    <div class="sidebar-content">
        <!-- Mobile Header (visible only on mobile) -->
        <div class="sidebar-header d-md-none">
            <div class="d-flex align-items-center justify-content-between p-3">
                <div class="d-flex align-items-center">
                    @if(setting('appearance.logo'))
                        <img src="{{ setting('appearance.logo') }}" 
                            alt="{{ setting('system.name', config('app.name')) }}" 
                            class="sidebar-logo me-2">
                    @endif
                    <span class="sidebar-brand">{{ setting('system.name', config('app.name')) }}</span>
                </div>
                <button class="btn btn-sm btn-outline-light" id="sidebarClose" aria-label="Close navigation">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <hr class="sidebar-divider">
        </div>

        <!-- User Info Section (mobile) -->
        <div class="user-info d-md-none">
            <div class="d-flex align-items-center p-3">
                <div class="user-avatar me-3">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-department">{{ Auth::user()->department?->name ?? 'No Department' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <ul class="nav nav-pills flex-column" role="menubar">
            <!-- Quick Actions (Mobile Only) -->
            <li class="nav-item d-md-none" role="none">
                <div class="quick-actions">
                    <a href="{{ route('documents.create') }}" class="btn btn-sm btn-outline-light me-2" role="menuitem">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>New Doc</span>
                    </a>
                    <a href="{{ url('/track') }}" class="btn btn-sm btn-outline-light" role="menuitem">
                        <i class="bi bi-search me-1"></i>
                        <span>Track</span>
                    </a>
                </div>
            </li>

            <!-- Dashboard -->
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                   href="{{ route('dashboard') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                    <i class="bi bi-speedometer2" aria-hidden="true"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Documents -->
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" 
                   href="{{ route('documents.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('documents.*') ? 'page' : 'false' }}">
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    <span class="nav-text">Documents</span>
                    @if(isset($pendingDocumentsCount) && $pendingDocumentsCount > 0)
                        <span class="notification-badge ms-auto" 
                              aria-label="{{ $pendingDocumentsCount }} pending documents">
                            {{ $pendingDocumentsCount > 99 ? '99+' : $pendingDocumentsCount }}
                        </span>
                    @endif
                </a>
            </li>
            
            <!-- Document Routes -->
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('document-routes.*') ? 'active' : '' }}" 
                   href="{{ route('document-routes.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('routes.*') ? 'page' : 'false' }}">
                    <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                    <span class="nav-text">Document Routes</span>
                </a>
            </li>

            <!-- QR Codes -->
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('qr-codes.*') ? 'active' : '' }}" 
                   href="{{ route('qr-codes.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('qr-codes.*') ? 'page' : 'false' }}">
                    <i class="bi bi-qr-code" aria-hidden="true"></i>
                    <span class="nav-text">QR Codes</span>
                </a>
            </li>
            
            <!-- Reports -->
            @can('view-reports')
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" 
                   href="{{ route('reports.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('reports.*') ? 'page' : 'false' }}">
                    <i class="bi bi-graph-up" aria-hidden="true"></i>
                    <span class="nav-text">Reports</span>
                </a>
            </li>
            @endcan
            
            <!-- Administration Section -->
            @can('manage-settings')
            <li class="nav-item nav-section-divider" role="none">
                <div class="nav-section-header">
                    <span class="nav-text">ADMINISTRATION</span>
                </div>
            </li>

            <!-- Analytics -->
            @can('view-analytics')
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}" 
                   href="{{ route('analytics.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('analytics.*') ? 'page' : 'false' }}">
                    <i class="bi bi-bar-chart" aria-hidden="true"></i>
                    <span class="nav-text">Analytics</span>
                </a>
            </li>
            @endcan
            
            <!-- Users -->
            @can('viewAny', App\Models\User::class)
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                   href="{{ route('users.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('users.*') ? 'page' : 'false' }}">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    <span class="nav-text">Users</span>
                </a>
            </li>
            @endcan
            
            <!-- Departments -->
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" 
                   href="{{ route('admin.departments.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('admin.departments.*') ? 'page' : 'false' }}">
                    <i class="bi bi-building" aria-hidden="true"></i>
                    <span class="nav-text">Departments</span>
                </a>
            </li>
            
            <!-- Document Types -->
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('admin.document-types.*') ? 'active' : '' }}" 
                   href="{{ route('admin.document-types.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('admin.document-types.*') ? 'page' : 'false' }}">
                    <i class="bi bi-tags" aria-hidden="true"></i>
                    <span class="nav-text">Document Types</span>
                </a>
            </li>
            
            <!-- Settings -->
            <li class="nav-item" role="none">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                   href="{{ route('admin.settings.index') }}" 
                   role="menuitem"
                   aria-current="{{ request()->routeIs('admin.settings.*') ? 'page' : 'false' }}">
                    <i class="bi bi-gear" aria-hidden="true"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
            @endcan
        </ul>
        
        <!-- Mobile Footer Actions -->
        <div class="sidebar-footer">
            <!-- Desktop Auto-hide Toggle -->
            <button class="btn btn-outline-light btn-sm w-100 d-none d-md-flex align-items-center justify-content-center" 
                    id="autoHideToggle" 
                    title="Toggle Auto-hide"
                    aria-label="Toggle sidebar auto-hide">
                <i class="bi bi-pin-angle" id="autoHideIcon" aria-hidden="true"></i>
                <span class="nav-text ms-2">Auto-hide</span>
            </button>

            <!-- Mobile Footer Links -->
            <div class="mobile-footer-links d-md-none">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-light btn-sm w-100">
                            <i class="bi bi-person me-1"></i>
                            <span>Profile</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-light btn-sm w-100 position-relative">
                            <i class="bi bi-bell me-1"></i>
                            <span>Alerts</span>
                            <span class="notification-badge-small position-absolute top-0 start-100 translate-middle" 
                                  id="mobileNotificationBadge" style="display: none;">0</span>
                        </a>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </button>
                </form>
                
                <!-- App Info -->
                <div class="app-info mt-3 text-center">
                    <small class="text-light opacity-75">
                        {{ setting('system.name', config('app.name')) }} v{{ config('app.version', '1.0') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Resize Handle (Desktop) -->
    <div class="sidebar-resize-handle d-none d-md-block" id="sidebarResizeHandle"></div>
</nav>

<!-- Enhanced Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

<style>
/* Enhanced Mobile Sidebar Styles */
.sidebar {
    /* Base styles from main layout remain the same */
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

/* Mobile Header */
.sidebar-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-logo {
    height: 28px;
    width: auto;
}

.sidebar-brand {
    font-weight: 600;
    font-size: 0.95rem;
    color: white;
}

.sidebar-divider {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 0;
}

#sidebarClose {
    min-width: 36px;
    min-height: 36px;
    border-radius: 50%;
}

/* User Info Section */
.user-info {
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.user-avatar i {
    font-size: 2rem;
    color: rgba(255, 255, 255, 0.8);
}

.user-name {
    font-weight: 600;
    color: white;
    font-size: 0.9rem;
    line-height: 1.2;
}

.user-department {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.2;
}

/* Quick Actions */
.quick-actions {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 0.5rem;
}

.quick-actions .btn {
    font-size: 0.8rem;
    padding: 0.375rem 0.75rem;
    min-height: 32px;
}

/* Enhanced Navigation Links */
.nav-link {
    position: relative;
    border-radius: 0.5rem;
    margin: 0.125rem 0.5rem;
    padding: 0.75rem 1rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 48px;
    display: flex;
    align-items: center;
}

@media (max-width: 768px) {
    .nav-link {
        margin: 0.125rem 0.75rem;
        min-height: 52px; /* Larger touch targets on mobile */
        padding: 0.875rem 1rem;
    }
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white !important;
    transform: translateX(4px);
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    color: white !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 24px;
    background-color: white;
    border-radius: 0 2px 2px 0;
}

.nav-item .nav-link i {
    width: 24px;
    height: 24px;
    text-align: center;
    margin-right: 12px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

/* Section Headers */
.nav-section-divider {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.nav-section-header {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 0 1rem;
    margin-bottom: 0.5rem;
    position: relative;
}

.nav-section-header::after {
    content: '';
    position: absolute;
    bottom: -0.25rem;
    left: 1rem;
    right: 1rem;
    height: 1px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
}

/* Notification Badges */
.notification-badge {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-radius: 10px;
    padding: 0.2rem 0.5rem;
    font-size: 0.7rem;
    font-weight: 600;
    min-width: 1.5rem;
    height: 1.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.notification-badge-small {
    background: #ef4444;
    color: white;
    border-radius: 50%;
    padding: 0.125rem 0.375rem;
    font-size: 0.6rem;
    font-weight: 600;
    min-width: 1rem;
    height: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Footer Styles */
.sidebar-footer {
    margin-top: auto;
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-footer-links .btn {
    font-size: 0.8rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
}

.app-info {
    padding-top: 0.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

/* Auto-hide Toggle */
#autoHideToggle {
    border-color: rgba(255, 255, 255, 0.3);
    transition: all 0.2s ease;
}

#autoHideToggle:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-1px);
}

/* Sidebar Resize Handle */
.sidebar-resize-handle {
    position: absolute;
    top: 0;
    right: -2px;
    width: 4px;
    height: 100%;
    cursor: col-resize;
    background: transparent;
    transition: background-color 0.2s ease;
}

.sidebar-resize-handle:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Collapsed Sidebar Styles */
.sidebar.collapsed .nav-text {
    opacity: 0;
    transform: translateX(-10px);
}

.sidebar.collapsed .nav-section-header {
    opacity: 0;
}

.sidebar.collapsed .notification-badge {
    display: none;
}

.sidebar.collapsed .sidebar-footer .nav-text {
    opacity: 0;
}

.sidebar.collapsed .nav-link {
    padding: 0.75rem;
    justify-content: center;
    margin: 0.125rem 0.25rem;
}

.sidebar.collapsed .nav-item .nav-link i {
    margin-right: 0;
}

.sidebar.collapsed .quick-actions,
.sidebar.collapsed .user-info,
.sidebar.collapsed .mobile-footer-links {
    display: none;
}

/* Mobile-specific styles */
@media (max-width: 768px) {
    .sidebar-content {
        padding-top: 0;
        height: 100vh;
    }
    
    .nav {
        padding-bottom: 2rem;
    }
    
    /* Hide desktop-only elements */
    .sidebar.collapsed .d-md-none {
        display: block !important;
    }
    
    /* Enhanced touch targets */
    .nav-link {
        -webkit-tap-highlight-color: rgba(255, 255, 255, 0.1);
    }
    
    /* Smooth scrolling */
    .sidebar-content {
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
}

/* Dark mode adjustments */
@media (prefers-color-scheme: dark) {
    .sidebar {
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .nav-link {
        border: 1px solid transparent;
    }
    
    .nav-link:hover, .nav-link:focus {
        border-color: white;
    }
    
    .nav-link.active {
        border-color: white;
        background-color: rgba(255, 255, 255, 0.3);
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .nav-link {
        transition: none;
    }
    
    .nav-link:hover {
        transform: none;
    }
    
    .nav-text {
        transition: none;
    }
}

/* Focus styles for keyboard navigation */
.nav-link:focus {
    outline: 2px solid white;
    outline-offset: 2px;
}

#autoHideToggle:focus {
    outline: 2px solid white;
    outline-offset: 2px;
}

/* Loading states */
.nav-link.loading {
    pointer-events: none;
    opacity: 0.6;
}

.nav-link.loading::after {
    content: '';
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translateY(-50%) rotate(0deg); }
    100% { transform: translateY(-50%) rotate(360deg); }
}
</style>

<script>
// Enhanced Mobile Navigation JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarClose = document.getElementById('sidebarClose');
    const mobileNotificationBadge = document.getElementById('mobileNotificationBadge');
    
    // Mobile sidebar close functionality
    sidebarClose?.addEventListener('click', function() {
        const sidebarManager = window.sidebarManager;
        if (sidebarManager) {
            sidebarManager.hideMobileSidebar();
        } else {
            // Fallback
            sidebar?.classList.remove('show');
            document.getElementById('sidebarOverlay')?.classList.remove('show');
        }
    });
    
    // Enhanced navigation link interactions
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        // Add loading state on navigation
        link.addEventListener('click', function(e) {
            if (this.href && !this.href.includes('#')) {
                this.classList.add('loading');
                
                // Remove loading state if navigation is cancelled
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 5000);
            }
        });
        
        // Touch feedback for mobile
        if ('ontouchstart' in window) {
            link.addEventListener('touchstart', function() {
                this.style.backgroundColor = 'rgba(255, 255, 255, 0.15)';
            }, { passive: true });
            
            link.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 150);
            }, { passive: true });
        }
    });
    
    // Sync mobile notification badge with main notification count
    function syncMobileNotificationBadge() {
        const mainBadge = document.getElementById('notificationCount');
        const mobileBadge = document.getElementById('mobileNotificationBadge');
        
        if (mainBadge && mobileBadge) {
            const count = mainBadge.textContent;
            const isVisible = mainBadge.style.display !== 'none';
            
            if (isVisible && count) {
                mobileBadge.textContent = count;
                mobileBadge.style.display = 'block';
            } else {
                mobileBadge.style.display = 'none';
            }
        }
    }
    
    // Initial sync and periodic updates
    syncMobileNotificationBadge();
    setInterval(syncMobileNotificationBadge, 5000);
    
    // Keyboard navigation support
    document.addEventListener('keydown', function(e) {
        // Alt + M to toggle mobile sidebar
        if (e.altKey && e.key === 'm' && window.innerWidth < 769) {
            e.preventDefault();
            const sidebarManager = window.sidebarManager;
            if (sidebarManager) {
                sidebarManager.toggleMobileSidebar();
            }
        }
        
        // Escape to close mobile sidebar
        if (e.key === 'Escape' && window.innerWidth < 769) {
            const sidebarManager = window.sidebarManager;
            if (sidebarManager && sidebar?.classList.contains('show')) {
                sidebarManager.hideMobileSidebar();
            }
        }
    });
    
    // Sidebar resize functionality (desktop only)
    if (window.innerWidth >= 769) {
        const resizeHandle = document.getElementById('sidebarResizeHandle');
        let isResizing = false;
        let startX = 0;
        let startWidth = 0;
        
        resizeHandle?.addEventListener('mousedown', function(e) {
            isResizing = true;
            startX = e.clientX;
            startWidth = parseInt(document.defaultView.getComputedStyle(sidebar).width, 10);
            document.addEventListener('mousemove', doResize);
            document.addEventListener('mouseup', stopResize);
            e.preventDefault();
        });
        
        function doResize(e) {
            if (!isResizing) return;
            const newWidth = startWidth + e.clientX - startX;
            if (newWidth >= 200 && newWidth <= 400) {
                sidebar.style.width = newWidth + 'px';
                document.documentElement.style.setProperty('--sidebar-width', newWidth + 'px');
            }
        }
        
        function stopResize() {
            isResizing = false;
            document.removeEventListener('mousemove', doResize);
            document.removeEventListener('mouseup', stopResize);
        }
    }
    
    // Progressive enhancement for better performance
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });
        
        navLinks.forEach(link => {
            observer.observe(link);
        });
    }
    
    // Accessibility improvements
    sidebar?.addEventListener('focus', function() {
        this.setAttribute('aria-expanded', 'true');
    });
    
    sidebar?.addEventListener('blur', function() {
        if (!this.contains(document.activeElement)) {
            this.setAttribute('aria-expanded', 'false');
        }
    });
    
    // Store reference for external access
    window.navigationSidebar = {
        syncNotificationBadge: syncMobileNotificationBadge,
        element: sidebar
    };
});
</script>