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
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-state">
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
@endsection
