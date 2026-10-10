@extends('layouts.app')

@section('page_title', __('messages.title_riwayat'))
@section('page_subtitle', 'SAKTI Desk')


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

        <div class="toolbar fade-up" style="animation-delay: 0.15s; margin-bottom:20px;">
            <div class="search">
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

        <div class="fade-up" style="animation-delay: 0.2s;">
            <div class="table-scroll-wrapper">
            <table class="tickets" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th width="120">{{ __('messages.id_laporan') }}</th>
                        <th width="120">{{ __('messages.col_tanggal') }}</th>
                        <th width="180">{{ __('messages.aplikasi') }}</th>
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
                        <td><div style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:500;">{{ $t->permasalahan }}</div></td>
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
            <button type="button" class="modal-x" onclick="closeModal('modal-preview-{{ $t->ticket_id }}'); event.stopPropagation();">✕</button>
        </div>
        
        <div class="modal-body" style="padding: 24px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #1d4ed8; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-bottom: 20px;">
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
            <button type="button" class="modal-x" onclick="closeModal('modal-detail-{{ $t->ticket_id }}'); event.stopPropagation();">✕</button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div style="margin-bottom: 24px;">
                <a href="{{ route('pelapor.tickets.dokumen', $t->ticket_id) }}" class="btn btn-ghost" style="display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #3b82f6; color: #1d4ed8; background: #eff6ff; padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; width: 100%;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    {{ __('messages.open_history_letter_document') }}
                </a>
            </div>
            @php $statusStr = is_object($t->status) ? $t->status->value : $t->status; @endphp
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
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.lampiran_bukti') }}</div>
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
                    @endif
                @endforeach
                </div>
            </div>
            @endif

            @if($t->lampiran_support)
            <div style="margin-bottom: 16px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Lampiran Respons Support</div>
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
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lampSupp) }}', '{{ $extSupp }}', '{{ addslashes(basename($lampSupp)) }}')" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
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

            @if($t->template_laporan)
                @php
                    $tplUrl = route('download.template', basename($t->template_laporan));
                @endphp
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.template_surat_dari_support') ?? 'Template Surat Dari Support' }}</div>
                    <button type="button" onclick="openUniversalPreview('{{ $tplUrl }}', '{{ pathinfo($t->template_laporan, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($t->template_laporan)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #3b82f6; color: #1d4ed8; background: #eff6ff; padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer;">
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
            <button type="button" class="modal-x" onclick="closeModal('modal-delete-{{ $t->ticket_id }}'); event.stopPropagation();">✕</button>
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
            <button type="button" class="modal-x" onclick="closeModal('modal-edit-{{ $t->ticket_id }}'); openModal('modal-detail-{{ $t->ticket_id }}');">✕</button>
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
</script>
@endsection
