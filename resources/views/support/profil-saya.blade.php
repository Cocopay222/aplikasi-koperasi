@extends('layouts.app')

@section('page_title', 'Profil Saya')
@section('page_subtitle', 'SAKTI Desk')

@section('sidebar_menu')
    <a href="{{ route('support.dashboard') }}">
        <span class="ic"><img src="{{ asset('analysis.png') }}" alt=""></span> {{ __('messages.beranda_support') }}
    </a>
    <a href="{{ route('support.recap') }}">
        <span class="ic"><img src="{{ asset('file.png') }}" alt=""></span> {{ __('messages.rekapitulasi') }}
    </a>
    <a href="{{ route('support.master-data.index') }}">
        <span class="ic"><img src="{{ asset('folder.png') }}" alt=""></span> {{ __('messages.master_data') }}
    </a>
@endsection

@section('content')
<div class="pelapor-panel">

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
            background: var(--sage, #22c55e);
        }
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            background: var(--paper-raised, #fff);
            border-radius: 50%;
            transition: transform 0.3s ease;
        }
        .toggle-switch:checked::after {
            transform: translateX(20px);
        }
    </style>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- SKELETON LOADING STATE                      --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="skeleton-wrap" id="skeleton-loading">
        <div class="skel" style="width: 180px; height: 36px; border-radius: 8px; margin-bottom: 24px;"></div>
        <div class="skel-panel" style="padding: 30px; max-width: 100%;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom: 24px;">
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom: 8px;">
                        <div class="skel" style="width: 28px; height: 28px; border-radius: 6px;"></div>
                        <div class="skel" style="width: 180px; height: 24px; border-radius: 4px;"></div>
                    </div>
                    <div class="skel" style="width: 280px; height: 14px; border-radius: 4px;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 24px;">
                <div class="skel" style="width: 100px; height: 12px; margin-bottom:6px; border-radius: 4px;"></div>
                <div class="skel" style="width: 240px; height: 18px; border-radius: 4px;"></div>
            </div>
            <div style="margin-bottom: 24px;">
                <div class="skel" style="width: 100px; height: 12px; margin-bottom:6px; border-radius: 4px;"></div>
                <div class="skel" style="width: 400px; height: 16px; border-radius: 4px; margin-bottom: 4px;"></div>
                <div class="skel" style="width: 320px; height: 16px; border-radius: 4px;"></div>
            </div>
            <div style="margin-bottom: 32px;">
                <div class="skel" style="width: 100px; height: 12px; margin-bottom:6px; border-radius: 4px;"></div>
                <div class="skel" style="width: 180px; height: 16px; border-radius: 4px;"></div>
            </div>
            
            <div style="border-top: 1px solid var(--line); padding-top: 20px;">
                <div class="skel" style="width: 160px; height: 36px; border-radius: 8px;"></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- ACTUAL CONTENT                              --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="content-wrap" id="actual-content" style="display: none;">
        
        <div class="fade-up" style="margin-bottom: 24px; animation-delay: 0.1s;">
            <a href="{{ route('support.dashboard') }}" class="btn btn-ghost" style="padding: 8px 16px; border: 1px solid var(--line); background: var(--paper-raised);">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:inline-block; margin-right: 6px; vertical-align:-2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('messages.kembali_ke_dashboard') }}
            </a>
        </div>

        <!-- HEADER -->
        <div class="fade-up" style="margin-bottom: 24px; animation-delay: 0.12s;">
            <div style="font-size: 11px; letter-spacing: 1px; color: var(--ink-soft); text-transform: uppercase; margin-bottom: 4px; font-weight: 600;">
                TIM SUPPORT &middot; {{ Auth::user()->nama }}
            </div>
            <h2 style="margin: 0; font-size: 28px; font-weight: 700; color: var(--ink);">
                Profil Saya
            </h2>
        </div>
        
        @if(session('success'))
            <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.14s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); transition: opacity 0.6s ease, transform 0.6s ease;">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
            </div>
        @endif
        
        @if($errors->any())
            <div id="error-alert" class="alert-dismiss fade-up" style="animation-delay: 0.14s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: #fee2e2; color: #dc2626; border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(220, 38, 38, 0.2);">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" onclick="document.getElementById('error-alert').style.display='none'" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
            </div>
        @endif

        <!-- MAIN PROFILE PANEL -->
        <div class="panel fade-up" style="padding: 30px; max-width: 100%; animation-delay: 0.15s; margin-bottom: 24px;">



            <!-- VIEW MODE -->
            <div id="view-mode" class="fade-up" style="animation-delay: 0.25s;">
                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.nama_lengkap') }}</label>
                    <h4 style="font-size: calc(16px * var(--text-scale, 1)); color: var(--ink); margin: 0;">{{ Auth::user()->nama ?? '-' }}</h4>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.nik_id_agen') }}</label>
                    <p class="mono" style="color: var(--ink); line-height: 1.6; margin: 0; font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->nik ?? '-' }}</p>
                </div>
                <div style="margin-bottom: 32px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.no_whatsapp_hp') }}</label>
                    <div class="mono" style="color: var(--ink); font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->whatsapp ?? '-' }}</div>
                </div>
                <div style="margin-bottom: 32px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.alamat_email') }}</label>
                    <div class="mono" style="color: var(--ink); font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->email ?? '-' }}</div>
                </div>
                
                <div style="border-top: 1px solid var(--line); padding-top: 20px;">
                    <button type="button" class="btn btn-ghost" onclick="toggleEditMode(true)" style="padding: 9px 18px;">
                        <span class="ic"><img src="{{ asset('edit.png') }}" alt="Edit" style="width: 14px; height: 14px; object-fit: contain; vertical-align: middle; margin-right: 4px; margin-top: -2px;"></span> Edit Data Diri
                    </button>
                </div>
            </div>

            <!-- EDIT MODE -->
            <div id="edit-mode" style="display: none;">
                <form action="{{ route('support.profil.saya.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Avatar Upload Section -->
                    <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 28px;">
                        @php
                            $nama = Auth::user()->nama ?? 'User';
                            $initials = collect(explode(' ', $nama))->map(function($w){return strtoupper(substr($w,0,1));})->take(2)->join('');
                        @endphp
                        <div style="position: relative; width: 100px; height: 100px; border-radius: 50%; border: 3px solid var(--line); background: var(--paper-sunken); overflow: hidden; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" onclick="document.getElementById('avatar-input').click()">
                            @if(Auth::user()->avatar)
                                <img id="avatar-preview" src="{{ asset('storage/' . Auth::user()->avatar) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            @else
                                <img id="avatar-preview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                <span id="avatar-placeholder" style="font-size: 36px; font-weight: bold; color: var(--ink-soft);">{{ $initials }}</span>
                            @endif
                            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.6); padding: 4px 0; text-align: center; color: white; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">
                                {{ __('messages.ubah_foto') }}
                            </div>
                        </div>
                        <input type="file" name="avatar" id="avatar-input" style="display: none;" accept="image/*" onchange="previewAvatar(this)">
                        <div style="font-size: 11px; color: var(--ink-soft); margin-top: 8px;">{{ __('messages.maks_2mb') }}</div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        <div class="field">
                            <label>{{ __('messages.nama_lengkap') }}</label>
                            <input type="text" name="nama" value="{{ Auth::user()->nama }}" required style="background: var(--paper-sunken);">
                        </div>
                        <div class="field">
                            <label>{{ __('messages.nik_id_agen') }}</label>
                            <input type="text" name="nik" value="{{ Auth::user()->nik }}" placeholder="Contoh: SKK-EMP-0417" style="background: var(--paper-sunken);">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        <div class="field">
                            <label>{{ __('messages.no_whatsapp_hp') }}</label>
                            <input type="text" name="whatsapp" value="{{ Auth::user()->whatsapp }}" placeholder="Contoh: +62 812 3300 4455" style="background: var(--paper-sunken);">
                        </div>
                        <div class="field">
                            <label>{{ __('messages.alamat_email') }}</label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}" required style="background: var(--paper-sunken);">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line);">
                        <button type="button" class="btn btn-ghost" onclick="toggleEditMode(false)" style="padding: 10px 20px;">{{ __('messages.batal') }}</button>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">{{ __('messages.simpan_perubahan') }}</button>
                    </div>
                </form>





            </div>
        </div>



        <script>
            function toggleEditMode(showEdit) {
                document.getElementById('view-mode').style.display = showEdit ? 'none' : 'block';
                document.getElementById('edit-mode').style.display = showEdit ? 'block' : 'none';
                
                const kartu = document.getElementById('kartu-identitas');
                if (kartu) kartu.style.display = showEdit ? 'none' : 'block';
            }

            function previewAvatar(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('avatar-preview');
                        const placeholder = document.getElementById('avatar-placeholder');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const skeleton = document.getElementById('skeleton-loading');
                const content  = document.getElementById('actual-content');
                
                if(skeleton && content) {
                    setTimeout(function () {
                        skeleton.style.display = 'none';
                        content.style.display = 'block';
                        content.classList.add('loaded');
                    }, 400);
                }
            });
        </script>
        
    </div>
</div>
@endsection
