@extends('layouts.app')

@section('content')

<div class="pelapor-panel active">

{{-- ═══════════════════════════════════════════ --}}
{{-- SKELETON LOADING STATE                      --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="skeleton-wrap" id="skeleton-loading">
    <div class="skel-recap-header">
        <div>
            <div class="skel" style="height:20px; width:260px; margin-bottom:8px;"></div>
            <div class="skel" style="height:12px; width:300px;"></div>
        </div>
        <div class="skel" style="height:38px; width:150px; border-radius:8px;"></div>
    </div>

    <div class="skel-recap-grid" style="grid-template-columns: 1fr;">
        <div class="skel-recap-chart" style="height: 350px;">
            <div class="skel skel-chart-title"></div>
            <div class="skel skel-chart-area"></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- ACTUAL CONTENT                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="content-wrap" id="actual-content" style="display: none;">

@section('page_title', __('messages.title_history_pic'))
@section('page_subtitle', 'internal.ptskk.id')

<div class="page-head fade-up" style="animation-delay: 0.1s; margin-bottom: 2rem;">
    <div>
        <h1 style="margin: 0; font-size: 2rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.title_history_pic') }}</h1>
    </div>
</div>

{{-- FILTER CARD --}}
<div class="glass-panel fade-up" style="animation-delay: 0.15s; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
    <form action="{{ route('support.recap.history-pic') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin: 0;">
        <div style="flex: 1; min-width: 220px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_ticket_placeholder') }}" style="width: 100%; padding: 9px 14px; border-radius: 8px; border: 1px solid var(--line); font-size: 0.88rem; outline: none; background: var(--paper-sunken); color: var(--ink);">
        </div>
        <div style="min-width: 160px;">
            <select name="status" style="width: 100%; padding: 9px 14px; border-radius: 8px; border: 1px solid var(--line); font-size: 0.88rem; outline: none; background: var(--paper-sunken); color: var(--ink); cursor: pointer;">
                <option value="">{{ __('messages.all_statuses') }}</option>
                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Proses</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>Done ({{ __('messages.done_status') }})</option>
            </select>
        </div>
        <div style="min-width: 180px;">
            <select name="pic_id" class="searchable-select" style="width: 100%; padding: 9px 14px; border-radius: 8px; border: 1px solid var(--line); font-size: 0.88rem; outline: none; background: var(--paper-sunken); color: var(--ink); cursor: pointer;">
                <option value="">{{ __('messages.all_pic_support') }}</option>
                @foreach($supportUsers as $sup)
                    <option value="{{ $sup->user_id }}" {{ request('pic_id') == $sup->user_id ? 'selected' : '' }}>{{ $sup->nama }}</option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="padding: 9px 18px; font-weight: 600; font-size: 0.88rem; border-radius: 8px;">{{ __('messages.btn_search') }}</button>
            @if(request()->anyFilled(['search', 'status', 'pic_id']))
                <a href="{{ route('support.recap.history-pic') }}" class="btn" style="padding: 9px 14px; background: #e2e8f0; color: #475569; font-weight: 600; font-size: 0.88rem; border-radius: 8px; text-decoration: none;">{{ __('messages.btn_reset') }}</a>
            @endif
        </div>
    </form>
</div>

{{-- TABLE CARD --}}
<div class="glass-panel fade-up" style="animation-delay: 0.2s; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 15px;">
        <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.history_update_ticket_pic') }}</h3>
        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">{{ __('messages.total_tickets', ['count' => $tickets->total()]) }}</span>
    </div>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
            <thead style="background: var(--paper-sunken);">
                <tr>
                    <th style="padding: 1rem 1.25rem; text-align: left; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.col_ticket_koperasi') }}</th>
                    <th style="padding: 1rem 1.25rem; text-align: left; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.col_pic_updater') }}</th>
                    <th style="padding: 1rem 1.25rem; text-align: center; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.col_last_update') }}</th>
                    <th style="padding: 1rem 1.25rem; text-align: center; font-size: 0.75rem; color: var(--ink); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.col_detail') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $tkt)
                    @php
                        $stVal = is_object($tkt->status) ? $tkt->status->value : $tkt->status;
                        $stLower = strtolower($stVal);
                        $badgeBg = '#e2e8f0'; $badgeClr = '#475569';
                        if ($stLower === 'open') { $badgeBg = '#f1f5f9'; $badgeClr = '#475569'; }
                        elseif ($stLower === 'proses') { $badgeBg = '#dbeafe'; $badgeClr = '#1d4ed8'; }
                        elseif ($stLower === 'pending') { $badgeBg = '#fef08a'; $badgeClr = '#854d0e'; }
                        elseif ($stLower === 'done') { $badgeBg = '#dcfce3'; $badgeClr = '#166534'; }
                    @endphp
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                            <div style="font-weight: 700; color: #2563eb; font-size: 0.9rem; font-family: 'JetBrains Mono', monospace;">{{ $tkt->ticket_id }}</div>
                            <div style="font-size: 0.85rem; color: var(--ink); font-weight: 600; margin-top: 2px;">{{ $tkt->pelapor->instansi->nama_instansi ?? __('messages.koperasi') ?? 'Koperasi' }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">{{ __('messages.reporter') ?? 'Pelapor' }}: {{ $tkt->pelapor->nama ?? '-' }}</div>
                        </td>
                        <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                            @if($tkt->picSupport)
                                <div>
                                    <div style="font-weight: 600; font-size: 0.88rem; color: var(--ink);">{{ $tkt->picSupport->nama }}</div>
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.25rem; text-align: center; vertical-align: middle; font-size: 0.82rem; color: var(--text-muted);">
                            {{ $tkt->updated_at ? $tkt->updated_at->format('d M Y - H:i') : '-' }}
                        </td>
                        <td style="padding: 1rem 1.25rem; text-align: center; vertical-align: middle;">
                            <button type="button" 
                                    class="btn-detail-log"
                                    data-ticket-id="{{ $tkt->ticket_id }}"
                                    data-instansi="{{ $tkt->pelapor->instansi->nama_instansi ?? 'Koperasi' }}"
                                    data-pelapor="{{ $tkt->pelapor->nama ?? '-' }}"
                                    data-pic="{{ $tkt->picSupport->nama ?? 'Sistem' }}"
                                    data-time="{{ $tkt->updated_at ? $tkt->updated_at->format('d M Y - H:i') : '-' }}"
                                    data-status="{{ $stVal }}"
                                    data-kategori="{{ $tkt->kategori->nama_kategori ?? '-' }}"
                                    data-permasalahan="{{ $tkt->permasalahan ?? '-' }}"
                                    data-solusi="{{ $tkt->penyelesaian ?? '' }}"
                                    data-pencegahan="{{ $tkt->pencegahan ?? '' }}"
                                    onclick="openLogModalFromEl(this)"
                                    style="background: #2563eb; color: #fff; border: none; padding: 7px 16px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer;">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            {{ __('messages.empty_history_update_ticket') }}
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

{{-- MODAL DETAIL LOG TIKET --}}
<div id="modalDetailLog" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); justify-content: center; align-items: center; padding: 20px;">
    <div style="background: var(--paper-raised); border-radius: 14px; border: 1px solid var(--line); width: 100%; max-width: 580px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        
        {{-- MODAL HEADER --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--line); background: var(--paper-raised); flex-wrap: wrap; gap: 15px;">
            <div>
                <h4 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--ink); font-family: 'Poppins', sans-serif;" id="modalLogTitle">{{ __('messages.modal_log_title') }}</h4>
                <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 2px;" id="modalLogSubtitle">{{ __('messages.koperasi') ?? 'Koperasi' }}</div>
            </div>
            <button type="button" onclick="closeLogModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: var(--text-muted); line-height: 1;">&times;</button>
        </div>

        {{-- MODAL BODY --}}
        <div style="padding: 1.5rem; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 1.1rem;">
            
            {{-- PETUGAS & WAKTU --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: var(--paper-sunken); border-radius: 10px; padding: 1rem; border: 1px solid var(--line);">
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.modal_log_pic') }}</div>
                    <div style="font-size: 0.92rem; font-weight: 700; color: var(--brand-primary); margin-top: 3px;" id="modalLogPic">-</div>
                </div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.modal_log_time') }}</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: var(--ink); margin-top: 3px;" id="modalLogTime">-</div>
                </div>
            </div>

            {{-- STATUS & KATEGORI --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div style="background: var(--paper-sunken); border-radius: 10px; padding: 0.85rem 1rem; border: 1px solid var(--line);">
                    <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.modal_log_status') }}</div>
                    <div style="margin-top: 4px;" id="modalLogStatusBadge">-</div>
                </div>
                <div style="background: var(--paper-sunken); border-radius: 10px; padding: 0.85rem 1rem; border: 1px solid var(--line);">
                    <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">{{ __('messages.modal_log_category') }}</div>
                    <div style="font-size: 0.88rem; font-weight: 600; color: var(--ink); margin-top: 4px;" id="modalLogKategori">-</div>
                </div>
            </div>

            {{-- PERMASALAHAN --}}
            <div style="background: var(--paper-sunken); border-radius: 10px; padding: 1rem; border: 1px solid var(--line);">
                <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px;">{{ __('messages.modal_log_problem') }}</div>
                <div style="font-size: 0.88rem; color: var(--ink); line-height: 1.5; white-space: pre-line;" id="modalLogPermasalahan">-</div>
            </div>

            {{-- TERAKHIR DI EDIT --}}
            <div style="background: var(--brand-primary-soft); border-radius: 10px; padding: 1rem; border: 1px solid var(--line);">
                <div style="font-size: 0.72rem; color: var(--brand-primary); font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px;">{{ __('messages.modal_log_last_edit') }}</div>
                <div style="display: flex; flex-direction: column; gap: 8px;" id="modalLogEditList">
                    <div style="font-size: 0.88rem; color: var(--ink); line-height: 1.5;" id="modalLogSolusi">-</div>
                </div>
            </div>

        </div>

        {{-- MODAL FOOTER --}}
        <div style="padding: 1rem 1.5rem; background: var(--paper-sunken); border-top: 1px solid var(--line); text-align: right;">
            <button type="button" onclick="closeLogModal()" style="background: #475569; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">{{ __('messages.btn_tutup') }}</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const skeleton = document.getElementById('skeleton-loading');
        const content  = document.getElementById('actual-content');

        setTimeout(function () {
            skeleton.style.display = 'none';
            content.style.display = 'block';
            content.classList.add('loaded');
        }, 350);
    });

    function openLogModalFromEl(el) {
        const ticketId = el.getAttribute('data-ticket-id');
        const instansi = el.getAttribute('data-instansi');
        const pelapor = el.getAttribute('data-pelapor');
        const pic = el.getAttribute('data-pic');
        const time = el.getAttribute('data-time');
        const status = el.getAttribute('data-status');
        const kategori = el.getAttribute('data-kategori');
        const permasalahan = el.getAttribute('data-permasalahan');
        const solusi = el.getAttribute('data-solusi');
        const pencegahan = el.getAttribute('data-pencegahan');

        document.getElementById('modalLogTitle').innerText = '{{ __('messages.modal_log_title') }} ' + ticketId;
        document.getElementById('modalLogSubtitle').innerText = instansi + ' ({{ __('messages.reporter') }}: ' + pelapor + ')';
        document.getElementById('modalLogPic').innerText = pic;
        document.getElementById('modalLogTime').innerText = time;
        document.getElementById('modalLogKategori').innerText = kategori;
        document.getElementById('modalLogPermasalahan').innerText = permasalahan || '-';

        // Status Badge
        let stLower = (status || '').toLowerCase();
        let badgeBg = '#e2e8f0', badgeClr = '#475569';
        if (stLower === 'open') { badgeBg = '#f1f5f9'; badgeClr = '#475569'; }
        else if (stLower === 'proses') { badgeBg = '#dbeafe'; badgeClr = '#1d4ed8'; }
        else if (stLower === 'pending') { badgeBg = '#fef08a'; badgeClr = '#854d0e'; }
        else if (stLower === 'done') { badgeBg = '#dcfce3'; badgeClr = '#166534'; }

        document.getElementById('modalLogStatusBadge').innerHTML = `<span style="background: ${badgeBg}; color: ${badgeClr}; padding: 3px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-block;">${status}</span>`;

        // Render TERAKHIR DI EDIT items
        let editListHtml = `
            <div style="font-size: 0.88rem; color: var(--ink); line-height: 1.5;">
                <strong style="color: var(--ink);">{{ __('messages.solusi_penyelesaian') ?? 'Solusi / Penyelesaian:' }}</strong> ${solusi || '{{ __('messages.belum_ada_solusi') ?? 'Belum ada solusi diinput oleh PIC Support.' }}'}
            </div>
        `;

        if (pencegahan && pencegahan.trim() !== '') {
            editListHtml += `
                <div style="font-size: 0.88rem; color: var(--ink); line-height: 1.5; margin-top: 4px;">
                    <strong style="color: var(--ink);">{{ __('messages.tindakan_pencegahan') ?? 'Tindakan Pencegahan:' }}</strong> ${pencegahan}
                </div>
            `;
        }

        document.getElementById('modalLogEditList').innerHTML = editListHtml;

        document.getElementById('modalDetailLog').style.display = 'flex';
    }

    function closeLogModal() {
        document.getElementById('modalDetailLog').style.display = 'none';
    }
</script>
</div>
@endsection
