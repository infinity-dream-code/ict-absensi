@extends('layouts.app')

@section('title', 'Login - Sistem Absensi')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="card p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-500 to-purple-600 rounded-full mb-4 shadow-lg">
                    <i class="fas fa-user-circle text-4xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Login</h2>
                <p class="text-gray-600">Masuk dengan NIK dan Password</p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2 text-primary-500"></i>NIK (Nomor Induk Karyawan)
                    </label>
                    <input type="text" 
                           class="form-input @error('nik') border-red-500 @enderror" 
                           id="nik" 
                           name="nik" 
                           value="{{ old('nik') }}" 
                           required 
                           autofocus
                           placeholder="Masukkan NIK">
                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-primary-500"></i>Password
                    </label>
                    <input type="password" 
                           class="form-input @error('password') border-red-500 @enderror" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="Masukkan Password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" 
                           id="remember" 
                           name="remember">
                    <label class="ml-2 text-sm text-gray-600" for="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
