@extends('admin.layouts.app')

@section('title', 'Kelola Libur')

@section('styles')
<style>
    .page-header {
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
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
    
    .holiday-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
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
    
    .form-input, .form-select {
        width: 100%;
        padding: 12px 16px;
        font-size: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        outline: none;
    }
    
    .form-input:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
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
    
    .btn-delete {
        padding: 6px 12px;
        background: #ef4444;
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
    
    .btn-delete:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }
    
    .year-selector {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .year-selector select {
        padding: 8px 16px;
        font-size: 14px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
    }
    
    .empty-state {
        padding: 48px;
        text-align: center;
        color: #6b7280;
    }
    
    .btn-sync {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    
    .btn-sync:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }
    
    .btn-edit {
        padding: 6px 12px;
        background: #3b82f6;
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
        margin-right: 8px;
    }
    
    .btn-edit:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }
    
    .btn-save {
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
        margin-right: 8px;
    }
    
    .btn-save:hover {
        background: #059669;
    }
    
    .btn-cancel {
        padding: 6px 12px;
        background: #6b7280;
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
        margin-right: 8px;
    }
    
    .btn-cancel:hover {
        background: #4b5563;
    }
    
    .edit-input {
        width: 100%;
        padding: 8px 12px;
        font-size: 14px;
        border: 2px solid #3b82f6;
        border-radius: 6px;
    }
    
    @media (max-width: 768px) {
        .holiday-card {
            padding: 24px;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Kelola Libur</h1>
        <p class="page-subtitle">Atur tanggal libur nasional dan perusahaan</p>
    </div>
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <form method="POST" action="{{ route('admin.holiday.sync') }}" style="display: inline;">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn-sync" onclick="return confirm('Impor hari libur nasional tahun {{ $year }} dari API?')">
                <i class="fas fa-sync"></i>
                <span>Sync Libur Nasional</span>
            </button>
        </form>
        <div class="year-selector">
            <form method="GET" action="{{ route('admin.holiday.index') }}" style="display: flex; gap: 8px; align-items: center;">
                <label for="year" style="font-size: 14px; font-weight: 600; color: #374151;">Tahun:</label>
                <select name="year" id="year" onchange="this.form.submit()" style="padding: 8px 16px; font-size: 14px; border: 2px solid #e5e7eb; border-radius: 8px;">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
</div>

<!-- Add Holiday Card -->
<div class="holiday-card">
    <div class="form-section">
        <h3 class="section-title">
            <i class="fas fa-calendar-plus"></i>
            Tambah Libur
        </h3>
        <form action="{{ route('admin.holiday.store') }}" method="POST">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <div class="form-grid">
                <div class="form-group">
                    <label for="date" class="form-label">Tanggal</label>
                    <input type="date" 
                           id="date" 
                           name="date" 
                           class="form-input"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Keterangan</label>
                    <input type="text" 
                           id="description" 
                           name="description" 
                           class="form-input"
                           placeholder="Contoh: Hari Raya Idul Fitri"
                           required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="notes" class="form-label">Catatan (Opsional)</label>
                <textarea id="notes" 
                          name="notes" 
                          class="form-input"
                          rows="3"
                          placeholder="Tambahkan catatan tambahan jika diperlukan..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Libur</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Holiday List Card -->
<div class="table-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Keterangan</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($holidays as $holiday)
                <tr id="row-{{ $holiday->id }}">
                    <td>{{ \Carbon\Carbon::parse($holiday->date)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                    <td>{{ \Carbon\Carbon::parse($holiday->date)->locale('id')->isoFormat('dddd') }}</td>
                    <td>
                        <span id="desc-view-{{ $holiday->id }}">{{ $holiday->description }}</span>
                        <form id="desc-edit-{{ $holiday->id }}" style="display: none;" action="{{ route('admin.holiday.update', $holiday->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="description" class="edit-input" value="{{ $holiday->description }}" required>
                            <textarea name="notes" class="edit-input" rows="2" placeholder="Catatan (opsional)" style="margin-top: 8px;">{{ $holiday->notes }}</textarea>
                        </form>
                    </td>
                    <td>
                        <span id="notes-view-{{ $holiday->id }}" style="font-size: 13px; color: #6b7280;">{{ $holiday->notes ?? '-' }}</span>
                    </td>
                    <td>
                        <button type="button" class="btn-edit" id="btn-edit-{{ $holiday->id }}" onclick="editHoliday({{ $holiday->id }})">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button type="button" class="btn-save" id="btn-save-{{ $holiday->id }}" style="display: none;" onclick="saveHoliday({{ $holiday->id }})">
                            <i class="fas fa-save"></i>
                            Simpan
                        </button>
                        <button type="button" class="btn-cancel" id="btn-cancel-{{ $holiday->id }}" style="display: none;" onclick="cancelEdit({{ $holiday->id }})">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                        <form action="{{ route('admin.holiday.destroy', $holiday->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus libur ini?')"
                              style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">
                                <i class="fas fa-trash"></i>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-state">
                        Tidak ada libur untuk tahun {{ $year }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
let originalDescriptions = {};

function editHoliday(id) {
    // Save original description
    const descView = document.getElementById(`desc-view-${id}`);
    const notesView = document.getElementById(`notes-view-${id}`);
    originalDescriptions[id] = descView.textContent;
    
    // Toggle visibility
    descView.style.display = 'none';
    notesView.style.display = 'none';
    document.getElementById(`desc-edit-${id}`).style.display = 'block';
    document.getElementById(`btn-edit-${id}`).style.display = 'none';
    document.getElementById(`btn-save-${id}`).style.display = 'inline-flex';
    document.getElementById(`btn-cancel-${id}`).style.display = 'inline-flex';
}

function saveHoliday(id) {
    document.getElementById(`desc-edit-${id}`).submit();
}

function cancelEdit(id) {
    // Restore original description
    const descView = document.getElementById(`desc-view-${id}`);
    const notesView = document.getElementById(`notes-view-${id}`);
    const descEdit = document.getElementById(`desc-edit-${id}`);
    const input = descEdit.querySelector('input');
    
    input.value = originalDescriptions[id];
    
    // Toggle visibility
    descView.style.display = 'inline';
    notesView.style.display = 'inline';
    descEdit.style.display = 'none';
    document.getElementById(`btn-edit-${id}`).style.display = 'inline-flex';
    document.getElementById(`btn-save-${id}`).style.display = 'none';
    document.getElementById(`btn-cancel-${id}`).style.display = 'none';
}
</script>
@endsection
