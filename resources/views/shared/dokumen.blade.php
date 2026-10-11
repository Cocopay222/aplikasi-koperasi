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
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-start;">
                        @php $lampirans = is_array($ticket->lampiran) ? $ticket->lampiran : [$ticket->lampiran]; @endphp
                        @foreach($lampirans as $lamp)
                            @php $ext = strtolower(pathinfo($lamp, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <div style="border: 1.5px solid var(--line, #e2e8f0); border-radius: 10px; overflow: hidden; background: white; width: 140px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column;">
                                    <div style="position: relative; width: 140px; height: 100px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: pointer;" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')">
                                        <img src="{{ Storage::url($lamp) }}" alt="Lampiran" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; background: #f1f5f9; color: var(--text-muted);">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            <span style="font-size: 0.65rem; font-weight: 600; margin-top: 4px; color: #64748b; text-transform: uppercase;">{{ $ext }}</span>
                                        </div>
                                        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.35); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                            <span style="color: white; font-size: 0.75rem; font-weight: 600; background: rgba(0,0,0,0.5); padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                Lihat
                                            </span>
                                        </div>
                                    </div>
                                    <div style="padding: 6px 8px; border-top: 1px solid var(--line, #e2e8f0); background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 4px;">
                                        <span title="{{ basename($lamp) }}" style="font-size: 0.72rem; font-weight: 500; color: var(--ink, #1e293b); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 95px; display: block;">{{ preg_replace('/^\d+_/', '', basename($lamp)) }}</span>
                                        <a href="{{ Storage::url($lamp) }}" download="{{ basename($lamp) }}" target="_blank" style="color: var(--primary, #0284c7); display: inline-flex; align-items: center; padding: 2px;" title="Unduh">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div style="border: 1.5px solid var(--line, #e2e8f0); border-radius: 10px; background: white; padding: 8px 12px; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                                    <div style="width: 36px; height: 36px; border-radius: 8px; background: {{ $ext === 'pdf' ? '#fee2e2' : ($ext === 'mp4' ? '#ede9fe' : (in_array($ext, ['doc','docx']) ? '#e0f2fe' : '#f1f5f9')) }}; display: flex; align-items: center; justify-content: center; color: {{ $ext === 'pdf' ? '#dc2626' : ($ext === 'mp4' ? '#7c3aed' : (in_array($ext, ['doc','docx']) ? '#0284c7' : '#475569')) }}; font-weight: 700; font-size: 0.75rem;">
                                        {{ strtoupper($ext ?: 'FILE') }}
                                    </div>
                                    <div style="display: flex; flex-direction: column;">
                                        <span title="{{ basename($lamp) }}" style="font-size: 0.8rem; font-weight: 600; color: var(--ink, #1e293b); max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ preg_replace('/^\d+_/', '', basename($lamp)) }}</span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted, #64748b);">Dokumen</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 4px; margin-left: 6px;">
                                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost btn-sm" style="padding: 4px 8px; font-size: 0.75rem; border: 1px solid var(--line, #cbd5e1); border-radius: 6px; cursor: pointer;">
                                            Buka
                                        </button>
                                        <a href="{{ Storage::url($lamp) }}" download="{{ basename($lamp) }}" target="_blank" class="btn btn-ghost btn-sm" style="padding: 4px 8px; font-size: 0.75rem; border: 1px solid var(--line, #cbd5e1); border-radius: 6px; color: var(--ink); text-decoration: none;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        </a>
                                    </div>
                                </div>
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
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-start;">
                        @php $lampiranSupports = is_array($ticket->lampiran_support) ? $ticket->lampiran_support : [$ticket->lampiran_support]; @endphp
                        @foreach($lampiranSupports as $lamp)
                            @php $ext = strtolower(pathinfo($lamp, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <div style="border: 1.5px solid #f59e0b; border-radius: 10px; overflow: hidden; background: white; width: 140px; box-shadow: 0 2px 6px rgba(245,158,11,0.08); transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column;">
                                    <div style="position: relative; width: 140px; height: 100px; background: #fffbeb; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: pointer;" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')">
                                        <img src="{{ Storage::url($lamp) }}" alt="Lampiran Support" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; background: #fef3c7; color: #b45309;">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            <span style="font-size: 0.65rem; font-weight: 600; margin-top: 4px; color: #b45309; text-transform: uppercase;">{{ $ext }}</span>
                                        </div>
                                        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.35); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                            <span style="color: white; font-size: 0.75rem; font-weight: 600; background: rgba(0,0,0,0.5); padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                Lihat
                                            </span>
                                        </div>
                                    </div>
                                    <div style="padding: 6px 8px; border-top: 1px solid #fde68a; background: #fffbeb; display: flex; align-items: center; justify-content: space-between; gap: 4px;">
                                        <span title="{{ basename($lamp) }}" style="font-size: 0.72rem; font-weight: 500; color: #92400e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 95px; display: block;">{{ preg_replace('/^\d+_/', '', basename($lamp)) }}</span>
                                        <a href="{{ Storage::url($lamp) }}" download="{{ basename($lamp) }}" target="_blank" style="color: #b45309; display: inline-flex; align-items: center; padding: 2px;" title="Unduh">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div style="border: 1.5px solid #fde68a; border-radius: 10px; background: #fffbeb; padding: 8px 12px; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 2px 6px rgba(245,158,11,0.06);">
                                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #b45309; font-weight: 700; font-size: 0.75rem;">
                                        {{ strtoupper($ext ?: 'FILE') }}
                                    </div>
                                    <div style="display: flex; flex-direction: column;">
                                        <span title="{{ basename($lamp) }}" style="font-size: 0.8rem; font-weight: 600; color: #92400e; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ preg_replace('/^\d+_/', '', basename($lamp)) }}</span>
                                        <span style="font-size: 0.7rem; color: #b45309;">Respons Support</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 4px; margin-left: 6px;">
                                        <button type="button" onclick="openUniversalPreview('{{ Storage::url($lamp) }}', '{{ $ext }}', '{{ addslashes(basename($lamp)) }}')" class="btn btn-ghost btn-sm" style="padding: 4px 8px; font-size: 0.75rem; border: 1px solid #fcd34d; border-radius: 6px; cursor: pointer; color: #b45309;">
                                            Buka
                                        </button>
                                        <a href="{{ Storage::url($lamp) }}" download="{{ basename($lamp) }}" target="_blank" class="btn btn-ghost btn-sm" style="padding: 4px 8px; font-size: 0.75rem; border: 1px solid #fcd34d; border-radius: 6px; color: #b45309; text-decoration: none;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        </a>
                                    </div>
                                </div>
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
