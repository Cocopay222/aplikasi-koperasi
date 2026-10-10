@extends('layouts.app')

@section('content')
<style>
    .doc-timeline-wrap {
        background: var(--paper-sunken, #f8fafc);
        border: 1px solid var(--line, #e2e8f0);
        border-radius: 16px;
        padding: 32px;
        margin-top: 24px;
    }
    .doc-step-card {
        background: var(--paper, #ffffff);
        border: 1px solid var(--line, #e2e8f0);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .doc-step-dot {
        border: 4px solid var(--paper-sunken, #ffffff);
    }
    .doc-btn-ghost {
        background: var(--paper, #ffffff);
        border: 1px solid var(--line, #e2e8f0);
        color: var(--ink, #1e293b);
    }

    html.dark-mode .doc-timeline-wrap {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    html.dark-mode .doc-step-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important;
    }
    html.dark-mode .doc-step-dot {
        border-color: #0f172a !important;
    }
    html.dark-mode .doc-btn-ghost {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    html.dark-mode .doc-img-btn {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    html.dark-mode .doc-img-label {
        background: #1e293b !important;
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }
    html.dark-mode .doc-text-box {
        background: #0f172a !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }
    html.dark-mode .doc-line-timeline {
        border-left-color: #334155 !important;
    }
</style>

<div style="padding: 32px; max-width: 1000px; width: 100%; box-sizing: border-box; margin: 0 auto;">
<div class="header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="color: var(--ink, #1e293b);">{{ __('messages.history_letter_document') }}</h1>
            <p class="subtitle" style="color: var(--text-muted, #64748b);">Tiket #{{ $ticket->ticket_id }} &mdash; {{ $ticket->aplikasi->nama_aplikasi ?? '-' }}</p>
        </div>
        @php
            $defaultBack = Auth::check() && Auth::user()->role === \App\Enums\UserRole::PELAPOR->value
                ? route('pelapor.dashboard')
                : route('support.dashboard');
            $prevUrl = url()->previous();
            if (!$prevUrl || $prevUrl === url()->current()) {
                $prevUrl = $defaultBack;
            }
        @endphp
        <a href="{{ $prevUrl }}" onclick="if(document.referrer && document.referrer !== window.location.href){ window.history.back(); return false; }" class="btn btn-ghost doc-btn-ghost" style="display: inline-flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            {{ __('messages.kembali') }}
        </a>
    </div>
</div>

<div class="doc-timeline-wrap">
    
    <div class="doc-line-timeline" style="position: relative; padding-left: 20px; border-left: 2px solid #e2e8f0; margin-left: 10px;">
        
        <!-- STEP 1: Pelaporan Awal -->
        <div style="margin-bottom: 32px; position: relative;">
            <div class="doc-step-dot" style="position: absolute; left: -29px; top: 0; width: 16px; height: 16px; border-radius: 50%; background: #3b82f6;"></div>
            <div class="doc-step-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <strong style="font-size: 1.1rem; color: var(--ink);">1. Laporan Tiket & Lampiran Bukti</strong>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Dari: {{ $ticket->pelapor->nama ?? 'Pelapor' }} ({{ $ticket->pelapor->instansi->nama_instansi ?? '-' }})</div>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); text-align: right;">
                        {{ $ticket->tanggal_input->format('d M Y, H:i') }}<br>
                        {{ $ticket->tanggal_input->diffForHumans() }}
                    </div>
                </div>
                
                <div class="doc-text-box" style="font-size: 0.95rem; color: var(--ink); line-height: 1.6; white-space: pre-wrap; background: var(--paper-raised, #f1f5f9); padding: 12px; border-radius: 8px; margin-bottom: 16px;">{{ $ticket->permasalahan }}</div>

                @if($ticket->lampiran)
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Dokumen Pendukung:</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @php $lampirans = is_array($ticket->lampiran) ? $ticket->lampiran : [$ticket->lampiran]; @endphp
                        @foreach($lampirans as $lamp)
                            @php $ext = strtolower(pathinfo($lamp, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="doc-img-btn" style="border: 1px solid var(--line); border-radius: 8px; overflow: hidden; background: white; cursor: pointer; padding: 0; width: fit-content; text-align: center; display: flex; flex-direction: column;">
                                    <img src="{{ Storage::url($lamp) }}" alt="Lampiran" style="height: 100px; width: auto; object-fit: cover;">
                                    <span class="doc-img-label" style="font-size: 0.7rem; padding: 4px; border-top: 1px solid var(--line); color: var(--text-muted); background: #f8fafc; display: block;">{{ Str::limit(basename($lamp), 15) }}</span>
                                </button>
                            @else
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost btn-sm doc-btn-ghost" style="display: inline-flex; align-items: center; gap: 8px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    Lihat {{ strtoupper($ext) }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
                @else
                <div style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">Tidak ada lampiran.</div>
                @endif
            </div>
        </div>

        <!-- STEP 2: Surat Laporan Template -->
        @if($ticket->template_laporan)
        @php
            $tplUrl = route('download.template', basename($ticket->template_laporan));
        @endphp
        <div style="margin-bottom: 32px; position: relative;">
            <div class="doc-step-dot" style="position: absolute; left: -29px; top: 0; width: 16px; height: 16px; border-radius: 50%; background: #8b5cf6;"></div>
            <div class="doc-step-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <strong style="font-size: 1.1rem; color: var(--ink);">2. Draft Surat Izin Akses Login Aplikasi</strong>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Dibuat Otomatis oleh Sistem</div>
                    </div>
                </div>
                
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px;">Sistem telah menghasilkan draf surat otomatis berdasarkan form yang diajukan.</p>

                <button type="button" onclick="openUniversalPreview('{{ $tplUrl }}', '{{ pathinfo($ticket->template_laporan, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($ticket->template_laporan)) }}')" class="btn btn-ghost" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #8b5cf6; color: #8b5cf6; background: rgba(139, 92, 246, 0.1); padding: 8px 16px; border-radius: 6px; font-weight: 600;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Lihat {{ str_replace(['_', '-'], ' ', pathinfo($ticket->template_laporan, PATHINFO_FILENAME)) }} ({{ strtoupper(pathinfo($ticket->template_laporan, PATHINFO_EXTENSION)) }})
                </button>
            </div>
        </div>
        @endif

        <!-- STEP 3: Surat Balasan Pelapor -->
        @if($ticket->surat_balasan)
        <div style="margin-bottom: 32px; position: relative;">
            <div class="doc-step-dot" style="position: absolute; left: -29px; top: 0; width: 16px; height: 16px; border-radius: 50%; background: #10b981;"></div>
            <div class="doc-step-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <strong style="font-size: 1.1rem; color: var(--ink);">3. Surat Balasan / Dokumen Legalitas</strong>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Diunggah oleh: {{ $ticket->pelapor->nama ?? 'Pelapor' }}</div>
                    </div>
                </div>
                
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px;">Dokumen surat resmi yang telah ditandatangani dan diunggah kembali oleh instansi.</p>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @php
                        $balasanFiles = json_decode($ticket->surat_balasan, true);
                        if (!is_array($balasanFiles)) {
                            $balasanFiles = [$ticket->surat_balasan];
                        }
                    @endphp
                    @foreach($balasanFiles as $index => $file)
                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($file) }}', '{{ pathinfo($file, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($file)) }}')" class="btn btn-ghost" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #10b981; color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 8px 16px; border-radius: 6px; font-weight: 600; width: fit-content;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            Lihat Dokumen Balasan {{ count($balasanFiles) > 1 ? '#' . ($index + 1) : '' }} ({{ strtoupper(pathinfo($file, PATHINFO_EXTENSION)) }})
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- STEP 4: Respons Support -->
        @if($ticket->status === \App\Enums\TicketStatus::DONE || $ticket->penyelesaian || $ticket->lampiran_support)
        <div style="margin-bottom: 32px; position: relative;">
            <div class="doc-step-dot" style="position: absolute; left: -29px; top: 0; width: 16px; height: 16px; border-radius: 50%; background: #f59e0b;"></div>
            <div class="doc-step-card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <strong style="font-size: 1.1rem; color: var(--ink);">4. Respons Akhir / Penanganan Support</strong>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Ditangani oleh: {{ $ticket->picSupport->nama ?? 'Tim Support' }}</div>
                    </div>
                    @if($ticket->tanggal_penyelesaian)
                    <div style="font-size: 0.8rem; color: var(--text-muted); text-align: right;">
                        {{ $ticket->tanggal_penyelesaian->format('d M Y, H:i') }}<br>
                        {{ $ticket->tanggal_penyelesaian->diffForHumans() }}
                    </div>
                    @endif
                </div>

                @if($ticket->penyelesaian)
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 6px;">Tindakan Penyelesaian:</div>
                    <div style="font-size: 0.95rem; color: #10b981; line-height: 1.6; white-space: pre-wrap; background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.3);">{{ $ticket->penyelesaian }}</div>
                </div>
                @endif

                @if($ticket->pencegahan)
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 6px;">Tindakan Pencegahan:</div>
                    <div style="font-size: 0.95rem; color: #f59e0b; line-height: 1.6; white-space: pre-wrap; background: rgba(245, 158, 11, 0.1); padding: 12px; border-radius: 8px; border: 1px solid rgba(245, 158, 11, 0.3);">{{ $ticket->pencegahan }}</div>
                </div>
                @endif

                @if($ticket->lampiran_support)
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Lampiran Respons Dokumen:</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @php $lampiranSupports = is_array($ticket->lampiran_support) ? $ticket->lampiran_support : [$ticket->lampiran_support]; @endphp
                        @foreach($lampiranSupports as $lamp)
                            @php $ext = strtolower(pathinfo($lamp, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="doc-img-btn" style="border: 1px solid #f59e0b; border-radius: 8px; overflow: hidden; background: white; cursor: pointer; padding: 0; width: fit-content; text-align: center; display: flex; flex-direction: column;">
                                    <img src="{{ Storage::url($lamp) }}" alt="Lampiran" style="height: 100px; width: auto; object-fit: cover;">
                                    <span class="doc-img-label" style="font-size: 0.7rem; padding: 4px; border-top: 1px solid #f59e0b; color: #b45309; background: #fef3c7; display: block;">{{ Str::limit(basename($lamp), 15) }}</span>
                                </button>
                            @else
                                <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost" style="display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #f59e0b; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    Lihat {{ strtoupper($ext) }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        
    </div>

</div>

</div>
@endsection
