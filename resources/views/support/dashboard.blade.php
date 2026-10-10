@extends('layouts.app')

@section('content')

<div class="pelapor-panel active">

{{-- ═══════════════════════════════════════════ --}}
{{-- SKELETON LOADING STATE                      --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="skeleton-wrap" id="skeleton-loading">
    {{-- Page Head Skeleton --}}
    <div class="skel-page-head">
        <div>
            <div class="skel skel-page-head-eyebrow"></div>
            <div class="skel skel-page-head-title"></div>
        </div>
        <div class="skel skel-page-head-btn"></div>
    </div>

    {{-- Toolbar Skeleton --}}
    <div class="skel-toolbar">
        <div class="skel skel-toolbar-search"></div>
        <div class="skel skel-toolbar-chip"></div>
        <div class="skel skel-toolbar-chip"></div>
        <div class="skel skel-toolbar-chip"></div>
    </div>

    {{-- Table Skeleton --}}
    <div class="skel-table">
        <div class="skel-table-head">
            <div class="skel skel-table-head-cell" style="width:80px;"></div>
            <div class="skel skel-table-head-cell" style="width:70px;"></div>
            <div class="skel skel-table-head-cell" style="width:140px;"></div>
            <div class="skel skel-table-head-cell" style="flex:1;"></div>
            <div class="skel skel-table-head-cell" style="width:70px;"></div>
            <div class="skel skel-table-head-cell" style="width:80px;"></div>
        </div>
        @for($i = 0; $i < 5; $i++)
        <div class="skel-table-row">
            <div class="skel skel-table-cell" style="width:80px;"></div>
            <div class="skel skel-table-cell" style="width:70px;"></div>
            <div class="skel skel-table-cell" style="width:140px;"></div>
            <div class="skel skel-table-cell" style="flex:1;"></div>
            <div class="skel skel-table-cell" style="width:68px; border-radius:100px;"></div>
            <div class="skel skel-table-cell" style="width:80px;"></div>
        </div>
        @endfor
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- ACTUAL CONTENT                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="content-wrap" id="actual-content" style="display: none;">

@if(session('success'))
    <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); transition: opacity 0.6s ease, transform 0.6s ease;">
        <span>{{ session('success') }}</span>
        <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
    </div>
@endif

<div class="page-head fade-up" style="animation-delay: 0.1s;">
    <div>
        <p class="eyebrow">{{ __('messages.dashboard_support') }}</p>
        <h1>{{ __('messages.manajemen_laporan') }}</h1>
    </div>
    <div>
        <a href="{{ route('support.recap') }}" class="btn btn-amber">{{ __('messages.buka_rekapitulasi') }}</a>
    </div>
</div>

<div class="toolbar fade-up" style="animation-delay: 0.15s;">
    <div class="search">
        <img src="{{ asset('magnifying-glass.png') }}" alt="Search" style="width: 14px; height: 14px; margin-right: 8px; vertical-align: middle; opacity: 0.4; filter: grayscale(100%);">
        <input type="text" placeholder="{{ __('messages.cari_tiket') }}" style="border:none; background:transparent; width:100%; outline:none;" id="search-input">
    </div>
    <form id="filter-form" action="{{ route('support.dashboard') }}" method="GET" style="margin:0;">
        <select name="status" onchange="document.getElementById('filter-form').submit()" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--line); font-family: var(--font-body); font-weight: 500; color: var(--ink); background: var(--paper-raised); cursor: pointer; outline:none;">
            <option value="">{{ __('messages.semua_status') }}</option>
            <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
            <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Proses</option>
            <option value="In Review" {{ request('status') == 'In Review' ? 'selected' : '' }}>In Review</option>
            <option value="Waiting" {{ request('status') == 'Waiting' ? 'selected' : '' }}>Waiting</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>{{ __('messages.selesai') }}</option>
        </select>
    </form>
</div>

@if($pendingUsers->count() > 0)
<div class="fade-up" style="animation-delay: 0.2s; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(145deg, #fffbeb, #fef3c7); border-left: 4px solid #f59e0b; padding: 16px 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div style="background: #fef08a; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);">
            <img src="{{ asset('bell.png') }}" alt="Notification" style="width: 26px; height: 26px; object-fit: contain;">
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <div style="font-weight: 700; color: #92400e; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                {{ __('messages.permintaan_verifikasi') }}
                <span style="background: #ef4444; color: white; font-size: 0.75rem; padding: 2px 8px; border-radius: 12px; font-weight: 600; letter-spacing: 0.5px;">{{ $pendingUsers->count() }} {{ __('messages.baru') }}</span>
            </div>
            <div style="color: #b45309; font-size: 0.9rem;">{{ __('messages.akun_menunggu', ['count' => $pendingUsers->count()]) }}</div>
        </div>
    </div>
    <button class="btn" style="background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);" onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'" onclick="openModal('modal-verify')">
        {{ __('messages.lihat_verifikasi') }}
    </button>
</div>
@endif

<div class="fade-up" style="animation-delay: 0.25s; overflow-x: auto; width: 100%;">
<table class="tickets" id="tickets-table" style="min-width: 1100px;">
    <thead>
        <tr>
            <th style="text-transform: uppercase;">{{ __('messages.col_tiket') }}</th>
            <th style="text-transform: uppercase;">{{ __('messages.col_nama_koperasi') }}</th>
            <th style="text-transform: uppercase;">{{ __('messages.col_pic_koperasi') }}</th>
            <th style="text-transform: uppercase;">{{ __('messages.col_aplikasi') }}</th>
            <th style="text-transform: uppercase;">{{ __('messages.col_kategori') }}</th>
            <th style="text-transform: uppercase;">{{ __('messages.col_pic_support') }}</th>
            <th width="100" style="text-transform: uppercase;">{{ __('messages.col_status') }}</th>
            <th width="120" style="text-transform: uppercase;">{{ __('messages.col_tanggal') }}</th>
            <th width="100"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $t)
        <tr class="clickable-row hoverable-row" data-target="modal-preview-{{ $t->ticket_id }}" style="cursor: pointer;">
            <td class="mono" style="color: var(--text-muted); font-size: 0.85rem; white-space: nowrap;">{{ $t->ticket_id }}</td>
            <td style="font-weight: 500; color: var(--ink); font-size: 0.9rem; white-space: nowrap;">{{ $t->pelapor->instansi->nama_instansi ?? '-' }}</td>
            <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                    @if($t->pelapor && $t->pelapor->avatar)
                        <img src="{{ asset('storage/' . $t->pelapor->avatar) }}" alt="Avatar" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; display: block;">
                    @else
                        @php
                            $nameParts = explode(' ', $t->pelapor->nama ?? 'U');
                            $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                        @endphp
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 600;">
                            {{ $initials }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight: 500; color: var(--text-muted); font-size: 0.85rem; white-space: nowrap;">{{ $t->pelapor->nama ?? '-' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); opacity: 0.95;">{{ $t->pelapor->whatsapp ?? $t->pelapor->instansi->no_telp ?? '-' }}</div>
                    </div>
                </div>
            </td>
            <td style="color: var(--text-muted); font-size: 0.9rem; white-space: nowrap;">{{ $t->aplikasi->nama_aplikasi ?? '-' }}</td>
            <td>
                @if($t->kategori)
                    <span style="display: inline-block; white-space: nowrap; background: #ffe4e6; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; color: #be123c; border: none; text-transform: uppercase;">
                        {{ $t->kategori->nama_kategori }}
                    </span>
                @else
                    -
                @endif
            </td>
            <td>
                @if($t->picSupport)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if($t->picSupport->avatar)
                            <img src="{{ asset('storage/' . $t->picSupport->avatar) }}" alt="Avatar" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; display: block;">
                        @else
                            @php
                                $picParts = explode(' ', $t->picSupport->nama);
                                $picInitials = strtoupper(substr($picParts[0], 0, 1) . (isset($picParts[1]) ? substr($picParts[1], 0, 1) : ''));
                            @endphp
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 600;">
                                {{ $picInitials }}
                            </div>
                        @endif
                        <span style="font-weight: 500; color: var(--text-muted); font-size: 0.85rem; white-space: nowrap;">{{ $t->picSupport->nama }}</span>
                    </div>
                @else
                    -
                @endif
            </td>
            <td>
                @php
                    $rawStatus = $t->status instanceof \BackedEnum ? $t->status->value : (string)$t->status;
                    $statusStr = strtolower($rawStatus);
                    $statusClass = match(true) {
                        str_contains($statusStr, 'open') => 'status-open',
                        str_contains($statusStr, 'proses') => 'status-proses',
                        str_contains($statusStr, 'pending') => 'status-pending',
                        str_contains($statusStr, 'review') => 'status-review',
                        str_contains($statusStr, 'waiting') => 'status-waiting',
                        str_contains($statusStr, 'done') || str_contains($statusStr, 'selesai') => 'status-done',
                        default => 'status-open'
                    };
                @endphp
                <span class="status {{ $statusClass }}" data-status="{{ $statusStr }}">{{ $rawStatus }}</span>
            </td>
            <td class="mono" style="color: var(--text-muted); font-size: 0.85rem; white-space: nowrap;">
                {{ $t->updated_at->format('d M Y') }}
            </td>
            <td style="display: flex; gap: 6px; justify-content: flex-end;">
                <a href="{{ route('support.tickets.dokumen', $t->ticket_id) }}" class="btn btn-ghost btn-sm" style="padding: 6px 10px; color: #3b82f6; border: 1px solid #bfdbfe; background: #eff6ff;" title="Lihat Riwayat Dokumen">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </a>
                <button class="btn btn-primary btn-sm" onclick="openModal('modal-edit-{{ $t->ticket_id }}')">
                    {{ $t->status === \App\Enums\TicketStatus::DONE ? __('messages.selesai') : __('messages.respons') }}
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@if($tickets->hasPages())
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--line); background: var(--paper);">
        {{ $tickets->appends(request()->query())->links('vendor.pagination.custom') }}
    </div>
@endif

</div>

<!-- Modals for Tickets -->
@foreach($tickets as $t)
<div class="overlay" id="modal-preview-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head" style="border-bottom: 1px solid var(--line); padding-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 2px;">{{ __('messages.modal_preview_title') }}</h3>
                    <p class="mono" style="font-size: 0.8rem; color: var(--text-muted);">{{ $t->ticket_id }}</p>
                </div>
            </div>
            <button type="button" class="modal-x" onclick="closeModal('modal-preview-{{ $t->ticket_id }}')">✕</button>
        </div>
        
        <div class="modal-body" style="padding: 24px;">
            <!-- Informasi Waktu -->
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                <div style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #1d4ed8; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    {{ __('messages.disubmit') }} {{ $t->tanggal_input->locale(app()->getLocale())->diffForHumans(['parts' => 2]) }} &middot; {{ $t->tanggal_input->format('d M Y, H:i') }}
                </div>
                @if($t->status === \App\Enums\TicketStatus::DONE && $t->tanggal_penyelesaian)
                <div style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #065f46; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    {{ __('messages.selesai') }} &middot; {{ $t->tanggal_penyelesaian->format('d M Y, H:i') }}
                </div>
                @endif
            </div>

            <!-- Kendala Utama -->
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.deskripsi_permasalahan') }}</div>
                <div style="font-size: 0.95rem; color: var(--ink); line-height: 1.6; white-space: pre-wrap; background: var(--paper-raised); padding: 16px; border-radius: 8px; border: 1px solid var(--line);">{{ $t->permasalahan }}</div>
            </div>

            @if($t->lampiran)
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.lampiran_bukti') }}</div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @php $lampirans = is_array($t->lampiran) ? $t->lampiran : [$t->lampiran]; @endphp
                @foreach($lampirans as $lamp)
                    @php $ext = strtolower(pathinfo($lamp, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                        <div style="text-align: center;">
                            <a href="{{ Storage::url($lamp) }}" target="_blank">
                                <img src="{{ Storage::url($lamp) }}" alt="Lampiran" style="max-width: 100%; max-height: 140px; border-radius: 8px; border: 1px solid var(--line); display: block; object-fit: cover;">
                            </a>
                        </div>
                    @elseif($ext === 'mp4')
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                            {{ __('messages.lihat_video') }}
                        </button>
                    @elseif($ext === 'pdf')
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid var(--line); cursor: pointer;">
                            {{ __('messages.unduh_pdf') }}
                        </button>
                    @else
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid var(--line); cursor: pointer;">
                            Lihat {{ strtoupper($ext) }}
                        </button>
                    @endif
                @endforeach
                </div>
            </div>
            @endif

            @if($t->surat_balasan)
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.surat_balasan_dari_pelapor') }}</div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @php
                        $balasanFiles = json_decode($t->surat_balasan, true);
                        if (!is_array($balasanFiles)) {
                            $balasanFiles = [$t->surat_balasan];
                        }
                    @endphp
                    @foreach($balasanFiles as $index => $file)
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($file) }}', '{{ pathinfo($file, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($file)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid var(--sage); color: var(--sage); background: var(--sage-soft); padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; width: fit-content;">
                            {{ __('messages.lihat_surat_balasan') }} {{ count($balasanFiles) > 1 ? '#' . ($index + 1) : '' }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            @if($t->template_laporan)
                @php
                    $tplUrl = route('download.template', basename($t->template_laporan));
                @endphp
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Surat Laporan Template</div>
                    <button type="button" onclick="openUniversalPreview('{{ $tplUrl }}', '{{ pathinfo($t->template_laporan, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($t->template_laporan)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #3b82f6; color: #1d4ed8; background: #eff6ff; padding: 8px 14px; border-radius: 6px; font-weight: 600; width: fit-content; cursor: pointer;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span>{{ str_replace(['_', '-'], ' ', pathinfo($t->template_laporan, PATHINFO_FILENAME)) }}</span>
                    </button>
                </div>
            @endif

            @if($t->status === \App\Enums\TicketStatus::DONE || $t->penyelesaian)
                <!-- Tindakan Penyelesaian -->
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.tindakan_penyelesaian') }}</div>
                    <div style="font-size: 0.95rem; color: #065f46; line-height: 1.6; white-space: pre-wrap; background: #ecfdf5; padding: 16px; border-radius: 8px; border: 1px solid #a7f3d0;">{{ $t->penyelesaian ?? '-' }}</div>
                </div>

                <!-- Tindakan Pencegahan -->
                @if($t->pencegahan)
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.tindakan_pencegahan') }}</div>
                    <div style="font-size: 0.95rem; color: #92400e; line-height: 1.6; white-space: pre-wrap; background: #fffbeb; padding: 16px; border-radius: 8px; border: 1px solid #fde68a;">{{ $t->pencegahan }}</div>
                </div>
                @endif
            @endif

            @if($t->lampiran_support)
            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Lampiran Respons Support</div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @php $lampiranSupports = is_array($t->lampiran_support) ? $t->lampiran_support : [$t->lampiran_support]; @endphp
                @foreach($lampiranSupports as $lampSupp)
                    @php $extSupp = strtolower(pathinfo($lampSupp, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($extSupp, ['jpg', 'jpeg', 'png']))
                        <div style="text-align: center;">
                            <a href="{{ Storage::url($lampSupp) }}" target="_blank">
                                <img src="{{ Storage::url($lampSupp) }}" alt="Lampiran Support" style="max-width: 100%; max-height: 140px; border-radius: 8px; border: 1px solid var(--line); display: block; object-fit: cover;">
                            </a>
                        </div>
                    @elseif($extSupp === 'mp4')
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lampSupp) }}', '{{ $extSupp }}', '{{ basename($lampSupp) }}')" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                            {{ __('messages.lihat_video') }}
                        </button>
                    @elseif($extSupp === 'pdf')
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lampSupp) }}', '{{ $extSupp }}', '{{ addslashes(basename($lampSupp)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid var(--line); cursor: pointer;">
                            {{ __('messages.unduh_pdf') }}
                        </button>
                    @else
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lampSupp) }}', '{{ $extSupp }}', '{{ addslashes(basename($lampSupp)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid var(--line); cursor: pointer;">
                            Lihat {{ strtoupper($extSupp) }}
                        </button>
                    @endif
                @endforeach
                </div>
            </div>
            @endif

            <!-- Detail Pelapor & Aplikasi (Grid 2 Kolom Clean) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: var(--paper-sunken); padding: 18px; border-radius: 12px; border: 1px solid var(--line); align-items: start;">
                <!-- Kolom Kiri: Instansi & Aplikasi -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.instansi') }}</div>
                        <div style="font-weight: 600; color: var(--ink); font-size: 0.85rem;">{{ $t->pelapor->instansi->nama_instansi ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.aplikasi') }}</div>
                        <div style="font-weight: 600; color: var(--ink); font-size: 0.85rem;">{{ $t->aplikasi->nama_aplikasi ?? '-' }}</div>
                    </div>
                </div>

                <!-- Kolom Kanan: PIC Pelapor & Kategori -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.pic_pelapor') }}</div>
                        <div style="font-weight: 600; color: var(--ink); font-size: 0.85rem;">{{ $t->pelapor->nama ?? '-' }}</div>
                        @if($t->pelapor)
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; display: flex; flex-direction: column; gap: 4px;">
                            <div style="display: flex; align-items: center; gap: 5px; color: var(--ink-soft); font-weight: 500;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span>{{ $t->pelapor->whatsapp ?? $t->pelapor->instansi->no_telp ?? '-' }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 5px; color: var(--ink-soft); font-weight: 500;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span style="word-break: break-all;">{{ $t->pelapor->email ?? '-' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('support.pelapor.profile', $t->pelapor->user_id) }}" class="btn btn-ghost" style="padding: 4px 10px; font-size: 0.7rem; margin-top: 8px; border: 1px solid var(--line); display: inline-flex; align-items: center; gap: 4px; border-radius: 6px; text-decoration: none;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            {{ __('messages.lihat_profil') }}
                        </a>
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.kategori') }}</div>
                        <div style="font-weight: 600; color: var(--ink); font-size: 0.85rem;">
                            @if($t->kategori)
                                <span style="color: #be123c;">{{ $t->kategori->nama_kategori }}</span>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-foot" style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--line);">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modal-preview-{{ $t->ticket_id }}')">{{ __('messages.tutup') }}</button>
            <button type="button" class="btn btn-primary" onclick="closeModal('modal-preview-{{ $t->ticket_id }}'); openModal('modal-edit-{{ $t->ticket_id }}')">
                {{ $t->status === \App\Enums\TicketStatus::DONE ? 'Edit Respons' : __('messages.respons_tiket') }}
            </button>
        </div>
    </div>
</div>

<div class="overlay" id="modal-edit-{{ $t->ticket_id }}">
    <div class="modal" style="width: 800px; max-width: 95vw;">
        <div class="modal-head">
            <div>
                <h3>{{ $t->ticket_id }} &mdash; {{ Str::limit($t->permasalahan, 50) }}</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                    {{ $t->pelapor->instansi->nama_instansi ?? '-' }} (PIC: {{ $t->pelapor->nama ?? '-' }}) &middot; {{ __('messages.aplikasi') }}: {{ $t->aplikasi->nama_aplikasi ?? '-' }} &middot; {{ __('messages.col_tanggal') }}: {{ $t->tanggal_input->format('d M Y, H:i') }} <span style="font-weight: 500; color: var(--primary);">&mdash; {{ $t->tanggal_input->locale(app()->getLocale())->diffForHumans(['parts' => 2]) }}</span>
                </p>
            </div>
            <button type="button" class="modal-x" onclick="cancelEditModalSupport('{{ $t->ticket_id }}')">✕</button>
        </div>
        
        <form action="{{ route('support.tickets.update', $t->ticket_id) }}" method="POST" enctype="multipart/form-data" onsubmit="return checkFileSize(this, 'lampiran_input_supp_{{ $t->ticket_id }}', 5);">
            @csrf
            @method('PUT')
            
            <div class="modal-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- KOLOM KIRI -->
                <div>
                    <div class="field">
                        <label>{{ __('messages.deskripsi_permasalahan') }}</label>
                        <textarea readonly style="background: var(--paper-raised); min-height: 100px;">{{ $t->permasalahan }}</textarea>
                    </div>

                    @if($t->lampiran)
                    <div style="margin-bottom: 16px;">
                        <label>{{ __('messages.lampiran_bukti') }}</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @php $lampirans = is_array($t->lampiran) ? $t->lampiran : [$t->lampiran]; @endphp
                        @foreach($lampirans as $lamp)
                            @php $ext = strtolower(pathinfo($lamp, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                <div style="text-align: center;">
                                    <a href="{{ Storage::url($lamp) }}" target="_blank">
                                        <img src="{{ Storage::url($lamp) }}" alt="Lampiran" style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid var(--line); display: block; margin-top: 8px; object-fit: cover;">
                                    </a>
                                </div>
                            @elseif($ext === 'mp4')
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ basename($lamp) }}')" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; margin-top: 8px; cursor: pointer;">
                                    {{ __('messages.lihat_video') }}
                                </button>
                            @elseif($ext === 'pdf')
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid var(--line); margin-top: 8px; cursor: pointer;">
                                    {{ __('messages.unduh_pdf') }}
                                </button>
                            @else
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid var(--line); margin-top: 8px; cursor: pointer;">
                                    Lihat {{ strtoupper($ext) }}
                                </button>
                            @endif
                        @endforeach
                        </div>
                    </div>
                    @endif

                    @if($t->surat_balasan)
                    <div style="margin-bottom: 16px;">
                        <label>{{ __('messages.surat_balasan_dari_pelapor') }}</label>
                        <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 8px;">
                            @php
                                $balasanFiles = json_decode($t->surat_balasan, true);
                                if (!is_array($balasanFiles)) {
                                    $balasanFiles = [$t->surat_balasan];
                                }
                            @endphp
                            @foreach($balasanFiles as $index => $file)
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($file) }}', '{{ pathinfo($file, PATHINFO_EXTENSION) }}', '{{ basename($file) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid var(--sage); color: var(--sage); background: var(--sage-soft); padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; width: fit-content;">
                                    {{ __('messages.lihat_surat_balasan') }} {{ count($balasanFiles) > 1 ? '#' . ($index + 1) : '' }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="field">
                        <label>{{ __('messages.kategori_tiket') }}</label>
                        <select name="kategori_id" required>
                            <option value="">{{ __('messages.pilih_kategori') }}</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->kategori_id }}" {{ $t->kategori_id == $kat->kategori_id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>{{ __('messages.tautan_eksternal_opsional') }}</label>
                        <input type="text" name="link_ticket" value="{{ $t->link_ticket ?? '' }}" placeholder="https://...">
                    </div>

                    <div class="field">
                        <label>{{ __('messages.surat_laporan_template') }}</label>
                        <select name="template_laporan">
                            <option value="">-- Pilih Template --</option>
                            @php
                                $templateFiles = glob(public_path('templates/*.{docx,doc,pdf}'), GLOB_BRACE) ?: [];
                            @endphp
                            @foreach($templateFiles as $filepath)
                                @php
                                    $filename = basename($filepath);
                                    $displayName = str_replace(['_', '-'], ' ', pathinfo($filename, PATHINFO_FILENAME));
                                @endphp
                                <option value="{{ $filename }}" {{ $t->template_laporan == $filename ? 'selected' : '' }}>{{ $displayName }} ({{ strtoupper(pathinfo($filename, PATHINFO_EXTENSION)) }})</option>
                            @endforeach
                        </select>
                        <div class="helper" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Pilih template untuk men-generate surat laporan otomatis.</div>
                    </div>
                </div>

                <!-- KOLOM KANAN -->
                <div>
                    <div class="field">
                        <label>{{ __('messages.status') }}</label>
                        <select name="status" required>
                            <option value="Open" {{ $t->status === \App\Enums\TicketStatus::OPEN ? 'selected' : '' }}>Open</option>
                            <option value="Proses" {{ $t->status === \App\Enums\TicketStatus::PROSES ? 'selected' : '' }}>Proses</option>
                            <option value="In Review" {{ $t->status === \App\Enums\TicketStatus::REVIEW ? 'selected' : '' }}>In Review</option>
                            <option value="Waiting" {{ $t->status === \App\Enums\TicketStatus::WAITING ? 'selected' : '' }}>Waiting</option>
                            <option value="Pending" {{ $t->status === \App\Enums\TicketStatus::PENDING ? 'selected' : '' }}>Pending</option>
                            <option value="Done" {{ $t->status === \App\Enums\TicketStatus::DONE ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>

                    <div class="field">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label style="margin-bottom: 0;">{{ __('messages.tindakan_penyelesaian') }}</label>
                            <button type="button" class="btn btn-ghost btn-sm" style="font-size: 0.75rem; padding: 4px 8px; border: 1px solid var(--line); color: var(--brand-primary);" onclick="openFaqModal('penyelesaian_{{ $t->ticket_id }}')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: middle;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                {{ __('messages.insert_faq') }}
                            </button>
                        </div>
                        <textarea name="penyelesaian" id="penyelesaian_{{ $t->ticket_id }}" style="min-height: 90px;" placeholder="{{ __('messages.langkah_perbaikan') }}">{{ $t->penyelesaian }}</textarea>
                        
                        <label style="display: flex; align-items: center; gap: 8px; margin-top: 8px; cursor: pointer; font-size: 0.8rem; color: var(--ink); font-weight: 500; background: var(--paper-sunken); padding: 8px 12px; border-radius: 6px; border: 1px dashed var(--line);">
                            <input type="checkbox" name="is_faq" value="1" style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--brand-primary);">
                            <span>{{ __('messages.tandai_sebagai_faq') }} <span style="color: var(--text-muted); font-weight: 400;">{{ __('messages.otomatis_simpan_kb_internal') }}</span></span>
                        </label>
                    </div>

                    <div class="field">
                        <label>{{ __('messages.tindakan_pencegahan') }}</label>
                        <textarea name="pencegahan" style="min-height: 90px;" placeholder="{{ __('messages.langkah_preventif') }}">{{ $t->pencegahan ?? '' }}</textarea>
                    </div>

                    <div class="field" style="margin-top: 14px;">
                        <label>{{ __('messages.lampiran_respons_opsional') }}</label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="file" id="lampiran_input_supp_{{ $t->ticket_id }}" name="lampiran_support[]" multiple accept=".jpg,.jpeg,.png,.mp4,.pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.ppsx,.xlsm,.docm,.xlsb,.zip,.rar" style="flex: 1; width:100%; font-size: calc(13px * var(--text-scale, 1)); font-family:var(--font-body); padding:8px; border:1.5px dashed var(--line); border-radius:8px; background:var(--paper); cursor:pointer;" onchange="
                                if (!window.dtSupportMap) window.dtSupportMap = {};
                                if (!window.dtSupportMap['{{ $t->ticket_id }}']) window.dtSupportMap['{{ $t->ticket_id }}'] = new DataTransfer();
                                for(let i=0; i<this.files.length; i++) {
                                    window.dtSupportMap['{{ $t->ticket_id }}'].items.add(this.files[i]);
                                }
                                this.files = window.dtSupportMap['{{ $t->ticket_id }}'].files;

                                const clearBtn = document.getElementById('clear_lampiran_btn_supp_{{ $t->ticket_id }}');
                                const infoSpan = document.getElementById('lampiran_info_supp_{{ $t->ticket_id }}');
                                if (this.files.length > 0) {
                                    clearBtn.style.display = 'inline-block';
                                    infoSpan.style.display = 'block';
                                    let totalSize = 0;
                                    for(let i=0; i<this.files.length; i++) totalSize += this.files[i].size;
                                    infoSpan.innerHTML = '✅ ' + this.files.length + ' file dipilih (' + (totalSize/1024/1024).toFixed(2) + ' MB)';
                                } else {
                                    clearBtn.style.display = 'none';
                                    infoSpan.style.display = 'none';
                                }
                            ">
                            <button type="button" id="clear_lampiran_btn_supp_{{ $t->ticket_id }}" style="display: none; padding: 8px 12px; background: #fee2e2; color: #ef4444; border: 1px solid #f87171; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;" onclick="if(window.dtSupportMap) window.dtSupportMap['{{ $t->ticket_id }}'] = new DataTransfer(); document.getElementById('lampiran_input_supp_{{ $t->ticket_id }}').value = ''; this.style.display = 'none'; document.getElementById('lampiran_info_supp_{{ $t->ticket_id }}').style.display='none';">{{ __('messages.hapus_lampiran') }}</button>
                        </div>
                        <div id="lampiran_info_supp_{{ $t->ticket_id }}" style="display: none; font-size: 12.5px; color: #059669; font-weight: 600; margin-top: 6px;"></div>
                        <div class="helper" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">{{ __('messages.format_lampiran') }}</div>
                        @error('lampiran_support') <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div> @enderror
                        
                        @if($t->lampiran_support)
                            <div style="margin-top: 12px; padding: 12px; background: var(--surface); border: 1px solid var(--line); border-radius: 8px;">
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Lampiran Saat Ini:</div>
                                <div id="lampiran_preview_supp_{{ $t->ticket_id }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
                                    @php $lampiranSupports = is_array($t->lampiran_support) ? $t->lampiran_support : [$t->lampiran_support]; @endphp
                                    @foreach($lampiranSupports as $lampSupp)
                                        @php $extSupp = strtolower(pathinfo($lampSupp, PATHINFO_EXTENSION)); @endphp
                                        @if(in_array($extSupp, ['jpg', 'jpeg', 'png']))
                                            <a href="{{ Storage::url($lampSupp) }}" target="_blank">
                                                <img src="{{ Storage::url($lampSupp) }}" alt="Lampiran Support" style="max-width: 100%; max-height: 100px; border-radius: 8px; border: 1px solid var(--line); display: block; object-fit: cover;">
                                            </a>
                                        @elseif($extSupp === 'mp4')
                                            <a href="{{ Storage::url($lampSupp) }}" target="_blank" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                                {{ __('messages.lihat_video') }}
                                            </a>
                                        @elseif($extSupp === 'pdf')
                                            <a href="{{ Storage::url($lampSupp) }}" target="_blank" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid var(--line); text-decoration: none;">
                                                {{ __('messages.unduh_pdf') }}
                                            </a>
                                        @else
                                            <a href="{{ Storage::url($lampSupp) }}" target="_blank" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 6px; border: 1.5px solid var(--line); text-decoration: none;">
                                                Unduh {{ strtoupper($extSupp) }}
                                            </a>
                                        @endif
                                    @endforeach
                                    <button type="button" style="color: #ef4444; background: none; border: 1.5px solid #fecaca; padding: 6px 10px; font-size: 0.75rem; font-weight: 500; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: 0.2s; height: fit-content;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'" onclick="document.getElementById('hapus_lampiran_checkbox_supp_{{ $t->ticket_id }}').checked = true; document.getElementById('lampiran_preview_supp_{{ $t->ticket_id }}').style.display = 'none';">
                                        {{ __('messages.hapus_lampiran') }}
                                    </button>
                                </div>
                                <input type="checkbox" name="hapus_lampiran_support" value="1" id="hapus_lampiran_checkbox_supp_{{ $t->ticket_id }}" style="display: none;">
                            </div>
                        @endif
                    </div>

                    <div class="field">
                        <label>{{ __('messages.tanggal_selesai') }}</label>
                        <input type="text" readonly value="{{ $t->tanggal_penyelesaian ? $t->tanggal_penyelesaian->format('d-m-Y H:i') : __('messages.otomatis_selesai') }}" style="background: var(--paper-raised); color: var(--text-muted); cursor: not-allowed;">
                    </div>
                </div>
            </div>
            
            <div class="modal-foot" style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-ghost" onclick="cancelEditModalSupport('{{ $t->ticket_id }}')">{{ __('messages.batal') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.update_tiket') }}</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Verifikasi Akun -->
@if($pendingUsers->count() > 0)
<div class="overlay" id="modal-verify">
        <div class="modal" style="width: 1000px; max-width: 95vw;">
        <div class="modal-head" style="border-bottom: 1px solid var(--line); padding-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 8px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--ink); margin-bottom: 4px;">{{ __('messages.verifikasi_akun') }}</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0; font-family: inherit;">Setujui atau tolak akun pelapor baru yang baru saja mendaftar.</p>
                </div>
            </div>
            <button type="button" class="modal-x" onclick="closeModal('modal-verify')">✕</button>
        </div>
        <div class="modal-body" style="padding: 0; overflow-x: auto;">
            <table class="tickets" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--paper-sunken); border-bottom: 1px solid var(--line);">
                        <th style="padding: 16px; text-transform: uppercase; font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.5px; text-align: left;">{{ __('messages.nama_pelapor') ?? 'Nama Pelapor' }}</th>
                        <th style="padding: 16px; text-transform: uppercase; font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.5px; text-align: left;">{{ __('messages.koperasi') ?? 'Koperasi' }}</th>
                        <th style="padding: 16px; text-transform: uppercase; font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.5px; text-align: left;">{{ __('messages.kontak') ?? 'Kontak' }}</th>
                        <th style="padding: 16px; text-transform: uppercase; font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.5px; text-align: left;">{{ __('messages.col_tanggal') ?? 'Tgl Daftar' }}</th>
                        <th style="padding: 16px; text-transform: uppercase; font-size: 0.75rem; color: var(--text-muted); letter-spacing: 0.5px; text-align: center;">{{ __('messages.aksi') ?? 'Aksi' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUsers as $pu)
                    <tr style="border-bottom: 1px solid var(--line);" class="hoverable-row">
                        <td style="padding: 16px; font-weight: 600; color: var(--ink);">{{ $pu->nama }}</td>
                        <td style="padding: 16px; color: var(--ink);">{{ $pu->instansi->nama_instansi ?? '-' }}</td>
                        <td style="padding: 16px; font-size: 0.85rem; color: var(--text-muted);">
                            <div style="margin-bottom: 4px; display: flex; align-items: center; gap: 6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> {{ $pu->email }}</div>
                            <div style="display: flex; align-items: center; gap: 6px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> {{ $pu->instansi->no_telp ?? '-' }}</div>
                        </td>
                        <td style="padding: 16px; font-size: 0.85rem; color: var(--text-muted);">
                            <div style="font-weight: 600; color: var(--ink);">{{ $pu->created_at->timezone('Asia/Jakarta')->locale('id')->diffForHumans() }}</div>
                            <div>{{ $pu->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <form action="{{ route('support.users.verify', $pu->user_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2); display: flex; align-items: center; gap: 6px;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'" title="Setujui akun ini">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Setujui
                                    </button>
                                </form>
                                <form action="{{ route('support.users.reject', $pu->user_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak dan menghapus akun {{ $pu->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2); display: flex; align-items: center; gap: 6px;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'" title="Tolak dan hapus akun">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modal-verify')">{{ __('messages.tutup') }}</button>
        </div>
    </div>
</div>
@endif

<!-- Modal FAQ Insert -->
<div class="overlay" id="modal-faq-insert" style="z-index: 1000;">
    <div class="modal w-sm">
        <div class="modal-head">
            <div>
                <h3>{{ __('messages.pilih_template_faq') }}</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">{{ __('messages.pilih_jawaban_kb_desc') }}</p>
            </div>
            <button type="button" class="modal-x" onclick="closeModal('modal-faq-insert')">✕</button>
        </div>
        <div class="modal-body" style="padding: 16px;">
            <div class="search" style="margin-bottom: 16px;">
                <img src="{{ asset('magnifying-glass.png') }}" alt="Search" style="width: 14px; height: 14px; margin-right: 8px; vertical-align: middle; opacity: 0.4; filter: grayscale(100%);">
                <input type="text" placeholder="{{ __('messages.cari_pertanyaan_faq') }}" id="faq-search-input" style="border:none; background:transparent; width:100%; outline:none;">
            </div>
            
            <div id="faq-list-container" style="max-height: 350px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                <div id="faq-skeleton" style="padding: 12px; background: var(--paper-sunken); border: 1px dashed var(--line); border-radius: 8px;">
                    <div class="skel" style="width: 60%; height: 12px; margin-bottom: 8px; border-radius: 4px;"></div>
                    <div class="skel" style="width: 90%; height: 10px; margin-bottom: 6px; border-radius: 4px;"></div>
                    <div class="skel" style="width: 75%; height: 10px; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .faq-insert-item {
        padding: 12px;
        background: var(--paper-raised);
        border: 1px solid var(--line);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .faq-insert-item:hover {
        border-color: var(--brand-primary);
        background: var(--brand-primary-soft, #eef2ff);
    }
    .faq-insert-item .q {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--ink);
        margin-bottom: 4px;
    }
    .faq-insert-item .a {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .faq-insert-item .badge {
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        margin-right: 6px;
    }
</style>

<script>
    let currentFaqTargetId = null;
    let allFaqs = [];

    function openFaqModal(targetId) {
        currentFaqTargetId = targetId;
        openModal('modal-faq-insert');
        loadFaqs();
    }

    function loadFaqs() {
        const container = document.getElementById('faq-list-container');
        const skeleton = document.getElementById('faq-skeleton');
        const searchInput = document.getElementById('faq-search-input');
        
        // Reset search
        searchInput.value = '';
        
        container.innerHTML = '';
        container.appendChild(skeleton);
        skeleton.style.display = 'block';

        fetch('{{ url('/support/faq/list') }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            allFaqs = data;
            renderFaqs(allFaqs);
        })
        .catch(err => {
            console.error('Error fetching FAQs:', err);
            container.innerHTML = '<div style="text-align:center; padding: 20px; color: var(--text-muted); font-size: 0.85rem;">Gagal memuat FAQ.</div>';
        });
    }

    function renderFaqs(faqs) {
        const container = document.getElementById('faq-list-container');
        container.innerHTML = '';

        if (faqs.length === 0) {
            container.innerHTML = '<div style="text-align:center; padding: 20px; color: var(--text-muted); font-size: 0.85rem;">Tidak ada FAQ ditemukan.</div>';
            return;
        }

        faqs.forEach(faq => {
            const visibilityBadge = faq.visibility === 'public' 
                ? '<span class="badge" style="background:#dcfce7; color:#16a34a;">Public</span>'
                : '<span class="badge" style="background:#fef3c7; color:#d97706;">Internal</span>';
                
            const catBadge = faq.kategori ? `<span class="badge" style="background:#f1f5f9; color:#475569;">${faq.kategori.nama_kategori}</span>` : '';

            const item = document.createElement('div');
            item.className = 'faq-insert-item';
            item.innerHTML = `
                <div class="q">${faq.pertanyaan}</div>
                <div style="margin-bottom: 6px;">${visibilityBadge} ${catBadge}</div>
                <div class="a">${faq.jawaban}</div>
            `;
            item.onclick = () => {
                insertFaqContent(faq.jawaban);
            };
            container.appendChild(item);
        });
    }

    function insertFaqContent(jawaban) {
        if (currentFaqTargetId) {
            const textarea = document.getElementById(currentFaqTargetId);
            if (textarea) {
                // If there's already text, append with newline, else just set
                if (textarea.value.trim() !== '') {
                    textarea.value = textarea.value + '\n\n' + jawaban;
                } else {
                    textarea.value = jawaban;
                }
            }
        }
        closeModal('modal-faq-insert');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('faq-search-input')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const filtered = allFaqs.filter(faq => 
                faq.pertanyaan.toLowerCase().includes(query) || 
                faq.jawaban.toLowerCase().includes(query) ||
                (faq.kategori && faq.kategori.nama_kategori.toLowerCase().includes(query))
            );
            renderFaqs(filtered);
        });
    });

    function cancelEditModalSupport(ticketId) {
        const fileInput = document.getElementById('lampiran_input_supp_' + ticketId);
        if (fileInput) fileInput.value = '';
        
        const clearBtn = document.getElementById('clear_lampiran_btn_supp_' + ticketId);
        if (clearBtn) clearBtn.style.display = 'none';

        const checkbox = document.getElementById('hapus_lampiran_checkbox_supp_' + ticketId);
        if (checkbox) checkbox.checked = false;

        const preview = document.getElementById('lampiran_preview_supp_' + ticketId);
        if (preview) preview.style.display = 'flex';

        closeModal('modal-edit-' + ticketId);
    }

    function checkFileSize(form, inputId, maxMb) {
        const fileInput = document.getElementById(inputId);
        if (fileInput && fileInput.files.length > 0) {
            let totalSize = 0;
            for (let i = 0; i < fileInput.files.length; i++) {
                totalSize += fileInput.files[i].size;
            }
            const totalSizeMb = totalSize / 1024 / 1024;
            

            if (totalSizeMb > maxMb) {
                var errorMsg = "{{ __('messages.error_file_too_large') }}".replace(':maxMb', maxMb).replace(':totalSizeMb', totalSizeMb.toFixed(2));
                alert(errorMsg);
                return false;
            }
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const skeleton = document.getElementById('skeleton-loading');
        const content  = document.getElementById('actual-content');
        setTimeout(function () {
            skeleton.style.display = 'none';
            content.style.display = 'block';
            content.classList.add('loaded');
        }, 1200);

        document.getElementById('search-input').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#tickets-table tbody tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                if(text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('a')) {
                    openModal(this.getAttribute('data-target'));
                }
            });
        });
    });
</script>
</div>
@endsection
