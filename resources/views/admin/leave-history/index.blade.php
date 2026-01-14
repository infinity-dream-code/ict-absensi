@extends('admin.layouts.app')

@section('title', 'History Perizinan')

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
    
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }
    
    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-input, .form-select {
        padding: 10px 14px;
        font-size: 14px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        outline: none;
    }
    
    .form-input:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .btn-filter {
        padding: 10px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .table-wrapper {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background: #f9fafb;
    }
    
    th {
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    td {
        padding: 16px;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }
    
    tbody tr:hover {
        background: #f9fafb;
    }
    
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge-cuti {
        background: #fef3c7;
        color: #92400e;
    }
    
    .badge-izin {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .badge-sakit {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .pagination-wrapper {
        padding: 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: center;
    }
    
    .empty-state {
        padding: 48px;
        text-align: center;
        color: #6b7280;
    }
    
    .btn-view-image {
        padding: 6px 12px;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    
    .btn-view-image:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    
    .image-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .image-modal-content {
        background-color: white;
        border-radius: 12px;
        width: 100%;
        max-width: 800px;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
    }
    
    .image-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .image-modal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #1f2937;
    }
    
    .image-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .image-modal-close:hover {
        background: #f3f4f6;
        color: #1f2937;
    }
    
    .image-modal-body {
        padding: 24px;
        overflow-y: auto;
        text-align: center;
    }
    
    .image-modal-body img {
        max-width: 100%;
        max-height: 70vh;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        .filter-form {
            grid-template-columns: 1fr;
        }
        
        .page-title {
            font-size: 24px;
        }
        
        table {
            font-size: 12px;
        }
        
        th, td {
            padding: 12px 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">History Perizinan</h1>
    <p class="page-subtitle">Riwayat perizinan semua karyawan</p>
</div>

<!-- Filter Card -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.leave-history.index') }}" class="filter-form">
        <div class="form-group">
            <label for="date_from" class="form-label">Dari Tanggal</label>
            <input type="date" 
                   id="date_from" 
                   name="date_from" 
                   value="{{ request('date_from') }}"
                   class="form-input">
        </div>
        
        <div class="form-group">
            <label for="date_to" class="form-label">Sampai Tanggal</label>
            <input type="date" 
                   id="date_to" 
                   name="date_to" 
                   value="{{ request('date_to') }}"
                   class="form-input">
        </div>
        
        <div class="form-group">
            <label for="year" class="form-label">Tahun</label>
            <select id="year" name="year" class="form-select">
                <option value="">Semua</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        
        <div class="form-group">
            <label for="month" class="form-label">Bulan</label>
            <select id="month" name="month" class="form-select">
                <option value="">Semua</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                    </option>
                @endfor
            </select>
        </div>
        
        <div class="form-group">
            <label for="leave_type" class="form-label">Jenis Izin</label>
            <select id="leave_type" name="leave_type" class="form-select">
                <option value="all" {{ request('leave_type') == 'all' || !request('leave_type') ? 'selected' : '' }}>Semua</option>
                <option value="cuti" {{ request('leave_type') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="izin" {{ request('leave_type') == 'izin' ? 'selected' : '' }}>Izin</option>
                <option value="sakit" {{ request('leave_type') == 'sakit' ? 'selected' : '' }}>Sakit</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="search" class="form-label">Cari (Nama/NIK)</label>
            <input type="text" 
                   id="search" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Cari nama atau NIK"
                   class="form-input">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i>
                <span>Filter</span>
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jenis Izin</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($leave->leave_date)->locale('id')->isoFormat('D MMM YYYY') }}</td>
                    <td style="font-weight: 600;">{{ $leave->user->nik }}</td>
                    <td>{{ $leave->user->name }}</td>
                    <td>
                        <span class="badge badge-{{ $leave->leave_type }}">
                            {{ ucfirst($leave->leave_type) }}
                        </span>
                    </td>
                    <td>{{ $leave->notes ?: '-' }}</td>
                    <td>
                        @if($leave->attachment)
                            <button type="button" 
                                    class="btn-view-image" 
                                    onclick="viewImage('{{ $leave->attachment }}', '{{ $leave->user->name }}')">
                                <i class="fas fa-image"></i> Foto
                            </button>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        Tidak ada data perizinan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($leaves->hasPages())
    <div class="pagination-wrapper">
        {{ $leaves->links() }}
    </div>
    @endif
</div>

<!-- Modal View Image -->
<div id="imageModal" class="image-modal">
    <div class="image-modal-content">
        <div class="image-modal-header">
            <h3>Lampiran Perizinan</h3>
            <button type="button" class="image-modal-close" onclick="closeImageModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="image-modal-body">
            <img id="imageModalImage" src="" alt="Lampiran Perizinan" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <div style="margin-top: 16px; padding: 16px; background: #f9fafb; border-radius: 8px;">
                <p style="margin: 0; font-size: 14px; color: #374151;"><strong>Nama:</strong> <span id="imageEmployeeName"></span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function viewImage(imageUrl, employeeName) {
        document.getElementById('imageEmployeeName').textContent = employeeName;
        document.getElementById('imageModalImage').src = imageUrl;
        document.getElementById('imageModal').style.display = 'flex';
    }
    
    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const imageModal = document.getElementById('imageModal');
        if (event.target == imageModal) {
            closeImageModal();
        }
    }
</script>
@endsection
