@extends('layouts.app')

@section('content')

<div class="pelapor-panel active">

<div class="content-wrap" id="actual-content">

@section('page_title', __('messages.recap_template_surat'))
@section('page_subtitle', 'internal.ptskk.id')

<div class="page-head fade-up" style="animation-delay: 0.1s; margin-bottom: 2rem;">
    <div>
        <h1 style="margin: 0; font-size: 2rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.recap_template_surat') }}</h1>
    </div>
</div>

{{-- KARTU RINGKASAN REKAP PER TEMPLATE SURAT --}}
<div class="fade-up" style="animation-delay: 0.12s; margin-bottom: 2rem;">
    <h3 style="margin: 0 0 1rem; font-size: 1.1rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.recap_template_summary_title') }}</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
        @forelse($templateStats as $tplName => $count)
            @php
                $cleanName = str_replace(['_', '-'], ' ', pathinfo($tplName, PATHINFO_FILENAME));
                $ext = strtoupper(pathinfo($tplName, PATHINFO_EXTENSION));
            @endphp
            <div style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div style="min-width: 0;">
                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $cleanName }}">
                        {{ $cleanName }}
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        File {{ $ext }}
                    </div>
                </div>
                <div style="text-align: right; flex-shrink: 0; margin-left: 10px;">
                    <span style="background: #dbeafe; color: #1d4ed8; padding: 4px 10px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; display: inline-block;">
                        {{ $count }} {{ __('messages.recap_template_surat_count') }}
                    </span>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; background: var(--paper-raised); border: 1px dashed var(--line); border-radius: 12px; padding: 1.5rem; text-align: center; color: var(--text-muted);">
                Belum ada file template surat di folder public/templates/.
            </div>
        @endforelse
    </div>
</div>

{{-- FILTER CARD --}}
<div class="glass-panel fade-up" style="animation-delay: 0.15s; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
    <form action="{{ route('support.recap.template-surat') }}" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin: 0;">
        <div style="flex-grow: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">{{ __('messages.recap_template_pilih_label') }}</label>
            <select name="template" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-sunken); color: var(--ink); font-family: var(--font-body); outline: none;">
                <option value="">Semua Template Surat</option>
                @foreach($allTemplates as $tpl)
                    <option value="{{ $tpl }}" {{ request('template') == $tpl ? 'selected' : '' }}>
                        {{ str_replace(['_', '-'], ' ', pathinfo($tpl, PATHINFO_FILENAME)) }}
                    </option>
                @endforeach
            </select>
        </div>
        @if(request('template'))
            <div style="margin-top: 18px;">
                <a href="{{ route('support.recap.template-surat') }}" class="btn btn-ghost btn-sm" style="border: 1px solid var(--line); text-decoration: none; font-size: 0.85rem;">
                    Reset Filter
                </a>
            </div>
        @endif
    </form>
</div>

{{-- TABLE CARD --}}
<div class="glass-panel fade-up" style="animation-delay: 0.2s; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 15px;">
        <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.riwayat_penggunaan_surat') }}</h3>
    </div>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead style="background: var(--paper-sunken);">
                <tr>
                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.recap_template_th_no') }}</th>
                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.recap_template_th_kop') }}</th>
                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.recap_template_th_tpl') }}</th>
                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.recap_template_th_pic') }}</th>
                    <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.recap_template_th_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 1rem 1.5rem; font-weight: 600; font-family: 'Inter', sans-serif;">
                            {{ $t->ticket_id }}
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 600; color: var(--ink);">{{ $t->pelapor->instansi->nama_instansi ?? '-' }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $t->pelapor->nama ?? 'User Pelapor' }}</div>
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            @php
                                $tplUrl = route('download.template', basename($t->template_laporan));
                            @endphp
                            <button type="button" onclick="openUniversalPreview('{{ $tplUrl }}', '{{ pathinfo($t->template_laporan, PATHINFO_EXTENSION) }}', '{{ addslashes(basename($t->template_laporan)) }}')" style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; border: 1px solid #bfdbfe; cursor: pointer;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                {{ str_replace(['_', '-'], ' ', pathinfo($t->template_laporan, PATHINFO_FILENAME)) }}
                            </button>
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 500; color: var(--ink);">{{ $t->picSupport->nama ?? '-' }}</div>
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                            {{ $t->updated_at ? $t->updated_at->format('d M Y - H:i') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            {{ __('messages.no_data_template_surat') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tickets->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--line);">
            {{ $tickets->appends(request()->query())->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>

</div>
</div>
@endsection
