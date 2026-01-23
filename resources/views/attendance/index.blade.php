@extends('layouts.app')

@section('title', 'Absensi - Sistem Absensi')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #locationConfirmMap {
        height: 300px;
        width: 100%;
        border-radius: 8px;
        margin: 15px 0;
        z-index: 1;
    }
    .swal2-popup {
        max-width: 90% !important;
        width: 500px !important;
    }
    @media (max-width: 640px) {
        .swal2-popup {
            width: 95% !important;
        }
        #locationConfirmMap {
            height: 250px;
        }
    }
    .attendance-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 80vh;
        padding: 2rem 1rem;
    }
    /* Ensure inputs/textarea don't overflow the card */
    .form-control,
    .form-textarea {
        box-sizing: border-box;
        max-width: 100%;
    }
    .attendance-wrapper {
        width: 100%;
        max-width: 42rem;
    }
    .card-main {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .icon-box {
        width: 2.5rem;
        height: 2.5rem;
        background: #eef2ff;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-body {
        padding: 1.5rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .form-control {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .form-textarea {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        resize: none;
        transition: all 0.2s;
    }
    .form-textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .btn-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .btn-success {
        background: #10b981;
        color: white;
    }
    .btn-success:hover:not(:disabled) {
        background: #059669;
    }
    .btn-danger {
        background: #f43f5e;
        color: white;
    }
    .btn-danger:hover:not(:disabled) {
        background: #e11d48;
    }
    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .status-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        padding: 1.5rem;
        margin-top: 1.25rem;
    }
    .status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }
    .status-item {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1rem;
    }
    .preview-image {
        max-width: 100%;
        height: auto;
        max-height: 12rem;
        border-radius: 0.5rem;
        margin-top: 0.75rem;
    }
    .text-small {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.375rem;
    }
    
    /* SweetAlert2 Custom Styling */
    .swal2-popup {
        border-radius: 1rem !important;
    }
    
    .swal2-confirm,
    .swal2-confirm-custom {
        background-color: #6366f1 !important;
        border: none !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 2rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
        transition: all 0.2s !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
        cursor: pointer !important;
    }
    
    .swal2-confirm:hover,
    .swal2-confirm-custom:hover {
        background-color: #4f46e5 !important;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    
    .swal2-confirm:focus,
    .swal2-confirm-custom:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3) !important;
        outline: none !important;
    }
    
    .swal2-cancel,
    .swal2-cancel-custom {
        background-color: #ef4444 !important;
        border: none !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 2rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
        transition: all 0.2s !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
        cursor: pointer !important;
    }
    
    .swal2-cancel:hover,
    .swal2-cancel-custom:hover {
        background-color: #dc2626 !important;
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    
    .swal2-cancel:focus,
    .swal2-cancel-custom:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3) !important;
        outline: none !important;
    }
    
    .swal2-actions {
        gap: 0.75rem !important;
        margin-top: 1.5rem !important;
    }
    
    .swal2-title {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        color: #111827 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .swal2-html-container {
        font-size: 0.875rem !important;
        color: #6b7280 !important;
        margin-top: 0.5rem !important;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }
    
    .swal2-loading-popup {
        text-align: center;
    }
    
    .swal2-loading-popup .swal2-loader {
        border: 4px solid #f3f4f6;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
        display: block;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .swal2-loading-popup .swal2-html-container {
        padding: 0 !important;
        text-align: center;
    }
    
    .swal2-loading-html {
        text-align: center !important;
    }
    
    /* Image Source Selection Modal Styles */
    .image-source-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: flex-end;
        justify-content: center;
    }
    
    .image-source-modal.active {
        display: flex;
    }
    
    .image-source-container {
        background: white;
        border-radius: 1.5rem 1.5rem 0 0;
        width: 100%;
        max-width: 500px;
        padding: 1.5rem;
        padding-bottom: 2rem;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.3s ease-out;
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }
        to {
            transform: translateY(0);
        }
    }
    
    .image-source-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin: 0 0 1.5rem 0;
        text-align: center;
    }
    
    .image-source-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .image-source-option {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        width: 100%;
    }
    
    .image-source-option:hover {
        background: #f9fafb;
        border-color: #6366f1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    
    .image-source-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .image-source-icon.camera {
        background: #dbeafe;
        color: #2563eb;
    }
    
    .image-source-icon.file {
        background: #fef3c7;
        color: #d97706;
    }
    
    .image-source-text {
        flex: 1;
        font-size: 1rem;
        font-weight: 500;
        color: #111827;
    }
    
    .image-source-cancel {
        margin-top: 0.5rem;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #f9fafb;
        cursor: pointer;
        text-align: center;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s;
    }
    
    .image-source-cancel:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    /* Camera Modal Styles */
    .camera-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        align-items: center;
        justify-content: center;
    }
    
    .camera-modal.active {
        display: flex;
    }
    
    .camera-container {
        position: relative;
        width: 90%;
        max-width: 600px;
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .camera-preview {
        width: 100%;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #000;
        position: relative;
    }
    
    .camera-preview video,
    .camera-preview canvas {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .camera-controls {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
    }
    
    .camera-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    
    .camera-btn-capture {
        background: #10b981;
        color: white;
    }
    
    .camera-btn-capture:hover {
        background: #059669;
    }
    
    .camera-btn-cancel {
        background: #ef4444;
        color: white;
    }
    
    .camera-btn-cancel:hover {
        background: #dc2626;
    }
    
    .camera-btn-retake {
        background: #6b7280;
        color: white;
    }
    
    .camera-btn-retake:hover {
        background: #4b5563;
    }
    
    .camera-btn-use {
        background: #6366f1;
        color: white;
    }
    
    .camera-btn-use:hover {
        background: #4f46e5;
    }
    
    @media (max-width: 640px) {
        .camera-container {
            width: 95%;
            padding: 1rem;
        }
        
        .camera-controls {
            flex-wrap: wrap;
        }
        
        .camera-btn {
            flex: 1;
            min-width: 120px;
        }
    }
</style>
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-wrapper">
        <div class="card-main">
            <div class="card-header">
                <div class="header-content">
                    <div class="icon-box">
                        <i class="fas fa-calendar-day" style="color: #6366f1;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #111827; margin: 0;">Absensi Hari Ini</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                            {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="attendanceForm">
                    <div class="form-group">
                        <label for="work_type" class="form-label">Jenis Kerja</label>
                        <select class="form-control" id="work_type" name="work_type" required>
                            <option value="">Pilih jenis kerja</option>
                            <option value="WFA" {{ old('work_type', $attendance->work_type ?? '') == 'WFA' ? 'selected' : '' }}>WFA (Work From Anywhere)</option>
                            <option value="WFO" {{ old('work_type', $attendance->work_type ?? '') == 'WFO' ? 'selected' : '' }}>WFO (Work From Office)</option>
                            <option value="WFH" {{ old('work_type', $attendance->work_type ?? '') == 'WFH' ? 'selected' : '' }}>WFH (Work From Home)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-textarea" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan (opsional)">{{ old('notes', $attendance->notes ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Gambar (Opsional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" style="cursor: pointer; display: none;">
                        <button type="button" id="imageSelectBtn" class="btn" style="background: #6366f1; color: white; padding: 0.625rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500; width: 100%; justify-content: center;">
                            <i class="fas fa-image"></i>
                            <span>Pilih Gambar</span>
                        </button>
                        <p class="text-small">Format: JPG, PNG, GIF (Max: 5MB)</p>
                        <div id="imagePreview"></div>
                    </div>

                    <div class="form-group" id="locationGroup" style="display: none;">
                        <label class="form-label" style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <span>
                                <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #6366f1;"></i>Lokasi Saat Ini
                            </span>
                            <button type="button" id="refreshLocationBtn" style="padding: 6px 10px; background: #6366f1; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                <i class="fas fa-sync-alt"></i>
                                <span>Refresh</span>
                            </button>
                        </label>
                        <div id="locationMap" style="height: 220px; width: 100%; border-radius: 0.5rem; margin-bottom: 0.75rem; border: 1px solid #e5e7eb;"></div>
                        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.75rem;">
                            <div id="locationInfo" style="font-size: 0.875rem; color: #374151;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <i class="fas fa-spinner fa-spin" style="color: #6366f1;"></i>
                                    <span>Mengambil lokasi...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-grid">
                        <button type="button" id="checkInBtn" class="btn btn-success" {{ isset($attendance) && $attendance->check_out ? 'disabled' : '' }}>
                            <i class="fas fa-sign-in-alt"></i>
                            <span id="checkInBtnText">
                                @if(isset($attendance) && $attendance->check_out)
                                    Sudah Check-Out
                                @elseif(isset($attendance) && $attendance->check_in)
                                    Check-In Lagi
                                @else
                                    Check-In
                                @endif
                            </span>
                        </button>
                        
                        <button type="button" id="checkOutBtn" class="btn btn-danger" {{ isset($attendance) && $attendance->check_out ? 'disabled' : '' }}>
                            <i class="fas fa-sign-out-alt"></i>
                            <span>{{ isset($attendance) && $attendance->check_out ? 'Sudah Check-Out' : 'Check-Out' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($attendance))
        <div class="status-card">
            <h6 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0 0 1rem 0;">Status Absensi</h6>
            <div class="status-grid">
                <div class="status-item">
                    <p style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin: 0 0 0.25rem 0;">Check-In</p>
                    <p style="font-size: 1.25rem; font-weight: 600; margin: 0;">
                        @if($attendance->check_in)
                            <span style="color: #10b981;">{{ \Carbon\Carbon::parse($attendance->check_in)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </p>
                </div>
                <div class="status-item">
                    <p style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin: 0 0 0.25rem 0;">Check-Out</p>
                    <p style="font-size: 1.25rem; font-weight: 600; margin: 0;">
                        @if($attendance->check_out)
                            <span style="color: #10b981;">{{ \Carbon\Carbon::parse($attendance->check_out)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($attendance->logs && $attendance->logs->count() > 0)
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <p style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin: 0 0 0.5rem 0;">Riwayat Check-In</p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($attendance->logs->sortBy('check_in_time') as $log)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: #f9fafb; border-radius: 0.375rem;">
                        <div>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #374151;">{{ $log->status }}</span>
                            <span style="font-size: 0.75rem; color: #6b7280; margin-left: 0.5rem;">{{ \Carbon\Carbon::parse($log->check_in_time)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                        </div>
                        @if($log->notes)
                        <span style="font-size: 0.75rem; color: #6b7280;" title="{{ $log->notes }}">
                            <i class="fas fa-sticky-note"></i>
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Image Source Selection Modal -->
<div id="imageSourceModal" class="image-source-modal">
    <div class="image-source-container">
        <h3 class="image-source-title">Pilih Media dan File</h3>
        <div class="image-source-options">
            <button type="button" id="selectFromCameraBtn" class="image-source-option">
                <div class="image-source-icon camera">
                    <i class="fas fa-camera"></i>
                </div>
                <span class="image-source-text">Ambil Foto atau Video</span>
            </button>
            <button type="button" id="selectFromFileBtn" class="image-source-option">
                <div class="image-source-icon file">
                    <i class="fas fa-upload"></i>
                </div>
                <span class="image-source-text">Unggah dari File</span>
            </button>
        </div>
        <button type="button" id="cancelSourceBtn" class="image-source-cancel">
            Batal
        </button>
    </div>
</div>

<!-- Camera Modal -->
<div id="cameraModal" class="camera-modal">
    <div class="camera-container">
        <div class="camera-preview" id="cameraPreview">
            <video id="cameraVideo" autoplay playsinline style="display: none;"></video>
            <canvas id="cameraCanvas" style="display: none;"></canvas>
        </div>
        <div class="camera-controls" id="cameraControls">
            <button type="button" class="camera-btn camera-btn-cancel" id="cameraCancelBtn">
                <i class="fas fa-times"></i>
                <span>Batal</span>
            </button>
            <button type="button" class="camera-btn camera-btn-capture" id="cameraCaptureBtn">
                <i class="fas fa-camera"></i>
                <span>Ambil Foto</span>
            </button>
        </div>
        <div class="camera-controls" id="previewControls" style="display: none;">
            <button type="button" class="camera-btn camera-btn-retake" id="cameraRetakeBtn">
                <i class="fas fa-redo"></i>
                <span>Ulangi</span>
            </button>
            <button type="button" class="camera-btn camera-btn-use" id="cameraUseBtn">
                <i class="fas fa-check"></i>
                <span>Gunakan Foto</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Global variables untuk menyimpan lokasi
    let currentLatitude = null;
    let currentLongitude = null;
    let currentLocationName = null;
    let currentAccuracy = null;
    let locationMap = null;
    let locationMarker = null;

    function showLocationGroup() {
        document.getElementById('locationGroup').style.display = 'block';
        document.getElementById('locationInfo').innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <i class="fas fa-spinner fa-spin" style="color: #6366f1;"></i>
                <span>Mengambil lokasi...</span>
            </div>
        `;
    }

    // Generic fungsi ambil lokasi, return Promise
    function fetchLocation(highAccuracy = false) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                return reject({ message: 'Browser tidak mendukung geolocation.' });
            }

            navigator.geolocation.getCurrentPosition(
                position => {
                    resolve(position);
                },
                error => {
                    let errorMessage = 'Tidak dapat mendapatkan lokasi.';
                    if (error.code === error.PERMISSION_DENIED) {
                        errorMessage = 'Akses lokasi ditolak. Mohon izinkan akses lokasi.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errorMessage = 'Informasi lokasi tidak tersedia.';
                    } else if (error.code === error.TIMEOUT) {
                        errorMessage = 'Waktu permintaan lokasi habis.';
                    }
                    reject({ message: errorMessage, code: error.code });
                },
                {
                    enableHighAccuracy: highAccuracy,
                    timeout: highAccuracy ? 12000 : 8000,
                    maximumAge: highAccuracy ? 0 : 300000
                }
            );
        });
    }

    // Ambil dan tampilkan lokasi (untuk semua jenis kerja)
    function requestLocationAndDisplay(highAccuracy = false) {
        showLocationGroup();
        fetchLocation(highAccuracy)
            .then(position => {
                currentLatitude = position.coords.latitude;
                currentLongitude = position.coords.longitude;
                currentAccuracy = position.coords.accuracy;

                return getLocationName(currentLatitude, currentLongitude)
                    .then(locationName => {
                        currentLocationName = locationName;
                        displayLocationWithMap(currentLatitude, currentLongitude, currentLocationName, currentAccuracy);
                    })
                    .catch(() => {
                        displayLocationWithMap(currentLatitude, currentLongitude, null, currentAccuracy);
                    });
            })
            .catch(err => {
                document.getElementById('locationInfo').innerHTML = `
                    <div style="color: #dc2626; font-size: 0.875rem;">
                        <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                        ${err.message || 'Gagal mendapatkan lokasi.'}
                    </div>
                `;
            });
    }

    // Get location name via reverse geocoding
    function getLocationName(lat, lng) {
        return new Promise((resolve, reject) => {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                headers: {
                    'User-Agent': 'AbsensiICT/1.0'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    resolve(data.display_name);
                } else {
                    reject('Location name not available');
                }
            })
            .catch(() => {
                reject('Failed to get location name');
            });
        });
    }

    // Display location information with map
    function displayLocationWithMap(lat, lng, locationName, accuracy) {
        // Update location info
        let locationHtml = `
            <div style="margin-bottom: 0.5rem;">
                <div style="font-weight: 600; color: #111827; margin-bottom: 0.25rem;">
                    <i class="fas fa-map-marker-alt" style="color: #10b981; margin-right: 0.5rem;"></i>
                    ${locationName || 'Lokasi tidak tersedia'}
                </div>
                <div style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                    <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                    Koordinat: ${lat.toFixed(7)}, ${lng.toFixed(7)}
                </div>
                <div style="font-size: 0.75rem; color: #6b7280;">
                    <i class="fas fa-crosshairs" style="margin-right: 0.25rem;"></i>
                    Akurasi: ±${Math.round(accuracy)} meter
                </div>
            </div>
        `;
        document.getElementById('locationInfo').innerHTML = locationHtml;

        // Initialize or update map
        if (!locationMap) {
            locationMap = L.map('locationMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(locationMap);
        } else {
            locationMap.setView([lat, lng], 15);
        }

        // Remove existing marker if any
        if (locationMarker) {
            locationMap.removeLayer(locationMarker);
        }

        // Add marker
        const userIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color:#10b981; width:32px; height:32px; border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-map-marker-alt" style="font-size: 16px;"></i></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        locationMarker = L.marker([lat, lng], {icon: userIcon}).addTo(locationMap)
            .bindPopup(`<b>Lokasi Anda</b><br>${locationName || 'Koordinat: ' + lat.toFixed(7) + ', ' + lng.toFixed(7)}`)
            .openPopup();

        // Add office location and circle if available
        const officeLat = {{ $settings->latitude ?? 'null' }};
        const officeLng = {{ $settings->longitude ?? 'null' }};
        const officeRadius = {{ $settings->radius ?? 100 }};

        if (officeLat && officeLng && officeRadius) {
            // Remove existing office circle and marker if any
            locationMap.eachLayer(function(layer) {
                if (layer instanceof L.Circle || (layer instanceof L.Marker && layer !== locationMarker)) {
                    locationMap.removeLayer(layer);
                }
            });

            // Add office circle
            L.circle([officeLat, officeLng], {
                radius: officeRadius,
                color: '#667eea',
                fillColor: '#667eea',
                fillOpacity: 0.2,
                weight: 2
            }).addTo(locationMap);

            // Add office marker
            const officeIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:#667eea; width:32px; height:32px; border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-building" style="font-size: 16px;"></i></div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            L.marker([officeLat, officeLng], {icon: officeIcon}).addTo(locationMap)
                .bindPopup(`<b>Lokasi Kantor</b><br>Radius: ${officeRadius}m`);

            // Fit bounds to show both locations
            const group = new L.featureGroup([
                locationMarker,
                L.marker([officeLat, officeLng])
            ]);
            locationMap.fitBounds(group.getBounds().pad(0.2));
        }

        // Recalculate map size
        setTimeout(() => {
            locationMap.invalidateSize();
        }, 100);
    }

    // Event listener untuk work type dropdown - selalu tampilkan lokasi untuk semua work type
    document.getElementById('work_type').addEventListener('change', function() {
        // Jika belum ada lokasi, request lokasi
        if (!currentLatitude || !currentLongitude) {
            requestLocationAndDisplay();
        } else {
            // Pastikan section terlihat untuk semua work type
            document.getElementById('locationGroup').style.display = 'block';
        }
    });

    // On load: langsung minta lokasi sekali untuk semua work type
    document.addEventListener('DOMContentLoaded', function() {
        requestLocationAndDisplay();
    });

    // Tombol refresh di atas map
    document.getElementById('refreshLocationBtn').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span> Refresh...</span>';

        requestLocationAndDisplay(true);

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }, 1500);
    });

    // Image source selection functionality
    const imageSourceModal = document.getElementById('imageSourceModal');
    const imageSelectBtn = document.getElementById('imageSelectBtn');
    const selectFromFileBtn = document.getElementById('selectFromFileBtn');
    const selectFromCameraBtn = document.getElementById('selectFromCameraBtn');
    const cancelSourceBtn = document.getElementById('cancelSourceBtn');
    const imageInput = document.getElementById('image');

    // Open image source selection modal
    imageSelectBtn.addEventListener('click', function() {
        imageSourceModal.classList.add('active');
    });

    // Select from file
    selectFromFileBtn.addEventListener('click', function() {
        imageSourceModal.classList.remove('active');
        imageInput.click();
    });

    // Select from camera
    selectFromCameraBtn.addEventListener('click', function() {
        imageSourceModal.classList.remove('active');
        openCamera();
    });

    // Cancel source selection
    cancelSourceBtn.addEventListener('click', function() {
        imageSourceModal.classList.remove('active');
    });

    // Close on outside click or overlay
    imageSourceModal.addEventListener('click', function(e) {
        if (e.target === imageSourceModal) {
            imageSourceModal.classList.remove('active');
        }
    });

    // Camera functionality
    let cameraStream = null;
    const cameraModal = document.getElementById('cameraModal');
    const cameraVideo = document.getElementById('cameraVideo');
    const cameraCanvas = document.getElementById('cameraCanvas');
    const cameraPreview = document.getElementById('cameraPreview');
    const cameraCancelBtn = document.getElementById('cameraCancelBtn');
    const cameraCaptureBtn = document.getElementById('cameraCaptureBtn');
    const cameraRetakeBtn = document.getElementById('cameraRetakeBtn');
    const cameraUseBtn = document.getElementById('cameraUseBtn');
    const cameraControls = document.getElementById('cameraControls');
    const previewControls = document.getElementById('previewControls');

    // Open camera modal
    function openCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire({
                icon: 'error',
                title: 'Kamera Tidak Tersedia',
                text: 'Browser Anda tidak mendukung akses kamera. Silakan gunakan opsi upload file.',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        cameraModal.classList.add('active');
        cameraVideo.style.display = 'block';
        cameraCanvas.style.display = 'none';
        cameraControls.style.display = 'flex';
        previewControls.style.display = 'none';

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire({
                icon: 'error',
                title: 'Kamera Tidak Tersedia',
                text: 'Browser Anda tidak mendukung akses kamera. Silakan gunakan opsi upload file.',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        cameraModal.classList.add('active');
        cameraVideo.style.display = 'block';
        cameraCanvas.style.display = 'none';
        cameraControls.style.display = 'flex';
        previewControls.style.display = 'none';

        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment', // Use back camera on mobile
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        })
        .then(function(stream) {
            cameraStream = stream;
            cameraVideo.srcObject = stream;
        })
        .catch(function(error) {
            console.error('Error accessing camera:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengakses Kamera',
                text: 'Pastikan Anda memberikan izin akses kamera atau gunakan opsi upload file.',
                confirmButtonColor: '#6366f1'
            });
            closeCamera();
        });
    }

    // Capture photo
    cameraCaptureBtn.addEventListener('click', function() {
        const context = cameraCanvas.getContext('2d');
        cameraCanvas.width = cameraVideo.videoWidth;
        cameraCanvas.height = cameraVideo.videoHeight;
        context.drawImage(cameraVideo, 0, 0);
        
        cameraVideo.style.display = 'none';
        cameraCanvas.style.display = 'block';
        cameraControls.style.display = 'none';
        previewControls.style.display = 'flex';
    });

    // Retake photo
    cameraRetakeBtn.addEventListener('click', function() {
        cameraVideo.style.display = 'block';
        cameraCanvas.style.display = 'none';
        cameraControls.style.display = 'flex';
        previewControls.style.display = 'none';
    });

    // Use captured photo
    cameraUseBtn.addEventListener('click', function() {
        cameraCanvas.toBlob(function(blob) {
            const file = new File([blob], 'camera-photo.jpg', { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            imageInput.files = dataTransfer.files;
            
            // Trigger preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `
                    <div style="position: relative; display: inline-block; margin-top: 0.75rem;">
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: auto; max-height: 12rem; border-radius: 0.5rem;">
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('image').value='';" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #f43f5e; color: white; border-radius: 9999px; width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                            <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }, 'image/jpeg', 0.9);
        
        closeCamera();
    });

    // Cancel camera
    cameraCancelBtn.addEventListener('click', function() {
        closeCamera();
    });

    // Close camera function
    function closeCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        cameraModal.classList.remove('active');
        cameraVideo.srcObject = null;
    }

    // Close on outside click
    cameraModal.addEventListener('click', function(e) {
        if (e.target === cameraModal) {
            closeCamera();
        }
    });

    // File input change handler
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `
                    <div style="position: relative; display: inline-block; margin-top: 0.75rem;">
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: auto; max-height: 12rem; border-radius: 0.5rem;">
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('image').value='';" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #f43f5e; color: white; border-radius: 9999px; width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                            <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('checkInBtn').addEventListener('click', function() {
        const workType = document.getElementById('work_type').value;
        
        if (!workType) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Silakan pilih jenis kerja terlebih dahulu!',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        // Pastikan lokasi sudah ada; jika belum, ambil dulu (untuk semua work type)
        if (!currentLatitude || !currentLongitude) {
            Swal.fire({
                title: 'Mengambil Lokasi',
                html: '<div style="text-align: center; padding: 12px 0;"><div class="swal2-loader" style="margin: 0 auto 12px;"></div><p style="margin-top: 8px; color: #6b7280; font-size: 14px; margin-bottom: 0;">Mohon izinkan akses lokasi untuk absensi</p></div>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                customClass: {
                    popup: 'swal2-loading-popup',
                    htmlContainer: 'swal2-loading-html'
                }
            });

            fetchLocation(true)
                .then(position => {
                    currentLatitude = position.coords.latitude;
                    currentLongitude = position.coords.longitude;
                    currentAccuracy = position.coords.accuracy;

                    return getLocationName(currentLatitude, currentLongitude)
                        .then(locationName => {
                            currentLocationName = locationName;
                            displayLocationWithMap(currentLatitude, currentLongitude, currentLocationName, currentAccuracy);
                        })
                        .catch(() => {
                            displayLocationWithMap(currentLatitude, currentLongitude, null, currentAccuracy);
                        });
                })
                .then(() => {
                    Swal.close();
                    // Untuk semua work type, langsung submit (validasi hanya di backend untuk WFO)
                    submitCheckIn(currentLatitude, currentLongitude, true);
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal Mengambil Lokasi',
                        html: '<p style="margin-bottom: 12px;">Tidak dapat mendapatkan lokasi.</p><p style="font-size: 13px; color: #6b7280;">Lanjutkan check-in tanpa lokasi?</p>',
                        showCancelButton: true,
                        confirmButtonColor: '#6366f1',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitCheckIn(null, null, true);
                        }
                    });
                });
        } else {
            // Lokasi sudah ada, langsung submit (validasi hanya di backend untuk WFO)
            submitCheckIn(currentLatitude, currentLongitude, true);
        }
    });

    // Fungsi untuk menghitung jarak (Haversine formula)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius bumi dalam km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; // Jarak dalam km
    }


    function submitCheckIn(latitude, longitude, locationValid = true) {
        const formData = new FormData();
        const workType = document.getElementById('work_type').value;
        formData.append('work_type', workType);
        formData.append('notes', document.getElementById('notes').value);
        
        // Simpan lokasi untuk semua work type (WFA, WFH, WFO)
        if (latitude && longitude) {
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
        }
        
        const imageFile = document.getElementById('image').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        Swal.fire({
            title: 'Memproses...',
            html: '<div style="display: flex; justify-content: center; align-items: center; padding: 20px 0;"><div class="swal2-loader"></div></div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        axios.post('{{ route('attendance.checkin') }}', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            let icon = 'success';
            let title = 'Berhasil!';
            let text = response.data.message;
            
            // Show warning if location is invalid HANYA untuk WFO
            // WFA dan WFH tidak perlu validasi lokasi
            if (workType === 'WFO' && (response.data.location_valid === false || locationValid === false)) {
                icon = 'warning';
                title = 'Peringatan!';
                text = 'Check-in berhasil! Namun lokasi Anda berada di luar jangkauan kantor. Hubungi admin jika Anda merasa salah.';
            }
            
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                timer: icon === 'warning' ? 5000 : 2000,
                showConfirmButton: icon === 'warning',
                confirmButtonColor: '#6366f1'
            }).then(() => {
                // Update button text to show "Check-In Lagi" after first check-in
                document.getElementById('checkInBtnText').textContent = 'Check-In Lagi';
                
                // Clear form for next check-in
                document.getElementById('notes').value = '';
                document.getElementById('image').value = '';
                
                // Reload to update status card
                location.reload();
            });
        })
        .catch(error => {
            let message = 'Terjadi kesalahan!';
            if (error.response && error.response.data && error.response.data.message) {
                message = error.response.data.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#6366f1'
            });
        });
    }

    document.getElementById('checkOutBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Yakin ingin check-out?',
            text: 'Pastikan Anda sudah menyelesaikan pekerjaan hari ini',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Check-Out',
            cancelButtonText: 'Batal',
            buttonsStyling: true,
            reverseButtons: false,
            focusConfirm: false,
            allowOutsideClick: false,
            allowEscapeKey: true,
            customClass: {
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    html: '<div style="display: flex; justify-content: center; align-items: center; padding: 20px 0;"><div class="swal2-loader"></div></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post('{{ route('attendance.checkout') }}')
                    .then(response => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            confirmButtonColor: '#6366f1'
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        let message = 'Terjadi kesalahan!';
                        if (error.response && error.response.data && error.response.data.message) {
                            message = error.response.data.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            confirmButtonColor: '#6366f1'
                        });
                    });
            }
        });
    });
</script>
@endsection