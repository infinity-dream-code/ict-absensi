<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#667eea">
    <title>@yield('title', 'Admin Dashboard - Absensi ICT')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Absensi ICT">
    <meta name="description" content="Admin Panel Sistem Absensi Karyawan ICT">
    <meta name="application-name" content="Absensi ICT">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" href="{{ asset('logo-512.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo-512.png') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0);
            }
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        
        .logo-text {
            font-weight: bold;
            font-size: 16px;
            color: #1f2937;
        }
        
        .close-sidebar {
            display: block;
            background: none;
            border: none;
            color: #6b7280;
            font-size: 20px;
            cursor: pointer;
        }
        
        @media (min-width: 1024px) {
            .close-sidebar {
                display: none;
            }
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 16px;
            overflow-y: auto;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
            margin-bottom: 8px;
            transition: all 0.2s;
        }
        
        .nav-item:hover {
            background: #f3f4f6;
        }
        
        .nav-item.active {
            background: #eef2ff;
            color: #667eea;
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid #e5e7eb;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #eef2ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
        }
        
        .user-details {
            flex: 1;
            min-width: 0;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-role {
            font-size: 12px;
            color: #6b7280;
        }
        
        .btn-logout {
            width: 100%;
            padding: 10px 16px;
            background: #fef2f2;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-logout:hover {
            background: #fee2e2;
        }
        
        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        
        .overlay.active {
            display: block;
        }
        
        @media (min-width: 1024px) {
            .overlay {
                display: none !important;
            }
        }
        
        /* Main Content */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }
        
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 260px;
            }
        }
        
        .topbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .menu-toggle {
            display: block;
            background: none;
            border: none;
            color: #6b7280;
            font-size: 20px;
            cursor: pointer;
        }
        
        @media (min-width: 1024px) {
            .menu-toggle {
                display: none;
            }
        }
        
        .topbar-date {
            font-size: 14px;
            color: #6b7280;
        }
        
        .page-content {
            padding: 24px;
        }
        
        /* Alerts */
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            color: #065f46;
        }
        
        .alert-error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        
        .alert i {
            font-size: 18px;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-wrapper">
                <div class="logo-icon" style="background: transparent; padding: 0;">
                    <img src="{{ asset('logo-512.png') }}" alt="Logo" style="width: 32px; height: 32px; border-radius: 8px;">
                </div>
                <span class="logo-text">Absensi ICT</span>
            </div>
            <button class="close-sidebar" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>Set Waktu</span>
            </a>
            <a href="{{ route('admin.employees.index') }}" class="nav-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Kelola Karyawan</span>
            </a>
            <a href="{{ route('admin.today-attendance.index') }}" class="nav-item {{ request()->routeIs('admin.today-attendance.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>Absen Hari Ini</span>
            </a>
            <a href="{{ route('admin.attendance-history.index') }}" class="nav-item {{ request()->routeIs('admin.attendance-history.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>History Absensi</span>
            </a>
            <a href="{{ route('admin.leave-history.index') }}" class="nav-item {{ request()->routeIs('admin.leave-history.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-times"></i>
                <span>History Izin</span>
            </a>
            <a href="{{ route('admin.location.index') }}" class="nav-item {{ request()->routeIs('admin.location.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i>
                <span>Set Lokasi</span>
            </a>
            <a href="{{ route('admin.holiday.index') }}" class="nav-item {{ request()->routeIs('admin.holiday.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Libur</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="button" id="admin-logout-btn" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Overlay -->
    <div class="overlay" id="sidebarOverlay"></div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <button class="menu-toggle" id="openSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-date">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const openBtn = document.getElementById('openSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        
        openBtn.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });
        
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
        
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
        
        // Setup CSRF token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Function untuk refresh CSRF token
        function refreshCsrfToken() {
            return fetch('/csrf-token', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.token) {
                      document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                      axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                      document.querySelectorAll('input[name="_token"]').forEach(input => {
                          input.value = data.token;
                      });
                  }
                  return data.token;
              }).catch(() => {
                  return null;
              });
        }

        // Refresh CSRF token saat halaman load dan setelah login/logout
        function initializeCsrfToken() {
            return refreshCsrfToken().then(() => {
                // Update semua form token
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        input.value = metaToken.getAttribute('content');
                    }
                });
                return true;
            });
        }
        
        // Refresh token hanya saat diperlukan (tidak force refresh)
        // Karena sekarang tidak regenerate session setelah login,
        // token di meta tag sudah match dengan session
        window.addEventListener('DOMContentLoaded', function() {
            // Sync form token dengan meta tag (tidak perlu refresh dari server)
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                const token = metaToken.getAttribute('content');
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = token;
                });
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
        });

        // Handle 419 error (Page Expired) dengan refresh halaman
        window.addEventListener('unhandledrejection', function(event) {
            if (event.reason && event.reason.response && event.reason.response.status === 419) {
                // Jika 419 error, refresh halaman untuk mendapatkan token baru
                window.location.reload();
            }
        });

        // Intercept form submission untuk memastikan token valid
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.tagName === 'FORM' && form.method.toUpperCase() === 'POST') {
                // Skip untuk logout form karena sudah dihandle khusus
                if (form.id === 'admin-logout-form') {
                    e.preventDefault();
                    return;
                }
                
                const tokenInput = form.querySelector('input[name="_token"]');
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                
                // Pastikan token form sama dengan token meta
                if (tokenInput && metaToken && tokenInput.value !== metaToken.getAttribute('content')) {
                    tokenInput.value = metaToken.getAttribute('content');
                }
            }
        }, false);

        // Handle admin logout dengan AJAX dan retry mechanism
        const adminLogoutBtn = document.getElementById('admin-logout-btn');
        if (adminLogoutBtn) {
            adminLogoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = document.getElementById('admin-logout-form');
                const tokenInput = form.querySelector('input[name="_token"]');
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                
                // Pastikan token terbaru
                if (tokenInput && metaToken) {
                    tokenInput.value = metaToken.getAttribute('content');
                }
                
                // Fungsi untuk logout
                function performLogout(retryCount = 0) {
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.status === 419 && retryCount < 2) {
                            // Jika 419, refresh token dan retry
                            return refreshCsrfToken().then(() => {
                                // Update token di form
                                const newToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                formData.set('_token', newToken);
                                return performLogout(retryCount + 1);
                            });
                        }
                        
                        if (response.redirected) {
                            // Clear storage sebelum redirect
                            sessionStorage.clear();
                            localStorage.clear();
                            window.location.href = response.url;
                        } else if (response.ok) {
                            return response.json().then(data => {
                                // Clear storage sebelum redirect
                                sessionStorage.clear();
                                localStorage.clear();
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.href = '{{ route("admin.login") }}';
                                }
                            });
                        } else {
                            // Jika masih error, gunakan GET fallback
                            sessionStorage.clear();
                            localStorage.clear();
                            window.location.href = '{{ route("admin.logout") }}?fallback=1';
                        }
                    })
                    .catch(error => {
                        console.error('Logout error:', error);
                        // Fallback ke GET logout
                        window.location.href = '{{ route("admin.logout") }}?fallback=1';
                    });
                }
                
                performLogout();
            });
        }

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // Unregister service worker lama yang mungkin cache HTML dengan token lama
                navigator.serviceWorker.getRegistrations().then((registrations) => {
                    for (let registration of registrations) {
                        // Unregister service worker dengan cache name lama
                        if (registration.active) {
                            registration.unregister().then(() => {
                                console.log('Old service worker unregistered');
                            });
                        }
                    }
                    
                    // Register service worker baru
                    navigator.serviceWorker.register('{{ asset("sw.js") }}?v=2')
                        .then((registration) => {
                            console.log('Service Worker registered successfully:', registration.scope);
                            // Force update service worker
                            registration.update();
                        })
                        .catch((error) => {
                            console.log('Service Worker registration failed:', error);
                        });
                });
            });
        }
    </script>
    
    @yield('scripts')
</body>
</html>
