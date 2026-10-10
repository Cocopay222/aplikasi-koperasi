@extends('layouts.app')

@section('page_title', __('messages.profil_lengkap_pelapor'))
@section('page_subtitle', 'internal.ptskk.id')

@section('content')
<div class="pelapor-panel">
    {{-- SKELETON LOADING STATE --}}
    <div class="skeleton-wrap" id="skeleton-loading">
        <div class="skel-panel" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; display: flex; gap: 24px;">
            <div class="skel" style="width: 120px; height: 120px; border-radius: 50%;"></div>
            <div style="flex: 1;">
                <div class="skel" style="width: 250px; height: 32px; border-radius: 6px; margin-bottom: 12px;"></div>
                <div class="skel" style="width: 180px; height: 16px; border-radius: 4px; margin-bottom: 24px;"></div>
                <div class="skel" style="width: 100%; height: 80px; border-radius: 8px;"></div>
            </div>
        </div>
    </div>

    {{-- ACTUAL CONTENT --}}
    <div class="content-wrap" id="actual-content" style="display: none;">
        <div class="glass-panel fade-up" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 3rem 2rem; margin-bottom: 2rem; animation-delay: 0.1s;">
            
            <div style="display: flex; align-items: flex-start; gap: 32px; flex-wrap: wrap;">
                <!-- Avatar Section -->
                <div style="flex-shrink: 0; text-align: center;">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid var(--paper-sunken); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    @else
                        @php
                            $nameParts = explode(' ', $user->nama);
                            $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                        @endphp
                        <div style="width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; border: 4px solid var(--paper-sunken); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            {{ $initials }}
                        </div>
                    @endif
                    
                    <div style="margin-top: 16px;">
                        @if($user->is_verified)
                            <span style="background: #dcfce7; color: #16a34a; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> {{ __('messages.terverifikasi') }}
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #dc2626; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> {{ __('messages.belum_diverifikasi') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Profile Details -->
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 1.8rem; color: var(--ink); margin: 0 0 4px 0;">{{ $user->nama }}</h2>
                    <p style="color: var(--text-muted); font-size: 1rem; margin: 0 0 24px 0;">
                        @if($user->posisi)
                            <span style="font-weight: 600; color: var(--primary);">{{ $user->posisi }}</span> &bull; 
                        @endif
                        {{ __('messages.pic_koperasi_pelapor') }}
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; background: var(--paper-sunken); padding: 24px; border-radius: 12px; border: 1px solid var(--line);">
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.email_label') }}</div>
                            <div style="font-weight: 500; color: var(--ink); font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                {{ $user->email }}
                            </div>
                        </div>
                        
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.koperasi_label') }}</div>
                            <div style="font-weight: 500; color: var(--ink); font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                {{ $user->instansi->nama_instansi ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.nomor_telepon') }}</div>
                            <div style="font-weight: 500; color: var(--ink); font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                {{ $user->whatsapp ?? $user->instansi->no_telp ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.bergabung_sejak') }}</div>
                            <div style="font-weight: 500; color: var(--ink); font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                {{ $user->created_at->format('d M Y') }}
                            </div>
                        </div>

                        <div style="grid-column: 1 / -1;">
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">{{ __('messages.alamat_koperasi_label') }}</div>
                            <div style="font-weight: 500; color: var(--ink); font-size: 0.95rem; display: flex; align-items: flex-start; gap: 8px; line-height: 1.5;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6; margin-top: 2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                {{ $user->instansi->alamat ?? __('messages.belum_ada_data_alamat') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--line); display: flex; justify-content: flex-end;">
                <a href="{{ route('support.dashboard') }}" class="btn btn-ghost" style="padding: 10px 20px;">&larr; {{ __('messages.kembali_ke_dashboard') }}</a>
            </div>
        </div>
    </div>
    
    <script>
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
