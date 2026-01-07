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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('styles')
</head>
<body class="min-h-screen bg-gray-50">
    @if(auth()->check() && auth()->user()->role !== 'admin')
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 text-gray-900 font-semibold text-lg">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white text-sm"></i>
                    </div>
                    <span>Sistem Absensi</span>
                </a>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2 text-gray-700">
                        <i class="fas fa-user text-gray-400 text-sm"></i>
                        <span class="font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    <a href="{{ route('profile.change-password') }}" class="text-gray-600 hover:text-gray-900 flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-key text-sm"></i>
                        <span class="hidden sm:inline text-sm font-medium">Ganti Password</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900 flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-sign-out-alt text-sm"></i>
                            <span class="hidden sm:inline text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endif

    <div class="container mx-auto px-4 py-8">
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