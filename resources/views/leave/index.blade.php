@extends('layouts.app')

@section('title', 'Perizinan - Sistem Absensi')

@section('styles')
<style>
    .leave-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 80vh;
        padding: 2rem 1rem;
    }
    .leave-wrapper {
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
    .form-control, .form-textarea {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .form-control:focus, .form-textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .form-textarea {
        resize: none;
    }
    .btn-submit {
        width: 100%;
        padding: 0.875rem 1rem;
        background: #6366f1;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s;
        margin-top: 1.5rem;
    }
    .btn-submit:hover:not(:disabled) {
        background: #4f46e5;
    }
    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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
<div class="leave-container">
    <div class="leave-wrapper">
        <div class="card-main">
            <div class="card-header">
                <div class="header-content">
                    <div class="icon-box">
                        <i class="fas fa-calendar-times" style="color: #6366f1;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #111827; margin: 0;">Ajukan Perizinan</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                            Isi formulir untuk mengajukan perizinan
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="leaveForm">
                    <div class="form-group">
                        <label for="leave_date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" 
                               class="form-control" 
                               id="leave_date_from" 
                               name="leave_date_from" 
                               required
                               min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="leave_date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" 
                               class="form-control" 
                               id="leave_date_to" 
                               name="leave_date_to" 
                               required
                               min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="leave_type" class="form-label">Jenis Izin</label>
                        <select class="form-control" id="leave_type" name="leave_type" required>
                            <option value="">Pilih jenis izin</option>
                            <option value="cuti">Cuti</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">Keterangan</label>
                        <textarea class="form-textarea" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Tambahkan keterangan (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="attachment" class="form-label">Lampiran Foto (Opsional)</label>
                        <input type="file" 
                               class="form-control" 
                               id="attachment" 
                               name="attachment" 
                               accept="image/*"
                               style="display: none;">
                        <button type="button" id="attachmentSelectBtn" class="btn-submit" style="margin-top: 0;">
                            <i class="fas fa-image"></i>
                            <span>Pilih Gambar</span>
                        </button>
                        <p style="font-size: 12px; color: #6b7280; margin-top: 6px;">
                            <i class="fas fa-info-circle"></i> 
                            Upload foto surat dokter atau dokumen pendukung (Max: 2MB)
                        </p>
                        <div id="attachmentPreview"></div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>Ajukan Perizinan</span>
                    </button>
                </form>
            </div>
        </div>
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
<script>
    // Image source selection functionality
    const imageSourceModal = document.getElementById('imageSourceModal');
    const attachmentSelectBtn = document.getElementById('attachmentSelectBtn');
    const selectFromFileBtn = document.getElementById('selectFromFileBtn');
    const selectFromCameraBtn = document.getElementById('selectFromCameraBtn');
    const cancelSourceBtn = document.getElementById('cancelSourceBtn');
    const attachmentInput = document.getElementById('attachment');

    // Open image source selection modal
    attachmentSelectBtn.addEventListener('click', function() {
        imageSourceModal.classList.add('active');
    });

    // Select from file
    selectFromFileBtn.addEventListener('click', function() {
        imageSourceModal.classList.remove('active');
        attachmentInput.click();
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
            attachmentInput.files = dataTransfer.files;
            
            // Trigger preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('attachmentPreview');
                preview.innerHTML = `
                    <div style="position: relative; display: inline-block; margin-top: 0.75rem;">
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: auto; max-height: 12rem; border-radius: 0.5rem;">
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('attachment').value='';" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #f43f5e; color: white; border-radius: 9999px; width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
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
    document.getElementById('attachment').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('attachmentPreview');
                preview.innerHTML = `
                    <div style="position: relative; display: inline-block; margin-top: 0.75rem;">
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: auto; max-height: 12rem; border-radius: 0.5rem;">
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''; document.getElementById('attachment').value='';" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #f43f5e; color: white; border-radius: 9999px; width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                            <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('leaveForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span> Mengajukan...</span>';

        const dateFrom = document.getElementById('leave_date_from').value;
        const dateTo = document.getElementById('leave_date_to').value;

        if (dateFrom && dateTo) {
            const from = new Date(dateFrom);
            const to = new Date(dateTo);
            
            if (from > to) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal!',
                    confirmButtonColor: '#6366f1'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                return;
            }
        }

        const formData = new FormData(this);

        axios.post('{{ route('leave.store') }}', formData)
            .then(response => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    confirmButtonColor: '#6366f1'
                }).then(() => {
                    this.reset();
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
    });
</script>
@endsection
