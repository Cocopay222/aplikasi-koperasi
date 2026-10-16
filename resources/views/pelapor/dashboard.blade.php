@extends('layouts.app')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'SupportFlow')


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
                <button class="btn btn-amber" onclick="openModal('modal-create')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>{{ __('messages.buat_laporan_baru') }}</span>
                </button>
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
                            <div class="meta" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span>{{ $t->aplikasi->nama_aplikasi }} &middot; {{ $t->tanggal_input->format('d M Y') }} &middot; {{ $t->tanggal_input->format('H:i') }}</span>
                                @php
                                    $urgensi = $t->urgensi ?? 'Sedang';
                                    $urgensiColor = match($urgensi) {
                                        'Tinggi' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'color' => '#dc2626'],
                                        'Rendah' => ['bg' => 'rgba(2, 132, 199, 0.12)', 'color' => '#0284c7'],
                                        default => ['bg' => 'rgba(217, 119, 6, 0.12)', 'color' => '#d97706'],
                                    };
                                @endphp
                                <span style="background: {{ $urgensiColor['bg'] }}; color: {{ $urgensiColor['color'] }}; padding: 2px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 600;">
                                    ⚡ {{ $urgensi }}
                                </span>
                                @if($t->rating)
                                    <span style="color: #f59e0b; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 2px;">
                                        ★ {{ $t->rating }}/5
                                    </span>
                                @endif
                            </div>
                            
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
                                    &rarr;
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
            <button type="button" class="modal-x" onclick="closeModal('modal-ticket-{{ $t->ticket_id }}'); event.stopPropagation();">&times;</button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div style="margin-bottom: 24px;">
                <a href="{{ route('pelapor.tickets.dokumen', $t->ticket_id) }}" class="btn btn-ghost" style="display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #686c9d; color: #3c3f68; background: #f4f5fa; padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; width: 100%;">
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
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4d5183" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: 0.5px;">Target SLA Layanan</span>
                    </div>
                    <span style="font-size: 0.8rem; font-weight: 600; color: #4d5183; background: rgba(77, 81, 131, 0.1); padding: 2px 8px; border-radius: 6px;">Target: {{ $targetSla }} Jam</span>
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
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">{{ __('messages.lampiran_respons_support') ?? 'Lampiran Respons Support' }}</div>
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
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Surat Laporan Template</div>
                    <button type="button" onclick="openUniversalPreview('{{ $tplUrl }}', '{{ pathinfo($t->template_laporan, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($t->template_laporan)) }}')" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #686c9d; color: #3c3f68; background: #f4f5fa; padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer;">
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
                                <span style="font-weight: 600; color: {{ $isMe ? 'var(--primary)' : ($isSupport ? '#4d5183' : 'var(--ink)') }};">
                                    {{ $isMe ? 'Anda' : $comment->user?->nama }}
                                </span>
                                @if($isSupport)
                                    <span style="background: rgba(77, 81, 131, 0.12); color: #4d5183; padding: 1px 6px; border-radius: 4px; font-size: 0.65rem; font-weight: 700;">SUPPORT</span>
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
            <button type="button" class="modal-x" onclick="closeModal('modal-edit-{{ $t->ticket_id }}'); openModal('modal-ticket-{{ $t->ticket_id }}');">&times;</button>
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
                        <option value="Rendah" {{ ($t->urgensi ?? 'Sedang') === 'Rendah' ? 'selected' : '' }}>Rendah (Pertanyaan / Konsultasi)</option>
                        <option value="Sedang" {{ ($t->urgensi ?? 'Sedang') === 'Sedang' ? 'selected' : '' }}>Sedang (Gangguan Normal)</option>
                        <option value="Tinggi" {{ ($t->urgensi ?? 'Sedang') === 'Tinggi' ? 'selected' : '' }}>Tinggi (Mendesak / Operasional Macet)</option>
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
@endforeach

<!-- Modals for FAQs -->
@foreach($faqs as $faq)
<div class="overlay" id="modal-faq-{{ $faq->faq_id }}">
    <div class="modal w-sm">
        <div class="modal-head">
            <div><h3>{{ __('messages.faq_detail') ?? 'FAQ Detail' }}</h3><p>{{ $faq->kategori->nama_kategori ?? __('messages.umum') ?? 'Umum' }}</p></div>
            <button type="button" class="modal-x" onclick="closeModal('modal-faq-{{ $faq->faq_id }}'); event.stopPropagation();">&times;</button>
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
            <button type="button" class="modal-x" onclick="closeModal('modal-create')">&times;</button>
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

                <div class="field" style="margin-top: 14px;">
                    <label style="display: flex; align-items: center; justify-content: space-between;">
                        <span>Tingkat Urgensi / Prioritas</span>
                        <span style="font-size: 11px; color: var(--text-muted); font-weight: normal;">Pilih sesuai dampak kendala</span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 6px;">
                        <label style="display: flex; align-items: flex-start; gap: 8px; padding: 10px 12px; border: 1.5px solid var(--line); border-radius: 8px; cursor: pointer; background: var(--paper); transition: all 0.2s;" class="urgensi-radio-label" onclick="selectUrgensi(this, 'Rendah')">
                            <input type="radio" name="urgensi" value="Rendah" style="margin-top: 2px;">
                            <div>
                                <div style="font-weight: 600; font-size: 13px; color: #0284c7;">Rendah</div>
                                <div style="font-size: 11px; color: var(--text-muted); line-height: 1.3;">Konsultasi / Tanya</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 8px; padding: 10px 12px; border: 1.5px solid #f59e0b; border-radius: 8px; cursor: pointer; background: #fffbeb; transition: all 0.2s;" class="urgensi-radio-label" onclick="selectUrgensi(this, 'Sedang')">
                            <input type="radio" name="urgensi" value="Sedang" checked style="margin-top: 2px;">
                            <div>
                                <div style="font-weight: 600; font-size: 13px; color: #d97706;">Sedang</div>
                                <div style="font-size: 11px; color: #b45309; line-height: 1.3;">Gangguan Normal</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 8px; padding: 10px 12px; border: 1.5px solid var(--line); border-radius: 8px; cursor: pointer; background: var(--paper); transition: all 0.2s;" class="urgensi-radio-label" onclick="selectUrgensi(this, 'Tinggi')">
                            <input type="radio" name="urgensi" value="Tinggi" style="margin-top: 2px;">
                            <div>
                                <div style="font-weight: 600; font-size: 13px; color: #dc2626;">Tinggi</div>
                                <div style="font-size: 11px; color: var(--text-muted); line-height: 1.3;">Operasional Macet</div>
                            </div>
                        </label>
                    </div>
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
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <label style="margin-bottom: 0;">{{ __('messages.upload_lampiran_opsional') }}</label>
                        <span style="font-size: 11.5px; color: #0284c7; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            📋 Bisa Paste (Ctrl + V)
                        </span>
                    </div>
                    <div style="font-size: 11.5px; color: #64748b; margin-bottom: 8px;">
                        Pilih file atau tekan <b>Ctrl + V</b> di mana saja pada form ini untuk menempelkan screenshot layar.
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="file" id="lampiran_input" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.mp4,.pdf,.doc,.docx,.xlsx,.csv,.pptx,.ppsx,.xlsm,.docm,.xlsb,.zip,.rar" style="flex: 1; width:100%; font-size: calc(13px * var(--text-scale, 1)); font-family:var(--font-body); padding:8px; border:1.5px dashed var(--line); border-radius:8px; background:var(--paper); cursor:pointer;" onchange="handleLampiranFileChange(this)">
                        <button type="button" id="clear_lampiran_btn" style="display: none; padding: 8px 12px; background: #fee2e2; color: #ef4444; border: 1px solid #f87171; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;" onclick="clearAllLampiran()">{{ __('messages.hapus_lampiran') }}</button>
                    </div>
                    <div id="lampiran_preview_container" style="display: none; margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px;"></div>
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

function selectUrgensi(label, val) {
    document.querySelectorAll('.urgensi-radio-label').forEach(l => {
        l.style.borderColor = 'var(--line)';
        l.style.background = 'var(--paper)';
    });
    label.style.borderColor = val === 'Tinggi' ? '#dc2626' : (val === 'Sedang' ? '#f59e0b' : '#0284c7');
    label.style.background = val === 'Tinggi' ? '#fef2f2' : (val === 'Sedang' ? '#fffbeb' : '#f4f5fa');
    const radio = label.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}

function handleLampiranFileChange(input) {
    if (!window.dtPelapor) window.dtPelapor = new DataTransfer();
    for (let i = 0; i < input.files.length; i++) {
        window.dtPelapor.items.add(input.files[i]);
    }
    input.files = window.dtPelapor.files;
    renderLampiranPreviews();
}

function clearAllLampiran() {
    window.dtPelapor = new DataTransfer();
    const input = document.getElementById('lampiran_input');
    if (input) input.value = '';
    renderLampiranPreviews();
}

function removeLampiranIndex(index) {
    if (!window.dtPelapor) return;
    const newDt = new DataTransfer();
    for (let i = 0; i < window.dtPelapor.files.length; i++) {
        if (i !== index) newDt.items.add(window.dtPelapor.files[i]);
    }
    window.dtPelapor = newDt;
    const input = document.getElementById('lampiran_input');
    if (input) input.files = window.dtPelapor.files;
    renderLampiranPreviews();
}

function renderLampiranPreviews() {
    const container = document.getElementById('lampiran_preview_container');
    const infoSpan = document.getElementById('lampiran_info');
    const clearBtn = document.getElementById('clear_lampiran_btn');
    const input = document.getElementById('lampiran_input');

    if (!container || !input) return;
    container.innerHTML = '';

    const files = window.dtPelapor ? window.dtPelapor.files : input.files;
    if (!files || files.length === 0) {
        container.style.display = 'none';
        if (infoSpan) infoSpan.style.display = 'none';
        if (clearBtn) clearBtn.style.display = 'none';
        return;
    }

    container.style.display = 'flex';
    if (clearBtn) clearBtn.style.display = 'inline-block';
    if (infoSpan) {
        infoSpan.style.display = 'block';
        let totalSize = 0;
        for (let i = 0; i < files.length; i++) totalSize += files[i].size;
        infoSpan.innerHTML = '&#9989; ' + files.length + ' file dipilih (' + (totalSize / 1024 / 1024).toFixed(2) + ' MB)';
    }

    Array.from(files).forEach((file, idx) => {
        const item = document.createElement('div');
        item.style.cssText = 'position: relative; width: 85px; height: 85px; border-radius: 8px; border: 1.5px solid var(--line); overflow: hidden; background: var(--paper-raised); display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 11px; padding: 4px; box-sizing: border-box;';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.innerHTML = '&times;';
        removeBtn.style.cssText = 'position: absolute; top: 2px; right: 2px; width: 18px; height: 18px; border-radius: 50%; background: #ef4444; color: white; border: none; font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 2;';
        removeBtn.onclick = function(e) {
            e.stopPropagation();
            removeLampiranIndex(idx);
        };
        item.appendChild(removeBtn);

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; border-radius: 6px;';
            item.appendChild(img);
        } else {
            const ext = file.name.split('.').pop().toUpperCase();
            item.innerHTML += `<div style="font-weight: 700; color: var(--primary); font-size: 12px;">${ext}</div><div style="font-size: 9.5px; color: var(--text-muted); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 100%;">${file.name}</div>`;
        }

        container.appendChild(item);
    });
}

// Paste Screenshot Langsung dari Clipboard (Ctrl + V)
document.addEventListener('paste', function(e) {
    const modalCreate = document.getElementById('modal-create');
    if (!modalCreate) return;
    const isVisible = modalCreate.classList.contains('active') || modalCreate.style.display === 'flex' || modalCreate.style.display === 'block';
    if (!isVisible) return;

    const items = (e.clipboardData || e.originalEvent?.clipboardData)?.items;
    if (!items) return;

    let foundImage = false;
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        if (item.kind === 'file' && item.type.startsWith('image/')) {
            const blob = item.getAsFile();
            const dateStr = new Date().toISOString().replace(/[:.]/g, '-');
            const file = new File([blob], 'screenshot_' + dateStr + '.png', { type: blob.type });

            if (!window.dtPelapor) window.dtPelapor = new DataTransfer();
            window.dtPelapor.items.add(file);

            const input = document.getElementById('lampiran_input');
            if (input) input.files = window.dtPelapor.files;

            renderLampiranPreviews();
            foundImage = true;
        }
    }
    if (foundImage) {
        e.preventDefault();
    }
});

// Star Rating CSAT Handlers
function setRating(ticketId, val) {
    const input = document.getElementById('rating-input-' + ticketId);
    if (input) input.value = val;
    const labels = ['', 'Sangat Buruk 😡', 'Kurang Memuaskan 🙁', 'Cukup Baik 😐', 'Memuaskan 😊', 'Sangat Puas! 🌟'];
    const labelEl = document.getElementById('rating-label-' + ticketId);
    if (labelEl) labelEl.innerText = labels[val] || val + ' Bintang';
    const box = document.getElementById('star-box-' + ticketId);
    if (!box) return;
    const stars = box.querySelectorAll('.star-item');
    stars.forEach((s, idx) => {
        s.style.color = (idx < val) ? '#f59e0b' : '#cbd5e1';
    });
}
function hoverRating(ticketId, val) {
    const box = document.getElementById('star-box-' + ticketId);
    if (!box) return;
    const stars = box.querySelectorAll('.star-item');
    stars.forEach((s, idx) => {
        s.style.color = (idx < val) ? '#fbbf24' : '#cbd5e1';
    });
}
function resetRatingHover(ticketId) {
    const currentVal = parseInt(document.getElementById('rating-input-' + ticketId)?.value) || 0;
    const box = document.getElementById('star-box-' + ticketId);
    if (!box) return;
    const stars = box.querySelectorAll('.star-item');
    stars.forEach((s, idx) => {
        s.style.color = (idx < currentVal) ? '#f59e0b' : '#cbd5e1';
    });
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
