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

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>Ajukan Perizinan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
