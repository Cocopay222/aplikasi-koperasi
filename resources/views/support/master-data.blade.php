@extends('layouts.app')

@section('page_title', 'Master Data')
@section('page_subtitle', 'internal.ptskk.id')

@section('content')
<style>
    /* CSS Kustom untuk Layout Master Data */
    .md-layout {
        display: flex;
        align-items: flex-start;
        gap: 2rem;
    }
    .md-sidebar {
        width: 250px;
        flex-shrink: 0;
        background: var(--paper-raised);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .md-tab-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 8px;
        background: transparent;
        border: none;
        color: var(--ink-soft);
        font-family: var(--font-body);
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
    }
    .md-tab-btn:hover {
        background: var(--paper-sunken);
        color: var(--ink);
    }
    .md-tab-btn.active {
        background: var(--brand-primary-soft);
        color: var(--brand-primary);
        position: relative;
    }
    .md-tab-btn.active::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 50%;
        background: var(--brand-primary);
        border-radius: 0 4px 4px 0;
    }
    .md-tab-btn svg {
        width: 18px;
        height: 18px;
        opacity: 0.7;
    }
    .md-tab-btn.active svg {
        opacity: 1;
    }
    
    .md-content-area {
        flex: 1;
        min-width: 0; /* Mencegah blowout */
    }
    .md-tab-pane {
        display: none;
        animation: fadeIn 0.3s ease forwards;
    }
    .md-tab-pane.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* MOBILE RESPONSIVE LAYOUT */
        /* Custom scrollbar horizontal selalu tampil (berlaku di Webkit) */
        .glass-panel > div[style*="overflow: auto"]::-webkit-scrollbar {
            height: 8px;
        }
        .glass-panel > div[style*="overflow: auto"]::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
            margin: 0 12px;
        }
        .glass-panel > div[style*="overflow: auto"]::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .glass-panel > div[style*="overflow: auto"]::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

    @media (max-width: 768px) {
        .md-layout {
            flex-direction: column;
            gap: 1rem;
        }
        /* Ubah Sidebar jadi Tab Horizontal Scroll */
        .md-sidebar {
            width: 100%;
            flex-direction: row;
            overflow-x: auto;
            border: none;
            background: transparent;
            padding: 0 0 8px 0;
            -webkit-overflow-scrolling: touch;
            display: flex;
            align-items: center;
            gap: 8px;
            box-sizing: border-box;
            max-width: 100%;
            min-width: 0;
        }
        .md-sidebar::-webkit-scrollbar {
            display: none; /* Sembunyikan scrollbar agar lebih rapi seperti di aplikasi asli */
        }
        .md-tab-btn {
            border: 1px solid var(--line);
            border-radius: 100px;
            padding: 8px 16px;
            white-space: nowrap;
            background: var(--paper-raised);
            font-size: 0.85rem;
            color: var(--ink-soft);
            flex-shrink: 0;
        }
        .md-tab-btn.active {
            background: var(--brand-primary-soft);
            color: var(--brand-primary);
            border-color: var(--brand-primary-soft);
        }
        .md-tab-btn.active::before {
            display: none;
        }
        
        /* Tombol Export dipindahkan dan diatur style nya */
        .md-sidebar > div[style*="border-top"] {
            border-top: none !important;
            padding-top: 0 !important;
            margin-top: 0 !important;
            margin-left: auto; /* Jika ingin didorong ke kanan? Tidak perlu karena di mobile posisinya berbeda. Wait, I will use javascript to move the export button out of sidebar, or just style it differently? */
            /* Sebenarnya kita bisa pindahkan Tombol Export dengan flex order? Tidak, kita akan buat tampilannya menyesuaikan nanti */
        }
        

        
        /* Header Card "Aplikasi" dsb */
        .glass-panel > div:first-child {
            border-bottom: none !important;
            padding: 16px 12px 12px 12px !important;
        }
        
        /* Sembunyikan eyebrow/deskripsi header halaman agar ringkas (opsional, ikuti screenshot) */
        .page-head { margin-bottom: 1rem !important; }
        .page-head h1 { font-size: 1.5rem !important; }
        
        .desktop-export { display: none !important; }
        .mobile-export { display: block !important; padding: 0 12px 12px 12px; }
    }
</style>

<div class="pelapor-panel active">
    
    {{-- ═══════════════════════════════════════════ --}}
    {{-- SKELETON LOADING STATE                      --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="skeleton-wrap" id="skeleton-loading" style="max-width: 1100px; margin: 0 auto;">
        <div class="skel-page-head" style="margin-bottom: 2.5rem;">
            <div>
                <div class="skel skel-page-head-eyebrow"></div>
                <div class="skel skel-page-head-title"></div>
                <div class="skel skel-panel-sub" style="margin-top: 0.5rem; width: 600px; height: 14px;"></div>
            </div>
        </div>
        
        <div class="md-layout">
            <div class="md-sidebar skel" style="height: 300px; border: none;"></div>
            <div class="md-content-area skel" style="height: 500px; border-radius: 12px; border: none;"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- ACTUAL CONTENT                              --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="content-wrap" id="actual-content" style="display: none; max-width: 1100px; margin: 0 auto;">
        @if(session('success'))
            <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; align-items: center; gap: 10px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 500; font-size: 0.9rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div id="error-alert" class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; align-items: center; gap: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 500; font-size: 0.9rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="page-head fade-up" style="animation-delay: 0.1s; margin-bottom: 2.5rem;">
            <div>
                <p class="eyebrow" style="text-transform: uppercase; letter-spacing: 2px; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem; font-weight: 600; font-family: 'Inter', sans-serif;">{{ __('messages.d2_master_data') }}</p>
                <h1 style="margin: 0; font-size: 2rem; color: var(--ink); font-family: 'Poppins', sans-serif; font-weight: 700;">{{ __('messages.kelola_master_data') }}</h1>
            </div>
        </div>

        <div class="md-layout fade-up" style="animation-delay: 0.15s;">
            <!-- SUB-SIDEBAR -->
            <div class="md-sidebar">
                <button class="md-tab-btn active" onclick="switchMdTab('aplikasi', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    {{ __('messages.aplikasi') }}
                </button>
                <button class="md-tab-btn" onclick="switchMdTab('kategori', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                    {{ __('messages.kategori') }}
                </button>
                <button class="md-tab-btn" onclick="switchMdTab('koperasi', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    {{ __('messages.nama_koperasi') }}
                </button>
                <button class="md-tab-btn" onclick="switchMdTab('picsupport', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    {{ __('messages.pic_tim_support') }}
                </button>
                <button class="md-tab-btn" onclick="switchMdTab('status', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    {{ __('messages.status_tiket') }}
                </button>
                <button class="md-tab-btn" onclick="switchMdTab('faq', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    FAQ
                </button>

                <div class="desktop-export" style="margin-top: 1rem; border-top: 1px solid var(--line); padding-top: 1rem;">
                    <a href="{{ url('/support/master-data/export') }}" class="btn" style="display: flex; justify-content: center; align-items: center; gap: 8px; padding: 0.8rem; border-radius: 8px; font-weight: 600; text-decoration: none; background-color: #10b981; color: white; border: 1px solid #10b981; transition: opacity 0.2s; width: 100%;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <!-- MAIN CONTENT AREA -->
            <div class="md-content-area">
                
                <!-- TAB 1: APLIKASI -->
                <div id="tab-aplikasi" class="md-tab-pane active">
                    <div class="glass-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.master_aplikasi') }}</h3>
                            </div>
                            <button type="button" onclick="openModal('modal-add-aplikasi')" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--line); border-radius: 6px; background: transparent; color: var(--ink); white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">+ {{ __('messages.tambah') }}</button>
                        </div>
                        <div class="table-scroll-wrapper" style="overflow: auto; border: none; border-radius: 0; background: transparent; margin: 0; padding-bottom: 0;">
                            <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                                <thead style="background: var(--paper-sunken); position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.nama_aplikasi') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.deskripsi') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">EBOOK</th>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.link_aplikasi') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: right; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.status') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.aksi') }}</th>
                                    </tr>
                                </thead>
                            <tbody>
                                @foreach($aplikasis as $app)
                                <tr style="border-bottom: 1px solid var(--line);">
                                    <td style="padding: 1.25rem 1.5rem; color: var(--ink); font-size: 0.95rem; font-weight: 500;">{{ $app->nama_aplikasi }}</td>
                                    <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.95rem; max-width: 400px; line-height: 1.6;">
                                        @if(strlen($app->deskripsi) > 60)
                                            <span id="desc-short-{{ $app->aplikasi_id }}">
                                                {{ Str::limit($app->deskripsi, 60) }}
                                                <a href="javascript:void(0)" onclick="toggleDesc({{ $app->aplikasi_id }})" style="color: var(--indigo); font-weight: 600; font-size: 0.85rem; text-decoration: none; margin-left: 4px;">{{ __('messages.lebih_banyak') }}</a>
                                            </span>
                                            <span id="desc-full-{{ $app->aplikasi_id }}" style="display: none;">
                                                {{ $app->deskripsi }}
                                                <a href="javascript:void(0)" onclick="toggleDesc({{ $app->aplikasi_id }})" style="color: var(--indigo); font-weight: 600; font-size: 0.85rem; text-decoration: none; margin-left: 4px;">{{ __('messages.lebih_sedikit') }}</a>
                                            </span>
                                        @else
                                            {{ $app->deskripsi }}
                                        @endif
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; font-size: 0.95rem;">
                                        @if(is_array($app->ebook) && count($app->ebook) > 0)
                                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                                @foreach($app->ebook as $index => $path)
                                                    <a href="{{ asset('storage/' . $path) }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem;" title="Download {{ preg_replace('/^\d+_/', '', basename($path)) }}">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                        {{ Str::limit(preg_replace('/^\d+_/', '', basename($path)), 25) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="color: var(--text-muted); font-style: italic; font-size: 0.85rem;">{{ __('messages.no_file') }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; font-size: 0.95rem;">
                                        @if($app->link)
                                            <a href="{{ $app->link }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                                {{ __('messages.btn_open') }}
                                            </a>
                                        @else
                                            <span style="color: var(--text-muted); font-style: italic;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: right;">
                                        <span class="badge" style="background: {{ $app->is_active ? 'rgba(34, 197, 94, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $app->is_active ? '#16a34a' : '#ef4444' }}; padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                            {{ $app->is_active ? __('messages.aktif') : __('messages.nonaktif') }}
                                        </span>
                                    </td>
                                    <td class="td-action-cell" style="padding: 1.25rem 1.5rem; text-align: center; position: relative;">
                                        <div style="position: relative; display: inline-block;">
                                            <button type="button" onclick="toggleMdDropdown(event, 'dropdown-app-{{ $app->aplikasi_id }}')" style="background: var(--paper-raised); border: 1.5px solid var(--line); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; color: var(--ink); display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--border-hover)'; this.style.background='var(--paper-sunken)'" onmouseout="this.style.borderColor='var(--line)'; this.style.background='var(--paper-raised)'" title="Aksi">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="12" cy="5" r="1.5"></circle><circle cx="12" cy="19" r="1.5"></circle></svg>
                                            </button>
                                            <div id="dropdown-app-{{ $app->aplikasi_id }}" class="md-dropdown-menu" style="display: none; position: absolute; right: 0; top: calc(100% + 4px); background: var(--paper-raised); border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); min-width: 140px; z-index: 100; text-align: left; padding: 6px; backdrop-filter: blur(8px);">
                                                <button type="button" onclick="openModal('modal-edit-aplikasi-{{ $app->aplikasi_id }}'); closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: var(--ink); cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='var(--paper-sunken)'" onmouseout="this.style.background='none'">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    {{ __('messages.edit_master_aplikasi') }}
                                                </button>
                                                <form action="{{ url('/support/master-data/aplikasi/' . $app->aplikasi_id) }}" method="POST" onsubmit="return confirm('{{ __('messages.konfirmasi_hapus_aplikasi') }}');" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                        {{ __('messages.hapus') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: KATEGORI -->
                <div id="tab-kategori" class="md-tab-pane">
                    <div class="glass-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.master_kategori') }}</h3>
                            </div>
                            <button type="button" onclick="openModal('modal-add-kategori')" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--line); border-radius: 6px; background: transparent; color: var(--ink); white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">+ {{ __('messages.tambah') }}</button>
                        </div>
                        <div class="table-scroll-wrapper" style="overflow: auto; border: none; border-radius: 0; background: transparent; margin: 0; padding-bottom: 0;">
                        <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                            <thead style="background: var(--paper-sunken); position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.nama_kategori') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: right; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.jumlah') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.aksi') }}</th>
                                </tr>
                            </thead>
                        <tbody>
                            @foreach($kategoris as $kategori)
                            <tr style="border-bottom: 1px solid var(--line);">
                                <td style="padding: 1.25rem 1.5rem; color: var(--ink); font-size: 0.95rem; font-weight: 500;">{{ $kategori->nama_kategori }}</td>
                                <td style="padding: 1.25rem 1.5rem; text-align: right;">
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                        {{ $kategori->tickets->count() ?? 0 }} {{ __('messages.jumlah') }}
                                    </span>
                                </td>
                                <td class="td-action-cell" style="padding: 1.25rem 1.5rem; text-align: center; position: relative;">
                                    <div style="position: relative; display: inline-block;">
                                        <button type="button" onclick="toggleMdDropdown(event, 'dropdown-kat-{{ $kategori->kategori_id }}')" style="background: var(--paper-raised); border: 1.5px solid var(--line); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; color: var(--ink); display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--border-hover)'; this.style.background='var(--paper-sunken)'" onmouseout="this.style.borderColor='var(--line)'; this.style.background='var(--paper-raised)'" title="Aksi">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="12" cy="5" r="1.5"></circle><circle cx="12" cy="19" r="1.5"></circle></svg>
                                        </button>
                                        <div id="dropdown-kat-{{ $kategori->kategori_id }}" class="md-dropdown-menu" style="display: none; position: absolute; right: 0; top: calc(100% + 4px); background: var(--paper-raised); border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); min-width: 140px; z-index: 100; text-align: left; padding: 6px; backdrop-filter: blur(8px);">
                                            <button type="button" onclick="openModal('modal-edit-kategori-{{ $kategori->kategori_id }}'); closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: var(--ink); cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='var(--paper-sunken)'" onmouseout="this.style.background='none'">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                {{ __('messages.edit_kategori') }}
                                            </button>
                                            <form action="{{ url('/support/master-data/kategori/' . $kategori->kategori_id) }}" method="POST" onsubmit="return confirm('{{ __('messages.konfirmasi_hapus_kategori') }}');" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    {{ __('messages.hapus') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    </div>
                </div>

                <!-- TAB 3: KOPERASI -->
                <div id="tab-koperasi" class="md-tab-pane">
                    <div class="glass-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.nama_koperasi') }}</h3>
                            </div>
                            
                            <div class="search" style="width: 250px; max-width: 100%; background: var(--paper-sunken); border: 1px solid var(--line); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; opacity: 0.5; color: var(--ink);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" placeholder="Cari koperasi..." id="search-koperasi" style="border:none; background:transparent; width:100%; outline:none; font-size: 0.85rem; color: var(--ink);">
                            </div>
                        </div>
                        <div class="table-scroll-wrapper" style="overflow: auto; border: none; border-radius: 0; background: transparent; margin: 0; padding-bottom: 0;">
                        <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                            <thead style="background: var(--paper-sunken); position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.nama_koperasi') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.kontak') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.total_akun') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.tiket_aktif') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.total_tiket') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-koperasi">
                                @foreach($instansis as $ins)
                                @php
                                    $phone = !empty($ins->no_telp) ? $ins->no_telp : ($ins->users->whereNotNull('whatsapp')->first()?->whatsapp ?? '-');
                                @endphp
                                <tr style="border-bottom: 1px solid var(--line);">
                                    <td style="padding: 1.25rem 1.5rem; color: var(--ink); font-size: 0.95rem; font-weight: 600; vertical-align: top;">
                                        {{ $ins->nama_instansi }}
                                        <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal; margin-top: 4px; line-height: 1.5;">{{ Str::limit($ins->alamat ?? 'Alamat belum diatur', 60) }}</div>
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.95rem; vertical-align: top;">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                            {{ $phone }}
                                        </div>
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: center; vertical-align: top;">
                                        <span style="background: #e0f2fe; color: #0284c7; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;">
                                            {{ $ins->users_count }} {{ __('messages.akun') }}
                                        </span>
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: center; vertical-align: top;">
                                        <span style="background: #fef3c7; color: #d97706; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;">
                                            {{ $ins->active_tickets_count ?? 0 }} {{ __('messages.tiket') }}
                                        </span>
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; text-align: center; vertical-align: top;">
                                        <span style="background: #f1f5f9; color: #475569; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;">
                                            {{ $ins->total_tickets_count ?? 0 }} {{ __('messages.tiket') }}
                                        </span>
                                    </td>
                                    <td class="td-action-cell" style="padding: 1.25rem 1.5rem; text-align: center; position: relative; vertical-align: top;">
                                        <div style="position: relative; display: inline-block;">
                                            <button type="button" onclick="toggleMdDropdown(event, 'dropdown-ins-{{ $ins->instansi_id }}')" style="background: var(--paper-raised); border: 1.5px solid var(--line); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; color: var(--ink); display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--border-hover)'; this.style.background='var(--paper-sunken)'" onmouseout="this.style.borderColor='var(--line)'; this.style.background='var(--paper-raised)'" title="{{ __('messages.aksi') }}">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="12" cy="5" r="1.5"></circle><circle cx="12" cy="19" r="1.5"></circle></svg>
                                            </button>
                                            <div id="dropdown-ins-{{ $ins->instansi_id }}" class="md-dropdown-menu" style="display: none; position: absolute; right: 0; top: calc(100% + 4px); background: var(--paper-raised); border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); min-width: 140px; z-index: 100; text-align: left; padding: 6px; backdrop-filter: blur(8px);">
                                                <button type="button" onclick="openModal('modal-edit-koperasi-{{ $ins->instansi_id }}'); closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: var(--ink); cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='var(--paper-sunken)'" onmouseout="this.style.background='none'">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                    Edit Koperasi
                                                </button>
                                                <form action="{{ url('/support/master-data/koperasi/' . $ins->instansi_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data koperasi ini?');" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                        {{ __('messages.hapus') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>

                <!-- TAB 4: PIC SUPPORT -->
                <div id="tab-picsupport" class="md-tab-pane">
                    <div class="glass-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.pic_tim_support') }}</h3>
                            </div>
                        </div>
                        <div class="table-scroll-wrapper" style="overflow: auto; border: none; border-radius: 0; background: transparent; margin: 0; padding-bottom: 0;">
                        <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                            <thead style="background: var(--paper-sunken); position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.profil_support') }}</th>
                                    <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.kontak') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supportPics as $pic)
                                <tr style="border-bottom: 1px solid var(--line);">
                                    <td style="padding: 1.25rem 1.5rem; vertical-align: middle;">
                                        <div style="display: flex; align-items: center; gap: 14px;">
                                            @if($pic->avatar)
                                                <img src="{{ Storage::url($pic->avatar) }}" alt="Avatar" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 1px solid var(--line);">
                                            @else
                                                @php
                                                    $words = explode(' ', $pic->nama);
                                                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : substr($words[0], 1, 1)));
                                                @endphp
                                                <div style="width: 42px; height: 42px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700; border: 1px solid #fecaca;">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight: 600; color: var(--ink); font-size: 1rem;">{{ $pic->nama }}</div>
                                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 3px;">{{ $pic->spesialisasi ?? __('messages.tim_support_umum') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.95rem; vertical-align: middle;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                            {{ $pic->email }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>

                <!-- TAB 5: STATUS TIKET -->
                <div id="tab-status" class="md-tab-pane">
                    <div class="glass-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.status_tiket') }}</h3>
                            </div>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                                <thead style="background: var(--paper-sunken);">
                                    <tr>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.value_sistem') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.label_visual') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.deskripsi_penggunaan') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statuses as $st)
                                    <tr style="border-bottom: 1px solid var(--line);">
                                        <td style="padding: 1.25rem 1.5rem; color: var(--ink); font-size: 0.95rem; font-family: monospace; background: var(--paper-sunken); font-weight: 600; width: 180px; vertical-align: top;">
                                            {{ $st->name }}
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; width: 220px; vertical-align: top;">
                                            @php
                                                $statusVal = $st->value;
                                                $statusLower = strtolower($statusVal);
                                                
                                                if ($statusLower === 'done') {
                                                    $bg = '#dcfce7'; $color = '#16a34a';
                                                } elseif ($statusLower === 'pending') {
                                                    $bg = '#fef9c3'; $color = '#ca8a04';
                                                } elseif ($statusLower === 'open') {
                                                    $bg = '#f1f5f9'; $color = '#475569';
                                                } elseif ($statusLower === 'proses' || $statusLower === 'on progress') {
                                                    $bg = '#fee2e2'; $color = '#dc2626';
                                                } elseif ($statusLower === 'in review' || $statusLower === 'review') {
                                                    $bg = '#e0e7ff'; $color = '#4f46e5';
                                                } elseif ($statusLower === 'waiting') {
                                                    $bg = '#ffedd5'; $color = '#ea580c';
                                                } else {
                                                    $bg = 'var(--paper-sunken)'; $color = 'var(--text-muted)';
                                                }
                                            @endphp
                                            <span style="background: {{ $bg }}; color: {{ $color }}; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $color }};"></span> {{ $statusVal }}
                                            </span>
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; vertical-align: top;">
                                            @if($st->name === 'OPEN')
                                                <strong style="color: var(--ink);">{{ __('messages.st_desc_open_title') }}</strong> {{ __('messages.st_desc_open_text') }}
                                            @elseif($st->name === 'PROSES')
                                                <strong style="color: var(--ink);">{{ __('messages.st_desc_proses_title') }}</strong> {{ __('messages.st_desc_proses_text') }}
                                            @elseif($st->name === 'PENDING')
                                                <strong style="color: var(--ink);">{{ __('messages.st_desc_pending_title') }}</strong> {{ __('messages.st_desc_pending_text') }}
                                            @elseif($st->name === 'REVIEW')
                                                <strong style="color: var(--ink);">{{ __('messages.st_desc_review_title') }}</strong> {{ __('messages.st_desc_review_text') }}
                                            @elseif($st->name === 'WAITING')
                                                <strong style="color: var(--ink);">{{ __('messages.st_desc_waiting_title') }}</strong> {{ __('messages.st_desc_waiting_text') }}
                                            @elseif($st->name === 'DONE')
                                                <strong style="color: var(--ink);">{{ __('messages.st_desc_done_title') }}</strong> {{ __('messages.st_desc_done_text') }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 6: FAQ -->
                <div id="tab-faq" class="md-tab-pane">
                    <div class="glass-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 0; overflow: hidden; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.knowledge_base_faq') }}</h3>
                            </div>
                            <button type="button" onclick="openModal('modal-add-faq')" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--line); border-radius: 6px; background: transparent; color: var(--ink); white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">{{ __('messages.tambah_faq') }}</button>
                        </div>
                        <div class="table-scroll-wrapper" style="overflow: auto; border: none; border-radius: 0; background: transparent; margin: 0; padding-bottom: 0;">
                            <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                                <thead style="background: var(--paper-sunken); position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.pertanyaan') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.kategori') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.visibility') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.status') }}</th>
                                        <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($faqs as $faq)
                                    <tr style="border-bottom: 1px solid var(--line);">
                                        <td style="padding: 1.25rem 1.5rem; color: var(--ink); font-size: 0.95rem; font-weight: 500; max-width: 320px; vertical-align: top;">
                                            {{ $faq->pertanyaan }}
                                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">{{ Str::limit($faq->jawaban, 80) }}</div>
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.9rem; vertical-align: top;">
                                            {{ $faq->kategori->nama_kategori ?? '-' }}
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; text-align: center; vertical-align: top;">
                                            @if($faq->visibility === 'public')
                                                <span style="background: #dbeafe; color: #2563eb; padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">{{ __('messages.public') }}</span>
                                            @else
                                                <span style="background: #fef3c7; color: #d97706; padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">{{ __('messages.internal') }}</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; text-align: center; vertical-align: top;">
                                            <span style="background: {{ $faq->is_active ? 'rgba(34, 197, 94, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $faq->is_active ? '#16a34a' : '#ef4444' }}; padding: 0.3rem 0.8rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                                {{ $faq->is_active ? __('messages.aktif') : __('messages.nonaktif') }}
                                            </span>
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; text-align: center; vertical-align: top; position: relative;">
                                            <div style="position: relative; display: inline-block;">
                                                <button type="button" onclick="toggleMdDropdown(event, 'dropdown-faq-{{ $faq->faq_id }}')" style="background: var(--paper-raised); border: 1.5px solid var(--line); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; color: var(--ink); display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--border-hover)'; this.style.background='var(--paper-sunken)'" onmouseout="this.style.borderColor='var(--line)'; this.style.background='var(--paper-raised)'" title="{{ __('messages.aksi') }}">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="12" cy="5" r="1.5"></circle><circle cx="12" cy="19" r="1.5"></circle></svg>
                                                </button>
                                                <div id="dropdown-faq-{{ $faq->faq_id }}" class="md-dropdown-menu" style="display: none; position: absolute; right: 0; top: calc(100% + 4px); background: var(--paper-raised); border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); min-width: 140px; z-index: 100; text-align: left; padding: 6px; backdrop-filter: blur(8px);">
                                                    <button type="button" onclick="openModal('modal-edit-faq-{{ $faq->faq_id }}'); closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: var(--ink); cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='var(--paper-sunken)'" onmouseout="this.style.background='none'">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                        {{ __('messages.edit_faq') }}
                                                    </button>
                                                    <form action="{{ url('/support/master-data/faq/' . $faq->faq_id) }}" method="POST" onsubmit="return confirm('{{ __('messages.yakin_hapus_faq') }}');" style="margin: 0;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="closeAllMdDropdowns();" style="width: 100%; background: none; border: none; padding: 8px 12px; text-align: left; font-size: 0.85rem; font-weight: 500; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 8px; border-radius: 6px;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                            {{ __('messages.hapus') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" style="padding: 3rem 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px; opacity: 0.4;"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                            <div>{{ __('messages.belum_ada_faq') }}</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mobile-export" style="display: none;">
                <a href="{{ url('/support/master-data/export') }}" class="btn" style="display: flex; justify-content: center; align-items: center; gap: 8px; padding: 0.8rem; border-radius: 8px; font-weight: 600; text-decoration: none; background-color: #10b981; color: white; border: 1px solid #10b981; transition: opacity 0.2s; width: 100%;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                    Export Excel
                </a>
            </div>
            
        </div>
    </div>

    <!-- Modal Tambah Aplikasi -->
    <div class="overlay" id="modal-add-aplikasi">
        <div class="modal w-sm modal-centered">
            <div class="modal-head">
                <div>
                    <h3>{{ __('messages.tambah_master_aplikasi') }}</h3>
                    <p>{{ __('messages.aplikasi_muncul_pelapor') }}</p>
                </div>
                <button type="button" class="modal-x" onclick="closeModal('modal-add-aplikasi')">✕</button>
            </div>
            <form action="{{ url('/support/master-data/aplikasi') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="field">
                        <label>{{ __('messages.nama_aplikasi') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama_aplikasi" id="add_nama_aplikasi" required placeholder="{{ __('messages.contoh_sakti') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.deskripsi_singkat') }}</label>
                        <textarea name="deskripsi" placeholder="{{ __('messages.penjelasan_singkat') }}" rows="3"></textarea>
                    </div>
                    <div class="field">
                        <label>Ebook (Bisa pilih banyak file)</label>
                        <input type="file" name="ebooks[]" multiple accept=".pdf,.doc,.docx,.zip,.rar" style="padding: 8px; border: 1px solid var(--line); border-radius: 6px; width: 100%;">
                        <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 4px;">Format: PDF, Word, ZIP. Maks: 10MB per file.</small>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.link_aplikasi') }}</label>
                        <input type="url" name="link" id="add_link" placeholder="{{ __('messages.masukkan_link_aplikasi') }}">
                    </div>
                    <div class="field">
                        <label>Username</label>
                        <input type="text" name="username" id="add_username" placeholder="Masukkan username">
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="text" name="password" id="add_password" placeholder="Masukkan password">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-add-aplikasi')">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_aplikasi') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div class="overlay" id="modal-add-kategori">
        <div class="modal w-sm modal-centered">
            <div class="modal-head">
                <div>
                    <h3>{{ __('messages.tambah_master_kategori') }}</h3>
                    <p>{{ __('messages.kategori_klasifikasi') }}</p>
                </div>
                <button type="button" class="modal-x" onclick="closeModal('modal-add-kategori')">✕</button>
            </div>
            <form action="{{ url('/support/master-data/kategori') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="field">
                        <label>{{ __('messages.nama_kategori') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama_kategori" required placeholder="{{ __('messages.contoh_bug') }}">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-add-kategori')">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_kategori') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals Edit Aplikasi -->
    @foreach($aplikasis as $app)
    <div class="overlay" id="modal-edit-aplikasi-{{ $app->aplikasi_id }}">
        <div class="modal w-sm modal-centered">
            <div class="modal-head">
                <div>
                    <h3>{{ __('messages.edit_master_aplikasi') }}</h3>
                    <p>{{ __('messages.edit_master_aplikasi_desc') }}</p>
                </div>
                <button type="button" class="modal-x" onclick="closeModal('modal-edit-aplikasi-{{ $app->aplikasi_id }}')">✕</button>
            </div>
            <form action="{{ url('/support/master-data/aplikasi/' . $app->aplikasi_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="field">
                        <label>{{ __('messages.nama_aplikasi') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama_aplikasi" required value="{{ $app->nama_aplikasi }}" placeholder="{{ __('messages.contoh_sakti') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.deskripsi_singkat') }}</label>
                        <textarea name="deskripsi" placeholder="{{ __('messages.penjelasan_singkat') }}" rows="3">{{ $app->deskripsi }}</textarea>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.uploaded_ebook') }}</label>
                        @if(is_array($app->ebook) && count($app->ebook) > 0)
                            <div style="margin-bottom: 12px; display: flex; flex-direction: column; gap: 8px;" id="ebook-list-{{ $app->aplikasi_id }}">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <label style="margin: 0; font-size: 12px; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                        <input type="checkbox" onchange="toggleAllEbooks(this, {{ $app->aplikasi_id }})" style="margin: 0;"> Pilih Semua
                                    </label>
                                    <button type="button" id="btn-bulk-delete-{{ $app->aplikasi_id }}" onclick="submitBulkDelete({{ $app->aplikasi_id }})" style="display: none; background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s;">Hapus yang Dipilih</button>
                                </div>
                                @foreach($app->ebook as $index => $path)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px; background: var(--paper-sunken); border: 1px solid var(--line); border-radius: 6px;">
                                        <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                            <input type="checkbox" name="ebook_selections_{{ $app->aplikasi_id }}[]" value="{{ $index }}" onchange="checkBulkDelete({{ $app->aplikasi_id }})" style="margin: 0; cursor: pointer;">
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" style="color: var(--primary); font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 6px;" title="{{ __('messages.btn_open') }} {{ preg_replace('/^\d+_/', '', basename($path)) }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">{{ Str::limit(preg_replace('/^\d+_/', '', basename($path)), 35) }}</span>
                                            </a>
                                        </div>
                                        <button type="button" onclick="if(confirm('{{ __('messages.confirm_delete_ebook') }}')) { document.getElementById('delete-ebook-form-{{ $app->aplikasi_id }}-{{ $index }}').submit(); }" style="background: none; border: none; color: var(--danger); cursor: pointer; padding: 4px; border-radius: 4px;" title="{{ __('messages.hapus') }}">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px; font-style: italic;">{{ __('messages.no_file_uploaded') }}</div>
                        @endif
                        
                        <label>{{ __('messages.add_new_ebook_optional') }}</label>
                        <input type="file" name="ebooks[]" multiple accept=".pdf,.doc,.docx,.zip,.rar" style="padding: 8px; border: 1px solid var(--line); border-radius: 6px; width: 100%;">
                        <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 4px;">{{ __('messages.ebook_upload_format_notice') }}</small>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.link_aplikasi') }}</label>
                        <input type="url" name="link" value="{{ $app->link }}" placeholder="{{ __('messages.masukkan_link_aplikasi') }}">
                    </div>
                    <div class="field">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ $app->username }}" placeholder="Masukkan username">
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="text" name="password" value="{{ $app->password }}" placeholder="Masukkan password">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.status_aplikasi') }} <span style="color:var(--danger)">*</span></label>
                        <select name="is_active" required style="width: 100%; padding: 0.65rem 0.8rem; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-raised); color: var(--ink); font-size: 0.9rem;">
                            <option value="1" {{ $app->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$app->is_active ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-aplikasi-{{ $app->aplikasi_id }}')">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_perubahan') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Modals Edit Kategori -->
    @foreach($kategoris as $kategori)
    <div class="overlay" id="modal-edit-kategori-{{ $kategori->kategori_id }}">
        <div class="modal w-sm modal-centered">
            <div class="modal-head">
                <div>
                    <h3>{{ __('messages.edit_master_kategori') }}</h3>
                    <p>{{ __('messages.ubah_nama_kategori') }}</p>
                </div>
                <button type="button" class="modal-x" onclick="closeModal('modal-edit-kategori-{{ $kategori->kategori_id }}')">✕</button>
            </div>
            <form action="{{ url('/support/master-data/kategori/' . $kategori->kategori_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="field">
                        <label>{{ __('messages.nama_kategori') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama_kategori" required value="{{ $kategori->nama_kategori }}" placeholder="{{ __('messages.contoh_bug') }}">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-kategori-{{ $kategori->kategori_id }}')">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_perubahan') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Modals Edit Koperasi -->
    @foreach($instansis as $ins)
    <div class="overlay" id="modal-edit-koperasi-{{ $ins->instansi_id }}">
        <div class="modal w-sm modal-centered">
            <div class="modal-head">
                <div>
                    <h3>Edit Master Koperasi</h3>
                    <p>Ubah nama, alamat, atau kontak koperasi</p>
                </div>
                <button type="button" class="modal-x" onclick="closeModal('modal-edit-koperasi-{{ $ins->instansi_id }}')">✕</button>
            </div>
            <form action="{{ url('/support/master-data/koperasi/' . $ins->instansi_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="field">
                        <label>Nama Koperasi <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama_instansi" required value="{{ $ins->nama_instansi }}" placeholder="Contoh: Koperasi Kredit Sejahtera">
                    </div>
                    <div class="field">
                        <label>Alamat Koperasi</label>
                        <textarea name="alamat" placeholder="Masukkan alamat..." rows="3">{{ $ins->alamat }}</textarea>
                    </div>
                    <div class="field">
                        <label>No. Telepon / Kontak</label>
                        <input type="text" name="no_telp" value="{{ $ins->no_telp }}" placeholder="Contoh: 081234567890">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-koperasi-{{ $ins->instansi_id }}')">Batal</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_perubahan') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Modal Tambah FAQ -->
    <div class="overlay" id="modal-add-faq">
        <div class="modal w-sm modal-centered">
            <div class="modal-head">
                <div>
                    <h3>{{ __('messages.tambah_faq_baru') }}</h3>
                    <p>{{ __('messages.tambah_faq_desc') }}</p>
                </div>
                <button type="button" class="modal-x" onclick="closeModal('modal-add-faq')">✕</button>
            </div>
            <form action="{{ url('/support/master-data/faq') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="field">
                        <label>{{ __('messages.kategori_kendala') }} <span style="color:var(--danger)">*</span></label>
                        <select name="kategori_id" required style="width: 100%; padding: 0.65rem 0.8rem; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-raised); color: var(--ink); font-size: 0.9rem;">
                            <option value="">{{ __('messages.pilih_kategori') }}</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->kategori_id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.pertanyaan') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="pertanyaan" required placeholder="{{ __('messages.contoh_pertanyaan') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.jawaban') }} <span style="color:var(--danger)">*</span></label>
                        <textarea name="jawaban" required placeholder="{{ __('messages.tulis_jawaban') }}" rows="4"></textarea>
                    </div>
                    <div class="grid2">
                        <div class="field">
                            <label>{{ __('messages.visibility') }} <span style="color:var(--danger)">*</span></label>
                            <select name="visibility" required style="width: 100%; padding: 0.65rem 0.8rem; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-raised); color: var(--ink); font-size: 0.9rem;">
                                <option value="public">{{ __('messages.visibility_public_desc') }}</option>
                                <option value="internal">{{ __('messages.visibility_internal_desc') }}</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>{{ __('messages.status') }} <span style="color:var(--danger)">*</span></label>
                            <select name="is_active" required style="width: 100%; padding: 0.65rem 0.8rem; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-raised); color: var(--ink); font-size: 0.9rem;">
                                <option value="1" selected>{{ __('messages.aktif') }}</option>
                                <option value="0">{{ __('messages.nonaktif') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-add-faq')">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_faq') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals Edit FAQ -->
    @foreach($faqs as $faq)
    <div class="overlay" id="modal-edit-faq-{{ $faq->faq_id }}">
        <div class="modal w-sm modal-centered">
            <div class="modal-head">
                <div>
                    <h3>{{ __('messages.edit_faq') }}</h3>
                    <p>{{ __('messages.edit_faq_desc') }}</p>
                </div>
                <button type="button" class="modal-x" onclick="closeModal('modal-edit-faq-{{ $faq->faq_id }}')">✕</button>
            </div>
            <form action="{{ url('/support/master-data/faq/' . $faq->faq_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="field">
                        <label>{{ __('messages.kategori_kendala') }} <span style="color:var(--danger)">*</span></label>
                        <select name="kategori_id" required style="width: 100%; padding: 0.65rem 0.8rem; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-raised); color: var(--ink); font-size: 0.9rem;">
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->kategori_id }}" {{ $faq->kategori_id == $kat->kategori_id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.pertanyaan') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="pertanyaan" required value="{{ $faq->pertanyaan }}">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.jawaban') }} <span style="color:var(--danger)">*</span></label>
                        <textarea name="jawaban" required rows="4">{{ $faq->jawaban }}</textarea>
                    </div>
                    <div class="grid2">
                        <div class="field">
                            <label>{{ __('messages.visibility') }} <span style="color:var(--danger)">*</span></label>
                            <select name="visibility" required style="width: 100%; padding: 0.65rem 0.8rem; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-raised); color: var(--ink); font-size: 0.9rem;">
                                <option value="public" {{ $faq->visibility === 'public' ? 'selected' : '' }}>{{ __('messages.public') }}</option>
                                <option value="internal" {{ $faq->visibility === 'internal' ? 'selected' : '' }}>{{ __('messages.internal') }}</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>{{ __('messages.status') }} <span style="color:var(--danger)">*</span></label>
                            <select name="is_active" required style="width: 100%; padding: 0.65rem 0.8rem; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-raised); color: var(--ink); font-size: 0.9rem;">
                                <option value="1" {{ $faq->is_active ? 'selected' : '' }}>{{ __('messages.aktif') }}</option>
                                <option value="0" {{ !$faq->is_active ? 'selected' : '' }}>{{ __('messages.nonaktif') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-faq-{{ $faq->faq_id }}')">{{ __('messages.batal') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.simpan_perubahan') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>

<script>
    function toggleMdDropdown(event, dropdownId) {
        event.stopPropagation();
        const target = document.getElementById(dropdownId);
        const isVisible = target.style.display === 'block';
        closeAllMdDropdowns();
        if (!isVisible) {
            target.style.display = 'block';
        }
    }

    function closeAllMdDropdowns() {
        document.querySelectorAll('.md-dropdown-menu').forEach(el => el.style.display = 'none');
    }

    document.addEventListener('click', function() {
        closeAllMdDropdowns();
    });

    // Tab switching logic for Master Data sub-sidebar
    function switchMdTab(tabId, btnElement) {
        // Remove active class from all buttons
        let buttons = document.querySelectorAll('.md-tab-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        
        // Add active class to clicked button
        if (btnElement) {
            btnElement.classList.add('active');
        }

        // Hide all tab panes
        let panes = document.querySelectorAll('.md-tab-pane');
        panes.forEach(pane => pane.classList.remove('active'));

        // Show the target tab pane
        let targetPane = document.getElementById('tab-' + tabId);
        if (targetPane) {
            targetPane.classList.add('active');
        }

        // Simpan state tab ke localStorage
        localStorage.setItem('activeMasterDataTab', tabId);
    }

    function toggleDesc(id) {
        let shortDesc = document.getElementById('desc-short-' + id);
        let fullDesc = document.getElementById('desc-full-' + id);
        if (shortDesc.style.display === 'none') {
            shortDesc.style.display = 'inline';
            fullDesc.style.display = 'none';
        } else {
            shortDesc.style.display = 'none';
            fullDesc.style.display = 'inline';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Kembalikan tab terakhir yang aktif dari localStorage
        const savedTab = localStorage.getItem('activeMasterDataTab');
        if (savedTab) {
            const tabBtn = document.querySelector(`.md-tab-btn[onclick*="'${savedTab}'"]`);
            if (tabBtn) {
                switchMdTab(savedTab, tabBtn);
            }
        }

        const skeleton = document.getElementById('skeleton-loading');
        const content  = document.getElementById('actual-content');
        if(skeleton && content) {
            setTimeout(function () {
                skeleton.style.display = 'none';
                content.style.display = 'block';
                content.classList.add('loaded');
            }, 800);
        }

        // Live Search Koperasi
        const searchKoperasi = document.getElementById('search-koperasi');
        if (searchKoperasi) {
            searchKoperasi.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tbody-koperasi tr');
                
                rows.forEach(row => {
                    const namaKoperasi = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                    if (namaKoperasi.includes(keyword)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        // Auto-fill logic untuk Laci
        const inputNamaAplikasi = document.getElementById('add_nama_aplikasi');
        if (inputNamaAplikasi) {
            inputNamaAplikasi.addEventListener('input', function() {
                if (this.value.toLowerCase().includes('laci')) {
                    const linkInput = document.getElementById('add_link');
                    const userInput = document.getElementById('add_username');
                    const passInput = document.getElementById('add_password');
                    
                    if (linkInput && !linkInput.value) linkInput.value = 'https://apps.laci.online/';
                    if (userInput && !userInput.value) userInput.value = 'demosakti';
                    if (passInput && !passInput.value) passInput.value = 'demosakti';
                }
            });
        }
    });
    // Fungsi Bulk Delete Ebook
    function toggleAllEbooks(checkbox, appId) {
        let checkboxes = document.querySelectorAll(`input[name="ebook_selections_${appId}[]"]`);
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        checkBulkDelete(appId);
    }

    function checkBulkDelete(appId) {
        let checkboxes = document.querySelectorAll(`input[name="ebook_selections_${appId}[]"]:checked`);
        let btn = document.getElementById(`btn-bulk-delete-${appId}`);
        if (checkboxes.length > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    }

    function submitBulkDelete(appId) {
        if (confirm('Apakah Anda yakin ingin menghapus ' + document.querySelectorAll(`input[name="ebook_selections_${appId}[]"]:checked`).length + ' ebook yang dipilih?')) {
            let form = document.getElementById(`bulk-delete-ebook-form-${appId}`);
            let checkboxes = document.querySelectorAll(`input[name="ebook_selections_${appId}[]"]:checked`);
            checkboxes.forEach(cb => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_ebook_indices[]';
                input.value = cb.value;
                form.appendChild(input);
            });
            form.submit();
        }
    }
</script>

    <!-- Hidden forms for deleting specific ebook files -->
    @foreach($aplikasis as $app)
        @if(is_array($app->ebook) && count($app->ebook) > 0)
            <!-- Form untuk bulk delete -->
            <form id="bulk-delete-ebook-form-{{ $app->aplikasi_id }}" action="{{ route('support.master-data.aplikasi.ebook.bulk-destroy', $app->aplikasi_id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
            
            @foreach($app->ebook as $index => $path)
                <form id="delete-ebook-form-{{ $app->aplikasi_id }}-{{ $index }}" action="{{ route('support.master-data.aplikasi.ebook.destroy', ['id' => $app->aplikasi_id, 'index' => $index]) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    @endforeach
@endsection
