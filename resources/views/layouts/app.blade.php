<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#667eea">
    <title>@yield('title', 'Sistem Absensi Karyawan')</title>
    
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Basic layout (native CSS) */
        body {
            min-height: 100vh;
            background: #f9fafb;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #111827;
            margin: 0;
        }
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .nav-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #111827;
            font-weight: 600;
            font-size: 17px;
        }
        .brand-icon {
            width: 32px;
            height: 32px;
            background: #4f46e5;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #374151;
            font-weight: 500;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }
        .nav-link:hover {
            background: #f3f4f6;
            color: #111827;
        }
        .nav-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border: none;
            border-radius: 8px;
            background: #f3f4f6;
            color: #374151;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .nav-button:hover {
            background: #e5e7eb;
            color: #111827;
        }
        .page-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 16px;
        }
    </style>
    @yield('styles')
</head>
<body>
    @if(auth()->check() && auth()->user()->role !== 'admin')
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('attendance.index') }}" class="brand">
                <div class="brand-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <span>Sistem Absensi</span>
            </a>
            <div class="nav-actions">
                <div class="nav-user">
                    <i class="fas fa-user" style="color:#9ca3af; font-size:13px;"></i>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <a href="{{ route('profile.change-password') }}" class="nav-link">
                    <i class="fas fa-key" style="font-size:13px;"></i>
                    <span style="font-size:13px;">Ganti Password</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="nav-button">
                        <i class="fas fa-sign-out-alt" style="font-size:13px;"></i>
                        <span style="font-size:13px;">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
    @endif

    <div class="page-container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
    
    @yield('scripts')
</body>
</html>