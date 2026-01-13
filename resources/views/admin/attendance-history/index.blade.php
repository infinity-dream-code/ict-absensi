@extends('admin.layouts.app')

@section('title', 'History Absensi')

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
    
    .btn-export {
        padding: 10px 20px;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-export:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-export-monthly {
        padding: 10px 20px;
        background: #3b82f6;
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
    
    .btn-export-monthly:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
    
    .badge-wfa {
        background: #e0e7ff;
        color: #4338ca;
    }
    
    .badge-wfo {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .badge-wfh {
        background: #d1fae5;
        color: #065f46;
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
    
    .btn-view-location {
        padding: 6px 12px;
        background: #667eea;
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
    
    .btn-view-location:hover {
        background: #4f46e5;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
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
    
    .location-modal {
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
    
    .location-modal-content {
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
    
    .location-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .location-modal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #1f2937;
    }
    
    .location-modal-close {
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
    
    .location-modal-close:hover {
        background: #f3f4f6;
        color: #1f2937;
    }
    
    .location-modal-body {
        padding: 24px;
        overflow-y: auto;
    }
    
    .location-info {
        margin-top: 16px;
        padding: 16px;
        background: #f9fafb;
        border-radius: 8px;
    }
    
    .location-info p {
        margin: 8px 0;
        font-size: 14px;
        color: #374151;
    }
    
    .location-info strong {
        color: #1f2937;
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
    <h1 class="page-title">History Absensi</h1>
    <p class="page-subtitle">Riwayat absensi semua karyawan</p>
</div>

<!-- Filter Card -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.attendance-history.index') }}" class="filter-form">
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
            <label for="work_type" class="form-label">Jenis Kerja</label>
            <select id="work_type" name="work_type" class="form-select">
                <option value="all" {{ request('work_type') == 'all' || !request('work_type') ? 'selected' : '' }}>Semua</option>
                <option value="WFA" {{ request('work_type') == 'WFA' ? 'selected' : '' }}>WFA</option>
                <option value="WFO" {{ request('work_type') == 'WFO' ? 'selected' : '' }}>WFO</option>
                <option value="WFH" {{ request('work_type') == 'WFH' ? 'selected' : '' }}>WFH</option>
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
    
    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.attendance-history.export', request()->all()) }}" class="btn-export">
            <i class="fas fa-file-excel"></i>
            <span>Export Absensi</span>
        </a>
        <button type="button" class="btn-export-monthly" onclick="exportMonthly()">
            <i class="fas fa-file-excel"></i>
            <span>Rekap Absensi Bulanan</span>
        </button>
    </div>
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
                    <th>Jenis</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->locale('id')->isoFormat('D MMM YYYY') }}</td>
                    <td style="font-weight: 600;">{{ $attendance->user->nik }}</td>
                    <td>{{ $attendance->user->name }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($attendance->work_type) }}">
                            {{ $attendance->work_type }}
                        </span>
                    </td>
                    <td>
                        @if($attendance->check_in)
                            {{ \Carbon\Carbon::parse($attendance->check_in)->setTimezone('Asia/Jakarta')->format('H:i:s') }}
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($attendance->check_out)
                            {{ \Carbon\Carbon::parse($attendance->check_out)->setTimezone('Asia/Jakarta')->format('H:i:s') }}
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $settings = \App\Models\Setting::getSettings();
                            $checkInEnd = \Carbon\Carbon::parse($attendance->attendance_date->format('Y-m-d') . ' ' . ($settings->check_in_end ?: '09:00:00'), 'Asia/Jakarta');
                            $checkInTime = $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in, 'Asia/Jakarta') : null;
                        @endphp
                        @if($checkInTime && $checkInTime->gt($checkInEnd))
                            <span style="color: #dc2626; font-weight: 600;">Terlambat</span>
                        @elseif($checkInTime)
                            <span style="color: #059669; font-weight: 600;">Tepat Waktu</span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($attendance->latitude && $attendance->longitude)
                            @if($attendance->work_type === 'WFO')
                                @if($attendance->location_name)
                                    <span style="color: #374151; font-weight: 500;">{{ $attendance->location_name }}</span>
                                @else
                                    @if($attendance->location_valid)
                                        <span style="color: #059669; font-weight: 600;">✓ Valid</span>
                                    @else
                                        <span style="color: #dc2626; font-weight: 600;">✗ Di Luar Jangkauan</span>
                                    @endif
                                @endif
                            @else
                                {{-- WFA dan WFH: hanya tampilkan nama lokasi tanpa status valid --}}
                                @if($attendance->location_name)
                                    <span style="color: #374151; font-weight: 500;">{{ $attendance->location_name }}</span>
                                @else
                                    <span style="color: #6b7280;">Lokasi tersedia</span>
                                @endif
                            @endif
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            @if($attendance->latitude && $attendance->longitude)
                                <button type="button" 
                                        class="btn-view-location" 
                                        onclick="viewLocation({{ $attendance->latitude }}, {{ $attendance->longitude }}, '{{ $attendance->user->name }}', '{{ \Carbon\Carbon::parse($attendance->check_in)->setTimezone('Asia/Jakarta')->format('H:i:s') }}', '{{ $attendance->work_type }}', {{ $attendance->location_valid ? 'true' : 'false' }}, '{{ addslashes($attendance->location_name ?? '') }}')">
                                    <i class="fas fa-map-marker-alt"></i> Lokasi
                                </button>
                            @endif
                            @if($attendance->image)
                                <button type="button" 
                                        class="btn-view-image" 
                                        onclick="viewImage('{{ $attendance->image }}', '{{ $attendance->user->name }}')">
                                    <i class="fas fa-image"></i> Gambar
                                </button>
                            @endif
                            @if(!$attendance->latitude && !$attendance->longitude && !$attendance->image)
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="empty-state">
                        Tidak ada data absensi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($paginator->hasPages())
    <div class="pagination-wrapper">
        {{ $paginator->links() }}
    </div>
    @endif
</div>

<!-- Modal View Image -->
<div id="imageModal" class="location-modal">
    <div class="location-modal-content">
        <div class="location-modal-header">
            <h3 id="imageModalTitle">Gambar Absensi</h3>
            <button type="button" class="location-modal-close" onclick="closeImageModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="location-modal-body">
            <div style="text-align: center;">
                <img id="imageModalImage" src="" alt="Gambar Absensi" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            </div>
            <div class="location-info">
                <p><strong>Nama:</strong> <span id="imageEmployeeName"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Location -->
<div id="locationModal" class="location-modal">
    <div class="location-modal-content">
        <div class="location-modal-header">
            <h3 id="locationModalTitle">Lokasi Check-In</h3>
            <button type="button" class="location-modal-close" onclick="closeLocationModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="location-modal-body">
            <div id="locationMap" style="width: 100%; height: 400px; border-radius: 8px;"></div>
            <div class="location-info">
                <p><strong>Nama:</strong> <span id="locationEmployeeName"></span></p>
                <p><strong>Waktu Check-In:</strong> <span id="locationCheckInTime"></span></p>
                <p id="locationStatusContainer" style="display: none;"><strong>Status Lokasi:</strong> <span id="locationStatus"></span></p>
                <p id="locationNameContainer" style="display: none;"><strong>Lokasi Check-In:</strong> <span id="locationName"></span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let locationMap;
    let locationMarker;
    
    function viewLocation(lat, lng, employeeName, checkInTime, workType, locationValid, locationName) {
        document.getElementById('locationEmployeeName').textContent = employeeName;
        document.getElementById('locationCheckInTime').textContent = checkInTime;
        
        // Show location status only for WFO
        const locationStatusContainer = document.getElementById('locationStatusContainer');
        const locationStatusSpan = document.getElementById('locationStatus');
        if (workType === 'WFO') {
            locationStatusSpan.innerHTML = locationValid 
                ? '<span style="color: #059669; font-weight: 600;">✓ Valid</span>' 
                : '<span style="color: #dc2626; font-weight: 600;">✗ Di Luar Jangkauan</span>';
            locationStatusContainer.style.display = 'block';
        } else {
            // WFA dan WFH tidak perlu status valid
            locationStatusContainer.style.display = 'none';
        }
        
        // Show location name if available
        const locationNameContainer = document.getElementById('locationNameContainer');
        const locationNameSpan = document.getElementById('locationName');
        if (locationName && locationName.trim() !== '') {
            locationNameSpan.textContent = locationName;
            locationNameContainer.style.display = 'block';
        } else {
            locationNameContainer.style.display = 'none';
        }
        
        document.getElementById('locationModal').style.display = 'flex';
        
        // Initialize or update map
        if (locationMap) {
            locationMap.remove();
        }
        
        locationMap = L.map('locationMap').setView([lat, lng], 17);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(locationMap);
        
        // Add marker
        if (locationMarker) {
            locationMarker.remove();
        }
        
        // Marker color: green untuk valid/invalid (WFO), atau blue untuk WFA/WFH
        let markerColor = 'blue'; // default untuk WFA/WFH
        if (workType === 'WFO') {
            markerColor = locationValid ? 'green' : 'red';
        }
        
        locationMarker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${markerColor}.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(locationMap);
        
        let popupContent = `<div style="padding: 4px;">
                <strong>${employeeName}</strong><br>
                Check-In: ${checkInTime}`;
        
        // Hanya tampilkan status untuk WFO
        if (workType === 'WFO') {
            popupContent += `<br>Status: ${locationValid ? '✓ Valid' : '✗ Di Luar Jangkauan'}`;
        }
        
        if (locationName && locationName.trim() !== '') {
            popupContent += `<br>Lokasi: ${locationName}`;
        }
        popupContent += `</div>`;
        
        locationMarker.bindPopup(popupContent).openPopup();
    }
    
    function closeLocationModal() {
        document.getElementById('locationModal').style.display = 'none';
    }
    
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
        const locationModal = document.getElementById('locationModal');
        const imageModal = document.getElementById('imageModal');
        if (event.target == locationModal) {
            closeLocationModal();
        }
        if (event.target == imageModal) {
            closeImageModal();
        }
    }
    
    // Export Monthly Summary
    function exportMonthly() {
        const yearSelect = document.getElementById('year');
        const monthSelect = document.getElementById('month');
        const year = yearSelect ? yearSelect.value : '';
        const month = monthSelect ? monthSelect.value : '';
        
        // Bisa export meskipun "Semua" dipilih
        const url = '{{ route("admin.attendance-history.export-monthly") }}?year=' + (year || '') + '&month=' + (month || '');
        window.location.href = url;
    }
</script>
@endsection

