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
        <div class="skel-recap-chart">
            <div class="skel skel-chart-title"></div>
            <div class="skel skel-chart-area"></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- ACTUAL CONTENT                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="content-wrap" id="actual-content" style="display: none;">

@section('page_title', 'Diagram Rekap')
@section('page_subtitle', 'internal.ptskk.id')

<div class="page-head fade-up" style="animation-delay: 0.1s; margin-bottom: 2.5rem;">
    <div>
        <h1 style="margin: 0; font-size: 2rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.rekapan_tiket_tahunan') }}</h1>
    </div>
</div>

<div class="glass-panel fade-up" style="animation-delay: 0.15s; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0; font-size: 1.25rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.rekap_tiket_bulanan') }}</h3>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <form action="{{ route('support.recap.diagram') }}" method="GET" id="yearFilterForm" style="margin: 0;">
                <select name="year" onchange="document.getElementById('yearFilterForm').submit()" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--line); font-family: var(--font-body); font-weight: 500; color: var(--ink); background: var(--paper-raised); cursor: pointer; outline:none;">
                    @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{!! __('messages.tahun_opt', ['year' => $y]) !!}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <div style="overflow-x: auto; margin-bottom: 2rem;">
        <div style="height: 320px; min-width: 700px;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    @php
        $totalTickets = array_sum($chartData);
    @endphp
    <div style="background: var(--paper-sunken); border-radius: 8px; padding: 1rem 1.5rem; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">
        {!! __('messages.menampilkan_rekap', ['year' => $year, 'total' => $totalTickets]) !!}
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const skeleton = document.getElementById('skeleton-loading');
        const content  = document.getElementById('actual-content');

        setTimeout(function () {
            skeleton.style.display = 'none';
            content.style.display = 'block';
            content.classList.add('loaded');

            // Render chart after content is visible
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const chartData = @json($chartData);
            const totalTickets = chartData.reduce((a, b) => a + b, 0);
            const baseLabels = ["{{ __('messages.jan') }}", "{{ __('messages.feb') }}", "{{ __('messages.mar') }}", "{{ __('messages.apr') }}", "{{ __('messages.may') }}", "{{ __('messages.jun') }}", "{{ __('messages.jul') }}", "{{ __('messages.aug') }}", "{{ __('messages.sep') }}", "{{ __('messages.oct') }}", "{{ __('messages.nov') }}", "{{ __('messages.dec') }}"];
            
            const multiLabels = baseLabels.map((month, index) => {
                const val = chartData[index];
                const percent = totalTickets > 0 ? Math.round((val / totalTickets) * 100) : 0;
                return [month, percent + "%"];
            });

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: multiLabels,
                    datasets: [{
                        label: "{{ __('messages.jumlah_tiket') }}",
                        data: chartData,
                        backgroundColor: document.documentElement.classList.contains('dark-mode') ? '#3A3A40' : '#e2e8f0',
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { display: false, beginAtZero: true },
                        x: { 
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8', autoSkip: false },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 6,
                            displayColors: false
                        }
                    }
                }
            });
        }, 400);
    });
</script>
</div>
@endsection
