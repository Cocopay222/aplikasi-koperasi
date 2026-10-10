@extends('layouts.app')

@section('page_title', __('messages.laporan_detail_support_title'))
@section('page_subtitle', 'internal.ptskk.id')

@section('content')
<div class="pelapor-panel">
    {{-- SKELETON LOADING STATE --}}
    <div class="skeleton-wrap" id="skeleton-loading">
        <div class="skel-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; max-width: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 15px;">
                <div>
                    <div class="skel" style="width: 250px; height: 28px; border-radius: 6px; margin-bottom: 12px;"></div>
                    <div class="skel" style="width: 450px; height: 14px; border-radius: 4px;"></div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <div class="skel" style="width: 100px; height: 32px; border-radius: 20px;"></div>
                    <div class="skel" style="width: 100px; height: 32px; border-radius: 20px;"></div>
                    <div class="skel" style="width: 120px; height: 32px; border-radius: 20px;"></div>
                </div>
            </div>
            
            <div class="skel" style="width: 100%; height: 400px; border-radius: 8px; margin-bottom: 1.5rem;"></div>
            
            <div>
                <div class="skel" style="width: 180px; height: 32px; border-radius: 6px;"></div>
            </div>
        </div>
    </div>

    {{-- ACTUAL CONTENT --}}
    <div class="content-wrap" id="actual-content" style="display: none;">
        <div class="glass-panel fade-up" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; animation-delay: 0.1s;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.laporan_detail_support_bulan', ['month' => $monthName, 'year' => $year]) }}</h3>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center;">
                <form action="{{ route('support.recap.detail') }}" method="GET" id="filterForm" style="display: flex; gap: 0.5rem; margin: 0; flex-wrap: wrap;">
                    <select name="month" onchange="document.getElementById('filterForm').submit()" style="padding: 6px 12px; border-radius: 20px; border: 1px solid var(--line); font-family: var(--font-body); font-weight: 500; font-size: 0.85rem; color: var(--ink); background: var(--paper-raised); cursor: pointer; outline:none;">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ __('messages.month_' . $m) }}</option>
                        @endforeach
                    </select>
                    <select name="year" onchange="document.getElementById('filterForm').submit()" style="padding: 6px 12px; border-radius: 20px; border: 1px solid var(--line); font-family: var(--font-body); font-weight: 500; font-size: 0.85rem; color: var(--ink); background: var(--paper-raised); cursor: pointer; outline:none;">
                        @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
                
                <div style="background: #1e3a8a; color: white; padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">
                    {{ __('messages.jumlah_data') }}: {{ count($tickets) }}
                </div>
            </div>
        </div>

        <div style="overflow-x: auto; border: 1px solid var(--line); border-radius: 8px;">
            <table id="recap-table" style="width: 100%; border-collapse: collapse; min-width: 1500px; font-size: 0.85rem;">
                <thead>
                    <tr style="background: #1e3a8a; color: white;">
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; text-transform: uppercase;">{{ __('messages.no') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.nama_koperasi') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.no_telepon') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.pic_koperasi') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.tanggal') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.permasalahan') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.penyelesaian') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.pencegahan') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.tanggal') }}</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; text-transform: uppercase;">{{ __('messages.kategori') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.pic') }}</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; text-transform: uppercase;">FAQ</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; text-transform: uppercase;">{{ __('messages.status_case') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; text-transform: uppercase;">{{ __('messages.link_ticket') }}</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; text-transform: uppercase;">{{ __('messages.ket') }}</th>
                    </tr>
                </thead>
                <tbody style="background: var(--paper-raised);">
                    @forelse($tickets as $index => $t)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 16px; text-align: center; color: var(--text-muted);">{{ $index + 1 }}</td>
                        <td style="padding: 12px 16px; color: var(--ink); font-weight: 500;">{{ $t->pelapor->instansi->nama_instansi ?? ($t->pelapor->nama ?? '-') }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted);">{{ $t->pelapor->instansi->no_telp ?? '-' }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted);">{{ $t->pelapor->nama ?? '-' }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted);">{{ $t->tanggal_input ? $t->tanggal_input->format('d-m-Y') : '-' }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted); max-width: 250px;">{{ $t->permasalahan ?? '-' }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted); max-width: 250px;">{{ $t->penyelesaian ?? '-' }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted); max-width: 200px;">{{ $t->pencegahan ?? '-' }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted);">{{ $t->tanggal_penyelesaian ? $t->tanggal_penyelesaian->format('d-m-Y') : '-' }}</td>
                        
                        <td style="padding: 12px 16px; text-align: center;">
                            @if($t->kategori)
                                <span style="background: rgba(217, 119, 108, 0.15); color: #c0564a; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; display: inline-block;">
                                    {{ $t->kategori->nama_kategori }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        
                        <td style="padding: 12px 16px; white-space: nowrap;">
                            @if($t->picSupport)
                                <span style="color: var(--text-muted);">{{ $t->picSupport->nama }}</span>
                            @else
                                -
                            @endif
                        </td>
                        
                        <td style="padding: 12px 16px; text-align: center; color: var(--text-muted);">
                            {{ $t->is_faq ? 'TRUE' : 'FALSE' }}
                        </td>
                        
                        <td style="padding: 12px 16px; text-align: center;">
                            @php
                                $statusVal = $t->status->value ?? $t->status;
                                $statusLower = strtolower($statusVal);
                                
                                if ($statusLower === 'done') {
                                    $bg = '#dcfce7'; $color = '#16a34a'; // Hijau
                                } elseif ($statusLower === 'pending') {
                                    $bg = '#fef9c3'; $color = '#ca8a04'; // Kuning
                                } elseif ($statusLower === 'open') {
                                    $bg = '#f1f5f9'; $color = '#475569'; // Abu-abu
                                } elseif ($statusLower === 'proses' || $statusLower === 'on progress') {
                                    $bg = '#fee2e2'; $color = '#dc2626'; // Merah
                                } else {
                                    $bg = 'var(--paper-sunken)'; $color = 'var(--text-muted)'; // Default abu-abu
                                }
                            @endphp
                            <span style="background: {{ $bg }}; color: {{ $color }}; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $color }};"></span> {{ $statusVal }}
                            </span>
                        </td>
                        
                        <td style="padding: 12px 16px;">
                            @if($t->link_ticket)
                                <a href="{{ $t->link_ticket }}" target="_blank" style="color: #3b82f6; text-decoration: none;">{{ $t->link_ticket }}</a>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        
                        <td style="padding: 12px 16px; text-align: center; color: var(--text-muted);">-</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            {{ __('messages.no_data_tickets') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <a href="{{ route('support.recap') }}" class="btn btn-ghost" style="padding: 8px 16px; font-size: 0.85rem;">&larr; {{ __('messages.kembali_ke_recap_support') }}</a>
            
            <button onclick="exportTableToExcel('recap-table', 'Laporan_Detail_Support_{{ $monthName }}_{{ $year }}')" class="btn btn-primary" style="background: #10b981; color: white; border: none; padding: 8px 16px; font-size: 0.85rem; border-radius: 6px; display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                </svg>
                Export Excel
            </button>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script>
        function exportTableToExcel(tableID, filename = ''){
            const workbook = new ExcelJS.Workbook();
            const sheet = workbook.addWorksheet("{{ strtoupper($monthName) }} {{ $year }}");

            // 1. Add Title & Subtitle
            sheet.mergeCells('A1:O1');
            const titleCell = sheet.getCell('A1');
            titleCell.value = "{{ __('messages.laporan_detail_support_bulan', ['month' => $monthName, 'year' => $year]) }}";
            titleCell.font = { bold: true, size: 12 };
            titleCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9EAD3' } }; // Light green background
            titleCell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };

            sheet.mergeCells('A2:O2');
            const subCell = sheet.getCell('A2');
            subCell.value = "{{ __('messages.jumlah_data') }}: {{ count($tickets) }}";
            subCell.font = { bold: true };
            subCell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };

            // 2. Add Main Table Headers
            const mainHeaders = ["{{ __('messages.no') }}", "{{ __('messages.nama_koperasi') }}", "{{ __('messages.no_telepon') }}", "{{ __('messages.pic_koperasi') }}", "{{ __('messages.tanggal') }}", "{{ __('messages.permasalahan') }}", "{{ __('messages.penyelesaian') }}", "{{ __('messages.pencegahan') }}", "{{ __('messages.tanggal') }}", "{{ __('messages.kategori') }}", "{{ __('messages.pic') }}", "FAQ", "{{ __('messages.status_case') }}", "{{ __('messages.link_ticket') }}", "{{ __('messages.ket') }}"];
            
            for(let i=1; i<=15; i++) {
                let cell = sheet.getRow(3).getCell(i);
                cell.value = mainHeaders[i-1];
                cell.font = { bold: true };
                cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEFEFEF' } };
                cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
            }

            // 3. Add Main Data
            var mainData = [];
            @foreach($tickets as $index => $t)
                @php
                    $statusVal = $t->status->value ?? $t->status;
                    $kategoriVal = $t->kategori ? $t->kategori->nama_kategori : '-';
                    $picVal = $t->picSupport ? $t->picSupport->nama : '-';
                    $isFaq = $t->is_faq ? 'TRUE' : 'FALSE';
                @endphp
                mainData.push([
                    "{{ $index + 1 }}",
                    "{!! addslashes(str_replace(["\r", "\n"], " ", $t->pelapor->instansi->nama_instansi ?? ($t->pelapor->nama ?? '-'))) !!}",
                    "{!! addslashes(str_replace(["\r", "\n"], " ", $t->pelapor->instansi->no_telp ?? '-')) !!}",
                    "{!! addslashes(str_replace(["\r", "\n"], " ", $t->pelapor->nama ?? '-')) !!}",
                    "{{ $t->tanggal_input ? $t->tanggal_input->format('d-m-Y') : '-' }}",
                    "{!! addslashes(str_replace(["\r", "\n"], " ", $t->permasalahan ?? '-')) !!}",
                    "{!! addslashes(str_replace(["\r", "\n"], " ", $t->penyelesaian ?? '-')) !!}",
                    "{!! addslashes(str_replace(["\r", "\n"], " ", $t->pencegahan ?? '-')) !!}",
                    "{{ $t->tanggal_penyelesaian ? $t->tanggal_penyelesaian->format('d-m-Y') : '-' }}",
                    "{{ $kategoriVal }}",
                    "{{ $picVal }}",
                    "{{ $isFaq }}",
                    "{{ $statusVal }}",
                    "{!! addslashes($t->link_ticket ?? '-') !!}",
                    "-"
                ]);
            @endforeach

            for(let i=0; i<mainData.length; i++) {
                let row = sheet.getRow(4+i);
                for(let c=0; c<15; c++) {
                    let cell = row.getCell(c+1);
                    cell.value = mainData[i][c];
                    cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
                    cell.alignment = { vertical: 'top', wrapText: true };
                }
            }

            // 4. Add Summary Table (Starting at column P = 16)
            @php
                $rekapKategori = [];
                foreach($tickets as $t) {
                    $kat = $t->kategori ? $t->kategori->nama_kategori : 'Belum ada Kategori';
                    $stat = strtolower($t->status->value ?? $t->status);
                    
                    if(!isset($rekapKategori[$kat])) {
                        $rekapKategori[$kat] = ['total' => 0, 'done' => 0, 'pending' => 0, 'proses' => 0];
                    }
                    
                    $rekapKategori[$kat]['total']++;
                    if($stat === 'done') $rekapKategori[$kat]['done']++;
                    elseif($stat === 'pending') $rekapKategori[$kat]['pending']++;
                    elseif(in_array($stat, ['proses', 'on progress', 'open'])) $rekapKategori[$kat]['proses']++;
                }
            @endphp

            var summaryData = [];
            var sumTotal = 0, sumDone = 0, sumPending = 0, sumProses = 0;
            @foreach($rekapKategori as $kat => $data)
                summaryData.push(["{!! addslashes($kat) !!}", {{ $data['total'] }}, {{ $data['done'] }}, {{ $data['pending'] }}, {{ $data['proses'] }}]);
                sumTotal += {{ $data['total'] }};
                sumDone += {{ $data['done'] }};
                sumPending += {{ $data['pending'] }};
                sumProses += {{ $data['proses'] }};
            @endforeach
            
            // Add summary headers at Q3..U3
            const sumHeaders = ["KATEGORI", "TOTAL", "DONE", "PENDING", "PROSES"];
            for(let c=0; c<5; c++) {
                let cell = sheet.getRow(3).getCell(17+c);
                cell.value = sumHeaders[c];
                cell.font = { bold: true };
                cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEFEFEF' } }; 
                cell.alignment = { horizontal: 'center' };
            }

            // Add summary data rows
            for(let i=0; i<summaryData.length; i++) {
                let row = sheet.getRow(4+i);
                for(let c=0; c<5; c++) {
                    let cell = row.getCell(17+c);
                    cell.value = summaryData[i][c];
                    cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
                }
            }
            
            // Add summary total row
            let totalRowIdx = 4 + summaryData.length;
            let totalRow = sheet.getRow(totalRowIdx);
            let totals = ["Total", sumTotal, sumDone, sumPending, sumProses];
            for(let c=0; c<5; c++) {
                let cell = totalRow.getCell(17+c);
                cell.value = totals[c];
                cell.font = { bold: true };
                cell.border = { top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'} };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9EAD3' } }; 
            }
            
            // 5. Column Widths Setup
            sheet.getColumn(1).width = 5;   // NO
            sheet.getColumn(2).width = 25;  // NAMA KOPERASI
            sheet.getColumn(3).width = 16;  // NO TELEPON
            sheet.getColumn(4).width = 20;  // PIC KOPERASI
            sheet.getColumn(5).width = 12;  // TANGGAL
            sheet.getColumn(6).width = 35;  // PERMASALAHAN
            sheet.getColumn(7).width = 35;  // PENYELESAIAN
            sheet.getColumn(8).width = 30;  // PENCEGAHAN
            sheet.getColumn(9).width = 12;  // TANGGAL
            sheet.getColumn(10).width = 15; // KATEGORI
            sheet.getColumn(11).width = 15; // PIC
            sheet.getColumn(12).width = 10; // FAQ
            sheet.getColumn(13).width = 15; // STATUS CASE
            sheet.getColumn(14).width = 25; // LINK TICKET
            sheet.getColumn(15).width = 10; // KET
            
            sheet.getColumn(17).width = 20; // Q (KATEGORI)
            sheet.getColumn(18).width = 10; // R
            sheet.getColumn(19).width = 10; // S
            sheet.getColumn(20).width = 10; // T
            sheet.getColumn(21).width = 10; // U

            // 6. Download the file
            workbook.xlsx.writeBuffer().then(function(buffer) {
                saveAs(new Blob([buffer], { type: "application/octet-stream" }), filename + '.xlsx');
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const skeleton = document.getElementById('skeleton-loading');
            const content  = document.getElementById('actual-content');
            if(skeleton && content) {
                setTimeout(function () {
                    skeleton.style.display = 'none';
                    content.style.display = 'block';
                    content.classList.add('loaded');
                }, 800);
            }
        });
    </script>
</div>
@endsection
