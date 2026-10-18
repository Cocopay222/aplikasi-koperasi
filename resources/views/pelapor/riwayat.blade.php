@extends('layouts.app')

@section('page_title', __('messages.title_riwayat'))
@section('page_subtitle', 'SupportFlow')


@section('content')
<div class="pelapor-panel active">
    {{-- SKELETON LOADING STATE --}}
    <div class="skeleton-wrap" id="skeleton-loading">
        <div class="skel" style="height: 100px; width: 100%; margin-bottom: 22px;"></div>
        <div class="skel" style="height: 300px; width: 100%;"></div>
    </div>

    {{-- ACTUAL CONTENT --}}
    <div class="content-wrap" id="actual-content" style="display:none;">
        @if(session('success'))
            <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); transition: opacity 0.6s ease, transform 0.6s ease;">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
            </div>
        @endif

        <div class="page-head fade-up" style="animation-delay: 0.1s; margin-bottom:24px;">
            <div>
                <h1 style="font-size: calc(24px * var(--text-scale, 1));">{{ __('messages.title_riwayat') }}</h1>
            </div>
        </div>

        <div class="toolbar fade-up" style="animation-delay: 0.15s; margin-bottom:20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; gap: 12px; align-items: center; flex: 1; min-width: 280px;">
                <div class="search" style="flex: 1;">
                    <img src="{{ asset('magnifying-glass.png') }}" alt="Search" style="width: 14px; height: 14px; margin-right: 8px; vertical-align: middle; opacity: 0.4; filter: grayscale(100%);">
                    <input type="text" placeholder="{{ __('messages.cari_laporan') }}" style="border:none; background:transparent; width:100%; outline:none;" id="search-input">
                </div>
                <form id="filter-form" action="{{ route('pelapor.riwayat') }}" method="GET" style="margin:0;">
                    <select name="status" onchange="document.getElementById('filter-form').submit()" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--line); font-family: var(--font-body); font-weight: 500; color: var(--ink); background: var(--paper-raised); cursor: pointer; outline:none;">
                        <option value="">{{ __('messages.status_semua') }}</option>
                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Proses</option>
                        <option value="In Review" {{ request('status') == 'In Review' ? 'selected' : '' }}>In Review</option>
                        <option value="Waiting" {{ request('status') == 'Waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>{{ __('messages.status_selesai') }}</option>
                    </select>
                </form>
            </div>
            <a href="{{ route('pelapor.riwayat.export', request()->query()) }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; border: 1px solid var(--line); color: var(--ink); background: var(--paper-raised); font-size: 0.85rem;" title="Download rekap laporan ke format Excel/CSV">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Export Excel / CSV
            </a>
        </div>

        <div class="fade-up" style="animation-delay: 0.2s;">
            <div class="table-scroll-wrapper">
            <table class="tickets" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th width="120">{{ __('messages.id_laporan') }}</th>
                        <th width="120">{{ __('messages.col_tanggal') }}</th>
                        <th width="160">{{ __('messages.aplikasi') }}</th>
                        <th width="110">Urgensi</th>
                        <th>{{ __('messages.permasalahan') }}</th>
                        <th width="120">{{ __('messages.status') }}</th>
                        <th width="100">{{ __('messages.aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                    <tr class="clickable-row hoverable-row" data-target="modal-preview-{{ $t->ticket_id }}" style="cursor: pointer;">
                        <td class="mono">#{{ $t->ticket_id }}</td>
                        <td class="mono">
                            {{ $t->tanggal_input->format('d M y') }}
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $t->tanggal_input->format('H:i') }}</div>
                        </td>
                        <td><div class="cat-tag">{{ $t->aplikasi->nama_aplikasi }}</div></td>
                        @php
                            $urgensi = $t->urgensi ?? 'Sedang';
                            $urgensiColor = match($urgensi) {
                                'Tinggi' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'color' => '#dc2626'],
                                'Rendah' => ['bg' => 'rgba(2, 132, 199, 0.12)', 'color' => '#0284c7'],
                                default => ['bg' => 'rgba(217, 119, 6, 0.12)', 'color' => '#d97706'],
                            };
                        @endphp
                        <td>
                            <span style="background: {{ $urgensiColor['bg'] }}; color: {{ $urgensiColor['color'] }}; padding: 3px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 600; white-space: nowrap;">
                                ⚡ {{ $urgensi }}
                            </span>
                        </td>
                        <td>
                            <div style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:500;">
                                {{ $t->permasalahan }}
                            </div>
                            @if($t->rating)
                                <div style="font-size: 0.75rem; color: #f59e0b; font-weight: 600; margin-top: 2px;">
                                    ★ {{ $t->rating }}/5
                                </div>
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
                        <td>
                            <button class="btn btn-ghost btn-sm" onclick="openModal('modal-detail-{{ $t->ticket_id }}')">{{ __('messages.btn_detail') }}</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:var(--ink-soft);">
                            {{ __('messages.belum_ada_tiket_laporan') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Tickets -->
@foreach($tickets as $t)
<div class="overlay" id="modal-preview-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head" style="border-bottom: 1px solid var(--line); padding-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: var(--brand-primary-soft); color: var(--primary); padding: 8px; border-radius: 8px; display: flex;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 2px; color: var(--ink);">{{ __('messages.modal_preview_title') }}</h3>
                    <p class="mono" style="font-size: 0.8rem; color: var(--text-muted);">{{ $t->ticket_id }}</p>
                </div>
            </div>
            <button type="button" class="modal-x" onclick="closeModal('modal-preview-{{ $t->ticket_id }}'); event.stopPropagation();">&times;</button>
        </div>
        
        <div class="modal-body" style="padding: 24px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; background: #eef6f6; color: #477678; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-bottom: 20px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                {{ __('messages.disubmit') }} {{ $t->tanggal_input->locale(app()->getLocale())->diffForHumans(['parts' => 2]) }} &middot; {{ $t->tanggal_input->format('d M Y, H:i') }}
            </div>

            <div style="margin-bottom: 24px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.deskripsi_permasalahan') }}</div>
                <div style="font-size: 0.95rem; color: var(--ink); line-height: 1.6; white-space: pre-wrap; background: var(--paper-raised); padding: 16px; border-radius: 8px; border: 1px solid var(--line);">{{ $t->permasalahan }}</div>
            </div>

        </div>
        <div class="modal-foot" style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--line);">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modal-preview-{{ $t->ticket_id }}')">{{ __('messages.btn_tutup') }}</button>
            <button type="button" class="btn btn-primary" onclick="closeModal('modal-preview-{{ $t->ticket_id }}'); openModal('modal-detail-{{ $t->ticket_id }}')">{{ __('messages.btn_detail') }}</button>
        </div>
    </div>
</div>

<div class="overlay" id="modal-detail-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head">
            <div><h3>{{ __('messages.detail_laporan') }}</h3><p>{{ $t->ticket_id }}</p></div>
            <button type="button" class="modal-x" onclick="closeModal('modal-detail-{{ $t->ticket_id }}'); event.stopPropagation();">&times;</button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div style="margin-bottom: 24px;">
                <a href="{{ route('pelapor.tickets.dokumen', $t->ticket_id) }}" class="btn btn-ghost" style="display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #75abac; color: #477678; background: #eef6f6; padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; width: 100%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    {{ __('messages.open_history_letter_document') }}
                </a>
            </div>
            @php
                $statusStr = is_object($t->status) ? $t->status->value : (string)$t->status;
                $isDone = in_array(strtolower($statusStr), ['done', 'selesai']);
                $targetSla = $t->kategori?->sla_jam ?? 24;
                if ($isDone) {
                    $endTime = $t->tanggal_penyelesaian ?? now();
                    $totalHours = max(1, round($t->tanggal_input->diffInMinutes($endTime) / 60, 1));
                    $onTime = $totalHours <= $targetSla;
                } else {
                    $hoursElapsed = round($t->tanggal_input->diffInMinutes(now()) / 60, 1);
                    $hoursLeft = max(0, round($targetSla - $hoursElapsed, 1));
                    $slaPercent = min(100, round(($hoursElapsed / $targetSla) * 100));
                }
            @endphp

            {{-- SLA Tracker Card --}}
            <div style="margin-bottom: 20px; background: var(--paper-sunken); border: 1px solid var(--line); border-radius: 10px; padding: 14px 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#5F9EA0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: 0.5px;">Target SLA Layanan</span>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; color: #5F9EA0; background: rgba(95, 158, 160, 0.15); padding: 2px 8px; border-radius: 6px;">Target: {{ $targetSla }} Jam</span>
                </div>
                @if($isDone)
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 0.85rem;">
                        <span style="color: var(--ink-soft);">Diselesaikan dalam: <b>{{ $totalHours }} Jam</b></span>
                        @if($onTime)
                            <span style="background: rgba(16, 185, 129, 0.15); color: #059669; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px;">
                                ✓ Tepat Waktu (Sesuai SLA)
                            </span>
                        @else
                            <span style="background: rgba(245, 158, 11, 0.15); color: #d97706; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 0.75rem;">
                                ⚠ Melewati Target SLA
                            </span>
                        @endif
                    </div>
                @else
                    <div style="width: 100%; height: 6px; background: rgba(0,0,0,0.06); border-radius: 10px; overflow: hidden; margin-bottom: 8px;">
                        <div style="width: {{ $slaPercent }}%; height: 100%; background: {{ $slaPercent > 85 ? '#ef4444' : ($slaPercent > 60 ? '#f59e0b' : '#10b981') }}; border-radius: 10px;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted);">
                        <span>Berjalan: {{ $hoursElapsed }} jam</span>
                        <span style="font-weight: 600; color: {{ $hoursLeft <= 2 ? '#dc2626' : 'var(--ink)' }};">Estimasi Sisa: ~{{ $hoursLeft }} jam</span>
                    </div>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.aplikasi') }}</div>
                    <div style="font-size: 0.95rem; color: var(--ink); font-weight: 500;">{{ $t->aplikasi->nama_aplikasi }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.kategori') }}</div>
                    <div style="font-size: 0.95rem; color: var(--ink); font-weight: 500;">{{ $t->kategori->nama_kategori ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Tingkat Urgensi</div>
                    @php
                        $mUrgensi = $t->urgensi ?? 'Sedang';
                        $mUrgensiColor = match($mUrgensi) {
                            'Tinggi' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'color' => '#dc2626'],
                            'Rendah' => ['bg' => 'rgba(2, 132, 199, 0.12)', 'color' => '#0284c7'],
                            default => ['bg' => 'rgba(217, 119, 6, 0.12)', 'color' => '#d97706'],
                        };
                    @endphp
                    <div>
                        <span style="background: {{ $mUrgensiColor['bg'] }}; color: {{ $mUrgensiColor['color'] }}; padding: 3px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                            ⚡ {{ $mUrgensi }}
                        </span>
                    </div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">PIC Tim Support</div>
                    <div style="font-size: 0.95rem; color: var(--ink); font-weight: 500;">{{ $t->picSupport?->nama ?? 'Menunggu Penugasan' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.col_tanggal') ?? 'Tanggal' }}</div>
                    <div style="font-size: 0.95rem; color: var(--ink); font-weight: 500;">{{ $t->tanggal_input ? $t->tanggal_input->format('d-m-Y H:i') : '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">{{ __('messages.tanggal_selesai') ?? 'Tanggal Selesai' }}</div>
                    <div style="font-size: 0.95rem; color: var(--ink); font-weight: 500;">{{ $t->tanggal_penyelesaian ? $t->tanggal_penyelesaian->format('d-m-Y H:i') : '-' }}</div>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.permasalahan') }}</div>
                <div style="font-size: 0.95rem; color: var(--ink); line-height: 1.5; white-space: pre-wrap; background: var(--paper-raised); padding: 12px; border-radius: 8px; border: 1px solid var(--line);">{{ $t->permasalahan }}</div>
            </div>

            @if($t->lampiran)
            <div style="margin-bottom: 16px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.lampiran_bukti') }}</div>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-start;">
                @php $lampirans = is_array($t->lampiran) ? $t->lampiran : [$t->lampiran]; @endphp
                @foreach($lampirans as $lamp)
                    @php $ext = strtolower(pathinfo($lamp, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <div style="border: 1.5px solid var(--line, #e2e8f0); border-radius: 8px; overflow: hidden; background: white; width: 120px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); display: flex; flex-direction: column;">
                            <div style="position: relative; width: 120px; height: 85px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: pointer;" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')">
                                <img src="{{ Storage::url($lamp) }}" alt="Lampiran" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; background: #f1f5f9; color: var(--text-muted);">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span style="font-size: 0.65rem; font-weight: 600; margin-top: 3px; color: #64748b; text-transform: uppercase;">{{ $ext }}</span>
                                </div>
                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                    <span style="color: white; font-size: 0.7rem; font-weight: 600; background: rgba(0,0,0,0.6); padding: 3px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        Lihat
                                    </span>
                                </div>
                            </div>
                            <div style="padding: 4px 6px; border-top: 1px solid var(--line, #e2e8f0); background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 3px;">
                                <span title="{{ \App\Models\UploadedFile::displayName($lamp) }}" style="font-size: 0.7rem; font-weight: 500; color: var(--ink, #1e293b); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80px; display: block;">{{ \App\Models\UploadedFile::displayName($lamp) }}</span>
                                <a href="{{ Storage::url($lamp) }}" download="{{ \App\Models\UploadedFile::displayName($lamp) }}" target="_blank" style="color: var(--primary, #0284c7); display: inline-flex; align-items: center;" title="Unduh">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    @else
                        <div style="border: 1.5px solid var(--line, #e2e8f0); border-radius: 8px; background: white; padding: 6px 10px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <div style="width: 30px; height: 30px; border-radius: 6px; background: {{ $ext === 'pdf' ? '#fee2e2' : ($ext === 'mp4' ? '#ede9fe' : (in_array($ext, ['doc','docx']) ? '#e0f2fe' : '#f1f5f9')) }}; display: flex; align-items: center; justify-content: center; color: {{ $ext === 'pdf' ? '#dc2626' : ($ext === 'mp4' ? '#7c3aed' : (in_array($ext, ['doc','docx']) ? '#0284c7' : '#475569')) }}; font-weight: 700; font-size: 0.68rem;">
                                {{ strtoupper($ext ?: 'FILE') }}
                            </div>
                            <span title="{{ \App\Models\UploadedFile::displayName($lamp) }}" style="font-size: 0.75rem; font-weight: 500; color: var(--ink, #1e293b); max-width: 110px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ \App\Models\UploadedFile::displayName($lamp) }}</span>
                            <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost btn-sm" style="padding: 3px 6px; font-size: 0.7rem; border: 1px solid var(--line, #cbd5e1); border-radius: 5px; cursor: pointer;">
                                Buka
                            </button>
                        </div>
                    @endif
                @endforeach
                </div>
            </div>
            @endif

            @if($t->lampiran_support)
            <div style="margin-bottom: 16px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Lampiran Respons Support</div>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-start;">
                @php $lampiranSupports = is_array($t->lampiran_support) ? $t->lampiran_support : [$t->lampiran_support]; @endphp
                @foreach($lampiranSupports as $lampSupp)
                    @php $extSupp = strtolower(pathinfo($lampSupp, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($extSupp, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <div style="border: 1.5px solid #f59e0b; border-radius: 8px; overflow: hidden; background: white; width: 120px; box-shadow: 0 2px 4px rgba(245,158,11,0.06); display: flex; flex-direction: column;">
                            <div style="position: relative; width: 120px; height: 85px; background: #fffbeb; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: pointer;" onclick="openUniversalPreview('{{ Storage::url($lampSupp) }}', '{{ $extSupp }}', '{{ addslashes(basename($lampSupp)) }}')">
                                <img src="{{ Storage::url($lampSupp) }}" alt="Lampiran Support" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; background: #fef3c7; color: #b45309;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span style="font-size: 0.65rem; font-weight: 600; margin-top: 3px; color: #b45309; text-transform: uppercase;">{{ $extSupp }}</span>
                                </div>
                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                    <span style="color: white; font-size: 0.7rem; font-weight: 600; background: rgba(0,0,0,0.6); padding: 3px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        Lihat
                                    </span>
                                </div>
                            </div>
                            <div style="padding: 4px 6px; border-top: 1px solid #fde68a; background: #fffbeb; display: flex; align-items: center; justify-content: space-between; gap: 3px;">
                                <span title="{{ \App\Models\UploadedFile::displayName($lampSupp) }}" style="font-size: 0.7rem; font-weight: 500; color: #92400e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80px; display: block;">{{ \App\Models\UploadedFile::displayName($lampSupp) }}</span>
                                <a href="{{ Storage::url($lampSupp) }}" download="{{ \App\Models\UploadedFile::displayName($lampSupp) }}" target="_blank" style="color: #b45309; display: inline-flex; align-items: center;" title="Unduh">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    @else
                        <div style="border: 1.5px solid #fde68a; border-radius: 8px; background: #fffbeb; padding: 6px 10px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(245,158,11,0.04);">
                            <div style="width: 30px; height: 30px; border-radius: 6px; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #b45309; font-weight: 700; font-size: 0.68rem;">
                                {{ strtoupper($extSupp ?: 'FILE') }}
                            </div>
                            <span title="{{ \App\Models\UploadedFile::displayName($lampSupp) }}" style="font-size: 0.75rem; font-weight: 500; color: #92400e; max-width: 110px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ \App\Models\UploadedFile::displayName($lampSupp) }}</span>
                            <button type="button" onclick="openUniversalPreview('{{ Storage::url($lampSupp) }}', '{{ $extSupp }}', '{{ addslashes(basename($lampSupp)) }}')" class="btn btn-ghost btn-sm" style="padding: 3px 6px; font-size: 0.7rem; border: 1px solid #fcd34d; border-radius: 5px; cursor: pointer; color: #b45309;">
                                Buka
                            </button>
                        </div>
                    @endif
                @endforeach
                </div>
            </div>
            @endif

            @if($t->template_laporan)
                @php
                    $tplUrl = route('download.template', basename($t->template_laporan));
                @endphp
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.template_surat_dari_support') ?? 'Template Surat Dari Support' }}</div>
                    <button type="button" onclick="openUniversalPreview('{{ $tplUrl }}', '{{ pathinfo($t->template_laporan, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($t->template_laporan)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #75abac; color: #477678; background: #eef6f6; padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span>Lihat {{ str_replace(['_', '-'], ' ', pathinfo($t->template_laporan, PATHINFO_FILENAME)) }} ({{ strtoupper(pathinfo($t->template_laporan, PATHINFO_EXTENSION)) }})</span>
                    </button>
                </div>
            @endif

            <div style="margin-bottom: 16px; background: var(--paper-sunken); padding: 12px; border-radius: 8px; border: 1px solid var(--line);">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.surat_balasan_dari_pelapor') }}</div>
                
                @if($t->surat_balasan)
                    <div style="margin-bottom: 10px; display: flex; flex-direction: column; gap: 8px;">
                        @php
                            $balasanFiles = json_decode($t->surat_balasan, true);
                            if (!is_array($balasanFiles)) {
                                $balasanFiles = [$t->surat_balasan];
                            }
                        @endphp
                        @foreach($balasanFiles as $index => $file)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($file) }}', '{{ pathinfo($file, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($file)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid var(--sage); color: var(--sage); background: var(--sage-soft); padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; width: fit-content;">
                                    {{ __('messages.lihat_surat_balasan_saat_ini') }} {{ count($balasanFiles) > 1 ? '#' . ($index + 1) : '' }}
                                </button>
                                @if($t->status !== \App\Enums\TicketStatus::DONE)
                                <form action="{{ route('pelapor.tickets.delete_balasan', ['ticket' => $t->ticket_id, 'index' => $index]) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--danger); border: 1.5px solid var(--danger-soft); padding: 8px; border-radius: 6px;" title="Hapus File">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                
                @if($t->status !== \App\Enums\TicketStatus::DONE)
                    <form action="{{ route('pelapor.tickets.upload_balasan', $t->ticket_id) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                        @csrf
                        <input type="file" name="surat_balasan[]" multiple required accept=".pdf,.doc,.docx,.xlsx,.csv,.pptx,.ppsx,.xlsm,.docm,.xlsb,.zip,.rar" style="flex: 1; min-width: 200px; font-size: 13px; padding: 6px; border: 1px solid var(--line); border-radius: 6px; background: var(--paper);">
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 6px 12px;">{{ __('messages.unggah') }}</button>
                    </form>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">{{ __('messages.format_lampiran_surat_balasan') }}</div>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.penyelesaian_support') }}</div>
                    @if($t->penyelesaian)
                        <div style="font-size: 0.95rem; color: #166534; line-height: 1.5; white-space: pre-wrap; background: #f0fdf4; padding: 12px; border-radius: 8px; border: 1px solid #bbf7d0;">{{ $t->penyelesaian }}</div>
                    @else
                        <div style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.5; white-space: pre-wrap; background: var(--paper-raised); padding: 12px; border-radius: 8px; border: 1px dashed var(--line);">{{ __('messages.belum_ada_catatan') }}</div>
                    @endif
                </div>
                @if($t->pencegahan)
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.tindakan_pencegahan') }}</div>
                    <div style="font-size: 0.95rem; color: #854d0e; line-height: 1.5; white-space: pre-wrap; background: #fefce8; padding: 12px; border-radius: 8px; border: 1px solid #fef08a;">{{ $t->pencegahan }}</div>
                </div>
                @endif
            </div>

            {{-- CSAT Rating Component --}}
            @if($isDone)
            <div style="margin-top: 20px; padding: 18px; border-radius: 12px; background: #fffbeb; border: 1.5px solid #fde68a;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <div style="font-weight: 700; font-size: 0.95rem; color: #92400e; display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 1.2rem;">⭐</span> Kepuasan Layanan Dukungan (CSAT)
                    </div>
                    @if($t->rating)
                        <span style="background: #fef3c7; color: #b45309; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Sudah Dinilai</span>
                    @endif
                </div>

                @if($t->rating)
                    <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 8px;">
                        @for($i = 1; $i <= 5; $i++)
                            <span style="font-size: 1.3rem; color: {{ $i <= $t->rating ? '#f59e0b' : '#cbd5e1' }};">★</span>
                        @endfor
                        <span style="font-weight: 700; color: #b45309; margin-left: 6px; font-size: 0.95rem;">{{ $t->rating }} / 5 Bintang</span>
                    </div>
                    @if($t->rating_ulasan)
                        <div style="font-size: 0.88rem; color: #78350f; font-style: italic; background: rgba(255,255,255,0.7); padding: 10px 14px; border-radius: 8px; border: 1px solid #fef08a;">
                            "{{ $t->rating_ulasan }}"
                        </div>
                    @endif
                @else
                    <p style="font-size: 0.82rem; color: #92400e; margin: 0 0 12px 0;">
                        Tiket ini telah selesai ditangani. Mohon berikan penilaian atas bantuan dari Tim Support:
                    </p>
                    <form action="{{ route('pelapor.tickets.rating', $t->ticket_id) }}" method="POST">
                        @csrf
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <div class="star-rating-box" id="star-box-{{ $t->ticket_id }}" style="display: flex; gap: 6px; font-size: 1.6rem; cursor: pointer;">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star-item" data-value="{{ $i }}" onclick="setRating('{{ $t->ticket_id }}', {{ $i }})" onmouseover="hoverRating('{{ $t->ticket_id }}', {{ $i }})" onmouseout="resetRatingHover('{{ $t->ticket_id }}')" style="color: #cbd5e1; transition: color 0.15s;">★</span>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating-input-{{ $t->ticket_id }}" value="" required>
                            <span id="rating-label-{{ $t->ticket_id }}" style="font-size: 0.85rem; font-weight: 600; color: #b45309; margin-left: 6px;">Pilih Bintang</span>
                        </div>
                        <textarea name="rating_ulasan" placeholder="Tuliskan ulasan atau masukan untuk tim support (opsional)..." style="width: 100%; padding: 8px 12px; border: 1px solid #fde68a; border-radius: 8px; font-size: 0.85rem; font-family: inherit; box-sizing: border-box; min-height: 60px; background: white; margin-bottom: 10px;"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm" style="background: #d97706; border-color: #d97706; font-size: 0.82rem; padding: 6px 14px;">Kirim Penilaian</button>
                    </form>
                @endif
            </div>
            @endif

            {{-- Discussion / Chat Thread Component --}}
            <div style="margin-top: 20px; border-top: 1px solid var(--line); padding-top: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: var(--ink); font-size: 0.95rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        Diskusi & Komentar Tiket
                    </div>
                    <span style="font-size: 0.75rem; background: var(--paper-sunken); border: 1px solid var(--line); color: var(--ink-soft); padding: 2px 8px; border-radius: 12px; font-weight: 600;">
                        {{ $t->comments->count() }} Pesan
                    </span>
                </div>

                {{-- Chat Thread List --}}
                <div style="max-height: 220px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; padding: 12px; background: var(--paper-sunken); border: 1px solid var(--line); border-radius: 10px; margin-bottom: 12px;">
                    @forelse($t->comments as $comment)
                        @php
                            $isMe = $comment->user_id === Auth::id();
                            $isSupport = in_array($comment->user?->role, [\App\Enums\UserRole::SUPPORT, \App\Enums\UserRole::SUPERADMIN]);
                        @endphp
                        <div style="display: flex; flex-direction: column; align-items: {{ $isMe ? 'flex-end' : 'flex-start' }};">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; font-size: 0.72rem; color: var(--text-muted);">
                                <span style="font-weight: 600; color: {{ $isMe ? 'var(--primary)' : ($isSupport ? '#5F9EA0' : 'var(--ink)') }};">
                                    {{ $isMe ? 'Anda' : $comment->user?->nama }}
                                </span>
                                @if($isSupport)
                                    <span style="background: rgba(95, 158, 160, 0.15); color: #5F9EA0; padding: 1px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 700;">SUPPORT</span>
                                @endif
                                <span>&middot; {{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="max-width: 85%; padding: 8px 14px; border-radius: 12px; font-size: 0.88rem; line-height: 1.45; white-space: pre-wrap; background: {{ $isMe ? 'var(--brand-primary)' : 'var(--paper-raised)' }}; color: {{ $isMe ? '#ffffff' : 'var(--ink)' }}; border: 1px solid {{ $isMe ? 'var(--brand-primary)' : 'var(--line)' }}; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                {{ $comment->pesan }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 16px; color: var(--text-muted); font-size: 0.82rem; font-style: italic;">
                            Belum ada pesan diskusi pada tiket ini. Tulis pesan di bawah untuk berkomunikasi langsung dengan tim Support.
                        </div>
                    @endforelse
                </div>

                {{-- Form Kirim Komentar --}}
                <form action="{{ route('pelapor.tickets.comment', $t->ticket_id) }}" method="POST" style="display: flex; gap: 8px; align-items: flex-end;">
                    @csrf
                    <div style="flex: 1;">
                        <textarea name="pesan" required placeholder="Tulis tanggapan atau pertanyaan untuk Tim Support..." style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--line); font-size: 0.85rem; font-family: inherit; background: var(--paper); color: var(--ink); box-sizing: border-box; resize: vertical; min-height: 42px; max-height: 100px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 16px; height: 42px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        Kirim
                    </button>
                </form>
            </div>
        </div>
        <div class="modal-foot" style="display: flex; gap: 12px; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid var(--line);">
            @if($statusStr === 'Open')
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modal-detail-{{ $t->ticket_id }}'); openModal('modal-edit-{{ $t->ticket_id }}');" style="padding: 6px 12px; font-size: 13px;">{{ __('messages.btn_edit_report') }}</button>
            @else
                <div></div>
            @endif
            <button type="button" class="btn btn-ghost" onclick="closeModal('modal-detail-{{ $t->ticket_id }}')">{{ __('messages.btn_tutup') }}</button>
        </div>
    </div>
</div>

@if($t->status === \App\Enums\TicketStatus::OPEN)
<div class="overlay" id="modal-delete-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head" style="border-bottom: 1px solid var(--line); padding-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: #fef2f2; color: #ef4444; padding: 8px; border-radius: 8px; display: flex;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 2px; color: var(--ink);">{{ __('messages.title_konfirmasi_hapus') }}</h3>
                    <p class="mono" style="font-size: 0.8rem; color: var(--text-muted);">{{ $t->ticket_id }}</p>
                </div>
            </div>
            <button type="button" class="modal-x" onclick="closeModal('modal-delete-{{ $t->ticket_id }}'); event.stopPropagation();">&times;</button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <p style="margin: 0; font-size: 0.95rem; line-height: 1.5; color: var(--ink-soft);">
                {{ __('messages.konfirmasi_hapus_tiket') }} {{ __('messages.tindakan_tidak_dapat_dibatalkan') }}
            </p>
        </div>
        <div class="modal-foot" style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--line);">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modal-delete-{{ $t->ticket_id }}'); openModal('modal-edit-{{ $t->ticket_id }}')">{{ __('messages.batal') }}</button>
            <form action="{{ route('pelapor.tickets.destroy', $t->ticket_id) }}" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    {{ __('messages.hapus_laporan') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@if((is_object($t->status) ? $t->status->value : $t->status) === 'Open')
<div class="overlay" id="modal-edit-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head">
            <div><h3>{{ __('messages.btn_edit_report') }}</h3><p>{{ $t->ticket_id }}</p></div>
            <button type="button" class="modal-x" onclick="closeModal('modal-edit-{{ $t->ticket_id }}'); openModal('modal-detail-{{ $t->ticket_id }}');">&times;</button>
        </div>
        <form action="{{ route('pelapor.tickets.update_pelapor', $t->ticket_id) }}" method="POST" enctype="multipart/form-data" onsubmit="return checkFileSize(this, 'edit_lampiran_input_{{ $t->ticket_id }}', 8);">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding: 24px;">
                <div class="field" style="margin-bottom: 16px;">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">{{ __('messages.aplikasi_bermasalah') }}</label>
                    <select name="aplikasi_id" required style="width: 100%; padding: 8px; border: 1px solid var(--line); border-radius: 6px; font-family: var(--font-body); font-size: 14px;">
                        <option value="">{{ __('messages.pilih_aplikasi') }}</option>
                        @foreach($aplikasis as $app)
                            <option value="{{ $app->aplikasi_id }}" {{ $t->aplikasi_id == $app->aplikasi_id ? 'selected' : '' }}>{{ $app->nama_aplikasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="margin-bottom: 16px;">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Tingkat Urgensi</label>
                    <select name="urgensi" style="width: 100%; padding: 8px; border: 1px solid var(--line); border-radius: 6px; font-family: var(--font-body); font-size: 14px;">
                        <option value="Rendah" {{ ($t->urgensi ?? 'Sedang') === 'Rendah' ? 'selected' : '' }}>🟢 Rendah (Pertanyaan umum / Tidak mengganggu operasional)</option>
                        <option value="Sedang" {{ ($t->urgensi ?? 'Sedang') === 'Sedang' ? 'selected' : '' }}>🟡 Sedang (Kendala wajar / Ada alternatif lain)</option>
                        <option value="Tinggi" {{ ($t->urgensi ?? 'Sedang') === 'Tinggi' ? 'selected' : '' }}>🔴 Tinggi (Sistem kritis lumpuh / Butuh penanganan segera)</option>
                    </select>
                </div>
                <div class="field" style="margin-bottom: 16px;">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">{{ __('messages.deskripsi_kendala') }}</label>
                    <textarea name="permasalahan" required style="width: 100%; padding: 8px; border: 1px solid var(--line); border-radius: 6px; font-family: var(--font-body); font-size: 14px; min-height: 100px;">{{ $t->permasalahan }}</textarea>
                </div>
                <div class="field" style="margin-bottom: 16px;">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Ganti Lampiran (Opsional)</label>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Biarkan kosong jika tidak ingin mengganti lampiran saat ini. Jika diisi, lampiran lama akan terhapus.</div>
                    <input type="file" id="edit_lampiran_input_{{ $t->ticket_id }}" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.mp4,.pdf,.doc,.docx,.xlsx,.csv,.pptx,.ppsx,.xlsm,.docm,.xlsb,.zip,.rar" style="width:100%; font-size: 13px; font-family:var(--font-body); padding:8px; border:1.5px dashed var(--line); border-radius:8px; background:var(--paper); cursor:pointer;">
                    <div class="helper" style="font-size: 11px; margin-top: 4px;">{{ __('messages.format_lampiran') }}</div>
                </div>
            </div>
            <div class="modal-foot" style="display: flex; gap: 12px; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid var(--line);">
                <button type="button" class="btn btn-danger" onclick="closeModal('modal-edit-{{ $t->ticket_id }}'); openModal('modal-delete-{{ $t->ticket_id }}')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    {{ __('messages.hapus_laporan') }}
                </button>
                <div style="display: flex; gap: 12px;">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-{{ $t->ticket_id }}'); openModal('modal-detail-{{ $t->ticket_id }}');">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_perubahan') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const skeleton = document.getElementById('skeleton-loading');
        const content  = document.getElementById('actual-content');
        
        // Use a simple mechanism to wait 0.8s then show content
        setTimeout(function () {
            if(skeleton) skeleton.style.display = 'none';
            if(content) content.style.display = 'block';
        }, 800);

        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('a')) {
                    openModal(this.getAttribute('data-target'));
                }
            });
        });
    });

    const currentRatings = {};
    const ratingLabels = {
        1: 'Sangat Buruk (1)',
        2: 'Kurang Puas (2)',
        3: 'Cukup (3)',
        4: 'Puas & Membantu (4)',
        5: 'Sangat Memuaskan! (5)'
    };

    function setRating(ticketId, star) {
        currentRatings[ticketId] = star;
        const input = document.getElementById('rating-input-' + ticketId);
        if (input) input.value = star;
        const label = document.getElementById('rating-label-' + ticketId);
        if (label) label.textContent = ratingLabels[star] || (star + ' Bintang');
        updateStarColors(ticketId, star);
    }

    function hoverRating(ticketId, star) {
        updateStarColors(ticketId, star);
        const label = document.getElementById('rating-label-' + ticketId);
        if (label) label.textContent = ratingLabels[star] || (star + ' Bintang');
    }

    function resetRatingHover(ticketId) {
        const selected = currentRatings[ticketId] || 0;
        updateStarColors(ticketId, selected);
        const label = document.getElementById('rating-label-' + ticketId);
        if (label) {
            label.textContent = selected > 0 ? (ratingLabels[selected] || (selected + ' Bintang')) : 'Pilih Bintang';
        }
    }

    function updateStarColors(ticketId, count) {
        const box = document.getElementById('star-box-' + ticketId);
        if (!box) return;
        const stars = box.querySelectorAll('.star-item');
        stars.forEach((s, idx) => {
            s.style.color = (idx < count) ? '#f59e0b' : '#cbd5e1';
        });
    }
</script>
@endsection
