@extends('layouts.app')

@section('page_title', 'Detail Data')
@section('page_subtitle', $implementasi->instansi->nama_instansi ?? 'Koperasi')

@section('topbar_right')
    <a href="{{ route('implementasi.index') }}" style="background-color: #64748b; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: background 0.2s;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
        {{ __('messages.btn_kembali') }}
    </a>
@endsection

@section('content')
<style>
    /* Styling Dasar & Header */
    .checklist-input::-webkit-scrollbar {
        width: 5px;
    }
    .checklist-input::-webkit-scrollbar-track {
        background: transparent;
    }
    .checklist-input::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .checklist-input::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .detail-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .impl-number {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
    }
    
    /* Dynamic Badge Sama dengan Index */
    .badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-abu { background-color: #e2e8f0; color: #475569; }
    .badge-biru { background-color: #dbeafe; color: #1d4ed8; }
    .badge-kuning { background-color: #fef08a; color: #854d0e; }
    .badge-oranye { background-color: #ffedd5; color: #c2410c; }
    .badge-merah { background-color: #fee2e2; color: #b91c1c; }
    .badge-hijau { background-color: #dcfce3; color: #166534; }

    /* Sub-Sidebar Layout (Master Data Style) */
    .md-layout {
        display: flex;
        align-items: stretch;
        gap: 1.5rem;
        margin-top: 20px;
    }
    .md-sidebar {
        width: 240px;
        flex-shrink: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .md-tab-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 8px;
        background: transparent;
        border: none;
        color: #64748b;
        font-family: inherit;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        width: 100%;
    }
    .md-tab-btn:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .md-tab-btn.active {
        background: #eff6ff;
        color: #2563eb;
        position: relative;
    }
    .md-tab-btn.active::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 50%;
        background: #2563eb;
        border-radius: 0 4px 4px 0;
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    .tab-content.active {
        display: block;
    }
    @media (max-width: 768px) {
        .md-layout {
            flex-direction: column;
        }
        .md-sidebar {
            width: 100%;
            flex-direction: row;
            overflow-x: auto;
            white-space: nowrap;
            padding: 6px;
            gap: 6px;
            box-sizing: border-box;
            -webkit-overflow-scrolling: touch;
            align-items: center;
            max-width: 100%;
            min-width: 0;
        }
        .md-sidebar::-webkit-scrollbar {
            height: 3px;
        }
        .md-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .md-tab-btn {
            width: auto;
            flex-shrink: 0;
            padding: 8px 14px;
            font-size: 13px;
            white-space: nowrap;
            justify-content: center;
        }
        .md-tab-btn svg {
            display: none !important;
        }
        .md-tab-btn.active::before {
            display: none;
        }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Grid Ringkasan */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    @media (max-width: 992px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 576px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
    .summary-item {
        background: #f8fafc;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .summary-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .summary-value {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
    }

    /* Checklist Table */
    .checklist-table {
        width: 100%;
        border-collapse: collapse;
    }
    .checklist-table th, .checklist-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .checklist-table th {
        background: #f8fafc;
        font-weight: 600;
        font-size: 14px;
    }
    .checklist-select {
        padding: 6px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        width: 100%;
    }
    .checklist-input {
        padding: 6px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        width: 100%;
    }

    /* Timeline Log */
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #2563eb;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #2563eb;
    }
    .timeline-time {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .timeline-content {
        background: #f8fafc;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: var(--sage-soft, #d1fae5);
        color: var(--sage, #065f46);
        border: 1px solid rgba(46, 125, 82, 0.2);
        padding: 12px 14px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(20px);
        opacity: 0;
        transition: opacity 0.6s ease, transform 0.6s ease;
        z-index: 9999;
        font-size: calc(13.5px * var(--text-scale, 1));
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-width: 250px;
    }
    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    /* Progress bar in header */
    .progress-wrapper {
        flex-grow: 1;
        max-width: 300px;
    }
    .progress-bar-bg {
        width: 100%;
        background-color: #e2e8f0;
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background-color: #2563eb;
        transition: width 0.5s ease-in-out;
    }

    /* Dark Mode Adjustments */
    .dark-mode .detail-card, .dark-mode .summary-item, .dark-mode .timeline-content, .dark-mode .md-sidebar {
        background: #1e293b;
        border-color: #334155;
    }
    .dark-mode .impl-number, .dark-mode .summary-value { color: #f8fafc; }
    .running-kategori-td { background: #f8fafc; border-right: 1px solid #e2e8f0; color: #1e293b; }
    .dark-mode .running-kategori-td { background: #0f172a !important; color: #f8fafc !important; border-color: #334155 !important; }
    .dark-mode .checklist-table th { background: #0f172a; color: #f8fafc; border-color: #334155; }
    .dark-mode .checklist-table td, .dark-mode .tabs-nav { border-color: #334155; }
    .dark-mode .checklist-input, .dark-mode .checklist-select { background: #0f172a; color: white; border-color: #334155; }
    .dark-mode .timeline::before { background: #334155; }
    .dark-mode .progress-bar-bg { background-color: #334155; }
    
    /* Dark mode for 7-Tab System */
    .dark-mode .md-sidebar { box-shadow: 0 1px 3px rgba(0,0,0,0.3); }
    .dark-mode .md-tab-btn { color: #94a3b8; }
    .dark-mode .md-tab-btn:hover { background: #334155; color: #f8fafc; }
    .dark-mode .md-tab-btn.active { background: #0f172a; color: #60a5fa; }

    /* Responsive Form Grids */
    .grid-2-col { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
    .grid-3-col { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
    .grid-3-2-col { display: grid; grid-template-columns: 3fr 2fr; gap: 15px; }
    
    @media (max-width: 768px) {
        .dark-mode .md-sidebar::-webkit-scrollbar-thumb { background: #475569; }
        .grid-2-col, .grid-3-col, .grid-3-2-col { grid-template-columns: 1fr; }
    }

    /* Forms and Containers */
    .tab-pane-inner { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; }
    .warning-banner { background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 8px 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 8px; background: white; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
    
    .dark-mode .tab-pane-inner { background: #1e293b; border-color: #334155; }
    .dark-mode .warning-banner { background-color: #451a03; border-color: #78350f; }
    .dark-mode .warning-banner span { color: #fcd34d !important; }
    .dark-mode .checkbox-grid { background: #0f172a; border-color: #334155; color: #f8fafc; }
    
    .dark-mode .form-control { background: #0f172a !important; color: #f8fafc !important; border-color: #334155 !important; }
    .dark-mode .form-control[readonly], .dark-mode .form-control:disabled { background: #1e293b !important; color: #94a3b8 !important; border-color: #334155 !important; }

</style>

@php
    $statusText = strtolower($implementasi->status);
    $badgeClass = 'badge-abu';
    if (str_contains($statusText, 'dijadwalkan')) $badgeClass = 'badge-biru';
    elseif (str_contains($statusText, 'menunggu')) $badgeClass = 'badge-oranye';
    elseif (str_contains($statusText, 'proses') || str_contains($statusText, 'persiapan') || str_contains($statusText, 'pendampingan')) $badgeClass = 'badge-kuning';
    elseif (str_contains($statusText, 'revisi') || str_contains($statusText, 'hold') || str_contains($statusText, 'dibatalkan')) $badgeClass = 'badge-merah';
    elseif (str_contains($statusText, 'selesai') || str_contains($statusText, 'stabil') || str_contains($statusText, 'go-live')) $badgeClass = 'badge-hijau';
@endphp

<!-- Toast Notification -->
<div id="toast" class="toast">
    <span id="toast-message">Berhasil disimpan!</span>
    <button type="button" onclick="document.getElementById('toast').classList.remove('show')" style="background: none; border: none; color: var(--sage, #065f46); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
</div>

<!-- Modal Konfirmasi Done Custom -->
<style>
    @keyframes fadeUpDoneModal {
        0% {
            opacity: 0;
            transform: translateY(35px) scale(0.94);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>
<div class="modal-overlay" id="modalConfirmDone" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; border-radius: 12px; width: 90%; max-width: 380px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); text-align: center; animation: fadeUpDoneModal 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        <div style="width: 52px; height: 52px; background: #d1fae5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <h4 style="margin: 0 0 8px; font-size: 17px; font-weight: 700; color: #1e293b;">Konfirmasi Selesai</h4>
        <p id="confirm-done-text" style="margin: 0 0 24px; font-size: 13.5px; color: #64748b; line-height: 1.5;">Apakah Anda yakin ingin menandai item checklist ini sebagai <strong>Sudah Valid / Done</strong>?</p>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" onclick="closeDoneModal()" style="flex: 1; padding: 9px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; cursor: pointer; font-size: 13px;">Batal</button>
            <button type="button" id="btn-confirm-done" style="flex: 1; padding: 9px 16px; border-radius: 6px; border: none; background: #10b981; color: #fff; font-weight: 600; cursor: pointer; font-size: 13px; box-shadow: 0 2px 4px rgba(16,185,129,0.2);">Ya, Selesai</button>
        </div>
    </div>
</div>

<!-- Modal Kelola / Hapus Checklist -->
<div class="modal-overlay" id="modalKelolaChecklist" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; border-radius: 12px; width: 90%; max-width: 550px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); animation: fadeUpDoneModal 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b;">{{ __('messages.kelola_item_checklist') }}</h4>
            <button type="button" onclick="closeKelolaModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div style="padding: 20px; overflow-y: auto; flex-grow: 1;">

            <div id="kelola-checklist-list" style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($implementasi->checklists as $chk)
                <div id="modal-item-{{ $chk->id }}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div>
                        <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; margin-right: 6px;">{{ $chk->kategori ?? 'Umum' }}</span>
                        <span style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $chk->nama_item }}</span>
                    </div>
                    <button type="button" onclick="deleteChecklist({{ $chk->id }}, '{{ addslashes($chk->nama_item) }}')" style="background: #ef4444; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;" title="Hapus Item Ini">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        {{ __('messages.btn_hapus') }}
                    </button>
                </div>
                @endforeach
            </div>

            <!-- Tambah Item Kustom -->
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">
                <h5 style="margin: 0 0 10px; font-size: 13px; font-weight: 600; color: #475569;">{{ __('messages.tambah_item_kustom') }}</h5>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="new-item-kategori" placeholder="{{ __('messages.kategori') }}" style="width: 110px; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                    <input type="text" id="new-item-nama" placeholder="{{ __('messages.nama_item_checklist') }}" style="flex-grow: 1; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                    <button type="button" onclick="addCustomChecklist({{ $implementasi->id }})" style="background: #2563eb; color: #fff; border: none; padding: 7px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; white-space: nowrap;">{{ __('messages.btn_tambah') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kelola / Hapus Migrasi Data -->
<div class="modal-overlay" id="modalKelolaMigrasi" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; border-radius: 12px; width: 90%; max-width: 550px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); animation: fadeUpDoneModal 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;">{{ __('messages.kelola_item_migrasi') }}</h4>
            <button type="button" onclick="closeKelolaMigrasiModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div style="padding: 20px; overflow-y: auto; flex-grow: 1;">

            <div id="kelola-migrasi-list" style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($implementasi->checklists->where('kategori', 'Migrasi') as $chk)
                <div id="modal-item-{{ $chk->id }}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div>
                        <span style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $chk->nama_item }}</span>
                    </div>
                    <button type="button" onclick="deleteChecklist({{ $chk->id }}, '{{ addslashes($chk->nama_item) }}')" style="background: #ef4444; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;" title="Hapus Item Ini">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        {{ __('messages.btn_hapus') }}
                    </button>
                </div>
                @endforeach
            </div>

            <!-- Tambah Item Migrasi Kustom -->
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">
                <h5 style="margin: 0 0 10px; font-size: 13px; font-weight: 600; color: #475569;">{{ __('messages.tambah_migrasi_kustom') }}</h5>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="new-migrasi-nama" placeholder="{{ __('messages.nama_item_migrasi') }}" style="flex-grow: 1; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                    <button type="button" onclick="addCustomMigrasi({{ $implementasi->id }})" style="background: #2563eb; color: #fff; border: none; padding: 7px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; white-space: nowrap;">{{ __('messages.btn_tambah') }}</button>
                </div>
            </div>
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            <button type="button" onclick="closeKelolaMigrasiModal()" style="padding: 7px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; cursor: pointer; font-size: 13px;">{{ __('messages.btn_selesai') }}</button>
        </div>
    </div>
</div>

<!-- Modal Kelola / Hapus Running Monitoring -->
<div class="modal-overlay" id="modalKelolaRunning" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; border-radius: 12px; width: 90%; max-width: 550px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); animation: fadeUpDoneModal 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;">Kelola Item Running Monitoring</h4>
            <button type="button" onclick="closeKelolaRunningModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div style="padding: 20px; overflow-y: auto; flex-grow: 1;">

            <div id="kelola-running-list" style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($implementasi->checklists->filter(function($item) { return str_starts_with($item->kategori ?? '', 'Running'); }) as $chk)
                <div id="modal-item-{{ $chk->id }}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div>
                        <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; margin-right: 6px;">{{ str_replace('Running - ', '', $chk->kategori) }}</span>
                        <span style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $chk->nama_item }}</span>
                    </div>
                    <button type="button" onclick="deleteChecklist({{ $chk->id }}, '{{ addslashes($chk->nama_item) }}')" style="background: #ef4444; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;" title="Hapus Item Ini">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        {{ __('messages.btn_hapus') }}
                    </button>
                </div>
                @endforeach
            </div>

            <!-- Tambah Item Running Kustom -->
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #cbd5e1;">
                <h5 style="margin: 0 0 10px; font-size: 13px; font-weight: 600; color: #475569;">Tambah Item Running Monitoring</h5>
                <div style="display: flex; gap: 8px;">
                    <select id="new-running-kategori" style="width: 140px; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; font-family: inherit;">
                        <option value="Running - Aplikasi">Aplikasi</option>
                        <option value="Running - Penggunaan Aplikasi">Penggunaan Aplikasi</option>
                        <option value="Running - Transaksi">Transaksi</option>
                        <option value="Running - Laporan">Laporan</option>
                        <option value="Running - Kemandirian">Kemandirian</option>
                    </select>
                    <input type="text" id="new-running-nama" placeholder="Nama Item Running Status..." style="flex-grow: 1; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                    <button type="button" onclick="addCustomRunning({{ $implementasi->id }})" style="background: #2563eb; color: #fff; border: none; padding: 7px 14px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 600; white-space: nowrap;">{{ __('messages.btn_tambah') }}</button>
                </div>
            </div>
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            <button type="button" onclick="closeKelolaRunningModal()" style="padding: 7px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; cursor: pointer; font-size: 13px;">{{ __('messages.btn_selesai') }}</button>
        </div>
    </div>
</div>

<div style="max-width: 1280px; margin: 0 auto; padding: 20px 30px;">
@if(session('success'))
    <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 24px; font-size: 13.5px; font-weight: 600; border: 1px solid #34d399; transition: opacity 0.6s ease, transform 0.6s ease;">
        <span>{{ session('success') }}</span>
        <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 18px; font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
    </div>
@endif

<div class="detail-card">
    <div class="detail-header">
        <div>
            <div class="impl-number">{{ $implementasi->nomor_implementasi }}</div>
            <div style="font-size: 14px; color: #64748b; margin-top: 5px;">
                <strong>{{ $implementasi->instansi->nama_instansi ?? 'Koperasi' }}</strong>
                @if($implementasi->kantor_cabang)
                    <span style="font-size: 12px; color: #475569; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline; margin-bottom: -2px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        {{ $implementasi->kantor_cabang }}
                    </span>
                @endif
                <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px;">
                    @if($implementasi->aplikasis && $implementasi->aplikasis->count() > 0)
                        @foreach($implementasi->aplikasis as $appItem)
                            <span style="display: inline-block; padding: 2px 8px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; border-radius: 4px; font-size: 12px; font-weight: 500;">{{ $appItem->nama_aplikasi }}</span>
                        @endforeach
                    @else
                        <span style="display: inline-block; padding: 2px 8px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; border-radius: 4px; font-size: 12px; font-weight: 500;">{{ $implementasi->aplikasi->nama_aplikasi ?? 'Aplikasi' }}</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="progress-wrapper">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px; font-weight: 600;">
                <span>{{ __('messages.progres_kesiapan') }}</span>
                <span id="progres-text">{{ $implementasi->progres }}%</span>
            </div>
            <div class="progress-bar-bg">
                <div id="progres-fill" class="progress-bar-fill" style="width: {{ $implementasi->progres }}%;"></div>
            </div>
        </div>

        <div>
            <span class="badge {{ $badgeClass }}">{{ $implementasi->status }}</span>
        </div>
    </div>
</div>

<div class="md-layout">
    <!-- SUB-SIDEBAR (Menu Kiri) -->
    <div class="md-sidebar">
        <button class="md-tab-btn active" onclick="openTab('tab-ringkasan', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            {{ __('messages.tab_ringkasan') }}
        </button>
        <button class="md-tab-btn" onclick="openTab('tab-checklist', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            {{ __('messages.tab_checklist_kesiapan') }}
        </button>
        <button class="md-tab-btn" onclick="openTab('tab-cut-off', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            {{ __('messages.tab_tgl_cutoff') }}
        </button>
        <button class="md-tab-btn" onclick="openTab('tab-migrasi', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            {{ __('messages.tab_migrasi_data') }}
        </button>
        <button class="md-tab-btn" onclick="openTab('tab-followup', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            {{ __('messages.tab_aktivitas_followup') }}
        </button>
        <button class="md-tab-btn" onclick="openTab('tab-target-golive', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
            {{ __('messages.tab_go_live') }}
        </button>
        <button class="md-tab-btn" onclick="openTab('tab-running', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-6M6 20V10M18 20V4"></path></svg>
            Running Status
        </button>
        <button class="md-tab-btn" onclick="openTab('tab-aktivitas', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            {{ __('messages.tab_aktivitas_log') }}
        </button>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column;">
        <div class="detail-card" style="margin-top: 0; flex-grow: 1;">

    <!-- TAB 1: RINGKASAN -->
    <div id="tab-ringkasan" class="tab-content active">
        <div class="summary-grid">
            <div class="summary-item" style="position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div class="summary-label">{{ __('messages.ringkasan_golive') }}</div>
                    <div style="position: relative;">
                        <button onclick="document.getElementById('golive-dropdown').style.display = document.getElementById('golive-dropdown').style.display === 'none' ? 'block' : 'none';" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle><circle cx="5" cy="12" r="1.5"></circle></svg>
                        </button>
                        <div id="golive-dropdown" style="display: none; position: absolute; right: 0; top: 100%; background: white; border: 1px solid #e2e8f0; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 10; min-width: 120px;">
                            <a href="#" onclick="event.preventDefault(); document.getElementById('golive-dropdown').style.display = 'none'; openTab('tab-target-golive', document.querySelector('[onclick*=\'tab-target-golive\']'))" style="display: block; padding: 10px 14px; color: #475569; text-decoration: none; font-size: 13px; font-weight: 500; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='transparent'">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    Edit
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="summary-value">{{ $implementasi->target_go_live ? $implementasi->target_go_live->format('d M Y') : 'Belum Ditentukan' }}</div>
            </div>
            <div class="summary-item" style="position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div class="summary-label">{{ __('messages.ringkasan_tgl_cutoff') }}</div>
                    <div style="position: relative;">
                        <button onclick="document.getElementById('cutoff-dropdown').style.display = document.getElementById('cutoff-dropdown').style.display === 'none' ? 'block' : 'none';" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle><circle cx="5" cy="12" r="1.5"></circle></svg>
                        </button>
                        <div id="cutoff-dropdown" style="display: none; position: absolute; right: 0; top: 100%; background: white; border: 1px solid #e2e8f0; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 10; min-width: 120px;">
                            <a href="#" onclick="event.preventDefault(); document.getElementById('cutoff-dropdown').style.display = 'none'; openTab('tab-cut-off', document.querySelector('[onclick*=\'tab-cut-off\']'))" style="display: block; padding: 10px 14px; color: #475569; text-decoration: none; font-size: 13px; font-weight: 500; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f1f5f9'" onmouseout="this.style.backgroundColor='transparent'">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    Edit
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="summary-value">{{ $implementasi->tanggal_cut_off ? $implementasi->tanggal_cut_off->format('d M Y') : 'Belum Ditentukan' }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('messages.ringkasan_tgl_pelatihan') }}</div>
                <div class="summary-value">
                    @if($implementasi->tanggal_pelatihan)
                        {{ $implementasi->tanggal_pelatihan->format('d M Y') }}
                        @if($implementasi->tanggal_selesai && $implementasi->tanggal_selesai->format('Y-m-d') !== $implementasi->tanggal_pelatihan->format('Y-m-d'))
                            - {{ $implementasi->tanggal_selesai->format('d M Y') }}
                        @endif
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="summary-item" style="border-left: 3px solid #2563eb; background: var(--paper-raised, #fff);">
                <div class="summary-label" style="color: #2563eb; font-weight: 700;">Masa Berlaku Tim Support</div>
                <div class="summary-value" style="font-size: 13px;">
                    @if($implementasi->tanggal_mulai_support || $implementasi->tanggal_selesai_support)
                        <div style="font-weight: 600; color: #1e293b;">
                            {{ $implementasi->tanggal_mulai_support ? $implementasi->tanggal_mulai_support->format('d M Y') : 'Awal' }}
                            &nbsp;&rarr;&nbsp;
                            <span style="color: #2563eb;">{{ $implementasi->tanggal_selesai_support ? $implementasi->tanggal_selesai_support->format('d M Y') : 'Selesai' }}</span>
                        </div>
                    @endif
                    @if($implementasi->durasi_support)
                        <div style="font-size: 12px; color: #64748b; font-weight: 500; margin-top: 2px;">
                            Durasi: <strong>{{ $implementasi->durasi_support }}</strong>
                        </div>
                    @endif
                    @if(!$implementasi->tanggal_mulai_support && !$implementasi->tanggal_selesai_support && !$implementasi->durasi_support)
                        <span style="color: #94a3b8;">Belum Ditentukan</span>
                    @endif
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('messages.ringkasan_berita_acara') }}</div>
                <div class="summary-value">
                    @if($implementasi->berita_acara)
                        <a href="{{ Storage::url($implementasi->berita_acara) }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-weight: 500;">Lihat PDF</a>
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="summary-item" style="max-width: 100%; word-break: break-word;">
                <div class="summary-label">{{ __('messages.ringkasan_anggota_hadir') }}</div>
                <div class="summary-value">
                    @if($implementasi->anggota_hadir)
                        <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px;">
                            @foreach(explode(',', $implementasi->anggota_hadir) as $anggotaItem)
                                <span style="display: inline-block; padding: 2px 7px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 11.5px; font-weight: 500;">{{ trim($anggotaItem) }}</span>
                            @endforeach
                        </div>
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('messages.ringkasan_kontak_pic') }}</div>
                <div class="summary-value">
                    <div style="font-weight:400; font-size:12px;">
                        <div style="margin-bottom: 8px;">
                            @if($implementasi->kontak_pic)
                                @php
                                    $wa_numbers = array_filter(array_map('trim', explode(',', $implementasi->kontak_pic)));
                                @endphp
                                @foreach($wa_numbers as $index => $wa)
                                    <div style="display: flex; margin-bottom: 2px;">
                                        <span style="width: 85px; flex-shrink: 0;">WA PIC ke-{{ $index + 1 }}</span>
                                        <span>: {{ $wa }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div style="display: flex; margin-bottom: 2px;">
                                    <span style="width: 85px; flex-shrink: 0;">WA</span>
                                    <span>: -</span>
                                </div>
                            @endif
                        </div>
                        <div style="display: flex;">
                            <span style="width: 85px; flex-shrink: 0;">Email</span>
                            <span style="word-break: break-all;">: {{ $implementasi->email_pic ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">{{ __('messages.ringkasan_tindakan_berikutnya') }}</div>
                <div class="summary-value">{{ $implementasi->tindakan_berikutnya ?? 'Belum ada' }}</div>
                <div style="font-size: 12px; margin-top: 5px; font-weight: 500; color: var(--primary);">Status: {{ $implementasi->status_tindakan ?? '-' }}</div>
            </div>
            <div class="summary-item" style="grid-column: 1 / -1; background: var(--paper-raised, #fff);">
                <div class="summary-label" style="font-weight: 700; color: #1e293b;">Catatan Pelatihan / Hasil Pelatihan</div>
                <div class="summary-value" style="font-weight: 400; font-size: 13.5px; line-height: 1.6; color: #334155; white-space: pre-wrap; margin-top: 4px;">
                    @if($implementasi->catatan_pelatihan)
                        {{ $implementasi->catatan_pelatihan }}
                    @else
                        <span style="color: #94a3b8; font-style: italic;">Belum ada catatan hasil pelatihan.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: CHECKLIST -->
    <div id="tab-checklist" class="tab-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #475569;">{{ __('messages.checklist_kesiapan_impl') }}</h4>
            @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                <button type="button" onclick="openKelolaModal()" style="background: #475569; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;" title="Hapus atau Tambah Item Checklist">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    {{ __('messages.btn_edit') }}
                </button>
            @endif
        </div>
        @php
            $nonMigrasiChecklists = $implementasi->checklists->where('kategori', '!=', 'Migrasi')->filter(function($item) {
                return !str_starts_with($item->kategori ?? '', 'Running');
            });
        @endphp
        @if($nonMigrasiChecklists->count() > 0)
            <div style="overflow-x: auto;">
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.kategori') }}</th>
                            <th>{{ __('messages.item_kesiapan') }}</th>
                            <th style="width: 200px;">{{ __('messages.status') }}</th>
                            <th>{{ __('messages.catatan_tambahan') }}</th>
                            <th style="width: 120px; text-align: center;">{{ __('messages.aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nonMigrasiChecklists as $chk)
                        <tr id="chk-row-{{ $chk->id }}">
                            <td>{{ $chk->kategori ?? '-' }}</td>
                            <td style="font-weight: 500;">{{ $chk->nama_item }}</td>
                            <td>
                                @if(Auth::user()->role === \App\Enums\UserRole::PELAPOR)
                                    <span style="font-weight:600; color: #475569;">{{ $chk->status }}</span>
                                @else
                                    <select id="status-{{ $chk->id }}" class="checklist-select">
                                        <option value="Belum Dikirim" {{ $chk->status == 'Belum Dikirim' ? 'selected' : '' }}>Belum Dikirim</option>
                                        <option value="Sudah Dikirim" {{ $chk->status == 'Sudah Dikirim' ? 'selected' : '' }}>Sudah Dikirim</option>
                                        <option value="Sedang Diproses" {{ $chk->status == 'Sedang Diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="Perlu Revisi" {{ $chk->status == 'Perlu Revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                                        <option value="Data tidak tersedia" {{ $chk->status == 'Data tidak tersedia' ? 'selected' : '' }}>Data tidak tersedia</option>
                                        <option value="Sudah Valid" {{ ($chk->status == 'Sudah Valid' || $chk->status == 'Done') ? 'selected' : '' }}>Sudah Valid (Done)</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->role === \App\Enums\UserRole::PELAPOR)
                                    <span style="color: #64748b; font-style: italic;">{{ $chk->catatan ?? '-' }}</span>
                                @else
                                    <textarea id="catatan-{{ $chk->id }}" class="checklist-input" placeholder="Tambahkan catatan..." rows="3" style="width: 100%; resize: vertical; padding: 6px; font-family: inherit;">{{ $chk->catatan }}</textarea>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                                    @php
                                        $isDone = ($chk->status == 'Sudah Valid' || $chk->status == 'Done' || $chk->status == 'Migrasi Selesai' || $chk->status == 'Selesai');
                                        $simpanStyle = "background: #2563eb; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 500;";
                                        $simpanStyle .= $isDone ? " opacity: 0.5; cursor: not-allowed;" : " cursor: pointer;";
                                        $doneStyle = "background: #10b981; color: #fff; border: none; padding: 6px 9px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center;";
                                        $doneStyle .= $isDone ? " opacity: 0.5; cursor: not-allowed;" : " cursor: pointer;";
                                    @endphp
                                    <div style="display: flex; gap: 6px; align-items: center; justify-content: center;">
                                        <button type="button" id="btn-simpan-{{ $chk->id }}" onclick="updateChecklist({{ $chk->id }})" style="{{ $simpanStyle }}" {{ $isDone ? 'disabled' : '' }}>{{ __('messages.btn_simpan') }}</button>
                                        <button type="button" id="btn-done-{{ $chk->id }}" onclick="markAsDone({{ $chk->id }})" style="{{ $doneStyle }}" title="Tandai Selesai & Naikkan Progres" {{ $isDone ? 'disabled' : '' }}>
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </button>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #64748b;">
                Belum ada data checklist kesiapan.
            </div>
        @endif
    </div>

    <!-- TAB 3: MIGRASI DATA -->
    <div id="tab-migrasi" class="tab-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #475569;">{{ __('messages.item_migrasi_data_koperasi') }}</h4>
            @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                <button type="button" onclick="openKelolaMigrasiModal()" style="background: #475569; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;" title="Hapus atau Tambah Item Migrasi">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit
                </button>
            @endif
        </div>
        @php
            $migrasiChecklists = $implementasi->checklists->where('kategori', 'Migrasi');
        @endphp
        @if($migrasiChecklists->count() > 0)
            <div style="overflow-x: auto;">
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.item_migrasi_data') }}</th>
                            <th style="width: 200px;">{{ __('messages.status') }}</th>
                            <th>{{ __('messages.catatan_tambahan') }}</th>
                            <th style="width: 120px; text-align: center;">{{ __('messages.aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($migrasiChecklists as $chk)
                        <tr id="chk-row-{{ $chk->id }}">
                            <td style="font-weight: 500;">{{ $chk->nama_item }}</td>
                            <td>
                                @if(Auth::user()->role === \App\Enums\UserRole::PELAPOR)
                                    <span style="font-weight:600; color: #475569;">{{ $chk->status }}</span>
                                @else
                                    <select id="status-{{ $chk->id }}" class="checklist-select">
                                        <option value="Belum Dimulai" {{ ($chk->status == 'Belum Dimulai' || $chk->status == 'Belum Dikirim') ? 'selected' : '' }}>Belum Dimulai</option>
                                        <option value="Menunggu Data" {{ ($chk->status == 'Menunggu Data' || $chk->status == 'Sudah Dikirim') ? 'selected' : '' }}>Menunggu Data</option>
                                        <option value="Sedang Diproses" {{ $chk->status == 'Sedang Diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="Menunggu Validasi" {{ $chk->status == 'Menunggu Validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                                        <option value="Perlu Perbaikan" {{ ($chk->status == 'Perlu Perbaikan' || $chk->status == 'Perlu Revisi') ? 'selected' : '' }}>Perlu Perbaikan</option>
                                        <option value="Data tidak tersedia" {{ $chk->status == 'Data tidak tersedia' ? 'selected' : '' }}>Data tidak tersedia</option>
                                        <option value="Migrasi Berhasil" {{ $chk->status == 'Migrasi Berhasil' ? 'selected' : '' }}>Migrasi Berhasil</option>
                                        <option value="Migrasi Selesai" {{ ($chk->status == 'Migrasi Selesai' || $chk->status == 'Sudah Valid' || $chk->status == 'Done') ? 'selected' : '' }}>Migrasi Selesai</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->role === \App\Enums\UserRole::PELAPOR)
                                    <span style="color: #64748b; font-style: italic;">{{ $chk->catatan ?? '-' }}</span>
                                @else
                                    <textarea id="catatan-{{ $chk->id }}" class="checklist-input" placeholder="{{ __('messages.placeholder_catatan') }}" rows="3" style="width: 100%; resize: vertical; padding: 6px; font-family: inherit;">{{ $chk->catatan }}</textarea>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                                    @php
                                        $isDone = ($chk->status == 'Sudah Valid' || $chk->status == 'Done' || $chk->status == 'Migrasi Selesai' || $chk->status == 'Selesai');
                                        $simpanStyle = "background: #2563eb; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 500;";
                                        $simpanStyle .= $isDone ? " opacity: 0.5; cursor: not-allowed;" : " cursor: pointer;";
                                        $doneStyle = "background: #10b981; color: #fff; border: none; padding: 6px 9px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center;";
                                        $doneStyle .= $isDone ? " opacity: 0.5; cursor: not-allowed;" : " cursor: pointer;";
                                    @endphp
                                    <div style="display: flex; gap: 6px; align-items: center; justify-content: center;">
                                        <button type="button" id="btn-simpan-{{ $chk->id }}" onclick="updateChecklist({{ $chk->id }})" style="{{ $simpanStyle }}" {{ $isDone ? 'disabled' : '' }}>{{ __('messages.btn_simpan') }}</button>
                                        <button type="button" id="btn-done-{{ $chk->id }}" onclick="markAsDone({{ $chk->id }})" style="{{ $doneStyle }}" title="Tandai Selesai" {{ $isDone ? 'disabled' : '' }}>
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </button>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #64748b;">
                Belum ada data migrasi.
            </div>
        @endif
    </div>

    <!-- TAB 4: GO-LIVE -->
    <div id="tab-target-golive" class="tab-content">
        @php
            $allDone = ['Sudah Valid', 'Done', 'Selesai', 'Migrasi Selesai'];
            $dataUtamaNotDone = $implementasi->checklists()->where('kategori', 'Data Utama')->whereNotIn('status', $allDone)->count();
            $migrasiNotDone = $implementasi->checklists()->where('kategori', 'Migrasi')->whereNotIn('status', $allDone)->count();
            
            $syarat = [
                'Pelatihan Selesai' => !empty($implementasi->tanggal_selesai) || !empty($implementasi->tanggal_pelatihan) || !empty($implementasi->berita_acara),
                'Data Utama & User Aplikasi Tersedia' => $dataUtamaNotDone === 0,
                'Data Cut-Off Disepakati' => !empty($implementasi->tanggal_cut_off),
                'Migrasi Selesai' => $migrasiNotDone === 0,
                'PIC Koperasi Ditentukan' => !empty($implementasi->anggota_hadir) || !empty($implementasi->pic_koperasi),
            ];
            
            $canGoLive = !in_array(false, $syarat, true);
        @endphp

        @if(!$canGoLive)
            <div id="go-live-alert" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span style="color: #b45309; font-size: 13.5px; font-weight: 700;">Syarat Penentuan Go-Live Belum Terpenuhi</span>
                </div>
                <p style="margin: 0 0 10px 0; font-size: 12.5px; color: #b45309;">Formulir Go-Live dikunci. Silakan penuhi prasyarat berikut terlebih dahulu:</p>
                <ul id="go-live-requirements-list" style="margin: 0; padding-left: 20px; font-size: 12.5px; color: #92400e;">
                    @foreach($syarat as $label => $terpenuhi)
                        @php
                            $safeId = preg_replace('/[^a-z0-9]/', '-', strtolower($label));
                        @endphp
                        <li id="req-{{ $safeId }}" style="margin-bottom: 4px; list-style-type: none; display: flex; align-items: center; gap: 6px;">
                            @if($terpenuhi)
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span style="color: #10b981; text-decoration: line-through;">{{ $label }}</span>
                            @else
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                <span style="font-weight: 600;">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <!-- Placeholder if it later becomes unmet -->
            <div id="go-live-alert" style="display: none; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span style="color: #b45309; font-size: 13.5px; font-weight: 700;">Syarat Penentuan Go-Live Belum Terpenuhi</span>
                </div>
                <p style="margin: 0 0 10px 0; font-size: 12.5px; color: #b45309;">Formulir Go-Live dikunci. Silakan penuhi prasyarat berikut terlebih dahulu:</p>
                <ul id="go-live-requirements-list" style="margin: 0; padding-left: 20px; font-size: 12.5px; color: #92400e;">
                    @foreach($syarat as $label => $terpenuhi)
                        @php
                            $safeId = preg_replace('/[^a-z0-9]/', '-', strtolower($label));
                        @endphp
                        <li id="req-{{ $safeId }}" style="margin-bottom: 4px; list-style-type: none; display: flex; align-items: center; gap: 6px;">
                            @if($terpenuhi)
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span style="color: #10b981; text-decoration: line-through;">{{ $label }}</span>
                            @else
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                <span style="font-weight: 600;">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('implementasi.golive.update', $implementasi->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #475569;">{{ __('messages.detail_go_live') }}</h4>
                <button type="submit" id="btn-submit-golive" {{ !$canGoLive ? 'disabled' : '' }} style="background-color: {{ $canGoLive ? '#3b82f6' : '#94a3b8' }}; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; {{ $canGoLive ? 'cursor: pointer;' : 'cursor: not-allowed;' }}">{{ __('messages.simpan_perubahan') }}</button>
            </div>
            
            <div class="tab-pane-inner">
                <!-- Auto-filled Fields -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.aplikasi_modul_label') }}</label>
                        <input type="text" class="form-control" value="{{ $implementasi->aplikasis && $implementasi->aplikasis->count() > 0 ? $implementasi->aplikasis->pluck('nama_aplikasi')->join(', ') : ($implementasi->aplikasi->nama_aplikasi ?? '-') }}" readonly style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: #e2e8f0; color: #64748b; cursor: not-allowed;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.pic_sakti') }}</label>
                        <input type="text" class="form-control" value="{{ $implementasi->nama_trainer ?? '-' }}" readonly style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: #e2e8f0; color: #64748b; cursor: not-allowed;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.pic_koperasi') }}</label>
                        <input type="text" class="form-control" value="{{ $implementasi->anggota_hadir ?? '-' }}" readonly style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: #e2e8f0; color: #64748b; cursor: not-allowed;">
                    </div>
                </div>

                <!-- Scheduling & Location -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.metode_pendampingan') }}</label>
                        <select name="metode_pendampingan" class="form-control" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                            <option value="">{{ __('messages.pilih_metode') }}</option>
                            <option value="Online (Zoom/Meet)" {{ $implementasi->metode_pendampingan == 'Online (Zoom/Meet)' ? 'selected' : '' }}>Online (Zoom/Meet)</option>
                            <option value="Offline (Kunjungan)" {{ $implementasi->metode_pendampingan == 'Offline (Kunjungan)' ? 'selected' : '' }}>Offline (Kunjungan)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.link_meeting') }}</label>
                        <input type="text" name="link_meeting" class="form-control" value="{{ $implementasi->link_meeting }}" placeholder="{{ __('messages.placeholder_link_meeting') }}" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.tanggal') }}</label>
                        <input type="date" name="target_go_live" class="form-control" value="{{ $implementasi->target_go_live ? $implementasi->target_go_live->format('Y-m-d') : '' }}" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.waktu') }}</label>
                        <input type="text" name="waktu_go_live" class="form-control" placeholder="13:50" value="{{ $implementasi->waktu_go_live ? \Carbon\Carbon::parse($implementasi->waktu_go_live)->format('H:i') : '' }}" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.tempat') }}</label>
                        @php
                            $currentTempat = $implementasi->tempat_go_live;
                            $isPreset = in_array($currentTempat, ['Zoom', 'Gmeet', '']);
                            $isCustom = !$isPreset || $currentTempat === 'Lokasi';
                        @endphp
                        <select name="tempat_go_live" onchange="toggleDetailLokasi(this, 'detail_lokasi_show')" class="form-control" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                            <option value="">{{ __('messages.pilih_tempat') }}</option>
                            <option value="Zoom" {{ $currentTempat == 'Zoom' ? 'selected' : '' }}>Zoom</option>
                            <option value="Gmeet" {{ $currentTempat == 'Gmeet' ? 'selected' : '' }}>Gmeet</option>
                            <option value="Lokasi" {{ $isCustom ? 'selected' : '' }}>Lokasi (Kunjungan Offline)</option>
                        </select>
                        <div id="detail_lokasi_show" style="margin-top: 8px; display: {{ $isCustom ? 'block' : 'none' }};">
                            <input type="text" name="detail_lokasi" class="form-control" placeholder="Tuliskan alamat / lokasi kunjungan..." value="{{ !$isPreset ? $currentTempat : '' }}" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px;">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.status') }}</label>
                        <select name="status_go_live" class="form-control" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                            <option value="Belum Siap Go Live" {{ $implementasi->status_go_live == 'Belum Siap Go Live' ? 'selected' : '' }}>{{ __('messages.status_belum_siap_golive') }}</option>
                            <option value="Siap Go Live" {{ $implementasi->status_go_live == 'Siap Go Live' ? 'selected' : '' }}>{{ __('messages.status_siap_golive') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Textareas -->
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.catatan_kesiapan') }}</label>
                    <textarea name="catatan_kesiapan" class="form-control" rows="3" placeholder="{{ __('messages.placeholder_catatan_kesiapan') }}" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical;">{{ $implementasi->catatan_kesiapan }}</textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.potensi_risiko') }}</label>
                    <textarea name="potensi_risiko" class="form-control" rows="3" placeholder="{{ __('messages.placeholder_potensi_risiko') }}" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical;">{{ $implementasi->potensi_risiko }}</textarea>
                </div>

                <div style="margin-bottom: 0;">
                    <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.rencana_mitigasi') }}</label>
                    <textarea name="rencana_mitigasi" class="form-control" rows="3" placeholder="{{ __('messages.placeholder_rencana_mitigasi') }}" {{ !$canGoLive ? 'disabled' : '' }} style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical;">{{ $implementasi->rencana_mitigasi }}</textarea>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 5: TGL CUT-OFF -->
    <div id="tab-cut-off" class="tab-content">
        <form action="{{ route('implementasi.cutoff.update', $implementasi->id) }}" method="POST">
            @csrf
            @method('PUT')
            @php
                $isCutoffComplete = !empty($implementasi->tanggal_cut_off) && 
                                    !empty($implementasi->periode_transaksi_terakhir) && 
                                    !empty($implementasi->saldo_terakhir) && 
                                    !empty($implementasi->tanggal_tutup_buku) && 
                                    !empty($implementasi->tanggal_mulai_aplikasi) && 
                                    !empty($implementasi->pic_validasi);
            @endphp
            
            @if(!$isCutoffComplete)
                <div class="warning-banner">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span style="color: #b45309; font-size: 12px; font-weight: 500;">
                        {{ __('messages.data_belum_lengkap_cutoff') }}
                    </span>
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h4 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #1e293b;">{{ __('messages.data_cutoff_transisi') }}</h4>

                </div>
                <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.status_cutoff') }}</label>
                        <select name="status_cutoff" class="form-control" style="border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 4px; background: white; min-width: 200px;">
                            <option value="Menunggu Penentuan Cut-Off" {{ $implementasi->status_cutoff == 'Menunggu Penentuan Cut-Off' ? 'selected' : '' }}>{{ __('messages.cutoff_menunggu') }}</option>
                            <option value="Cut-Off Dijadwalkan" {{ $implementasi->status_cutoff == 'Cut-Off Dijadwalkan' ? 'selected' : '' }}>{{ __('messages.cutoff_dijadwalkan') }}</option>
                            <option value="Cut-Off Diterima" {{ $implementasi->status_cutoff == 'Cut-Off Diterima' ? 'selected' : '' }}>{{ __('messages.cutoff_diterima') }}</option>
                            <option value="Cut-Off Perlu Revisi" {{ $implementasi->status_cutoff == 'Cut-Off Perlu Revisi' ? 'selected' : '' }}>{{ __('messages.cutoff_perlu_revisi') }}</option>
                            <option value="Cut-Off Valid" {{ $implementasi->status_cutoff == 'Cut-Off Valid' ? 'selected' : '' }}>{{ __('messages.cutoff_valid') }}</option>
                        </select>
                    </div>
                    <button type="submit" style="background: #0ea5e9; color: white; border: none; padding: 9px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; transition: background 0.2s; box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2); height: 38px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        {{ __('messages.btn_simpan') }}
                    </button>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 20px;">
                <!-- Column 1 -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.tanggal_cutoff') }} <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal_cut_off" value="{{ $implementasi->tanggal_cut_off ? $implementasi->tanggal_cut_off->format('Y-m-d') : '' }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">{{ __('messages.syarat_golive_otomatis') }}</div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.periode_transaksi_terakhir') }}</label>
                        <input type="text" name="periode_transaksi_terakhir" value="{{ $implementasi->periode_transaksi_terakhir }}" placeholder="{{ __('messages.placeholder_periode_transaksi') }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.saldo_terakhir') }}</label>
                        <input type="text" name="saldo_terakhir" value="{{ $implementasi->saldo_terakhir }}" placeholder="{{ __('messages.placeholder_saldo_terakhir') }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                    </div>
                </div>

                <!-- Column 2 -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.tgl_penutupan_buku') }}</label>
                        <input type="date" name="tanggal_tutup_buku" value="{{ $implementasi->tanggal_tutup_buku ? $implementasi->tanggal_tutup_buku->format('Y-m-d') : '' }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.tgl_mulai_aplikasi_baru') }}</label>
                        <input type="date" name="tanggal_mulai_aplikasi" value="{{ $implementasi->tanggal_mulai_aplikasi ? $implementasi->tanggal_mulai_aplikasi->format('Y-m-d') : '' }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.pic_validasi_data') }}</label>
                        <input type="text" name="pic_validasi" value="{{ $implementasi->pic_validasi }}" placeholder="{{ __('messages.placeholder_pic_validasi') }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: white;">
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 16px;">
                <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.catatan_khusus_cutoff') }}</label>
                <textarea name="catatan_cutoff" class="form-control" rows="3" placeholder="{{ __('messages.placeholder_catatan_cutoff') }}" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical; background: white;">{{ $implementasi->catatan_cutoff }}</textarea>
            </div>
        </form>
    </div>

    <!-- TAB 6: AKTIVITAS FOLLOW-UP -->
    <div id="tab-followup" class="tab-content">
        <form action="{{ route('implementasi.followup.update', $implementasi->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
                <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #475569;">{{ __('messages.detail_tindakan_followup') }}</h4>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="openRiwayatFollowUp()" style="background-color: #64748b; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        {{ __('messages.riwayat_aktivitas') }}
                    </button>
                    <button type="button" id="btn-edit-followup" onclick="enableEditFollowUp()" style="background-color: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        {{ __('messages.btn_edit') }}
                    </button>
                    <button type="button" id="btn-batal-followup" onclick="cancelEditFollowUp()" style="display: none; background-color: #94a3b8; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">{{ __('messages.btn_batal') }}</button>
                    <button type="submit" id="btn-simpan-followup" style="display: none; background-color: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">{{ __('messages.simpan_perubahan') }}</button>
                </div>
            </div>

            <!-- PREVIEW CARD -->
            <div id="preview-followup" style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <div class="grid-3-col" style="margin-bottom: 20px; gap: 20px;">
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.tanggal_follow_up') }}</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 500;">{{ $implementasi->tanggal_followup ? $implementasi->tanggal_followup->format('d M Y') : '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.tgl_follow_up_berikutnya') }}</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 500;">{{ $implementasi->tanggal_followup_berikutnya ? $implementasi->tanggal_followup_berikutnya->format('d M Y') : '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.target_tgl_tindakan') }}</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 500;">{{ $implementasi->target_tanggal_tindakan ? $implementasi->target_tanggal_tindakan->format('d M Y') : '-' }}</div>
                    </div>
                </div>

                <div class="grid-3-2-col" style="margin-bottom: 20px; gap: 20px; padding-top: 20px; border-top: 1px dashed #e2e8f0;">
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.jenis_aktivitas') }}</div>
                        <div style="font-size: 14px; color: #1e293b; font-weight: 500;">
                            @if($implementasi->jenis_tindakan)
                                @foreach(explode(',', $implementasi->jenis_tindakan) as $jenis)
                                    <span style="display: inline-block; padding: 2px 8px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px; margin-right: 4px; margin-bottom: 4px;">{{ trim($jenis) }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.pic_tindakan') }}</div>
                            <div style="font-size: 14px; color: #1e293b; font-weight: 500;">{{ $implementasi->pic_tindakan ?: '-' }}</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.status_tindakan') }}</div>
                            <div style="font-size: 14px; color: #1d4ed8; font-weight: 600;">
                                <span style="display: inline-block; padding: 4px 10px; background: #eff6ff; border-radius: 6px;">{{ $implementasi->status_tindakan ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="padding-top: 20px; border-top: 1px dashed #e2e8f0; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.hasil_komunikasi') }}</div>
                        <div style="font-size: 13.5px; color: #334155; line-height: 1.5; background: #f8fafc; padding: 10px; border-radius: 6px;">{{ $implementasi->hasil_komunikasi ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.kendala_koperasi') }}</div>
                        <div style="font-size: 13.5px; color: #334155; line-height: 1.5; background: #f8fafc; padding: 10px; border-radius: 6px;">{{ $implementasi->kendala_koperasi ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.komitmen_koperasi') }}</div>
                        <div style="font-size: 13.5px; color: #334155; line-height: 1.5; background: #f8fafc; padding: 10px; border-radius: 6px;">{{ $implementasi->komitmen_koperasi ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase;">{{ __('messages.detail_catatan_tindakan') }}</div>
                        <div style="font-size: 13.5px; color: #334155; line-height: 1.5; background: #f8fafc; padding: 10px; border-radius: 6px;">{{ $implementasi->tindakan_berikutnya ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- FORM EDIT (HIDDEN BY DEFAULT) -->
            <div id="form-followup" style="display: none;">
                <div class="tab-pane-inner">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    
                    <!-- Row 1: Tanggal-tanggalan -->
                    <div class="grid-3-col">
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.tanggal_follow_up') }}</label>
                            <input type="date" name="tanggal_followup" value="{{ $implementasi->tanggal_followup ? $implementasi->tanggal_followup->format('Y-m-d') : '' }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 7px; border-radius: 4px; background: white;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.tgl_follow_up_berikutnya') }}</label>
                            <input type="date" name="tanggal_followup_berikutnya" value="{{ $implementasi->tanggal_followup_berikutnya ? $implementasi->tanggal_followup_berikutnya->format('Y-m-d') : '' }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 7px; border-radius: 4px; background: white;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.target_tgl_tindakan') }}</label>
                            <input type="date" name="target_tanggal_tindakan" value="{{ $implementasi->target_tanggal_tindakan ? $implementasi->target_tanggal_tindakan->format('Y-m-d') : '' }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 7px; border-radius: 4px; background: white;">
                        </div>
                    </div>

                    <!-- Row 2: Jenis & PIC -->
                    <div class="grid-3-2-col">
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.jenis_aktivitas') }} <span style="color: #ef4444;">*</span></label>
                            <div class="checkbox-grid" style="padding: 10px;">
                                @php
                                    $options = [
                                        'Telepon', 'WhatsApp', 'GMeet', 'Zoom',
                                        'Permintaan data', 'Pengiriman file', 'Perubahan Data', 'Perubahan jadwal', 'Perubahan target'
                                    ];
                                    $selected_jenis = array_map('trim', explode(',', $implementasi->jenis_tindakan ?? ''));
                                @endphp
                                @foreach($options as $opt)
                                    <label style="display: flex; align-items: center; gap: 6px; font-size: 12px; cursor: pointer; color: #475569; margin: 0;">
                                        <input type="radio" name="jenis_tindakan" value="{{ $opt }}" {{ in_array($opt, $selected_jenis) ? 'checked' : '' }} style="cursor: pointer; margin: 0;">
                                        {{ $opt }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div>
                                <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.pic_tindakan') }}</label>
                                <input type="text" name="pic_tindakan" value="{{ $implementasi->pic_tindakan }}" placeholder="{{ __('messages.placeholder_pic_tindakan') }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 7px; border-radius: 4px; background: white;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.status_tindakan') }}</label>
                                <select name="status_tindakan" id="status_tindakan_select" data-original="{{ $implementasi->status_tindakan }}" class="form-control" style="width: 100%; border: 1px solid #cbd5e1; padding: 7px; border-radius: 4px; background: white;">
                                    <option value="Persiapan Data" {{ $implementasi->status_tindakan == 'Persiapan Data' ? 'selected' : '' }}>Persiapan Data</option>
                                    <option value="Migrasi Data" {{ $implementasi->status_tindakan == 'Migrasi Data' ? 'selected' : '' }}>Migrasi Data</option>
                                    <option value="Olah Data" {{ $implementasi->status_tindakan == 'Olah Data' ? 'selected' : '' }}>Olah Data</option>
                                    <option value="Setting Buku" {{ $implementasi->status_tindakan == 'Setting Buku' ? 'selected' : '' }}>Setting Buku</option>
                                    <option value="Input Transaksi" {{ $implementasi->status_tindakan == 'Input Transaksi' ? 'selected' : '' }}>Input Transaksi</option>
                                    <option value="Follow Up" {{ $implementasi->status_tindakan == 'Follow Up' ? 'selected' : '' }}>Follow Up</option>
                                    <option value="Pendampingan Online" {{ $implementasi->status_tindakan == 'Pendampingan Online' ? 'selected' : '' }}>Pendampingan Online</option>
                                    <option value="Pendampingan Offline" {{ $implementasi->status_tindakan == 'Pendampingan Offline' ? 'selected' : '' }}>Pendampingan Offline</option>
                                    <option value="Monitoring" {{ $implementasi->status_tindakan == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                                    <option value="Support Data" {{ $implementasi->status_tindakan == 'Support Data' ? 'selected' : '' }}>Support Data</option>
                                    <option value="Proses Running" {{ $implementasi->status_tindakan == 'Proses Running' ? 'selected' : '' }}>Proses Running</option>
                                    <option value="Proses Simulasi" {{ $implementasi->status_tindakan == 'Proses Simulasi' ? 'selected' : '' }}>Proses Simulasi</option>
                                    <option value="Perbaikan / Update Aplikasi" {{ $implementasi->status_tindakan == 'Perbaikan / Update Aplikasi' ? 'selected' : '' }}>Perbaikan / Update Aplikasi</option>
                                    <option value="Pelatihan Ulang" {{ $implementasi->status_tindakan == 'Pelatihan Ulang' ? 'selected' : '' }}>Pelatihan Ulang</option>
                                    <option value="Selesai" {{ $implementasi->status_tindakan == 'Selesai' ? 'selected' : '' }}>Selesai (Tuntas)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Textareas Group 1 -->
                    <div class="grid-2-col">
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.hasil_komunikasi') }}</label>
                            <textarea name="hasil_komunikasi" class="form-control" rows="2" placeholder="Tuliskan hasil percakapan / komunikasi..." style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical; background: white;">{{ $implementasi->hasil_komunikasi }}</textarea>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.kendala_koperasi') }}</label>
                            <textarea name="kendala_koperasi" class="form-control" rows="2" placeholder="Tuliskan kendala yang dihadapi koperasi..." style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical; background: white;">{{ $implementasi->kendala_koperasi }}</textarea>
                        </div>
                    </div>

                    <!-- Row 4: Textareas Group 2 -->
                    <div class="grid-2-col">
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.komitmen_koperasi') }}</label>
                            <textarea name="komitmen_koperasi" class="form-control" rows="2" placeholder="Tuliskan komitmen yang diberikan koperasi..." style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical; background: white;">{{ $implementasi->komitmen_koperasi }}</textarea>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 5px;">{{ __('messages.detail_catatan_tindakan') }}</label>
                            <textarea name="tindakan_berikutnya" class="form-control" rows="2" placeholder="{{ __('messages.placeholder_detail_tindakan') }}" style="width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; resize: vertical; background: white;">{{ $implementasi->tindakan_berikutnya }}</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    </div> <!-- END OF TAB FOLLOW-UP -->

    <!-- TAB RUNNING MONITORING -->
    <div id="tab-running" class="tab-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #475569;">Running Monitoring (Status Kemandirian)</h4>
            @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                <button type="button" onclick="openKelolaRunningModal()" style="background: #475569; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;" title="Hapus atau Tambah Item Running Monitoring">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    {{ __('messages.btn_edit') }}
                </button>
            @endif
        </div>
        @php
            $runningChecklists = $implementasi->checklists->filter(function($item) {
                return str_starts_with($item->kategori ?? '', 'Running');
            });
            $groupedRunning = $runningChecklists->groupBy('kategori');
        @endphp
        
        @if($runningChecklists->count() > 0)
            <div style="overflow-x: auto;">
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Item Pemeriksaan</th>
                            <th>Catatan</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedRunning as $kategori => $items)
                            @foreach($items as $index => $chk)
                            <tr id="chk-row-{{ $chk->id }}">
                                @if($index === 0)
                                <td rowspan="{{ count($items) }}" class="running-kategori-td" style="font-weight: 600; vertical-align: top; width: 200px;">
                                    {{ str_replace('Running - ', '', $chk->kategori) }}
                                </td>
                                @endif
                                <td style="font-weight: 500;">
                                    {{ $chk->nama_item }}
                                    <input type="hidden" id="status-{{ $chk->id }}" value="{{ $chk->status }}">
                                </td>
                                <td>
                                    @if(Auth::user()->role === \App\Enums\UserRole::PELAPOR)
                                        <span style="color: #64748b; font-style: italic;">{{ $chk->catatan ?? '-' }}</span>
                                    @else
                                        <textarea id="catatan-{{ $chk->id }}" class="checklist-input" placeholder="Tambahkan catatan..." rows="2" style="width: 100%; resize: vertical; padding: 6px; font-family: inherit;">{{ $chk->catatan }}</textarea>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                                        @php
                                            $isDone = ($chk->status == 'Sudah Valid' || $chk->status == 'Done');
                                            $simpanStyle = "background: #2563eb; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 500; cursor: pointer;";
                                            $doneStyle = "background: #10b981; color: #fff; border: none; padding: 6px 9px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center;";
                                            $doneStyle .= $isDone ? " opacity: 0.5; cursor: not-allowed;" : " cursor: pointer;";
                                        @endphp
                                        <div style="display: flex; gap: 6px; align-items: center; justify-content: center;">
                                            <button type="button" id="btn-simpan-{{ $chk->id }}" onclick="updateChecklist({{ $chk->id }})" style="{{ $simpanStyle }}">{{ __('messages.btn_simpan') }}</button>
                                            <button type="button" id="btn-done-{{ $chk->id }}" onclick="markAsDone({{ $chk->id }})" style="{{ $doneStyle }}" title="Tandai Selesai" {{ $isDone ? 'disabled' : '' }}>
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 30px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; color: #64748b;">
                Belum ada data Running Monitoring untuk implementasi ini.
            </div>
        @endif
    </div>

    <!-- TAB 7: AKTIVITAS & LOG -->
    <div id="tab-aktivitas" class="tab-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #475569;">{{ __('messages.riwayat_aktivitas_log') }}</h4>
        </div>
        <div class="timeline">
            @forelse($implementasi->logs as $log)
                @php
                    $logTime = '-';
                    if ($log->created_at) {
                        $logTime = is_string($log->created_at) ? \Carbon\Carbon::parse($log->created_at)->format('d M Y - H:i') : $log->created_at->format('d M Y - H:i');
                    }
                @endphp
                <div class="timeline-item">
                    <div class="timeline-time">{{ $logTime }} | <strong>{{ $log->user->nama ?? __('messages.sistem') }}</strong></div>
                    <div class="timeline-content">
                        <div style="font-weight: 600; margin-bottom: 5px;">{{ $log->aktivitas }}</div>
                        @if($log->catatan && !str_contains(strtolower($log->catatan), 'ajax'))
                            <div style="font-size: 12px; color: #475569; margin-top: 4px;">{{ $log->catatan }}</div>
                        @endif
                        @if($log->data_sebelum && $log->data_sesudah)
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                <strong>Status:</strong> <span style="text-decoration: line-through;">{{ is_array($log->data_sebelum) ? ($log->data_sebelum['status'] ?? '') : '' }}</span> &rarr; <span style="color:#2563eb;">{{ is_array($log->data_sesudah) ? ($log->data_sesudah['status'] ?? '') : '' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px 20px; color: #64748b; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    <div style="font-weight: 600; font-size: 14px; color: #475569; margin-bottom: 4px;">{{ __('messages.belum_ada_aktivitas') }}</div>
                    <div style="font-size: 13px; color: #64748b;">{{ __('messages.desc_belum_ada_aktivitas') }}</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
</div>
</div>
</div>

<script>
    function openRiwayatFollowUp() {
        const modal = document.getElementById('modalRiwayatFollowUp');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeRiwayatFollowUp() {
        const modal = document.getElementById('modalRiwayatFollowUp');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const statusTindakanSelect = document.getElementById('status_tindakan_select');
        if (statusTindakanSelect) {
            statusTindakanSelect.addEventListener('change', function(e) {
                if (this.value === 'Implementasi Selesai') {
                    if (!confirm('Pastikan semua catatan tindak lanjut dan kendala telah diselesaikan.\nApakah Anda yakin ingin menyelesaikan tindakan ini?')) {
                        this.value = this.getAttribute('data-original') || 'Persiapan Data';
                    } else {
                        this.setAttribute('data-original', this.value);
                    }
                } else {
                    this.setAttribute('data-original', this.value);
                }
            });
        }
    });

    // Tab Navigation Logic
    function openTab(tabId, btn) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        // Remove active class from buttons
        document.querySelectorAll('.md-tab-btn').forEach(el => el.classList.remove('active'));
        
        // Show target content
        document.getElementById(tabId).classList.add('active');
        // Set active button
        if (btn) {
            btn.classList.add('active');
        } else if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        }
    }

    // Edit Follow-Up
    function enableEditFollowUp() {
        document.getElementById('preview-followup').style.display = 'none';
        document.getElementById('form-followup').style.display = 'block';
        document.getElementById('btn-edit-followup').style.display = 'none';
        document.getElementById('btn-batal-followup').style.display = 'inline-block';
        document.getElementById('btn-simpan-followup').style.display = 'inline-block';
    }

    function cancelEditFollowUp() {
        document.getElementById('preview-followup').style.display = 'block';
        document.getElementById('form-followup').style.display = 'none';
        document.getElementById('btn-edit-followup').style.display = 'inline-block';
        document.getElementById('btn-batal-followup').style.display = 'none';
        document.getElementById('btn-simpan-followup').style.display = 'none';
    }

    // Quick Mark as Done Modal
    let selectedChecklistId = null;

    function markAsDone(id) {
        selectedChecklistId = id;
        const select = document.getElementById('status-' + id);
        const textElement = document.getElementById('confirm-done-text');
        
        if (select && textElement) {
            let currentStatus = select.options ? select.options[select.selectedIndex].text : (select.value || 'Belum');
            if (currentStatus !== 'Sudah Valid (Done)' && currentStatus !== 'Migrasi Selesai' && currentStatus !== 'Sudah Valid' && currentStatus !== 'Done') {
                textElement.innerHTML = `Apakah Anda yakin ingin menandai item checklist ini sebagai <strong>Sudah Valid / Done</strong>?`;
            } else {
                textElement.innerHTML = `Apakah Anda yakin ingin menandai item checklist ini sebagai <strong>Sudah Valid / Done</strong>?`;
            }
        }
        
        document.getElementById('modalConfirmDone').style.display = 'flex';
    }

    function closeDoneModal() {
        selectedChecklistId = null;
        document.getElementById('modalConfirmDone').style.display = 'none';
    }

    document.getElementById('btn-confirm-done').addEventListener('click', function() {
        if (selectedChecklistId) {
            const select = document.getElementById('status-' + selectedChecklistId);
            if (select) {
                if (select.options) {
                    let hasMigrasiSelesai = Array.from(select.options).some(opt => opt.value === 'Migrasi Selesai');
                    if (hasMigrasiSelesai) {
                        select.value = 'Migrasi Selesai';
                    } else {
                        select.value = 'Sudah Valid';
                    }
                } else {
                    select.value = 'Sudah Valid';
                }
            }
            updateChecklist(selectedChecklistId);
            closeDoneModal();
        }
    });

    // Kelola Checklist Modal
    function openKelolaModal() {
        document.getElementById('modalKelolaChecklist').style.display = 'flex';
    }

    function closeKelolaModal() {
        document.getElementById('modalKelolaChecklist').style.display = 'none';
    }

    function openKelolaMigrasiModal() {
        document.getElementById('modalKelolaMigrasi').style.display = 'flex';
    }

    function closeKelolaMigrasiModal() {
        document.getElementById('modalKelolaMigrasi').style.display = 'none';
    }

    function openKelolaRunningModal() {
        document.getElementById('modalKelolaRunning').style.display = 'flex';
    }

    function closeKelolaRunningModal() {
        document.getElementById('modalKelolaRunning').style.display = 'none';
    }

    function addCustomRunning(implId) {
        const namaInput = document.getElementById('new-running-nama');
        const kategoriSelect = document.getElementById('new-running-kategori');
        const nama = namaInput.value.trim();
        const kategori = kategoriSelect.value || 'Running - Aplikasi';

        if (!nama) {
            alert('Nama item running status tidak boleh kosong');
            return;
        }

        fetch(`{{ url('implementasi') }}/${implId}/checklist`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nama_item: nama, kategori: kategori })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                namaInput.value = '';

                document.getElementById('progres-text').innerText = data.new_progres + '%';
                document.getElementById('progres-fill').style.width = data.new_progres + '%';

                showToast("Item Running Monitoring berhasil ditambahkan");
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Gagal menambahkan item Running Monitoring');
        });
    }

    function deleteChecklist(id, name) {
        if (!confirm(`Apakah Anda yakin ingin menghapus item "${name}" dari checklist ini?`)) {
            return;
        }

        fetch(`{{ url('implementasi/checklist') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const modalItem = document.getElementById('modal-item-' + id);
                if (modalItem) modalItem.remove();

                const tableRow = document.getElementById('chk-row-' + id);
                if (tableRow) tableRow.remove();

                document.getElementById('progres-text').innerText = data.new_progres + '%';
                document.getElementById('progres-fill').style.width = data.new_progres + '%';

                showToast("{{ __('messages.toast_item_deleted') }}");
            }
        })
        .catch(err => {
            console.error(err);
            alert('{{ __('messages.error_delete_checklist') }}');
        });
    }

    function addCustomChecklist(implId) {
        const namaInput = document.getElementById('new-item-nama');
        const kategoriInput = document.getElementById('new-item-kategori');
        const nama = namaInput.value.trim();
        const kategori = kategoriInput.value.trim() || 'Lainnya';

        if (!nama) {
            alert('{{ __('messages.error_empty_checklist_name') }}');
            return;
        }

        fetch(`{{ url('implementasi') }}/${implId}/checklist`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nama_item: nama, kategori: kategori })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                namaInput.value = '';
                kategoriInput.value = '';

                document.getElementById('progres-text').innerText = data.new_progres + '%';
                document.getElementById('progres-fill').style.width = data.new_progres + '%';

                showToast("{{ __('messages.toast_item_added') }}");
                setTimeout(() => location.reload(), 1200);
            }
        })
        .catch(err => {
            console.error(err);
            alert('{{ __('messages.error_add_checklist') }}');
        });
    }

    function addCustomMigrasi(implId) {
        const namaInput = document.getElementById('new-migrasi-nama');
        const nama = namaInput.value.trim();

        if (!nama) {
            alert('{{ __('messages.error_empty_migration_name') }}');
            return;
        }

        fetch(`{{ url('implementasi') }}/${implId}/checklist`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nama_item: nama, kategori: 'Migrasi' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                namaInput.value = '';
                showToast("{{ __('messages.toast_migrasi_added') }}");
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(err => {
            console.error(err);
            alert('{{ __('messages.error_add_migration') }}');
        });
    }

    // AJAX Update Checklist
    function updateChecklist(id) {
        const status = document.getElementById('status-' + id).value;
        const catatan = document.getElementById('catatan-' + id).value;
        
        fetch(`{{ url('implementasi/checklist') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status, catatan: catatan })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Update Progress UI dynamically
                document.getElementById('progres-text').innerText = data.new_progres + '%';
                document.getElementById('progres-fill').style.width = data.new_progres + '%';
                
                // Update buttons state
                const btnSimpan = document.getElementById('btn-simpan-' + id);
                const btnDone = document.getElementById('btn-done-' + id);
                
                if (status === 'Sudah Valid' || status === 'Done' || status === 'Migrasi Selesai' || status === 'Selesai') {
                    if (btnSimpan) {
                        btnSimpan.disabled = true;
                        btnSimpan.style.opacity = '0.5';
                        btnSimpan.style.cursor = 'not-allowed';
                    }
                    if (btnDone) {
                        btnDone.disabled = true;
                        btnDone.style.opacity = '0.5';
                        btnDone.style.cursor = 'not-allowed';
                    }
                } else {
                    if (btnSimpan) {
                        btnSimpan.disabled = false;
                        btnSimpan.style.opacity = '1';
                        btnSimpan.style.cursor = 'pointer';
                    }
                    if (btnDone) {
                        btnDone.disabled = false;
                        btnDone.style.opacity = '1';
                        btnDone.style.cursor = 'pointer';
                    }
                }
                
                if (data.syarat) {
                    // Update syarat golive
                    Object.keys(data.syarat).forEach(key => {
                        const safeKey = key.toLowerCase().replace(/[^a-z0-9]/g, '-');
                        const li = document.getElementById('req-' + safeKey);
                        if (li) {
                            if (data.syarat[key]) {
                                li.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg><span style="color: #10b981; text-decoration: line-through;">${key}</span>`;
                            } else {
                                li.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg><span style="font-weight: 600;">${key}</span>`;
                            }
                        }
                    });
                    
                    const alertBox = document.getElementById('go-live-alert');
                    if (alertBox) {
                        alertBox.style.display = data.can_go_live ? 'none' : 'block';
                    }
                    
                    const btnSubmit = document.getElementById('btn-submit-golive');
                    if (btnSubmit) {
                        btnSubmit.disabled = !data.can_go_live;
                        btnSubmit.style.backgroundColor = data.can_go_live ? '#3b82f6' : '#94a3b8';
                        btnSubmit.style.cursor = data.can_go_live ? 'pointer' : 'not-allowed';
                    }
                }

                if (data.reload) {
                    // Show Toast Notification
                    showToast("Item berhasil diupdate. Memperbarui status Go-Live...");
                    setTimeout(() => location.reload(), 1500);
                } else {
                    // Show Toast Notification
                    showToast("{{ __('messages.toast_checklist_updated') }}");
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('messages.error_save_data') }}');
        });
    }

    // Toast Notification Logic
    function showToast(message) {
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').innerText = message;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
</script>

<!-- Modal Riwayat Follow-Up -->
<div class="modal-overlay" id="modalRiwayatFollowUp" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; width: 700px; max-width: 90%; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);">
        <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-radius: 12px 12px 0 0;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                {{ __('messages.riwayat_aktivitas_followup') }}
            </h4>
            <button type="button" onclick="closeRiwayatFollowUp()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div style="padding: 24px 30px; overflow-y: auto; flex-grow: 1;">
            @if($implementasi->followUps && $implementasi->followUps->count() > 0)
                <div class="timeline" style="background: white; padding-left: 28px; padding-top: 4px; padding-bottom: 4px; margin-left: 6px;">
                    @foreach($implementasi->followUps as $fu)
                        <div class="timeline-item">
                            <div class="timeline-time">
                                {{ $fu->created_at->format('d M Y - H:i') }} | <strong>{{ $fu->creator->nama ?? 'Sistem' }}</strong>
                            </div>
                            <div class="timeline-content" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <div style="font-weight: 600; font-size: 14px; color: #1e293b;">
                                        {{ $fu->jenis_tindakan ?? 'Update Aktivitas' }}
                                    </div>
                                    <span style="font-size: 11px; padding: 2px 6px; background: #dbeafe; color: #1d4ed8; border-radius: 4px; font-weight: 600;">
                                        {{ $fu->status_tindakan }}
                                    </span>
                                </div>
                                <div style="font-size: 13px; color: #475569; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <div><strong>PIC Tindakan:</strong> {{ $fu->pic_tindakan ?? '-' }}</div>
                                    <div><strong>Tgl Follow-Up:</strong> {{ $fu->tanggal_followup ? $fu->tanggal_followup->format('d M Y') : '-' }}</div>
                                </div>
                                
                                <div style="border-top: 1px dashed #cbd5e1; padding-top: 8px; font-size: 12px;">
                                    <div style="margin-bottom: 6px;"><strong>Hasil Komunikasi:</strong><br/>{{ $fu->hasil_komunikasi ?? '-' }}</div>
                                    <div style="margin-bottom: 6px;"><strong>Kendala:</strong><br/>{{ $fu->kendala_koperasi ?? '-' }}</div>
                                    <div style="margin-bottom: 6px;"><strong>Komitmen:</strong><br/>{{ $fu->komitmen_koperasi ?? '-' }}</div>
                                    <div><strong>Tindakan Berikutnya:</strong><br/>{{ $fu->tindakan_berikutnya ?? '-' }} (Target: {{ $fu->target_tanggal_tindakan ? $fu->target_tanggal_tindakan->format('d M Y') : '-' }})</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #64748b; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    {{ __('messages.belum_ada_riwayat_followup') }}
                </div>
            @endif
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; background: #f8fafc; border-radius: 0 0 12px 12px;">
            <button type="button" onclick="closeRiwayatFollowUp()" style="padding: 7px 16px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; cursor: pointer; font-size: 13px;">{{ __('messages.btn_tutup') }}</button>
        </div>
    </div>
</div>

<script>
function toggleDetailLokasi(selectEl, targetId) {
    const target = document.getElementById(targetId);
    if (!target) return;
    if (selectEl.value === 'Lokasi') {
        target.style.display = 'block';
    } else {
        target.style.display = 'none';
    }
}
</script>
@endsection
