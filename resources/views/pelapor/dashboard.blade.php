@extends('layouts.app')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'SAKTI Desk')


@section('content')
<div class="pelapor-panel active">
    {{-- SKELETON LOADING STATE --}}
    <div class="skeleton-wrap" id="skeleton-loading">
        <div class="skel" style="height: 100px; width: 100%; margin-bottom: 22px;"></div>
        <div class="skel" style="height: 120px; width: 100%; margin-bottom: 28px;"></div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px;">
            <div class="skel" style="height: 100px;"></div>
            <div class="skel" style="height: 100px;"></div>
            <div class="skel" style="height: 100px;"></div>
        </div>
        <div>
            <div class="skel" style="height: 30px; width: 40%; margin-bottom: 14px;"></div>
            <div class="skel" style="height: 80px; width: 100%; margin-bottom: 12px;"></div>
            <div class="skel" style="height: 80px; width: 100%; margin-bottom: 12px;"></div>
            <div class="skel" style="height: 80px; width: 100%; margin-bottom: 12px;"></div>
        </div>
    </div>

    {{-- ACTUAL CONTENT --}}
    <div class="content-wrap" id="actual-content" style="display:none;">
        @if(session('success'))
            <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; margin-bottom: 24px; font-size: calc(14px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); transition: opacity 0.6s ease, transform 0.6s ease;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
            </div>
        @endif

        <div class="welcome-banner fade-up" style="animation-delay: 0.1s;">
            <div>
                <h2>{{ __('messages.halo') }}, {{ Auth::user()->nama ?? 'User' }}!</h2>
                <p>{{ __('messages.selamat_datang') }}</p>
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="dashboard-tabs fade-up" style="display: flex; gap: 8px; border-bottom: 2px solid var(--line); margin-bottom: 24px; animation-delay: 0.12s; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
            <button type="button" id="tab-btn-tickets" class="dash-tab-btn active" onclick="switchDashTab('tickets', this)" style="padding: 10px 18px; font-weight: 600; font-size: 14px; border: none; background: none; color: var(--brand-primary); border-bottom: 2px solid var(--brand-primary); margin-bottom: -2px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <img src="{{ asset('ticket-laporan.svg') }}" alt="" style="width: 18px; height: 18px; object-fit: contain; vertical-align: middle;"> {{ __('messages.tiket_dan_laporan_saya') }}
            </button>
            <button type="button" id="tab-btn-faq" class="dash-tab-btn" onclick="switchDashTab('faq', this)" style="padding: 10px 18px; font-weight: 500; font-size: 14px; border: none; background: none; color: var(--ink-soft); border-bottom: 2px solid transparent; margin-bottom: -2px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <span style="-webkit-mask-image: url('{{ asset('question.png') }}'); mask-image: url('{{ asset('question.png') }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center; background-color: currentColor; width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></span> {{ __('messages.pertanyaan_sering_diajukan') }}
            </button>
        </div>

        <!-- PANE 1: TIKET & LAPORAN -->
        <div id="tab-pane-tickets" class="dash-tab-pane">
            <div class="cta-band fade-up" style="animation-delay: 0.15s;">
                <div>
                    <h2>{{ __('messages.ada_kendala') }}</h2>
                    <p>{{ __('messages.laporkan_sekali') }}</p>
                </div>
                <button class="btn btn-amber" onclick="openModal('modal-create')">＋ {{ __('messages.buat_laporan_baru') }}</button>
            </div>

            <!-- Statistik Sederhana -->
            <div class="stat-row fade-up" style="animation-delay: 0.2s;">
                <div class="stat-card"><div class="n" style="color:var(--clay)">{{ $totalOpen }}</div><div class="l">{{ __('messages.open_proses') }}</div></div>
                <div class="stat-card"><div class="n" style="color:#B8923F">{{ $totalPending }}</div><div class="l">{{ __('messages.pending_butuh_info') }}</div></div>
                <div class="stat-card"><div class="n" style="color:var(--sage)">{{ $totalDone }}</div><div class="l">{{ __('messages.selesai_total') }}</div></div>
            </div>

            <div class="fade-up" style="animation-delay: 0.25s;">
                <div>
                    <div class="page-head" style="margin-bottom:14px;">
                        <div><h1 style="font-size: calc(22px * var(--text-scale, 1));">{{ __('messages.riwayat_laporan_anda') }}</h1></div>
                    </div>
                    
                    <div class="ticket-list">
                        @forelse($tickets as $t)
                        <div class="ticket-card fade-up" onclick="openModal('modal-ticket-{{ $t->ticket_id }}')" style="cursor:pointer; animation-delay: {{ 0.3 + ($loop->index * 0.08) }}s;">
                            <div class="tid">{{ $t->ticket_id }}</div>
                            <div class="main">
                                <h3>{{ $t->permasalahan }}</h3>
                                <p>{{ $t->penyelesaian ?? __('messages.belum_ada_catatan') }}</p>
                            </div>
                            <div class="meta">{{ $t->aplikasi->nama_aplikasi }} &middot; {{ $t->tanggal_input->format('d M Y') }} &middot; {{ $t->tanggal_input->format('H:i') }}</div>
                            
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
                        </div>
                        @empty
                        <div class="ticket-card" style="justify-content:center; padding:30px;">
                            <p class="eyebrow">{{ __('messages.belum_ada_tiket_laporan') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- PANE 2: PUSAT SOLUSI / FAQ -->
        <div id="tab-pane-faq" class="dash-tab-pane" style="display: none;">
            <div class="fade-up" style="animation-delay: 0.1s; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--ink);">
                        {{ __('messages.pertanyaan_sering_diajukan') }}
                    </h3>
                    <p style="color: var(--ink-soft); font-size: 13px; margin: 4px 0 0 0;">
                        {{ __('messages.faq_relevan_desc') }}
                    </p>
                </div>

                <!-- Filter & Form Pencarian FAQ -->
                <form method="GET" action="{{ route('pelapor.dashboard') }}" style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;">
                    <input type="hidden" name="tab" value="faq">
                    <div style="flex: 1; min-width: 240px; position: relative;">
                        <span style="-webkit-mask-image: url('{{ asset('magnifying-glass.png') }}'); mask-image: url('{{ asset('magnifying-glass.png') }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center; background-color: currentColor; width: 14px; height: 14px; display: inline-block; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--ink-soft);"></span>
                        <input type="text" name="faq_search" value="{{ request('faq_search') }}" placeholder="{{ __('messages.cari_faq_placeholder') }}" style="width: 100%; padding: 10px 12px 10px 36px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 14px; box-sizing: border-box;">
                    </div>
                    
                    <select name="faq_kategori_id" onchange="this.form.submit()" style="padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 14px; font-weight: 500; cursor: pointer;">
                        <option value="">{{ __('messages.semua_kategori') }}</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->kategori_id }}" {{ request('faq_kategori_id') == $kat->kategori_id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-secondary" style="padding: 10px 18px; font-size: 14px; border-radius: 8px;">
                        {{ __('messages.cari') }}
                    </button>

                    @if(request('faq_search') || request('faq_kategori_id'))
                        <a href="{{ route('pelapor.dashboard', ['tab' => 'faq']) }}" class="btn btn-ghost" style="padding: 10px 14px; font-size: 14px; border-radius: 8px; color: var(--ink-soft); text-decoration: none;">
                            {{ __('messages.reset') }}
                        </a>
                    @endif
                </form>

                <!-- Daftar FAQ Accordion -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($faqs as $faq)
                        <div class="faq-item" style="border: 1px solid var(--line); border-radius: 10px; background: var(--paper); overflow: hidden; transition: transform 0.2s; cursor: pointer;" onmouseover="this.style.transform='scale(1.01)'" onmouseout="this.style.transform='scale(1)'">
                            <button type="button" onclick="openModal('modal-faq-{{ $faq->faq_id }}')" style="width: 100%; text-align: left; background: none; border: none; padding: 14px 18px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 14px; color: var(--ink);">
                                <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                                    <span style="background: var(--brand-primary-soft); color: var(--brand-primary); padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; white-space: nowrap;">
                                        {{ $faq->kategori->nama_kategori ?? __('messages.umum') ?? 'Umum' }}
                                    </span>
                                    <span style="font-weight: 600; font-size: 14px; line-height: 1.4;">
                                        {{ $faq->pertanyaan }}
                                    </span>
                                </div>
                                <span style="font-size: 16px; color: var(--ink-soft);">
                                    →
                                </span>
                            </button>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 30px 16px; border: 1.5px dashed var(--line); border-radius: 10px; color: var(--ink-soft);">
                            <div style="-webkit-mask-image: url('{{ asset('magnifying-glass.png') }}'); mask-image: url('{{ asset('magnifying-glass.png') }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center; background-color: currentColor; width: 28px; height: 28px; display: inline-block; margin-bottom: 6px;"></div>
                            <div style="font-weight: 600; font-size: 14px; color: var(--ink);">{{ __('messages.belum_ada_faq_public') }}</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Tickets -->
@foreach($tickets as $t)
<div class="overlay" id="modal-ticket-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head">
            <div><h3>{{ __('messages.detail_laporan') }}</h3><p>{{ $t->ticket_id }}</p></div>
            <button type="button" class="modal-x" onclick="closeModal('modal-ticket-{{ $t->ticket_id }}'); event.stopPropagation();">✕</button>
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
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.lampiran_respons_support') ?? 'Lampiran Respons Support' }}</div>
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
                            {{ __('messages.lihat') ?? 'Lihat' }} {{ strtoupper($extSupp) }}
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
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Surat Laporan Template</div>
                    <button type="button" onclick="openUniversalPreview('{{ $tplUrl }}', '{{ pathinfo($t->template_laporan, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($t->template_laporan)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #3b82f6; color: #1d4ed8; background: #eff6ff; padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span>{{ __('messages.lihat') ?? 'Lihat' }} {{ str_replace(['_', '-'], ' ', pathinfo($t->template_laporan, PATHINFO_FILENAME)) }} ({{ strtoupper(pathinfo($t->template_laporan, PATHINFO_EXTENSION)) }})</span>
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
                                <form action="{{ route('pelapor.tickets.delete_balasan', ['ticket' => $t->ticket_id, 'index' => $index]) }}" method="POST" style="margin: 0;" onsubmit="return confirm('{{ __('messages.apakah_yakin_hapus_file') ?? 'Apakah Anda yakin ingin menghapus file ini?' }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--danger); border: 1.5px solid var(--danger-soft); padding: 8px; border-radius: 6px;" title="{{ __('messages.hapus_file') ?? 'Hapus File' }}">
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
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modal-ticket-{{ $t->ticket_id }}'); openModal('modal-edit-{{ $t->ticket_id }}');" style="padding: 6px 12px; font-size: 13px;">{{ __('messages.btn_edit_report') }}</button>
            @else
                <div></div>
            @endif
            <button type="button" class="btn btn-ghost" onclick="closeModal('modal-ticket-{{ $t->ticket_id }}')">{{ __('messages.btn_tutup') }}</button>
        </div>
    </div>
</div>

@if((is_object($t->status) ? $t->status->value : $t->status) === 'Open')
<div class="overlay" id="modal-edit-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head">
            <div><h3>{{ __('messages.btn_edit_report') }}</h3><p>{{ $t->ticket_id }}</p></div>
            <button type="button" class="modal-x" onclick="closeModal('modal-edit-{{ $t->ticket_id }}'); openModal('modal-ticket-{{ $t->ticket_id }}');">✕</button>
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
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">{{ __('messages.ganti_lampiran_opsional') ?? 'Ganti Lampiran (Opsional)' }}</label>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">{{ __('messages.biarkan_kosong_lampiran') ?? 'Biarkan kosong jika tidak ingin mengganti lampiran saat ini. Jika diisi, lampiran lama akan terhapus.' }}</div>
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
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-{{ $t->ticket_id }}'); openModal('modal-ticket-{{ $t->ticket_id }}');">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_perubahan') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="overlay" id="modal-delete-{{ $t->ticket_id }}">
    <div class="modal w-sm">
        <div class="modal-head" style="border-bottom: 1px solid var(--line); padding-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: #fef2f2; color: #ef4444; padding: 8px; border-radius: 8px; display: flex;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 3-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
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
@endforeach

<!-- Modals for FAQs -->
@foreach($faqs as $faq)
<div class="overlay" id="modal-faq-{{ $faq->faq_id }}">
    <div class="modal w-sm">
        <div class="modal-head">
            <div><h3>{{ __('messages.faq_detail') ?? 'FAQ Detail' }}</h3><p>{{ $faq->kategori->nama_kategori ?? __('messages.umum') ?? 'Umum' }}</p></div>
            <button type="button" class="modal-x" onclick="closeModal('modal-faq-{{ $faq->faq_id }}'); event.stopPropagation();">✕</button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div style="margin-bottom: 16px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.pertanyaan') }}</div>
                <div style="font-size: 0.95rem; color: var(--ink); line-height: 1.5; font-weight: 500;">{{ $faq->pertanyaan }}</div>
            </div>
            <div style="margin-bottom: 8px;">
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.jawaban') }}</div>
                <div style="font-size: 0.95rem; color: var(--ink-soft); line-height: 1.6; white-space: pre-wrap; background: var(--paper-raised); padding: 16px; border-radius: 8px; border: 1px solid var(--line);">{!! nl2br(e($faq->jawaban)) !!}</div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Create -->
<div class="overlay" id="modal-create">
    <div class="modal w-sm">
        <div class="modal-head">
            <div><h3>{{ __('messages.buat_laporan_baru') }}</h3><p>{{ __('messages.jelaskan_kendala') }}</p></div>
            <button type="button" class="modal-x" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form action="{{ route('pelapor.tickets.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return checkFileSize(this, 'lampiran_input', 8);">
            @csrf
            <div class="modal-body">
                <div style="margin-bottom: 16px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 6px;">
                        <div class="field" style="margin-bottom: 0;">
                            <label>No. Telepon / WhatsApp (Opsional)</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', Auth::user()->whatsapp ?? '') }}" placeholder="Contoh: 08123456789">
                            @error('whatsapp') <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                        <div class="field" style="margin-bottom: 0;">
                            <label>Email (Opsional)</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" placeholder="nama@email.com">
                            @error('email') <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div style="font-size: 11.5px; color: #64748b; font-style: italic; display: flex; align-items: flex-start; gap: 5px; line-height: 1.4; margin-top: 4px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>Harap cantumkan atau perbarui dengan nomor WhatsApp & email yang aktif untuk mempermudah koordinasi Tim Support.</span>
                    </div>
                </div>

                <div class="field">
                    <label>{{ __('messages.aplikasi_bermasalah') }}</label>
                    <select name="aplikasi_id" required>
                        <option value="">{{ __('messages.pilih_aplikasi') }}</option>
                        @foreach($aplikasis as $app)
                            <option value="{{ $app->aplikasi_id }}" {{ old('aplikasi_id') == $app->aplikasi_id ? 'selected' : '' }}>{{ $app->nama_aplikasi }}</option>
                        @endforeach
                    </select>
                    @error('aplikasi_id') <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>
                <div class="field">
                    <label>{{ __('messages.deskripsi_kendala') }}</label>
                    <textarea name="permasalahan" id="permasalahan_input" required placeholder="{{ __('messages.tuliskan_detail') }}">{{ old('permasalahan') }}</textarea>
                    @error('permasalahan') <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div> @enderror
                    
                    <!-- Smart Auto-Suggest FAQ Box -->
                    <div id="faq-suggest-box" style="display: none; margin-top: 10px; padding: 12px; border: 1px solid var(--line); border-radius: 8px; background: var(--paper-sunken);">
                        <div style="font-size: 12px; font-weight: 700; color: var(--brand-primary); margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center; gap: 6px;">💡 {{ __('messages.solusi_cepat') }}</span>
                            <button type="button" style="background: none; border: none; font-size: 11px; color: var(--brand-primary); cursor: pointer; font-weight: 500; padding: 0;" onclick="closeModal('modal-create'); switchDashTab('faq', document.getElementById('tab-btn-faq'));">{{ __('messages.lihat_semua_faq') }}</button>
                        </div>
                        <div id="faq-suggest-list" style="display: flex; flex-direction: column; gap: 8px;"></div>
                    </div>
                </div>
                <div class="field" style="margin-top: 14px;">
                    <label>{{ __('messages.upload_lampiran_opsional') }}</label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="file" id="lampiran_input" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.mp4,.pdf,.doc,.docx,.xlsx,.csv,.pptx,.ppsx,.xlsm,.docm,.xlsb,.zip,.rar" style="flex: 1; width:100%; font-size: calc(13px * var(--text-scale, 1)); font-family:var(--font-body); padding:8px; border:1.5px dashed var(--line); border-radius:8px; background:var(--paper); cursor:pointer;" onchange="
                            if (!window.dtPelapor) window.dtPelapor = new DataTransfer();
                            for(let i=0; i<this.files.length; i++) {
                                window.dtPelapor.items.add(this.files[i]);
                            }
                            this.files = window.dtPelapor.files;
                            
                            const clearBtn = document.getElementById('clear_lampiran_btn');
                            const infoSpan = document.getElementById('lampiran_info');
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
                        <button type="button" id="clear_lampiran_btn" style="display: none; padding: 8px 12px; background: #fee2e2; color: #ef4444; border: 1px solid #f87171; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;" onclick="window.dtPelapor = new DataTransfer(); document.getElementById('lampiran_input').value = ''; this.style.display = 'none'; document.getElementById('lampiran_info').style.display='none';">{{ __('messages.hapus_lampiran') }}</button>
                    </div>
                    <div id="lampiran_info" style="display: none; font-size: 12.5px; color: #059669; font-weight: 600; margin-top: 6px;"></div>
                    <div class="helper">{{ __('messages.format_lampiran') }}</div>
                    @error('lampiran') <div style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-create')">{{ __('messages.batal') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.kirim_laporan') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchDashTab(tabName, btn) {
    document.querySelectorAll('.dash-tab-btn').forEach(b => {
        b.style.color = 'var(--ink-soft)';
        b.style.fontWeight = '500';
        b.style.borderBottomColor = 'transparent';
        b.classList.remove('active');
    });
    document.querySelectorAll('.dash-tab-pane').forEach(p => {
        p.style.display = 'none';
    });

    if (btn) {
        btn.style.color = 'var(--brand-primary)';
        btn.style.fontWeight = '600';
        btn.style.borderBottomColor = 'var(--brand-primary)';
        btn.classList.add('active');
    }

    const pane = document.getElementById('tab-pane-' + tabName);
    if (pane) {
        pane.style.display = 'block';
        // Force restart fade-up animations
        const fadeElements = pane.querySelectorAll('.fade-up');
        fadeElements.forEach(el => {
            el.style.animation = 'none';
            void el.offsetWidth; // trigger reflow
            el.style.animation = '';
        });
    }
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
        if(skeleton) skeleton.style.display = 'none';
        if(content) content.style.display = 'block';
    }, 1200);

    @if(request('tab') === 'faq' || request('faq_search') || request('faq_kategori_id'))
        const faqTabBtn = document.getElementById('tab-btn-faq');
        if (faqTabBtn) switchDashTab('faq', faqTabBtn);
    @endif

    @if($errors->any())
        openModal('modal-create');
    @endif

        // Smart Auto-Suggest FAQ Script
        const permasalahanInput = document.getElementById('permasalahan_input');
        const suggestBox = document.getElementById('faq-suggest-box');
        const suggestList = document.getElementById('faq-suggest-list');
        let searchDebounce = null;

        if (permasalahanInput && suggestBox && suggestList) {
            permasalahanInput.addEventListener('input', function () {
                const query = this.value.trim();
                clearTimeout(searchDebounce);

                if (query.length < 3) {
                    suggestBox.style.display = 'none';
                    suggestList.innerHTML = '';
                    return;
                }

                searchDebounce = setTimeout(function () {
                    fetch(`{{ route('pelapor.faq.search') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(faqs => {
                            if (faqs && faqs.length > 0) {
                                suggestList.innerHTML = faqs.map(faq => `
                                    <div style="border: 1px solid var(--line); border-radius: 6px; padding: 10px; background: var(--paper); font-size: 13px;">
                                        <div style="font-weight: 600; color: var(--ink); display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 4px;">
                                            <span><span style="-webkit-mask-image: url('{{ asset('question.png') }}'); mask-image: url('{{ asset('question.png') }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center; background-color: currentColor; width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></span>${faq.pertanyaan}</span>
                                            <span style="font-size: 10px; background: var(--brand-primary-soft); color: var(--brand-primary); padding: 2px 6px; border-radius: 4px; font-weight: 600; white-space: nowrap;">${faq.kategori ? faq.kategori.nama_kategori : 'Public'}</span>
                                        </div>
                                        <div style="font-size: 12px; color: var(--ink-soft); line-height: 1.4; white-space: pre-wrap;">${faq.jawaban}</div>
                                    </div>
                                `).join('');
                                suggestBox.style.display = 'block';
                            } else {
                                suggestBox.style.display = 'none';
                                suggestList.innerHTML = '';
                            }
                        })
                        .catch(() => {
                            suggestBox.style.display = 'none';
                        });
                }, 300);
            });
        }
    });
</script>
@endsection
