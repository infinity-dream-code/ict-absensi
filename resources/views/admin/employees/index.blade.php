@extends('admin.layouts.app')

@section('title', 'Kelola Karyawan')

@section('styles')
<style>
    .page-header {
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
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
    
    .btn-add {
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-add:hover {
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
    
    .action-buttons {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .btn-action {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-edit {
        background: #eef2ff;
        color: #667eea;
    }
    
    .btn-edit:hover {
        background: #e0e7ff;
    }
    
    .btn-delete {
        background: #fef2f2;
        color: #dc2626;
    }
    
    .btn-delete:hover {
        background: #fee2e2;
    }
    
    .btn-reset {
        background: #fef3c7;
        color: #d97706;
    }
    
    .btn-reset:hover {
        background: #fde68a;
    }
    
    .empty-state {
        padding: 48px;
        text-align: center;
        color: #6b7280;
    }
    
    .pagination-wrapper {
        padding: 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: center;
    }
    
    /* Style pagination normally - hide large arrows */
    .pagination-wrapper .pagination {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination-wrapper .pagination li {
        display: inline-block;
    }
    
    .pagination-wrapper .pagination li a,
    .pagination-wrapper .pagination li span {
        display: inline-block;
        padding: 8px 12px;
        text-decoration: none;
        color: #374151;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
        min-width: 40px;
        text-align: center;
    }
    
    .pagination-wrapper .pagination li.active span,
    .pagination-wrapper .pagination li.active a {
        background: #667eea;
        color: white;
        border-color: #667eea;
        font-weight: 600;
    }
    
    .pagination-wrapper .pagination li a:hover:not(.disabled) {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    
    .pagination-wrapper .pagination li.disabled span,
    .pagination-wrapper .pagination li.disabled a {
        color: #9ca3af;
        cursor: not-allowed;
        background: #f9fafb;
        opacity: 0.6;
    }
    
    /* Hide large arrow icons - only show text */
    .pagination-wrapper .pagination li i,
    .pagination-wrapper .pagination li .fa,
    .pagination-wrapper .pagination li [class*="fa-"],
    .pagination-wrapper .pagination li svg,
    #paginationContainer i,
    #paginationContainer .fa,
    #paginationContainer [class*="fa-"],
    #paginationContainer svg {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        font-size: 0 !important;
    }
    
    /* Ensure text is visible */
    .pagination-wrapper .pagination li a,
    .pagination-wrapper .pagination li span,
    #paginationContainer .pagination li a,
    #paginationContainer .pagination li span {
        font-size: 14px !important;
        padding: 8px 12px !important;
    }
    
    /* Hide links that only contain arrows */
    #paginationContainer .pagination li a:only-child:empty,
    #paginationContainer .pagination li span:only-child:empty {
        display: none !important;
    }
    
    /* Pagination Footer */
    .pagination-footer {
        padding: 24px 32px;
        border-top: 1px solid #e5e7eb;
        background: #ffffff;
    }
    
    .pagination-info {
        color: #4b5563;
        font-size: 14px;
        font-weight: 600;
    }
    
    .pagination-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* HIDE ALL ICONS - AGGRESSIVE */
    #paginationContainer i,
    #paginationContainer .fa,
    #paginationContainer [class*="fa-"],
    #paginationContainer svg,
    #paginationContainer .icon,
    #paginationContainer [class*="icon"],
    #paginationContainer * i,
    #paginationContainer * .fa,
    #paginationContainer * [class*="fa-"],
    #paginationContainer * svg {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        font-size: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Hide Laravel default pagination info text */
    #paginationContainer .pagination-info,
    #paginationContainer .showing,
    #paginationContainer .text-sm,
    #paginationContainer > div:not(.pagination):not(ul) {
        display: none !important;
    }
    
    /* PAGINATION STYLING - CLEAN BUTTONS */
    #paginationContainer .pagination,
    #paginationContainer ul.pagination,
    #paginationContainer ul {
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }
    
    #paginationContainer .pagination li,
    #paginationContainer ul.pagination li,
    #paginationContainer ul li {
        display: inline-block !important;
        margin: 0 !important;
        list-style: none !important;
    }
    
    #paginationContainer .pagination li a,
    #paginationContainer .pagination li span,
    #paginationContainer ul.pagination li a,
    #paginationContainer ul.pagination li span,
    #paginationContainer ul li a,
    #paginationContainer ul li span {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 10px 16px !important;
        text-decoration: none !important;
        color: #374151 !important;
        border: 1.5px solid #d1d5db !important;
        border-radius: 8px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        transition: all 0.2s !important;
        background: #ffffff !important;
        min-width: 44px !important;
        height: 44px !important;
        line-height: 1 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        cursor: pointer !important;
    }
    
    /* ACTIVE PAGE */
    #paginationContainer .pagination li.active span,
    #paginationContainer .pagination li.active a,
    #paginationContainer ul.pagination li.active span,
    #paginationContainer ul.pagination li.active a,
    #paginationContainer ul li.active span,
    #paginationContainer ul li.active a {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: #ffffff !important;
        border-color: #667eea !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
    }
    
    /* HOVER */
    #paginationContainer .pagination li a:hover:not(.disabled):not(.active),
    #paginationContainer ul.pagination li a:hover:not(.disabled):not(.active),
    #paginationContainer ul li a:hover:not(.disabled):not(.active) {
        background: #f3f4f6 !important;
        border-color: #667eea !important;
        color: #667eea !important;
        box-shadow: 0 2px 6px rgba(102, 126, 234, 0.2) !important;
    }
    
    /* DISABLED */
    #paginationContainer .pagination li.disabled span,
    #paginationContainer .pagination li.disabled a,
    #paginationContainer ul.pagination li.disabled span,
    #paginationContainer ul.pagination li.disabled a,
    #paginationContainer ul li.disabled span,
    #paginationContainer ul li.disabled a {
        color: #9ca3af !important;
        cursor: not-allowed !important;
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        opacity: 0.5 !important;
        box-shadow: none !important;
        pointer-events: none !important;
    }
    
    /* PREVIOUS/NEXT */
    #paginationContainer .pagination li:first-child a,
    #paginationContainer .pagination li:first-child span,
    #paginationContainer .pagination li:last-child a,
    #paginationContainer .pagination li:last-child span,
    #paginationContainer ul.pagination li:first-child a,
    #paginationContainer ul.pagination li:first-child span,
    #paginationContainer ul.pagination li:last-child a,
    #paginationContainer ul.pagination li:last-child span,
    #paginationContainer ul li:first-child a,
    #paginationContainer ul li:first-child span,
    #paginationContainer ul li:last-child a,
    #paginationContainer ul li:last-child span {
        min-width: 90px !important;
    }
    
    /* Content styles */
    .content-header {
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .content-title {
        font-size: 32px;
        font-weight: bold;
        color: #1f2937;
        margin: 0 0 8px 0;
    }
    
    .content-subtitle {
        font-size: 16px;
        color: #6b7280;
        margin: 0;
    }
    
    .table-row {
        transition: background 0.2s;
    }
    
    .table-row:hover {
        background: #f9fafb;
    }
    
    .empty-state-cell {
        padding: 48px;
        text-align: center;
        color: #6b7280;
    }
    
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
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
        
        .content-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .pagination-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div>
        <h1 class="content-title">Kelola Karyawan</h1>
        <p class="content-subtitle">Daftar semua karyawan</p>
    </div>
    <a href="{{ route('admin.employees.create') }}" class="btn-add">
        <i class="fas fa-plus"></i>
        <span>Tambah Karyawan</span>
    </a>
</div>

<div class="table-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                <tr class="table-row">
                    <td style="font-weight: 600;">{{ $employee->nik }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('admin.employees.reset-password', $employee) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin mereset password karyawan ini menjadi 123456?')">
                                @csrf
                                <button type="submit" class="btn-action btn-reset">
                                    <i class="fas fa-key"></i>
                                    <span>Reset Password</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="empty-state-cell">
                        Tidak ada data karyawan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($employees->hasPages())
    <div class="pagination-footer">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div class="pagination-info">
                Menampilkan {{ $employees->firstItem() }} sampai {{ $employees->lastItem() }} dari {{ $employees->total() }} hasil
            </div>
            <div id="paginationContainer" class="pagination-container">
                {{ $employees->links('vendor.pagination.default') }}
            </div>
        </div>
    </div>
    <script>
    (function() {
        function cleanPagination() {
            const container = document.getElementById('paginationContainer');
            if (!container) return;
            
            // HIDE Laravel default pagination info text (duplikasi)
            container.querySelectorAll('div, p, span').forEach(el => {
                const text = (el.textContent || '').trim();
                // Hide if contains "Showing" or "results" but not our custom info
                if ((text.includes('Showing') || text.includes('results') || 
                     (text.includes('Menampilkan') && !el.closest('.pagination-info'))) &&
                    !el.classList.contains('pagination-info') && 
                    !el.closest('.pagination-info') &&
                    !el.closest('li') &&
                    !el.closest('a')) {
                    el.style.display = 'none';
                }
            });
            
            // Remove ALL icon elements
            container.querySelectorAll('i, .fa, [class*="fa-"], svg, .icon, [class*="icon"]').forEach(icon => {
                icon.remove();
            });
            
            // Clean ALL pagination links - remove arrows and icons
            container.querySelectorAll('a, span').forEach(el => {
                // Skip if it's not a pagination link
                if (!el.closest('li') && !el.closest('.pagination') && !el.closest('ul')) {
                    return;
                }
                
                let text = (el.textContent || el.innerText || '').trim();
                
                // Remove arrow symbols
                text = text.replace(/[«»‹›←→]/g, '').trim();
                text = text.replace(/&laquo;|&raquo;|&lsaquo;|&rsaquo;|&larr;|&rarr;/g, '').trim();
                
                // Clean HTML
                let html = el.innerHTML || '';
                html = html.replace(/<i[^>]*>.*?<\/i>/gi, '');
                html = html.replace(/<svg[^>]*>.*?<\/svg>/gi, '');
                html = html.replace(/<span[^>]*class[^>]*fa[^>]*>.*?<\/span>/gi, '');
                html = html.replace(/[«»‹›←→]/g, '');
                html = html.replace(/&laquo;|&raquo;|&lsaquo;|&rsaquo;|&larr;|&rarr;/g, '');
                html = html.trim();
                
                // Hide if only arrows
                if (!text || /^[«»‹›←→\s]+$/.test(text)) {
                    el.style.display = 'none';
                } else {
                    el.textContent = text;
                    el.innerHTML = text;
                }
            });
            
            // Force apply button styles
            container.querySelectorAll('.pagination li a, .pagination li span, ul li a, ul li span').forEach(el => {
                if (el.closest('li.disabled')) return;
                
                if (!el.closest('li.active')) {
                    el.style.cssText = `
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        padding: 10px 16px !important;
                        text-decoration: none !important;
                        color: #374151 !important;
                        border: 1.5px solid #d1d5db !important;
                        border-radius: 8px !important;
                        font-size: 14px !important;
                        font-weight: 600 !important;
                        background: #ffffff !important;
                        min-width: 44px !important;
                        height: 44px !important;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
                        cursor: pointer !important;
                        transition: all 0.2s !important;
                    `;
                }
            });
        }
        
        // Run immediately and multiple times
        cleanPagination();
        setTimeout(cleanPagination, 50);
        setTimeout(cleanPagination, 150);
        setTimeout(cleanPagination, 300);
        setTimeout(cleanPagination, 600);
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                cleanPagination();
                setTimeout(cleanPagination, 100);
            });
        }
        
        // MutationObserver for dynamic changes
        const container = document.getElementById('paginationContainer');
        if (container && window.MutationObserver) {
            const observer = new MutationObserver(() => {
                cleanPagination();
            });
            observer.observe(container, { 
                childList: true, 
                subtree: true,
                characterData: true
            });
        }
    })();
    </script>
    @endif
</div>
@endsection
