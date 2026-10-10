<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAKTI Desk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,700&family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ date('YmdHis') }}">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Hide disabled options in TomSelect dropdowns entirely */
        .ts-dropdown .option.disabled {
            display: none !important;
        }
        
        /* Fix for Laravel default pagination SVG icons when Tailwind is missing */
        nav[role="navigation"] svg, .pagination svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }

        /* Force Open status badge to be clean gray in Light and Dark mode */
        .status-open, [data-status*="open"] {
            background: #e2e8f0 !important;
            color: #475569 !important;
        }
        .status-open::before {
            background: #64748b !important;
        }

        html.dark-mode .status-open, html.dark-mode [data-status*="open"] {
            background: #334155 !important;
            color: #cbd5e1 !important;
        }
        html.dark-mode .status-open::before {
            background: #94a3b8 !important;
        }

        /* Skeleton shimmer animation for updating dropdown */
        @keyframes skeletonPulse {
            0% { background-position: -200px 0; }
            100% { background-position: calc(200px + 100%) 0; }
        }
        .skeleton-updating {
            pointer-events: none !important;
            background: linear-gradient(90deg, #e2e8f0 0px, #f8fafc 80px, #e2e8f0 160px) !important;
            background-size: 200px 100% !important;
            animation: skeletonPulse 1.2s infinite ease-in-out !important;
            border-radius: 8px !important;
            opacity: 0.85 !important;
        }
        html.dark-mode .skeleton-updating {
            background: linear-gradient(90deg, #1e293b 0px, #334155 80px, #1e293b 160px) !important;
        }

        /* Smooth Fade-Up & Fade-Out animations for modal alert notification */
        @keyframes modalAlertFadeUp {
            0% {
                opacity: 0;
                transform: translateY(14px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes modalAlertFadeOut {
            0% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            100% {
                opacity: 0;
                transform: translateY(-10px) scale(0.97);
            }
        }
        .animate-fade-up {
            animation: modalAlertFadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }
        .animate-fade-out {
            animation: modalAlertFadeOut 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }
    </style>
    <script>
        // Apply personalization before page render to prevent FOUC
        (function() {
            var theme = localStorage.getItem('personal_theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
            var fontSize = localStorage.getItem('personal_font_size');
            if (fontSize) {
                document.documentElement.classList.add('text-' + fontSize);
            }
        })();
    </script>
</head>
<body>

@if(Auth::check() && !request()->is('login') && !request()->is('register') && !request()->is('forgot-password') && !request()->is('verify-otp') && !request()->is('reset-password'))
<div class="app-shell">
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="lg">
                <!-- Placeholder untuk logo -->
                <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;">
            </div>
            <div class="tx">
                @if(Auth::check() && Auth::user()->role === \App\Enums\UserRole::SUPPORT)
                    <strong>SAKTI Desk</strong>
                    <span style="text-transform: uppercase;">{{ __('messages.tim_support') ?? 'TIM SUPPORT' }}</span>
                @else
                    <strong>SAKTI Desk</strong>
                    <span>Tracker System</span>
                @endif
            </div>
        </div>
        
        <div class="sidebar-menu">
            @if(Auth::check() && in_array(Auth::user()->role, [\App\Enums\UserRole::SUPPORT, \App\Enums\UserRole::SUPERADMIN]))
                <a href="{{ route('support.dashboard') }}" class="{{ request()->routeIs('support.dashboard') ? 'active' : '' }}">
                    <span class="ic"><img src="{{ asset('analysis.png') }}" alt=""></span> {{ __('messages.dashboard') }}
                </a>
                <a href="{{ route('support.prioritas') }}" class="{{ request()->routeIs('support.prioritas') ? 'active' : '' }}">
                    <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; opacity: 0.9; margin-bottom: 2px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span> Antrean Prioritas
                </a>
                <a href="{{ route('support.master-data.index') }}" class="{{ request()->routeIs('support.master-data.*') ? 'active' : '' }}">
                    <span class="ic"><img src="{{ asset('folder.png') }}" alt=""></span> {{ __('messages.master_data') }}
                </a>
                @php
                    $isRecapActive = request()->routeIs('support.recap*');
                @endphp
                <div class="sidebar-dropdown {{ $isRecapActive ? 'open' : '' }}">
                    <button type="button" class="sidebar-dropdown-btn {{ $isRecapActive ? 'active-parent' : '' }}" onclick="toggleSidebarSubmenu(this)">
                        <span class="ic"><img src="{{ asset('file.png') }}" alt=""></span>
                        <span>{{ __('messages.recap_laporan') }}</span>
                        <svg class="chevron-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="sidebar-submenu" style="{{ $isRecapActive ? 'display: flex; flex-direction: column;' : 'display: none;' }}">
                        <a href="{{ route('support.recap.diagram') }}" class="{{ request()->routeIs('support.recap.diagram') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('messages.recap_diagram') }}
                        </a>
                        <a href="{{ route('support.recap.table') }}" class="{{ request()->routeIs('support.recap.table') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('messages.recap_support') }}
                        </a>
                        <a href="{{ route('support.recap.history-pic') }}" class="{{ request()->routeIs('support.recap.history-pic') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('messages.recap_history_pic') }}
                        </a>
                        <a href="{{ route('support.recap.template-surat') }}" class="{{ request()->routeIs('support.recap.template-surat') ? 'active' : '' }}">
                            <span class="sub-dot"></span> {{ __('messages.recap_template_surat') }}
                        </a>
                    </div>
                </div>
                <a href="{{ route('implementasi.index') }}" class="{{ request()->routeIs('implementasi.*') ? 'active' : '' }}">
                    <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; opacity: 0.9;"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg></span> {{ __('messages.monitoring_koperasi') }}
                </a>
                
                @if(Auth::user()->role === \App\Enums\UserRole::SUPERADMIN)
                <a href="{{ route('superadmin.pengguna') }}" class="{{ request()->routeIs('superadmin.pengguna') ? 'active' : '' }}">
                    <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; opacity: 0.9;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></span> {{ __('messages.manajemen_pengguna') }}
                </a>
                @endif

                <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1);"></div>
                <a href="{{ route('pengaturan') }}" class="{{ request()->routeIs('pengaturan') ? 'active' : '' }}">
                    <span class="ic"><img src="{{ asset('setting.png') }}" alt=""></span> {{ __('messages.pengaturan') }}
                </a>

            @else
                <a href="{{ route('pelapor.dashboard') }}" class="{{ request()->routeIs('pelapor.dashboard') ? 'active' : '' }}">
                    <span class="ic"><img src="{{ asset('analysis.png') }}" alt=""></span> {{ __('messages.dashboard') }}
                </a>
                <a href="{{ route('pelapor.riwayat') }}" class="{{ request()->routeIs('pelapor.riwayat') ? 'active' : '' }}">
                    <span class="ic"><img src="{{ asset('file.png') }}" alt=""></span> {{ __('messages.riwayat_lengkap') }}
                </a>
                
                <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1);"></div>
                <a href="{{ route('pengaturan') }}" class="{{ request()->routeIs('pengaturan') ? 'active' : '' }}">
                    <span class="ic"><img src="{{ asset('setting.png') }}" alt=""></span> {{ __('messages.pengaturan') }}
                </a>
            @endif
        </div>
        
        <div class="sidebar-foot">
            <button class="sidebar-foot-trigger" onclick="toggleProfilePopover(event)">
                <div class="av" style="overflow: hidden;">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ substr(Auth::user()->nama ?? 'A', 0, 1) }}
                    @endif
                </div>
                <div class="nm">
                    <strong>{{ Auth::user()->nama ?? 'User' }}</strong>
                    <span>{{ Auth::user()->instansi->nama_instansi ?? 'Administrator' }}</span>
                </div>
            </button>
            
            <div class="profile-popover" id="profile-popover">
                <div class="pop-head">
                    <strong>{{ Auth::user()->nama ?? 'User' }}</strong>
                    <span>{{ Auth::user()->email ?? 'email@example.com' }}</span>
                </div>
                <button onclick="window.location.href='{{ Auth::check() && Auth::user()->role !== \App\Enums\UserRole::PELAPOR ? route('support.profil.saya') : route('profil.instansi') }}'">
                    @if(Auth::check() && Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                        <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; opacity: 0.9;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span> {{ __('messages.profil_saya') }}
                    @else
                        <span class="ic"><img src="{{ asset('company.png') }}" alt="Company" style="width: 16px; height: 16px; object-fit: contain; vertical-align: middle; filter: brightness(0) invert(1); opacity: 0.9;"></span> {{ __('messages.profil_koperasi') }}
                    @endif
                </button>
                <button onclick="window.location.href='{{ route('pengaturan') }}'">
                    <span class="ic"><img src="{{ asset('setting.png') }}" alt="Settings" style="width: 16px; height: 16px; object-fit: contain; vertical-align: middle; filter: brightness(0) invert(1); opacity: 0.9;"></span> {{ __('messages.pengaturan') }}
                </button>
                @if(Auth::check() && Auth::user()->role === \App\Enums\UserRole::PELAPOR)
                <button onclick="window.location.href='{{ route('pelapor.bantuan') }}'">
                    <span class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; opacity: 0.9;"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span> {{ __('messages.title_pusat_bantuan') }}
                </button>
                @endif
                <div class="pop-div"></div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="danger">
                        <span class="ic"><img src="{{ asset('logout.png') }}" alt="Logout" style="width: 16px; height: 16px; object-fit: contain; vertical-align: middle;"></span> {{ __('messages.keluar_singkat') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="app-main">
        <div class="app-topbar">
            <div>
                <h1>@yield('page_title', 'Dashboard')</h1>
                @if(Auth::check() && Auth::user()->role === \App\Enums\UserRole::SUPPORT)
                    <div class="tag">@yield('page_subtitle', 'SAKTI Desk')</div>
                @else
                    <div class="tag">@yield('page_subtitle', 'SAKTI Desk')</div>
                @endif
            </div>
            <div class="app-topbar-right" style="display: flex; align-items: center;">
                <div class="notification-dropdown" style="position: relative; margin-right: 15px;">
                    @php
                        $unreadCount = Auth::user()->unreadNotifications->count();
                    @endphp
                    <button class="notif-btn" onclick="toggleNotifDropdown(event)" style="background: none; border: none; cursor: pointer; position: relative; padding: 5px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        @if($unreadCount > 0)
                            <span style="position: absolute; top: 0; right: 0; background: #ef4444; color: white; border-radius: 50%; font-size: 10px; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-weight: bold;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>
                    
                    <div id="notif-dropdown-menu" style="display: none; position: absolute; right: 0; top: 40px; width: 320px; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 8px; box-shadow: var(--shadow); z-index: 50;">
                        <div style="padding: 12px 15px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                            <strong style="font-size: 14px; color: var(--ink);">{{ __('messages.notifikasi_title') }}</strong>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: var(--brand-primary); font-size: 12px; cursor: pointer; padding: 0;">{{ __('messages.mark_all_read') }}</button>
                                </form>
                            @endif
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @forelse(Auth::user()->unreadNotifications as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}" style="display: block; padding: 12px 15px; border-bottom: 1px solid var(--line); text-decoration: none; color: inherit; transition: background 0.2s;" onmouseover="this.style.background='var(--paper-sunken)'" onmouseout="this.style.background='transparent'">
                                    <div style="font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 4px;">{{ $notification->data['title'] ?? 'Pengingat' }}</div>
                                    <div style="font-size: 12px; color: var(--ink-soft); line-height: 1.4;">{{ $notification->data['message'] ?? '' }}</div>
                                    <div style="font-size: 11px; color: var(--ink-soft); margin-top: 6px;">{{ $notification->created_at->diffForHumans() }}</div>
                                </a>
                            @empty
                                <div style="padding: 20px; text-align: center; color: var(--ink-soft); font-size: 13px;">{{ __('messages.no_new_notif') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @yield('topbar_right')
            </div>
        </div>

        @yield('content')
    </div>
</div>
@else
    @yield('content')
@endif

@if(Auth::check() && !request()->is('login') && !request()->is('register') && !request()->is('forgot-password') && !request()->is('verify-otp') && !request()->is('reset-password'))
<!-- ================= MOBILE UI COMPONENTS (TAHAP 1) ================= -->
<div class="mobile-top-header">
    <div class="m-left">
        <div class="m-title">
            <strong>@yield('page_title', 'Dashboard')</strong> / @yield('page_subtitle', 'SAKTI Desk')
        </div>
    </div>
    <div class="m-right">
        <button class="m-avatar" onclick="toggleMobileProfile(event)">
            @if(Auth::check() && Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="">
            @else
                {{ substr(Auth::user()->nama ?? 'D', 0, 1) }}
            @endif
        </button>
    </div>
</div>

<div class="mobile-profile-overlay" id="mobile-profile-overlay" onclick="toggleMobileProfile(event)"></div>
<div class="mobile-profile-popup" id="mobile-profile-popup">
    <div class="mp-head">
        <strong>{{ Auth::check() ? Auth::user()->nama : 'User' }}</strong>
        <span>{{ Auth::check() ? Auth::user()->email : 'email@example.com' }}</span>
    </div>
    <div class="mp-body">
        <button onclick="window.location.href='{{ Auth::check() && Auth::user()->role !== \App\Enums\UserRole::PELAPOR ? route('support.profil.saya') : route('profil.instansi') }}'">
            @if(Auth::check() && Auth::user()->role !== \App\Enums\UserRole::PELAPOR)
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                {{ __('messages.profil_saya') }}
            @else
                <img src="{{ asset('company.png') }}" alt="Company" style="width: 18px; height: 18px; object-fit: contain; vertical-align: middle; filter: brightness(0) invert(1); opacity: 0.9;">
                {{ __('messages.profil_koperasi') }}
            @endif
        </button>
        <button onclick="window.location.href='{{ route('pengaturan') }}'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            {{ __('messages.pengaturan') }}
        </button>
        @if(Auth::check() && Auth::user()->role === \App\Enums\UserRole::PELAPOR)
        <button onclick="window.location.href='{{ route('pelapor.bantuan') }}'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            {{ __('messages.title_pusat_bantuan') ?? 'Pusat Bantuan' }}
        </button>
        @endif
        <form action="{{ route('logout') }}" method="POST" style="margin:0; border-top:1px solid var(--line); padding-top:5px; margin-top:5px;">
            @csrf
            <button type="submit" class="danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                {{ __('messages.keluar_singkat') }}
            </button>
        </form>
    </div>
</div>

<div class="mobile-bottom-nav">
    @if(Auth::check() && in_array(Auth::user()->role, [\App\Enums\UserRole::SUPPORT, \App\Enums\UserRole::SUPERADMIN]))
        <a href="{{ route('support.dashboard') }}" class="{{ request()->routeIs('support.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('analysis.png') }}" alt="Dashboard">
            <span>{{ __('messages.dashboard') }}</span>
        </a>
        <a href="{{ route('support.master-data.index') }}" class="{{ request()->routeIs('support.master-data.*') ? 'active' : '' }}">
            <img src="{{ asset('folder.png') }}" alt="Master data">
            <span>{{ __('messages.master_data') }}</span>
        </a>
        <a href="#" onclick="toggleMobileRecapMenu(event)" class="{{ request()->routeIs('support.recap*') ? 'active' : '' }}" id="mobile-recap-btn">
            <img src="{{ asset('file.png') }}" alt="Rekap">
            <span>{{ __('messages.recap_laporan') ?? 'Rekap' }}</span>
        </a>
        <a href="{{ route('implementasi.index') }}" class="{{ request()->routeIs('implementasi.*') ? 'active' : '' }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px; margin-bottom: 2px; opacity: 0.8;"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            <span>{{ __('messages.monitoring_koperasi') ?? 'Monitoring' }}</span>
        </a>
        @if(Auth::user()->role === \App\Enums\UserRole::SUPERADMIN)
        <a href="{{ route('superadmin.pengguna') }}" class="{{ request()->routeIs('superadmin.pengguna') ? 'active' : '' }}">
            <img src="{{ asset('group.png') }}" alt="Pengguna">
            <span>{{ __('messages.manajemen_pengguna') ?? 'Pengguna' }}</span>
        </a>
        @endif
        <a href="{{ route('pengaturan') }}" class="{{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <img src="{{ asset('setting.png') }}" alt="Atur">
            <span>{{ __('messages.pengaturan') }}</span>
        </a>
    @else
        <!-- Menu Pelapor -->
        <a href="{{ route('pelapor.dashboard') }}" class="{{ request()->routeIs('pelapor.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('analysis.png') }}" alt="Dashboard">
            <span>{{ __('messages.dashboard') }}</span>
        </a>
        <a href="{{ route('pelapor.riwayat') }}" class="{{ request()->routeIs('pelapor.riwayat') ? 'active' : '' }}">
            <img src="{{ asset('file.png') }}" alt="Riwayat">
            <span>{{ __('messages.riwayat_lengkap') ?? 'Riwayat' }}</span>
        </a>
        <a href="{{ route('pengaturan') }}" class="{{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <img src="{{ asset('setting.png') }}" alt="Atur">
            <span>{{ __('messages.pengaturan') }}</span>
        </a>
    @endif
</div>
@endif

<!-- Mobile Rekap Dropdown -->
<div class="mobile-profile-overlay" id="mobile-recap-overlay" onclick="toggleMobileRecapMenu(event)"></div>
<div class="mobile-profile-popup" id="mobile-recap-popup" style="bottom: 74px; top: auto; right: auto; left: 50%; transform: translateX(-50%); width: 260px;">
    <div class="mp-head">
        <strong>{{ __('messages.recap_laporan') }}</strong>
    </div>
    <div class="mp-body">
        <a href="{{ route('support.recap.diagram') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: none; background: transparent; border-radius: 8px; font-size: 13.5px; color: var(--ink); text-align: left; text-decoration: none; font-weight: 600;">
            <span class="sub-dot" style="width: 6px; height: 6px; background: #3b82f6; border-radius: 50%;"></span> {{ __('messages.recap_diagram') }}
        </a>
        <a href="{{ route('support.recap.table') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: none; background: transparent; border-radius: 8px; font-size: 13.5px; color: var(--ink); text-align: left; text-decoration: none; font-weight: 600;">
            <span class="sub-dot" style="width: 6px; height: 6px; background: #3b82f6; border-radius: 50%;"></span> {{ __('messages.recap_support') }}
        </a>
        <a href="{{ route('support.recap.history-pic') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: none; background: transparent; border-radius: 8px; font-size: 13.5px; color: var(--ink); text-align: left; text-decoration: none; font-weight: 600;">
            <span class="sub-dot" style="width: 6px; height: 6px; background: #3b82f6; border-radius: 50%;"></span> {{ __('messages.recap_history_pic') }}
        </a>
        <a href="{{ route('support.recap.template-surat') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: none; background: transparent; border-radius: 8px; font-size: 13.5px; color: var(--ink); text-align: left; text-decoration: none; font-weight: 600;">
            <span class="sub-dot" style="width: 6px; height: 6px; background: #3b82f6; border-radius: 50%;"></span> {{ __('messages.recap_template_surat') }}
        </a>
    </div>
</div>

<script>
    function toggleMobileRecapMenu(e) {
        if(e) e.stopPropagation();
        e.preventDefault();
        const overlay = document.getElementById('mobile-recap-overlay');
        const popup = document.getElementById('mobile-recap-popup');
        if(overlay) overlay.classList.toggle('active');
        if(popup) popup.classList.toggle('active');
    }

    // Toggle Mobile Profile Popover
    function toggleMobileProfile(e) {
        if(e) e.stopPropagation();
        const overlay = document.getElementById('mobile-profile-overlay');
        const popup = document.getElementById('mobile-profile-popup');
        if(overlay) overlay.classList.toggle('active');
        if(popup) popup.classList.toggle('active');
    }
    function toggleProfilePopover(e) {
        e.stopPropagation();
        const pop = document.getElementById('profile-popover');
        if(pop) pop.classList.toggle('active');
    }

    // Close Profile Popover when clicking outside
    document.addEventListener('click', function(e) {
        const pop = document.getElementById('profile-popover');
        if(pop && pop.classList.contains('active') && !e.target.closest('.sidebar-foot')) {
            pop.classList.remove('active');
        }
    });

    // Generic Modal functions
    function openModal(id) {
        const modal = document.getElementById(id);
        if(modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
        }
    }
    
    function closeModal(id) {
        const modal = document.getElementById(id);
        if(modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    }

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismiss');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade-out-up');
                
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 600);
            }, 5000);
        });
    });

    // Enable Drag-to-Scroll for horizontal tables
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.table-scroll-wrapper, .md-sidebar');
        let isDown = false;
        let startX;
        let scrollLeft;
        let isDragging = false;

        sliders.forEach(slider => {
            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                isDragging = false;
                slider.style.cursor = 'grabbing';
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            slider.addEventListener('mouseleave', () => {
                isDown = false;
                slider.style.cursor = '';
            });
            slider.addEventListener('mouseup', () => {
                isDown = false;
                slider.style.cursor = '';
            });
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 1.5;
                if (Math.abs(walk) > 5) {
                    isDragging = true;
                    e.preventDefault();
                }
                slider.scrollLeft = scrollLeft - walk;
            });
            
            // Mencegah klik tombol jika pengguna sedang menggeser tabel
            slider.addEventListener('click', (e) => {
                if (isDragging) {
                    e.preventDefault();
                    e.stopPropagation();
                    isDragging = false;
                }
            }, true);
        });
    });

    function toggleSidebarSubmenu(btn) {
        const parent = btn.closest('.sidebar-dropdown');
        const submenu = parent.querySelector('.sidebar-submenu');
        const isOpen = parent.classList.contains('open');
        
        if (isOpen) {
            parent.classList.remove('open');
            submenu.style.display = 'none';
        } else {
            parent.classList.add('open');
            submenu.style.display = 'flex';
            submenu.style.flexDirection = 'column';
        }
    }

    function toggleNotifDropdown(e) {
        e.preventDefault();
        const menu = document.getElementById('notif-dropdown-menu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    document.addEventListener('click', function(e) {
        const notifMenu = document.getElementById('notif-dropdown-menu');
        const notifBtn = document.querySelector('.notif-btn');
        if (notifMenu && notifBtn) {
            if (!notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
                notifMenu.style.display = 'none';
            }
        }
    });
    window.activeTomSelectCallback = null;
    window.activeTomSelectInstance = null;

    function initSingleTomSelect(el) {
        if (!el || el.tomselect) return;
        const isAllowCreate = el.classList.contains('allow-create');
        new TomSelect(el, {
            create: isAllowCreate ? function(input, callback) {
                window.activeTomSelectCallback = callback;
                window.activeTomSelectInstance = this;
                document.getElementById('quick_nama_instansi').value = input;
                document.getElementById('quick_alamat_instansi').value = '';
                document.getElementById('quick_no_telp_instansi').value = '';
                const modal = document.getElementById('modalTambahKoperasiCepat');
                if (modal) modal.style.display = 'flex';
                return false;
            } : false,
            createFilter: function(input) { return input.trim().length >= 1; },
            render: {
                option_create: function(data, escape) {
                    return '<div class="create" style="padding: 10px 14px; background: #eff6ff; color: #2563eb; font-weight: 600; cursor: pointer; border-top: 1px dashed #bfdbfe;">+ Tambah "<strong>' + escape(data.input) + '</strong>" & Lengkapi Detail...</div>';
                }
            },
            sortField: { field: "text", direction: "asc" },
            placeholder: el.getAttribute('placeholder') || 'Pilih atau ketik nama baru...',
            onChange: function(value) {
                if (typeof updateAplikasiOptions === 'function') {
                    updateAplikasiOptions();
                }
            }
        });
    }

    function showGlobalToast(message, isSuccess = true) {
        // Jika modal Add Implementation Data sedang terbuka, tampilkan notifikasi HANYA di dalam pop-up modal tersebut
        const modalAlert = document.getElementById('modalDataBaruAlert');
        const modalAlertText = document.getElementById('modalDataBaruAlertText');
        const modalDataBaru = document.getElementById('modalDataBaru');
        
        let shownInModal = false;
        if (modalAlert && modalAlertText && modalDataBaru && getComputedStyle(modalDataBaru).display !== 'none') {
            modalAlertText.innerText = message;
            modalAlert.style.display = 'flex';
            modalAlert.classList.remove('animate-fade-out');
            modalAlert.classList.add('animate-fade-up');
            shownInModal = true;
            setTimeout(() => {
                modalAlert.classList.remove('animate-fade-up');
                modalAlert.classList.add('animate-fade-out');
                setTimeout(() => {
                    modalAlert.style.display = 'none';
                    modalAlert.classList.remove('animate-fade-out');
                }, 300);
            }, 4200);
        }

        // Jika notifikasi sudah muncul di dalam pop-up modal, batalkan notifikasi yang di luar/di atas pop up!
        if (shownInModal) return;

        let toast = document.getElementById('globalToast');
        if (!toast) return;

        const msgEl = document.getElementById('globalToastMessage');
        const iconEl = document.getElementById('globalToastIcon');
        msgEl.innerText = message;

        if (isSuccess) {
            toast.style.background = '#10b981';
            toast.style.color = '#ffffff';
            toast.style.boxShadow = '0 10px 25px -5px rgba(16, 185, 129, 0.45)';
            iconEl.style.color = '#ffffff';
            iconEl.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
        } else {
            toast.style.background = '#ef4444';
            toast.style.color = '#ffffff';
            toast.style.boxShadow = '0 10px 25px -5px rgba(239, 68, 68, 0.45)';
            iconEl.style.color = '#ffffff';
            iconEl.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
        }

        toast.style.display = 'flex';
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(-10px)';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 300);
        }, 3500);
    }

    function submitKoperasiCepat(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitKoperasiCepat');
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        const formData = new FormData(document.getElementById('formTambahKoperasiCepat'));

        fetch('{{ route('support.master-data.koperasi.ajax-store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) {
                const msg = (data && data.message) ? data.message : ('Terjadi kesalahan server (' + res.status + ')');
                throw new Error(msg);
            }
            return data;
        })
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Simpan Koperasi';

            if (data && data.success) {
                document.getElementById('modalTambahKoperasiCepat').style.display = 'none';

                // Notifikasi Toast Berada di Atas
                showGlobalToast(data.message || 'Koperasi berhasil ditambahkan!');

                // Skeleton Shimmer Loading pada Dropdown saat kembali
                let targetWrapper = null;
                if (window.activeTomSelectInstance && window.activeTomSelectInstance.wrapper) {
                    targetWrapper = window.activeTomSelectInstance.wrapper;
                    targetWrapper.classList.add('skeleton-updating');
                }

                if (window.activeTomSelectCallback) {
                    window.activeTomSelectCallback({
                        value: data.instansi.instansi_id,
                        text: data.instansi.nama_instansi
                    });
                }
                if (window.activeTomSelectInstance) {
                    window.activeTomSelectInstance.addOption({
                        value: data.instansi.instansi_id,
                        text: data.instansi.nama_instansi
                    });
                    window.activeTomSelectInstance.setValue(data.instansi.instansi_id);
                }

                document.querySelectorAll('select[name="instansi_id"]').forEach(select => {
                    if (select.tomselect && select.tomselect !== window.activeTomSelectInstance) {
                        select.tomselect.addOption({
                            value: data.instansi.instansi_id,
                            text: data.instansi.nama_instansi
                        });
                    }
                });

                if (targetWrapper) {
                    setTimeout(() => {
                        targetWrapper.classList.remove('skeleton-updating');
                    }, 650);
                }

                window.activeTomSelectCallback = null;
                window.activeTomSelectInstance = null;
            } else {
                showGlobalToast((data && data.message) ? data.message : 'Gagal menyimpan koperasi.', false);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Simpan Koperasi';
            showGlobalToast(err.message || 'Terjadi kesalahan saat menyimpan koperasi.', false);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.searchable-select').forEach(function(el) {
            initSingleTomSelect(el);
        });
    });

    function openUniversalPreview(url, extension, originalName = 'File') {
        const modal = document.getElementById('universalPreviewModal');
        const body = document.getElementById('universalPreviewBody');
        const downloadBtn = document.getElementById('universalPreviewDownloadBtn');
        const loading = document.getElementById('universalPreviewLoading');
        
        if (downloadBtn) {
            downloadBtn.href = url;
            downloadBtn.download = originalName;
        }
        
        // Reset modal body
        Array.from(body.children).forEach(child => {
            if (child.id !== 'universalPreviewLoading') {
                body.removeChild(child);
            }
        });
        
        if (loading) loading.style.display = 'block';
        if (modal) modal.style.display = 'flex';
        
        // Safety timeout to prevent infinite spinner/buffering
        const loadingTimeout = setTimeout(() => {
            if (loading) loading.style.display = 'none';
        }, 1200);

        const hideLoading = () => {
            clearTimeout(loadingTimeout);
            if (loading) loading.style.display = 'none';
        };

        const ext = (extension || '').toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            const img = document.createElement('img');
            img.src = url;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            img.style.objectFit = 'contain';
            img.onload = hideLoading;
            img.onerror = () => {
                hideLoading();
                body.innerHTML += '<div style="color: #ef4444; padding: 20px; text-align: center;">{{ __("messages.gagal_memuat_gambar") ?? "Gagal memuat gambar. File mungkin tidak ditemukan atau tautan telah kedaluwarsa." }}</div>';
            };
            body.appendChild(img);
        } else if (ext === 'pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.border = 'none';
            iframe.onload = hideLoading;
            body.appendChild(iframe);
        } else if (['mp4', 'webm', 'mov', 'avi'].includes(ext)) {
            const video = document.createElement('video');
            video.src = url;
            video.controls = true;
            video.autoplay = true;
            video.style.maxWidth = '100%';
            video.style.maxHeight = '100%';
            video.onloadeddata = hideLoading;
            video.oncanplay = hideLoading;
            video.onerror = () => {
                hideLoading();
                body.innerHTML += '<div style="color: #ef4444; padding: 20px; text-align: center;">{{ __("messages.format_video_tidak_didukung") ?? "Format video tidak dapat diputar langsung di browser ini. Silakan klik tombol Unduh File di bawah." }}</div>';
            };
            body.appendChild(video);
        } else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'].includes(ext)) {
            hideLoading();
            const docContainer = document.createElement('div');
            docContainer.style.cssText = 'display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 24px; box-sizing: border-box; text-align: center;';
            
            docContainer.innerHTML = `
                <div style="background: #ffffff; border: 1.5px dashed #3b82f6; border-radius: 16px; padding: 32px 24px; max-width: 480px; width: 100%; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.1);">
                    <div style="width: 64px; height: 64px; background: #dbeafe; color: #2563eb; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">{{ __("messages.dokumen") ?? "Dokumen" }} ${ext.toUpperCase()}</h4>
                    <p style="margin: 0 0 20px 0; font-size: 0.85rem; color: #64748b; line-height: 1.5; word-break: break-all;">${originalName}</p>
                    <a href="${url}" target="_blank" download="${originalName}" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; width: 100%; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        {{ __("messages.unduh_buka_dokumen") ?? "Unduh / Buka Dokumen" }}
                    </a>
                </div>
            `;
            body.appendChild(docContainer);
        } else {
            hideLoading();
            body.innerHTML += '<div style="padding: 20px; text-align: center; color: var(--text-muted);"><p>{{ __("messages.preview_format_not_supported") ?? "Format file ini tidak mendukung pratinjau langsung. Silakan unduh file melalui tombol di bawah." }}</p></div>';
        }
    }

    function closeUniversalPreview() {
        document.getElementById('universalPreviewModal').style.display = 'none';
        const body = document.getElementById('universalPreviewBody');
        Array.from(body.children).forEach(child => {
            if (child.id !== 'universalPreviewLoading') {
                body.removeChild(child);
            }
        });
    }
</script>

<!-- Universal Preview Modal -->
<div class="overlay" id="universalPreviewModal" style="display: none; align-items: center; justify-content: center; z-index: 999999; background: rgba(0,0,0,0.85);">
    <div class="modal w-lg" style="max-width: 900px; width: 90%; height: 85vh; display: flex; flex-direction: column; padding: 0;">
        <div class="modal-head" style="padding: 16px 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem;">{{ __('messages.preview_dokumen') ?? 'Preview Dokumen' }}</h3>
            <button class="close-btn" onclick="closeUniversalPreview()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="modal-body" id="universalPreviewBody" style="flex: 1; padding: 0; display: flex; align-items: center; justify-content: center; background: #f3f4f6; overflow: hidden; position: relative;">
            <div id="universalPreviewLoading" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: var(--text-muted);">
                Loading...
            </div>
        </div>
        <div class="modal-foot" style="padding: 16px 24px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
            <a id="universalPreviewDownloadBtn" href="#" target="_blank" class="btn btn-primary" style="text-decoration: none;">{{ __('messages.btn_unduh_file') ?? 'Unduh File' }}</a>
            <button class="btn btn-ghost" onclick="closeUniversalPreview()">{{ __('messages.tutup') ?? 'Tutup' }}</button>
        </div>
    </div>
</div>
<!-- Modal Quick Add Koperasi -->
<div class="modal-overlay" id="modalTambahKoperasiCepat" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999999; align-items: center; justify-content: center;">
    <div class="modal-container" style="background: var(--paper, #ffffff); width: 90%; max-width: 480px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid var(--line, #e2e8f0);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--line, #e2e8f0); padding-bottom: 12px;">
            <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink, #1e293b); font-weight: 700;">Tambah Detail Koperasi Baru</h3>
            <button type="button" onclick="document.getElementById('modalTambahKoperasiCepat').style.display='none'" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>
        <form id="formTambahKoperasiCepat" onsubmit="submitKoperasiCepat(event)">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ink); margin-bottom: 6px;">Nama Koperasi <span style="color: #ef4444;">*</span></label>
                <input type="text" id="quick_nama_instansi" name="nama_instansi" required class="form-control" placeholder="Nama Koperasi / Instansi" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); font-size: 0.9rem; background: var(--paper-sunken); color: var(--ink);">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ink); margin-bottom: 6px;">Alamat (Opsional)</label>
                <textarea id="quick_alamat_instansi" name="alamat" rows="3" class="form-control" placeholder="Alamat lengkap koperasi..." style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); font-size: 0.9rem; background: var(--paper-sunken); color: var(--ink); font-family: inherit;"></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--ink); margin-bottom: 6px;">No. Telepon / WhatsApp (Opsional)</label>
                <input type="text" id="quick_no_telp_instansi" name="no_telp" class="form-control" placeholder="Nomor telepon..." style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); font-size: 0.9rem; background: var(--paper-sunken); color: var(--ink);">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--line, #e2e8f0); padding-top: 16px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalTambahKoperasiCepat').style.display='none'" style="padding: 9px 18px; border-radius: 8px; font-weight: 600;">Batal</button>
                <button type="submit" id="btnSubmitKoperasiCepat" class="btn btn-primary" style="padding: 9px 18px; border-radius: 8px; font-weight: 600; background: #2563eb; color: white; border: none; cursor: pointer;">Simpan Koperasi</button>
            </div>
        </form>
    </div>
</div>
<!-- Global Toast Notification (Hijau Terang di Sekitar Top Modal) -->
<div id="globalToast" style="display: none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-10px); z-index: 99999999; background: #10b981; color: #ffffff; padding: 12px 24px; border-radius: 30px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.45); align-items: center; gap: 10px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-size: 0.9rem; font-weight: 600;">
    <div id="globalToastIcon" style="display: flex; align-items: center; justify-content: center; color: #ffffff;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    </div>
    <span id="globalToastMessage">Berhasil disimpan!</span>
</div>
</body>
</html>
