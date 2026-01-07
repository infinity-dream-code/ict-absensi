@extends('admin.layouts.app')

@section('title', 'Set Waktu')

@section('styles')
<style>
    .page-header {
        margin-bottom: 32px;
    }
    
    .page-title {
        font-size: 32px;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .page-subtitle {
        font-size: 16px;
        color: #6b7280;
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .form-section {
        margin-bottom: 32px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #667eea;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        font-size: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        outline: none;
    }
    
    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .form-help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }
    
    .error-message {
        color: #dc2626;
        font-size: 14px;
        margin-top: 6px;
    }
    
    .form-actions {
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
    }
    
    .btn-submit {
        padding: 14px 32px;
        font-size: 16px;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }
    
    @media (max-width: 768px) {
        .settings-card {
            padding: 24px;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Set Waktu</h1>
    <p class="page-subtitle">Atur waktu check-in dan check-out</p>
</div>

<div class="settings-card">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Check-In Time -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-sign-in-alt"></i>
                Waktu Check-In
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="check_in_start" class="form-label">Mulai Check-In</label>
                    <input type="time" 
                           id="check_in_start" 
                           name="check_in_start" 
                           value="{{ old('check_in_start', $settings->check_in_start ? substr($settings->check_in_start, 0, 5) : '08:00') }}"
                           class="form-input"
                           required>
                    @error('check_in_start')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="check_in_end" class="form-label">Batas Akhir Check-In</label>
                    <input type="time" 
                           id="check_in_end" 
                           name="check_in_end" 
                           value="{{ old('check_in_end', $settings->check_in_end ? substr($settings->check_in_end, 0, 5) : '09:00') }}"
                           class="form-input"
                           required>
                    @error('check_in_end')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="form-help">Check-in setelah waktu ini akan dianggap terlambat</p>
                </div>
            </div>
        </div>
        
        <!-- Check-Out Time -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-sign-out-alt"></i>
                Waktu Check-Out
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="check_out_start" class="form-label">Mulai Check-Out</label>
                    <input type="time" 
                           id="check_out_start" 
                           name="check_out_start" 
                           value="{{ old('check_out_start', $settings->check_out_start ? substr($settings->check_out_start, 0, 5) : '17:00') }}"
                           class="form-input"
                           required>
                    @error('check_out_start')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="check_out_end" class="form-label">Batas Akhir Check-Out</label>
                    <input type="time" 
                           id="check_out_end" 
                           name="check_out_end" 
                           value="{{ old('check_out_end', $settings->check_out_end ? substr($settings->check_out_end, 0, 5) : '18:00') }}"
                           class="form-input"
                           required>
                    @error('check_out_end')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i>
                <span>Simpan Pengaturan</span>
            </button>
        </div>
    </form>
</div>
@endsection
