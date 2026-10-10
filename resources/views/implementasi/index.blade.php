@extends('layouts.app')

@section('page_title', __('messages.monitoring_koperasi'))
@section('page_subtitle', __('messages.dashboard_monitoring'))

@section('content')
<style>
    /* Vanilla CSS styling for dashboard */
    .dashboard-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .table-responsive {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: scroll !important; /* FORCED to always show scrollbar */
        -webkit-overflow-scrolling: touch !important;
        margin-bottom: 10px;
    }
    
    /* Force custom scrollbar so it's always visible on desktop & mobile */
    .table-responsive::-webkit-scrollbar {
        height: 12px !important; /* Ensure height is thick enough */
        display: block !important;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9 !important; 
        border-radius: 8px !important;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #cbd5e1 !important; 
        border-radius: 8px !important;
        border: 2px solid #f1f5f9 !important;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8 !important; 
    }
    .dark-mode .table-responsive::-webkit-scrollbar-track {
        background: #1e293b !important;
    }
    .dark-mode .table-responsive::-webkit-scrollbar-thumb {
        background-color: #475569 !important;
        border: 2px solid #1e293b !important;
    }
    
    .implementasi-table {
        width: 100% !important;
        min-width: 1100px !important; /* Forces table to be wide */
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .implementasi-table th, .implementasi-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
        white-space: nowrap; /* Prevent text wrapping to force horizontal scroll */
    }
    
    .implementasi-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #333;
    }
    
    /* Dynamic Status Labels */
    .badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }
    
    .badge-abu { background-color: #e2e8f0; color: #475569; }
    .badge-biru { background-color: #dbeafe; color: #1d4ed8; }
    .badge-kuning { background-color: #fef08a; color: #854d0e; }
    .badge-oranye { background-color: #ffedd5; color: #c2410c; }
    .badge-merah { background-color: #fee2e2; color: #b91c1c; }
    .badge-hijau { background-color: #dcfce3; color: #166534; }
    
    /* Progress Bar */
    .progress-container {
        width: 100%;
        background-color: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        height: 8px;
        margin-top: 5px;
    }
    
    .progress-bar {
        height: 100%;
        background-color: #10b981;
        transition: width 0.3s ease;
    }
    
    .btn-action {
        padding: 8px 16px;
        background-color: #2563eb;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: background-color 0.2s, transform 0.1s;
    }
    
    .btn-action:hover {
        background-color: #1d4ed8;
    }
    
    .btn-action:active {
        transform: scale(0.97);
    }

    /* Skeleton Loading Shimmer Effect */
    .skeleton-box {
        display: inline-block;
        position: relative;
        overflow: hidden;
        background-color: #e2e8f0;
        border-radius: 6px;
    }
    .dark-mode .skeleton-box {
        background-color: #334155;
    }
    .skeleton-box::after {
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0,
            rgba(255, 255, 255, 0.6) 20%,
            rgba(255, 255, 255, 0.5) 60%,
            rgba(255, 255, 255, 0)
        );
        animation: shimmer 1.5s infinite;
        content: '';
    }
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }
    
    .dark-mode .dashboard-card {
        background: #1e293b;
        color: #f8fafc;
        box-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }
    
    .dark-mode .implementasi-table th {
        background-color: #0f172a;
        color: #e2e8f0;
        border-bottom-color: #334155;
    }
    
    .dark-mode .implementasi-table td {
        border-bottom-color: #334155;
    }
    
    .dark-mode .badge-abu { background-color: #334155; color: #cbd5e1; }
    .dark-mode .badge-biru { background-color: #1e3a8a; color: #bfdbfe; }
    .dark-mode .badge-kuning { background-color: #713f12; color: #fef08a; }
    .dark-mode .badge-oranye { background-color: #7c2d12; color: #ffedd5; }
    .dark-mode .badge-merah { background-color: #7f1d1d; color: #fecaca; }
    .dark-mode .badge-hijau { background-color: #14532d; color: #bbf7d0; }
    .dropdown-kebab {
        position: relative;
        display: inline-block;
    }
    .btn-kebab {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #64748b;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .btn-kebab:hover { background: #f1f5f9; }
    .kebab-menu-content {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 50;
        min-width: 120px;
        overflow: hidden;
    }
    .kebab-menu-content.show { display: block; }
    .kebab-item {
        display: block;
        width: 100%;
        text-align: left;
        padding: 10px 15px;
        color: #1e293b;
        text-decoration: none;
        font-size: 13px;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    .kebab-item:hover { background: #f1f5f9; }
    .kebab-item.text-red { color: #ef4444; }
    .kebab-item.text-red:hover { background: #fef2f2; }
    
    .dark-mode .progress-container { background-color: #334155; }
    
    @media (max-width: 768px) {
        .dashboard-wrapper {
            padding: 15px !important;
            width: 100% !important;
            max-width: 100vw !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .dashboard-card {
            padding: 15px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .table-responsive {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            border: none !important;
        }
    }
</style>

<div class="dashboard-wrapper" style="width: 100%; max-width: 100%; margin: 0 auto; padding: 20px 30px; box-sizing: border-box; overflow: hidden;">
    <div class="dashboard-card" style="width: 100%; max-width: 100%; box-sizing: border-box; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <h2 style="font-size: 18px; font-weight: 600; margin: 0;">{{ __('messages.monitoring_running_koperasi', ['count' => $implementasis->count()]) }}</h2>
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <form action="{{ route('implementasi.index') }}" method="GET" style="margin: 0; display: flex; align-items: center;">
                    <div class="search" style="margin-bottom: 0;">
                        <img src="{{ asset('magnifying-glass.png') }}" alt="Search" style="width: 14px; height: 14px; margin-right: 8px; vertical-align: middle; opacity: 0.4; filter: grayscale(100%);">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Impl atau Koperasi..." style="border:none; background:transparent; width:100%; outline:none;">
                    </div>
                </form>
                @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                    <button class="btn-action" style="background-color: #10b981;" onclick="openModal('modalDataBaru')">{{ __('messages.data_baru') }}</button>
                @endif
            </div>
        </div>

    @if(session('success'))
        <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); transition: opacity 0.6s ease, transform 0.6s ease;">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
        </div>
    @endif


    <div class="table-responsive">
        <table class="implementasi-table">
            <thead>
                <tr>
                    <th>{{ __('messages.no_impl') }}</th>
                    <th>{{ __('messages.koperasi') }}</th>
                    <th>{{ __('messages.aplikasi') }}</th>
                    <th>{{ __('messages.anggota_hadir') }}</th>
                    <th>{{ __('messages.go_live') }}</th>
                    <th>{{ __('messages.progres') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.next_action') }}</th>
                    <th>{{ __('messages.aksi') }}</th>
                </tr>
            </thead>
            <!-- Skeleton Loading Rows -->
            <tbody id="skeleton-loading-body">
                @for($i = 0; $i < 4; $i++)
                <tr>
                    <td><div class="skeleton-box" style="width: 120px; height: 16px;"></div></td>
                    <td><div class="skeleton-box" style="width: 140px; height: 16px;"></div></td>
                    <td><div class="skeleton-box" style="width: 80px; height: 16px;"></div></td>
                    <td><div class="skeleton-box" style="width: 30px; height: 16px;"></div></td>
                    <td><div class="skeleton-box" style="width: 90px; height: 16px;"></div></td>
                    <td>
                        <div class="skeleton-box" style="width: 45px; height: 14px; margin-bottom: 5px;"></div>
                        <div class="skeleton-box" style="width: 100px; height: 8px;"></div>
                    </td>
                    <td><div class="skeleton-box" style="width: 100px; height: 24px; border-radius: 20px;"></div></td>
                    <td>
                        <div class="skeleton-box" style="width: 130px; height: 14px;"></div>
                    </td>
                    <td><div class="skeleton-box" style="width: 60px; height: 28px; border-radius: 6px;"></div></td>
                </tr>
                @endfor
            </tbody>

            <!-- Real Table Body -->
            <tbody id="real-table-body" style="display: none;">
                @forelse($implementasis as $impl)
                    @php
                        $statusText = strtolower($impl->status);
                        $badgeClass = 'badge-abu';
                        
                        if (str_contains($statusText, 'dijadwalkan') || str_contains($statusText, 'monitoring')) {
                            $badgeClass = 'badge-biru';
                        } elseif (str_contains($statusText, 'menunggu') || str_contains($statusText, 'pengecekan') || str_contains($statusText, 'update') || str_contains($statusText, 'perbaikan') || str_contains($statusText, 'pending') || str_contains($statusText, 'follow-up')) {
                            $badgeClass = 'badge-oranye';
                        } elseif (str_contains($statusText, 'proses') || str_contains($statusText, 'persiapan') || str_contains($statusText, 'pendampingan') || str_contains($statusText, 'olah') || str_contains($statusText, 'migrasi') || str_contains($statusText, 'berlangsung')) {
                            $badgeClass = 'badge-kuning';
                        } elseif (str_contains($statusText, 'revisi') || str_contains($statusText, 'hold') || str_contains($statusText, 'dibatalkan') || str_contains($statusText, 'masalah') || str_contains($statusText, 'ulang')) {
                            $badgeClass = 'badge-merah';
                        } elseif (str_contains($statusText, 'selesai') || str_contains($statusText, 'stabil') || str_contains($statusText, 'siap go-live') || str_contains($statusText, 'go-live') || str_contains($statusText, 'running')) {
                            $badgeClass = 'badge-hijau';
                        }
                    @endphp
                    <tr>
                        <td style="white-space: nowrap;"><strong>{{ $impl->nomor_implementasi }}</strong></td>
                        <td style="max-width: 180px; word-break: break-word; white-space: normal;">
                            {{ $impl->instansi->nama_instansi ?? '-' }}
                            @if($impl->kantor_cabang)
                                <div style="margin-top: 4px;">
                                    <span style="display: inline-block; padding: 2px 6px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 11px; font-weight: 500;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline; margin-bottom: -1px; margin-right: 2px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        {{ $impl->kantor_cabang }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td style="max-width: 220px; word-break: break-word; white-space: normal;">
                            @if($impl->aplikasis && $impl->aplikasis->count() > 0)
                                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    @foreach($impl->aplikasis as $appItem)
                                        <span style="display: inline-block; padding: 2px 7px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; border-radius: 4px; font-size: 11.5px; font-weight: 500;">{{ $appItem->nama_aplikasi }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span style="display: inline-block; padding: 2px 7px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; border-radius: 4px; font-size: 11.5px; font-weight: 500;">{{ $impl->aplikasi->nama_aplikasi ?? '-' }}</span>
                            @endif
                        </td>
                        <td style="max-width: 250px; word-break: break-word; white-space: normal;">
                            @if($impl->anggota_hadir)
                                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    @foreach(explode(',', $impl->anggota_hadir) as $anggotaItem)
                                        <span style="display: inline-block; padding: 2px 7px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 11.5px; font-weight: 500;">{{ trim($anggotaItem) }}</span>
                                    @endforeach
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td style="white-space: nowrap;">{{ $impl->target_go_live ? $impl->target_go_live->format('d M Y') : '-' }}</td>
                        <td style="min-width: 120px;">
                            <div style="font-weight: 600;">{{ $impl->progres }}%</div>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: {{ $impl->progres }}%;"></div>
                            </div>
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $impl->status }}</span></td>
                        <td style="max-width: 200px; word-break: break-word; white-space: normal;">
                            @if($impl->tindakan_berikutnya || $impl->status_tindakan)
                                <div style="font-size: 13px; font-weight: 500;">{{ $impl->tindakan_berikutnya ?? '-' }}</div>
                                <div style="font-size: 12px; margin-top: 4px; color: var(--primary);">Status: {{ $impl->status_tindakan ?? '-' }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <a href="{{ route('implementasi.show', $impl->id) }}" class="btn-action">{{ __('messages.col_detail') }}</a>
                                
                                @if(Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                                <div class="dropdown-kebab">
                                    <button type="button" class="btn-kebab" onclick="toggleKebab({{ $impl->id }}, event)">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                    </button>
                                    <div id="kebab-menu-{{ $impl->id }}" class="kebab-menu-content">
                                        <button type="button" class="kebab-item" onclick="openEditModal({{ $impl->id }})">{{ __('messages.btn_edit') }}</button>
                                        <form action="{{ route('implementasi.destroy', $impl->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('{{ __('messages.confirm_delete_impl') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="kebab-item text-red">{{ __('messages.btn_hapus') }}</button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 30px; color: #64748b;">{{ __('messages.empty_impl_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</div>

<!-- Modal Data Baru -->
<style>
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-overlay.active { display: flex; }
    .modal-container {
        background: #fff;
        width: 100%;
        max-width: 600px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .dark-mode .modal-container {
        background: #1e293b;
        color: #f8fafc;
    }
    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dark-mode .modal-header { border-color: #334155; }
    .modal-body {
        padding: 20px;
        max-height: 70vh;
        overflow-y: auto;
    }
    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .dark-mode .modal-footer { border-color: #334155; }
    
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px; }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
    }
    .dark-mode .form-control {
        background: #0f172a;
        border-color: #334155;
        color: #f8fafc;
    }
    .btn-close-modal { background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b; }
    .btn-secondary { background: #e2e8f0; color: #475569; padding: 8px 15px; border-radius: 4px; border: none; cursor: pointer; font-weight: 500;}
    .dark-mode .btn-secondary { background: #334155; color: #cbd5e1; }
    .btn-primary { background: #2563eb; color: #fff; padding: 8px 15px; border-radius: 4px; border: none; cursor: pointer; font-weight: 500;}
    
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
</style>

<div class="modal-overlay" id="modalDataBaru">
    <div class="modal-container">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 16px;">{{ __('messages.add_impl_data') }}</h3>
            <button class="btn-close-modal" onclick="closeModal('modalDataBaru')">&times;</button>
        </div>
        <form action="{{ route('implementasi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div id="modalDataBaruAlert" style="display: none; background-color: #dcfce7; color: #15803d; padding: 10px 14px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #86efac; font-size: 13px; font-weight: 600; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span id="modalDataBaruAlertText"></span>
                </div>

                @if($errors->any())
                    <div style="background-color: #fef2f2; color: #b91c1c; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #fca5a5; font-size: 13px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            openModal('modalDataBaru');
                        });
                    </script>
                @endif
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.koperasi') }}</label>
                        <select name="instansi_id" class="form-control searchable-select allow-create" required>
                            <option value="">{{ __('messages.select_koperasi') }}</option>
                            @foreach($instansis as $instansi)
                                <option value="{{ $instansi->instansi_id }}">{{ $instansi->nama_instansi }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="kantor_cabang" class="form-control mt-2" placeholder="Kantor / Cabang (Opsional)" value="{{ old('kantor_cabang') }}" style="margin-top: 8px;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            @php
                                $createStatuses = [
                                    'Pelatihan Dijadwalkan' => 'status_impl_pelatihan_dijadwalkan',
                                    'Pelatihan Berlangsung' => 'status_impl_pelatihan_berlangsung',
                                    'Pelatihan Selesai' => 'status_impl_pelatihan_selesai',
                                    'Persiapan Data' => 'status_impl_persiapan_data',
                                    'Olah data' => 'status_impl_olah_data',
                                    'Migrasi data' => 'status_impl_migrasi_data',
                                    'Menunggu Validasi Data' => 'status_impl_menunggu_validasi_data',
                                    'Pengecekan' => 'status_impl_pengecekan',
                                    'Update/Perbaikan Aplikasi' => 'status_impl_update_perbaikan_aplikasi',
                                    'Monitoring' => 'status_impl_monitoring',
                                    'Pendampingan' => 'status_impl_pendampingan',
                                    'Running' => 'status_impl_running',
                                    'Go-Live' => 'status_impl_go_live',
                                    'Pending' => 'status_impl_pending',
                                    'Follow-up' => 'status_impl_follow_up',
                                    'Pelatihan Ulang' => 'status_impl_pelatihan_ulang',
                                    'Implementasi Selesai' => 'status_impl_implementasi_selesai',
                                    'Dibatalkan' => 'status_impl_dibatalkan',
                                ];
                            @endphp
                            @foreach($createStatuses as $val => $key)
                                @php
                                    $lbl = __('messages.' . $key);
                                    if ($lbl === 'messages.' . $key) {
                                        $lbl = $val;
                                    }
                                @endphp
                                <option value="{{ $val }}" {{ $val == 'Pelatihan Selesai' ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">{{ __('messages.aplikasi_modul') }}</label>
                    <div id="aplikasi-container">
                        <div class="aplikasi-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <select name="aplikasi_id[]" class="form-control searchable-select" required style="flex-grow: 1;">
                                <option value="" disabled selected>{{ __('messages.select_aplikasi') }}</option>
                                @foreach($aplikasis as $app)
                                    <option value="{{ $app->aplikasi_id }}">{{ $app->nama_aplikasi }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addAplikasiInput()">+</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('messages.tanggal_pelatihan') }}</label>
                    <div class="grid-2">
                        <div>
                            <label class="form-label" style="font-weight: normal; font-size: 12px; color: #64748b;">{{ __('messages.mulai') }}</label>
                            <input type="date" name="tanggal_pelatihan" class="form-control" required>
                        </div>
                        <div>
                            <label class="form-label" style="font-weight: normal; font-size: 12px; color: #64748b;">{{ __('messages.selesai_label') }}</label>
                            <input type="date" name="tanggal_selesai" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="background: var(--paper-sunken, #f8fafc); padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 15px;">
                    <label class="form-label" style="font-weight: 600; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>Masa Berlaku Tim Support</span>
                    </label>
                    <div class="grid-2">
                        <div>
                            <label class="form-label" style="font-weight: normal; font-size: 12px; color: #64748b;">Mulai Support</label>
                            <input type="date" name="tanggal_mulai_support" class="form-control">
                        </div>
                        <div>
                            <label class="form-label" style="font-weight: normal; font-size: 12px; color: #64748b;">Hingga Tanggal (Selesai Support)</label>
                            <input type="date" name="tanggal_selesai_support" class="form-control">
                        </div>
                    </div>
                    <div style="margin-top: 8px;">
                        <input type="text" name="durasi_support" class="form-control" placeholder="Durasi Support (Opsional, misal: 6 Bulan / 1 Tahun)">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.metode_pelatihan') }}</label>
                        <select name="metode_pelatihan" class="form-control" required>
                            <option value="Online (Zoom/Meet)">Online (Zoom/Meet)</option>
                            <option value="Offline (Kunjungan)">Offline (Kunjungan)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.berita_acara_pdf') }}</label>
                        <input type="file" name="berita_acara" class="form-control" accept=".pdf">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.anggota_hadir') }}</label>
                        <div id="anggota-container">
                            <div class="anggota-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <input type="text" name="anggota_hadir[]" class="form-control" placeholder="{{ __('messages.nama_anggota') }}" required style="flex: 1;">
                                <select name="posisi_anggota[]" class="form-control" onchange="this.style.color = this.value ? '' : '#94a3b8';" style="flex: 1; color: #94a3b8;">
                                    <option value="" disabled selected hidden style="color: #94a3b8;">{{ __('messages.posisi') }}</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Sekretaris">Sekretaris</option>
                                    <option value="Bendahara">Bendahara</option>
                                    <option value="Pengawas">Pengawas</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Akuntansi">Akuntansi</option>
                                    <option value="IT">IT</option>
                                    <option value="Staff">Staff</option>
                                </select>
                                <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addAnggotaInput()">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.nama_trainer') }}</label>
                        <div id="trainer-container">
                            <div class="trainer-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <select name="nama_trainer[]" class="form-control searchable-select">
                                    <option value="" disabled selected hidden>Pilih Trainer</option>
                                    @foreach($usersSupport as $user)
                                        <option value="{{ $user->nama }}">{{ $user->nama }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addTrainerInput()">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.email_pic') }}</label>
                        <input type="email" name="email_pic" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.whatsapp_pic') }}</label>
                        <div id="kontak-container">
                            <div class="kontak-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <input type="text" name="kontak_pic[]" class="form-control" required>
                                <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addKontakInput()">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('messages.catatan_pelatihan') }}</label>
                    <textarea name="catatan_pelatihan" class="form-control" rows="2" placeholder="{{ __('messages.placeholder_hasil_pelatihan') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalDataBaru')">{{ __('messages.btn_batal') }}</button>
                <button type="submit" class="btn-primary">{{ __('messages.btn_simpan_checklist') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Data -->
<style>
    .skeleton-box {
        background: #e2e8f0;
        border-radius: 4px;
        position: relative;
        overflow: hidden;
    }
    .skeleton-box::after {
        content: "";
        position: absolute;
        top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: loading 1.5s infinite;
    }
    @keyframes loading {
        100% { left: 100%; }
    }
    .dark-mode .skeleton-box { background: #334155; }
    .dark-mode .skeleton-box::after { background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent); }
</style>
<div class="modal-overlay" id="modalEditData">
    <div class="modal-container">
        <div class="modal-header">
            <h3 style="margin: 0; font-size: 16px;">{{ __('messages.edit_impl_data') }}</h3>
            <button class="btn-close-modal" onclick="closeModal('modalEditData')">&times;</button>
        </div>
        <div id="edit-modal-content">
            <!-- Form will be loaded here via AJAX -->
            <div style="padding: 20px;">
                <div class="skeleton-box" style="width: 30%; height: 14px; margin-bottom: 8px;"></div>
                <div class="skeleton-box" style="width: 100%; height: 38px; margin-bottom: 20px; border-radius: 6px;"></div>
                
                <div class="skeleton-box" style="width: 30%; height: 14px; margin-bottom: 8px;"></div>
                <div class="skeleton-box" style="width: 100%; height: 38px; margin-bottom: 20px; border-radius: 6px;"></div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <div class="skeleton-box" style="width: 50%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton-box" style="width: 100%; height: 38px; border-radius: 6px;"></div>
                    </div>
                    <div>
                        <div class="skeleton-box" style="width: 50%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton-box" style="width: 100%; height: 38px; border-radius: 6px;"></div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                    <div class="skeleton-box" style="width: 80px; height: 38px; border-radius: 4px;"></div>
                    <div class="skeleton-box" style="width: 140px; height: 38px; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function addAnggotaInput() {
        const container = document.getElementById('anggota-container');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'anggota-input-group';
        inputGroup.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
        
        inputGroup.innerHTML = `
            <input type="text" name="anggota_hadir[]" class="form-control" placeholder="{{ __('messages.nama_anggota') }}" required style="flex: 1;">
            <select name="posisi_anggota[]" class="form-control" onchange="this.style.color = this.value ? '' : '#94a3b8';" style="flex: 1; color: #94a3b8;">
                <option value="" disabled selected hidden style="color: #94a3b8;">{{ __('messages.posisi') }}</option>
                <option value="Manager">Manager</option>
                <option value="Sekretaris">Sekretaris</option>
                <option value="Bendahara">Bendahara</option>
                <option value="Pengawas">Pengawas</option>
                <option value="Admin">Admin</option>
                <option value="Akuntansi">Akuntansi</option>
                <option value="IT">IT</option>
                <option value="Staff">Staff</option>
            </select>
            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="removeAnggotaInput(this)">-</button>
        `;
        container.appendChild(inputGroup);
    }

    function removeAnggotaInput(btn) {
        btn.parentElement.remove();
    }

    function addTrainerInput() {
        const container = document.getElementById('trainer-container');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'trainer-input-group';
        inputGroup.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
        
        let optionsHtml = '<option value="" disabled selected hidden>Pilih Trainer</option>';
        @foreach($usersSupport as $user)
            optionsHtml += '<option value="{{ addslashes($user->nama) }}">{{ addslashes($user->nama) }}</option>';
        @endforeach
        
        inputGroup.innerHTML = `
            <select name="nama_trainer[]" class="form-control searchable-select">
                ${optionsHtml}
            </select>
            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="removeTrainerInput(this)">-</button>
        `;
        container.appendChild(inputGroup);
        // If searchable select logic requires re-init, it should be done here if needed.
    }

    function removeTrainerInput(btn) {
        btn.parentElement.remove();
    }

    function addKontakInput() {
        const container = document.getElementById('kontak-container');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'kontak-input-group';
        inputGroup.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
        
        inputGroup.innerHTML = `
            <input type="text" name="kontak_pic[]" class="form-control" required>
            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="removeKontakInput(this)">-</button>
        `;
        container.appendChild(inputGroup);
    }

    function removeKontakInput(btn) {
        btn.parentElement.remove();
    }

    function addAplikasiInput() {
        const container = document.getElementById('aplikasi-container');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'aplikasi-input-group';
        inputGroup.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
        
        inputGroup.innerHTML = `
            <select name="aplikasi_id[]" class="form-control searchable-select" required style="flex-grow: 1;">
                <option value="" disabled selected>Pilih Aplikasi</option>
                @foreach($aplikasis as $app)
                    <option value="{{ $app->aplikasi_id }}">{{ $app->nama_aplikasi }}</option>
                @endforeach
            </select>
            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="removeAplikasiInput(this)">-</button>
        `;
        container.appendChild(inputGroup);
        
        const newSelect = inputGroup.querySelector('select');
        if (typeof TomSelect !== 'undefined') {
            new TomSelect(newSelect, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: newSelect.getAttribute('placeholder') || 'Pilih...',
                onChange: function(value) {
                    if (typeof updateAplikasiOptions === 'function') updateAplikasiOptions();
                }
            });
        }
        updateAplikasiOptions();
    }

    function removeAplikasiInput(btn) {
        btn.parentElement.remove();
        updateAplikasiOptions();
    }
    
    function updateAplikasiOptions() {
        const containers = [
            document.getElementById('modalDataBaru'),
            document.getElementById('modalEditData')
        ];
        
        containers.forEach(container => {
            if (!container) return;
            const selects = container.querySelectorAll('select[name="aplikasi_id[]"]');
            if (selects.length === 0) return;
            
            const selectedValues = Array.from(selects).map(s => s.value).filter(v => v);
    
            selects.forEach(select => {
                let ts = select.tomselect;
                
                if (ts) {
                    Array.from(select.options).forEach(option => {
                        if (!option.value) return; 
                        let shouldDisable = selectedValues.includes(option.value) && select.value !== option.value;
                        let optionData = ts.options[option.value];
                        if (optionData && optionData.disabled !== shouldDisable) {
                            ts.updateOption(option.value, Object.assign({}, optionData, { disabled: shouldDisable }));
                        }
                    });
                } else {
                    Array.from(select.options).forEach(option => {
                        if (!option.value) return; 
                        let shouldDisable = selectedValues.includes(option.value) && select.value !== option.value;
                        option.disabled = shouldDisable;
                    });
                }
            });
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.name === 'aplikasi_id[]') {
            updateAplikasiOptions();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Run once on load to disable initially selected items
        setTimeout(updateAplikasiOptions, 500);
    });
    // Kebab Menu Logic
    function toggleKebab(id, event) {
        event.stopPropagation();
        // Close all other kebabs
        document.querySelectorAll('.kebab-menu-content').forEach(menu => {
            if (menu.id !== 'kebab-menu-' + id) {
                menu.classList.remove('show');
            }
        });
        // Toggle current kebab
        document.getElementById('kebab-menu-' + id).classList.toggle('show');
    }

    // Close kebab menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown-kebab')) {
            document.querySelectorAll('.kebab-menu-content').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });

    // Edit Modal Logic
    function openEditModal(id) {
        // Tutup kebab menu yang sedang terbuka
        document.querySelectorAll('.kebab-menu-content').forEach(menu => {
            menu.classList.remove('show');
        });
        
        openModal('modalEditData');
        const container = document.getElementById('edit-modal-content');
        container.innerHTML = `
            <div style="padding: 20px;">
                <div class="skeleton-box" style="width: 30%; height: 14px; margin-bottom: 8px;"></div>
                <div class="skeleton-box" style="width: 100%; height: 38px; margin-bottom: 20px; border-radius: 6px;"></div>
                
                <div class="skeleton-box" style="width: 30%; height: 14px; margin-bottom: 8px;"></div>
                <div class="skeleton-box" style="width: 100%; height: 38px; margin-bottom: 20px; border-radius: 6px;"></div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <div class="skeleton-box" style="width: 50%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton-box" style="width: 100%; height: 38px; border-radius: 6px;"></div>
                    </div>
                    <div>
                        <div class="skeleton-box" style="width: 50%; height: 14px; margin-bottom: 8px;"></div>
                        <div class="skeleton-box" style="width: 100%; height: 38px; border-radius: 6px;"></div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                    <div class="skeleton-box" style="width: 80px; height: 38px; border-radius: 4px;"></div>
                    <div class="skeleton-box" style="width: 140px; height: 38px; border-radius: 4px;"></div>
                </div>
            </div>`;
        
        const editUrl = "{{ url('implementasi') }}/" + id + "/edit";
        fetch(editUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Terjadi kesalahan server (' + response.status + '). Gagal memuat form edit.');
            return response.text();
        })
        .then(html => {
            container.innerHTML = html;
            container.querySelectorAll('.searchable-select').forEach(function(el) {
                if (typeof initSingleTomSelect === 'function') {
                    initSingleTomSelect(el);
                }
            });
        })
        .catch(error => {
            container.innerHTML = `<div style="padding: 30px; text-align: center; color: #ef4444; font-weight: 600;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block; margin: 0 auto 10px auto;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                ${error.message || 'Gagal memuat form edit.'}
            </div>`;
        });
    }

    // Dynamic inputs untuk form edit
    function addEditAplikasiInput() {
        const container = document.getElementById('edit-aplikasi-container');
        const originalGroup = container.querySelector('.aplikasi-input-group');
        const newGroup = document.createElement('div');
        newGroup.className = 'aplikasi-input-group';
        newGroup.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
        
        const originalSelect = originalGroup.querySelector('select').cloneNode(true);
        originalSelect.className = 'form-control searchable-select';
        originalSelect.removeAttribute('id');
        originalSelect.style.display = '';
        originalSelect.selectedIndex = 0;
        
        newGroup.appendChild(originalSelect);
        
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-action';
        btn.style = 'background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0;';
        btn.textContent = '-';
        btn.onclick = function() { removeEditInput(this); };
        newGroup.appendChild(btn);
        
        container.appendChild(newGroup);
        
        if (typeof TomSelect !== 'undefined') {
            new TomSelect(originalSelect, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: originalSelect.getAttribute('placeholder') || 'Pilih...',
                onChange: function(value) {
                    if (typeof updateAplikasiOptions === 'function') updateAplikasiOptions();
                }
            });
        }
        if (typeof updateAplikasiOptions === 'function') updateAplikasiOptions();
    }
    
    function addEditAnggotaInput() {
        const container = document.getElementById('edit-anggota-container');
        const originalGroup = container.querySelector('.anggota-input-group');
        const newGroup = originalGroup.cloneNode(true);
        newGroup.querySelector('input').value = '';
        
        const btn = newGroup.querySelector('button');
        btn.textContent = '-';
        btn.style.backgroundColor = '#ef4444';
        btn.onclick = function() { removeEditInput(this); };
        
        container.appendChild(newGroup);
    }
    
    function addEditTrainerInput() {
        const container = document.getElementById('edit-trainer-container');
        const originalGroup = container.querySelector('.trainer-input-group');
        const newGroup = originalGroup.cloneNode(true);
        const selectEl = newGroup.querySelector('select');
        if(selectEl) selectEl.value = '';
        
        const btn = newGroup.querySelector('button');
        btn.textContent = '-';
        btn.style.backgroundColor = '#ef4444';
        btn.onclick = function() { removeEditInput(this); };
        
        container.appendChild(newGroup);
    }

    function addEditKontakInput() {
        const container = document.getElementById('edit-kontak-container');
        const originalGroup = container.querySelector('.kontak-input-group');
        const newGroup = originalGroup.cloneNode(true);
        newGroup.querySelector('input').value = '';
        
        const btn = newGroup.querySelector('button');
        btn.textContent = '-';
        btn.style.backgroundColor = '#ef4444';
        btn.onclick = function() { removeEditInput(this); };
        
        container.appendChild(newGroup);
    }

    function removeEditInput(btn) {
        btn.parentElement.remove();
        if (typeof updateAplikasiOptions === 'function') updateAplikasiOptions();
    }

    // Hide Skeleton Loading & Show Real Content
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const skeleton = document.getElementById('skeleton-loading-body');
            const realBody = document.getElementById('real-table-body');
            if (skeleton && realBody) {
                skeleton.style.display = 'none';
                realBody.style.display = '';
            }
        }, 450);
    });
</script>
@endsection
