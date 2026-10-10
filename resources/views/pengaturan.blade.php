@extends('layouts.app')

@section('page_title', __('messages.title_pengaturan'))
@section('page_subtitle', __('messages.subtitle_pengaturan'))

@section('content')
<div class="pelapor-panel">
    
    {{-- SKELETON LOADING STATE --}}
    <div class="skeleton-wrap" id="skeleton-loading">
        <div style="margin-bottom: 32px; display: flex; gap: 10px;">
            <div class="skel" style="width: 150px; height: 36px; border-radius: 9999px;"></div>
            <div class="skel" style="width: 110px; height: 36px; border-radius: 9999px;"></div>
        </div>
        
        <div class="skel-panel" style="padding: 30px; max-width: 100%;">
            <div style="margin-bottom: 24px;">
                <div class="skel" style="width: 150px; height: 24px; border-radius: 6px; margin-bottom: 8px;"></div>
                <div class="skel" style="width: 250px; height: 14px; border-radius: 4px;"></div>
            </div>
            
            <div style="margin-bottom: 24px;">
                <div class="skel" style="width: 120px; height: 14px; border-radius: 4px; margin-bottom: 8px;"></div>
                <div style="display: flex; gap: 8px;">
                    <div class="skel" style="width: 120px; height: 36px; border-radius: 6px;"></div>
                    <div class="skel" style="width: 120px; height: 36px; border-radius: 6px;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 32px;">
                <div class="skel" style="width: 120px; height: 14px; border-radius: 4px; margin-bottom: 8px;"></div>
                <div class="skel" style="width: 200px; height: 36px; border-radius: 6px;"></div>
            </div>

            <div class="skel" style="width: 160px; height: 40px; border-radius: 6px;"></div>
        </div>
    </div>

    {{-- ACTUAL CONTENT --}}
    <div class="content-wrap fade-up" id="actual-content" style="display: none; animation-delay: 0.1s;">
    
    @if(session('success'))
        <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.2s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); transition: opacity 0.6s ease, transform 0.6s ease;">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
        </div>
    @endif
    
    @if($errors->any())
        <div id="error-alert" class="alert-dismiss fade-up" style="animation-delay: 0.2s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #fee2e2; color: #dc2626; border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(220, 38, 38, 0.2);">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" onclick="document.getElementById('error-alert').style.display='none'" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px; align-self: flex-start;">&times;</button>
        </div>
    @endif

    <style>
        .toggle-switch {
            appearance: none;
            width: 40px;
            height: 20px;
            background: #cbd5e1;
            border-radius: 20px;
            position: relative;
            cursor: pointer;
            outline: none;
            transition: background 0.3s ease;
        }
        .toggle-switch:checked {
            background: var(--sage);
        }
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            background: var(--paper-raised);
            border-radius: 50%;
            transition: transform 0.3s ease;
        }
        .toggle-switch:checked::after {
            transform: translateX(20px);
        }
        .pill-filter {
            font-size: calc(13px * var(--text-scale, 1));
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 9999px;
            border: 1px solid var(--line);
            background: var(--paper-raised);
            color: var(--ink-soft);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .pill-filter.active {
            background: var(--indigo);
            color: #fff;
            border-color: var(--indigo);
            font-weight: 600;
        }
        .dark .pill-filter.active, html.dark-mode .pill-filter.active {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
        }
        .icon-mask {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-color: currentColor;
            -webkit-mask-image: var(--mask-url);
            -webkit-mask-size: contain;
            -webkit-mask-repeat: no-repeat;
            mask-image: var(--mask-url);
            mask-size: contain;
            mask-repeat: no-repeat;
            vertical-align: middle;
            opacity: 0.9;
        }
    </style>

    <div style="margin-bottom: 32px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px;">
        <button type="button" class="pill-filter active" id="btn-tampilan" onclick="switchSettingTab('tampilan')"><span class="icon-mask" style="--mask-url: url('{{ asset('application.png') }}');"></span> {{ __('messages.tab_personalisasi') }}</button>
        <button type="button" class="pill-filter" id="btn-bahasa" onclick="switchSettingTab('bahasa')"><span class="icon-mask" style="--mask-url: url('{{ asset('world.png') }}');"></span> {{ __('messages.tab_bahasa') }}</button>
        <button type="button" class="pill-filter" id="btn-akun" onclick="switchSettingTab('akun')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.9;">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <path d="M9 12l2 2 4-4"></path>
            </svg>
            {{ __('messages.keamanan_akun') }}
        </button>
    </div>
    
    <div class="panel" id="panel-bahasa" style="padding: 30px; max-width: 100%; display: none;">
        <div style="margin-bottom: 32px;">
            <h3 style="font-size: calc(20px * var(--text-scale, 1)); display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                <span class="icon-mask" style="--mask-url: url('{{ asset('world.png') }}'); width: 22px; height: 22px;"></span> {{ __('messages.title_bahasa') }}
            </h3>
            <p class="sub" style="margin-bottom:0; font-size: calc(14px * var(--text-scale, 1)); color:var(--ink-soft);">{{ __('messages.subtitle_bahasa') }}</p>
        </div>
        <form action="{{ route('pengaturan.bahasa') }}" method="POST">
            @csrf
            <div style="background: var(--paper-sunken); padding: 20px; border-radius: 12px; border: 1px solid var(--line);">
                <div class="field" style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 12px; border: 1px solid var(--line); border-radius: 8px; background: var(--paper-raised); margin-bottom: 12px; {{ Auth::user()->locale !== 'en' ? 'border-color: var(--primary);' : '' }}">
                        <input type="radio" name="locale" value="id" {{ Auth::user()->locale !== 'en' ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--ink);">{{ __('messages.tab_bahasa') }} Indonesia</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ __('messages.lang_id_desc') }}</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 12px; border: 1px solid var(--line); border-radius: 8px; background: var(--paper-raised); {{ Auth::user()->locale === 'en' ? 'border-color: var(--primary);' : '' }}">
                        <input type="radio" name="locale" value="en" {{ Auth::user()->locale === 'en' ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: var(--ink);">{{ __('messages.lang_en') }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Use the interface in {{ __('messages.lang_en') }}</div>
                        </div>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">{{ __('messages.btn_simpan_pref') }}</button>
            </div>
        </form>
    </div>



    <!-- TAMPILAN PERSONAL PANEL -->
    <div class="panel" id="panel-tampilan-personal" style="padding: 30px; max-width: 100%; margin-top: 0; display: none;">
        <style>
            .segmented-control {
                display: flex;
                background: var(--paper-sunken);
                padding: 4px;
                border-radius: 8px;
                border: 1px solid var(--line);
                gap: 4px;
                margin-bottom: 24px;
            }
            .segmented-control label {
                flex: 1;
                text-align: center;
                padding: 10px 16px;
                cursor: pointer;
                border-radius: 6px;
                font-size: calc(14px * var(--text-scale, 1));
                font-weight: 600;
                color: var(--ink-soft);
                transition: all 0.2s ease;
                border: 1px solid transparent;
                margin: 0;
            }
            .segmented-control input[type="radio"] {
                display: none;
            }
            .segmented-control input[type="radio"]:checked + label {
                background: var(--paper-raised);
                color: var(--brand-primary);
                border-color: rgba(30,59,142,0.15);
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            html.dark-mode .segmented-control input[type="radio"]:checked + label {
                color: #fff;
                border-color: rgba(255,255,255,0.1);
            }
            .icon-mask {
            display: inline-block;
            width: 16px;
            height: 16px;
            background-color: currentColor;
            -webkit-mask-image: var(--mask-url);
            -webkit-mask-size: contain;
            -webkit-mask-repeat: no-repeat;
            mask-image: var(--mask-url);
            mask-size: contain;
            mask-repeat: no-repeat;
            vertical-align: middle;
            opacity: 0.9;
        }
    </style>

        <div style="margin-bottom: 24px;">
            <h3 style="font-size: calc(18px * var(--text-scale, 1)); display:flex; align-items:center; gap:8px; margin-bottom: 6px; color: var(--ink);">
                <span style="display:inline-block; width: 20px; height: 20px; background-color: var(--ink); -webkit-mask-image: url('{{ asset('application.png') }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('application.png') }}'); mask-size: contain; mask-repeat: no-repeat;"></span> {{ __('messages.tab_personalisasi') }}
            </h3>
            <p class="sub" style="margin-bottom:0; font-size: calc(14px * var(--text-scale, 1)); color:var(--ink-soft);">{{ __('messages.personalisasi_desc') }}</p>
        </div>

        <div>
            <label style="display:block; font-size: calc(13px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:8px;">{{ __('messages.mode_tema') }}</label>
            <div class="segmented-control" id="theme-control">
                <input type="radio" name="theme" id="theme-light" value="light">
                <label for="theme-light">
                    <span style="display:inline-block; width: 15px; height: 15px; background-color: currentColor; vertical-align: text-bottom; margin-right: 4px; -webkit-mask-image: url('{{ asset('light-mode.png') }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('light-mode.png') }}'); mask-size: contain; mask-repeat: no-repeat;"></span> Light Mode
                </label>
                
                <input type="radio" name="theme" id="theme-dark" value="dark">
                <label for="theme-dark">
                    <span style="display:inline-block; width: 15px; height: 15px; background-color: currentColor; vertical-align: text-bottom; margin-right: 4px; -webkit-mask-image: url('{{ asset('night-mode.png') }}'); -webkit-mask-size: contain; -webkit-mask-repeat: no-repeat; mask-image: url('{{ asset('night-mode.png') }}'); mask-size: contain; mask-repeat: no-repeat;"></span> Dark Mode
                </label>
            </div>
            
            <label style="display:block; font-size: calc(13px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:8px;">{{ __('messages.ukuran_teks') }}</label>
            <div class="segmented-control" id="font-control" style="margin-bottom: 8px;">
                <input type="radio" name="font_size" id="font-small" value="small">
                <label for="font-small">{{ __('messages.kecil') }}</label>
                
                <input type="radio" name="font_size" id="font-medium" value="medium">
                <label for="font-medium">{{ __('messages.sedang') }}</label>

                <input type="radio" name="font_size" id="font-large" value="large">
                <label for="font-large">{{ __('messages.besar') }}</label>
            </div>
            <div class="helper" style="margin-bottom: 32px;">{{ __('messages.membantu_anggota') }}</div>

            <button type="button" onclick="applyPersonalization()" class="btn btn-amber" style="padding: 10px 24px; font-weight: 600; background: #e11d48; color: white;">
                {{ __('messages.terapkan_tampilan') }}
            </button>
        </div>
    </div>

    <!-- PANEL KEAMANAN AKUN -->
    <div class="panel" id="panel-akun" style="padding: 30px; max-width: 100%; display: none;">
        <div style="margin-bottom: 32px;">
            <h3 style="font-size: calc(20px * var(--text-scale, 1)); display:flex; align-items:center; gap:8px; margin-bottom:6px; color: var(--ink);">
                {{ __('messages.keamanan_akun') }}
            </h3>
            <p class="sub" style="margin-bottom:0; font-size: calc(14px * var(--text-scale, 1)); color:var(--ink-soft);">
                {{ __('messages.keamanan_akun_desc') }}
            </p>
        </div>

        <form action="{{ route('profil.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="field" style="margin-bottom: 18px;">
                <label>{{ __('messages.kata_sandi_saat_ini') }}</label>
                <input type="password" name="current_password" required style="background: var(--paper-sunken);">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div class="field">
                    <label>{{ __('messages.kata_sandi_baru') }}</label>
                    <input type="password" name="password" required style="background: var(--paper-sunken);">
                </div>
                <div class="field">
                    <label>{{ __('messages.konfirmasi_kata_sandi') }}</label>
                    <input type="password" name="password_confirmation" required style="background: var(--paper-sunken);">
                </div>
            </div>
            <button type="submit" class="btn btn-ghost" style="border: 1px solid var(--line); background: var(--paper-sunken);">{{ __('messages.ubah_password') }}</button>
        </form>
    </div>

    </div> <!-- Close actual-content -->
</div> <!-- Close pelapor-panel -->

<script>
    // Initialize radio buttons from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        // Skeleton Loading Transition
        const skeleton = document.getElementById('skeleton-loading');
        const content  = document.getElementById('actual-content');
        if(skeleton && content) {
            setTimeout(function () {
                skeleton.style.display = 'none';
                content.style.display = 'block';
                content.classList.add('loaded');
            }, 800);
        }

        // Initialize radio buttons from localStorage
        var currentTheme = localStorage.getItem('personal_theme') || 'light';
        var currentFont = localStorage.getItem('personal_font_size') || 'medium';
        
        var themeRadio = document.querySelector('input[name="theme"][value="' + currentTheme + '"]');
        if (themeRadio) themeRadio.checked = true;
        
        var fontRadio = document.querySelector('input[name="font_size"][value="' + currentFont + '"]');
        if (fontRadio) fontRadio.checked = true;

        // Initialize active settings tab
        var activeTab = localStorage.getItem('settings_active_tab') || 'tampilan';
        if (activeTab === 'akun') {
            activeTab = 'tampilan'; // Fallback if user had 'akun' saved
        }
        switchSettingTab(activeTab);
    });

    function switchSettingTab(tab) {
        var tabs = ['tampilan', 'bahasa', 'akun'];
        tabs.forEach(function(t) {
            var panel = document.getElementById('panel-' + t);
            if (!panel && t === 'tampilan') panel = document.getElementById('panel-tampilan-personal');
            
            var btn = document.getElementById('btn-' + t);
            
            if (panel && btn) {
                if (tab === t) {
                    panel.style.display = 'block';
                    btn.classList.add('active');
                } else {
                    panel.style.display = 'none';
                    btn.classList.remove('active');
                }
            }
        });
        localStorage.setItem('settings_active_tab', tab);
    }

    function applyPersonalization() {
        var theme = document.querySelector('input[name="theme"]:checked').value;
        var font = document.querySelector('input[name="font_size"]:checked').value;
        
        // Save to localStorage
        localStorage.setItem('personal_theme', theme);
        localStorage.setItem('personal_font_size', font);
        
        // Apply Theme
        if (theme === 'dark') {
            document.documentElement.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark-mode');
        }
        
        // Apply Font Size
        document.documentElement.classList.remove('text-small', 'text-medium', 'text-large');
        if (font !== 'medium') {
            document.documentElement.classList.add('text-' + font);
        }

        // Show a brief success alert (optional, using existing alert style)
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert-dismiss fade-up';
        alertDiv.style.cssText = 'position:fixed; top: 20px; right: 20px; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); z-index: 9999; transition: opacity 0.6s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1);';
        alertDiv.innerHTML = '<span>{{ __('messages.berhasil_terapkan') }}</span><button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>';
        document.body.appendChild(alertDiv);
        
        setTimeout(function() {
            alertDiv.style.opacity = '0';
            setTimeout(function() { alertDiv.remove(); }, 600);
        }, 3000);
    }
</script>
</div>
@endsection
