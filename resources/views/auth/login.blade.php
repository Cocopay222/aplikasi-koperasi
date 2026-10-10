@extends('layouts.app')

@section('content')
<div class="login-wrap">
    {{-- ═══════════════════════════════════════════ --}}
    {{-- SKELETON LOADING STATE                      --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="content-wrap" id="skeleton-loading" style="width: 100%;">
        <div class="login-split-layout">
            <!-- Left Column Skeleton -->
            <div class="login-split-left">
                <div class="brand">
                    <div class="skel" style="width: 38px; height: 38px; border-radius: 8px; opacity: 0.2;"></div>
                    <div class="brand-text">
                        <div class="skel" style="width: 250px; height: 16px; margin-bottom: 6px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 200px; height: 10px; opacity: 0.2; border-radius: 4px;"></div>
                    </div>
                </div>
                
                <div style="margin: auto 0;">
                    <!-- H1 Skeleton (3 lines) -->
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px;">
                        <div class="skel" style="width: 95%; max-width: 480px; height: 26px; opacity: 0.2; border-radius: 6px;"></div>
                        <div class="skel" style="width: 90%; max-width: 480px; height: 26px; opacity: 0.2; border-radius: 6px;"></div>
                        <div class="skel" style="width: 50%; max-width: 480px; height: 26px; opacity: 0.2; border-radius: 6px;"></div>
                    </div>
                    
                    <!-- Paragraph Skeleton (8 lines) -->
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div class="skel" style="width: 98%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 95%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 92%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 96%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 94%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 97%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 85%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                        <div class="skel" style="width: 60%; max-width: 480px; height: 12px; opacity: 0.2; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column Skeleton -->
            <div class="login-split-right">
                <div class="login-box" style="margin: 0 auto; max-width: 380px; width: 100%;">
                    <div class="login-panel-card" style="border: 1px solid var(--line); padding: 28px 32px; border-radius: 20px; background: #fff;">
                        <!-- Centered headers -->
                        <div class="skel" style="width: 140px; height: 12px; margin: 0 auto 8px auto; opacity: 0.1; border-radius: 4px;"></div>
                        <div class="skel" style="width: 120px; height: 32px; margin: 0 auto 6px auto; opacity: 0.1; border-radius: 8px;"></div>
                        <div class="skel" style="width: 300px; height: 12px; margin: 0 auto 20px auto; opacity: 0.1; border-radius: 4px;"></div>
                        
                        <!-- Left-aligned inputs -->
                        <div style="margin-bottom: 14px;">
                            <div class="skel" style="width: 50px; height: 12px; margin-bottom: 6px; opacity: 0.1; border-radius: 4px;"></div>
                            <div class="skel" style="width: 100%; height: 42px; opacity: 0.1; border-radius: 8px;"></div>
                        </div>
                        
                        <div style="margin-bottom: 18px;">
                            <div class="skel" style="width: 80px; height: 12px; margin-bottom: 6px; opacity: 0.1; border-radius: 4px;"></div>
                            <div class="skel" style="width: 100%; height: 42px; opacity: 0.1; border-radius: 8px;"></div>
                        </div>
                        
                        <!-- Checkbox and Forgot Password line -->
                        <div style="display: flex; justify-content: space-between; margin-bottom: 18px;">
                            <div class="skel" style="width: 90px; height: 12px; opacity: 0.1; border-radius: 4px;"></div>
                            <div class="skel" style="width: 110px; height: 12px; opacity: 0.1; border-radius: 4px;"></div>
                        </div>

                        <!-- Button -->
                        <div class="skel" style="width: 100%; height: 44px; opacity: 0.1; border-radius: 8px; margin-bottom: 20px;"></div>
                        
                        <!-- Register Link -->
                        <div class="skel" style="width: 220px; height: 12px; margin: 0 auto; opacity: 0.1; border-radius: 4px;"></div>
                    </div>
                </div>

                <div class="skel" style="width: 200px; height: 14px; margin: 14px auto 0 auto; opacity: 0.1; border-radius: 4px;"></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- ACTUAL CONTENT                              --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="content-wrap" id="actual-content" style="width: 100%; display: none;">
        <div class="login-split-layout fade-up" style="animation-delay: 0.1s;">
            <!-- Left Column -->
            <div class="login-split-left">
                <div class="brand">
                    <img src="{{ asset('logo.png') }}" alt="Logo">
                    <div class="brand-text">
                        <h2>PT SAKTI Kinerja Kolaborasindo</h2>
                        <p>{{ __('messages.portal_layanan_dukungan_koperasi') ?? 'Portal Layanan &amp; Dukungan Koperasi' }}</p>
                    </div>
                </div>
                
                <div class="login-text-content" style="position: relative; z-index: 10;">
                    <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 8px; font-size: calc(12px * var(--text-scale, 1)); font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">{{ __('messages.portal_layanan_pengguna_caps') ?? 'PORTAL LAYANAN PENGGUNA' }}</p>
                    <h1 style="margin-top: 0;">{{ __('messages.portal_layanan_pengguna_aplikasi_sakti') ?? 'Portal Layanan Pengguna Aplikasi SAKTI' }}</h1>
                    <p class="desc" style="margin-bottom: 0;">
                        {{ __('messages.desc_portal_layanan') ?? 'Dapatkan dukungan dan informasi yang Anda butuhkan dalam menggunakan Aplikasi SAKTI. Melalui portal ini, pengguna dapat menyampaikan kendala, memperoleh bantuan layanan, serta mengakses informasi dan pembaruan secara lebih mudah dan terpusat.' }}
                    </p>
                    {{-- Teks singkat khusus mobile --}}
                    <p class="desc-mobile" style="display: none;">{{ __('messages.desc_mobile_pantau_status') ?? 'Pantau status, riwayat, dan estimasi penyelesaian tiket anda langsung dari dashboard.' }}</p>
                </div>

                {{-- Footer Info --}}
                <div style="margin-top: auto; position: relative; z-index: 10; font-size: 11px; color: rgba(255, 255, 255, 0.5); font-family: var(--font-mono); display: flex; align-items: center; gap: 10px; padding-top: 24px;">
                    <span>&copy; PT SAKTI Kinerja Kolaborasindo</span>
                    <span>&bull;</span>
                    <span>{{ __('messages.portal_pelaporan_mitra') ?? 'Portal Pelaporan Mitra' }}</span>
                </div>



            </div>

            <!-- Right Column -->
            <div class="login-split-right">
                <div class="login-box fade-up" style="animation-delay: 0.15s; margin: 0 auto; max-width: 380px; width: 100%;">
                    <div class="login-panel-card fade-up" style="animation-delay: 0.2s;">
                        <p class="eyebrow" style="color: var(--amber); margin-bottom: 6px; font-size: calc(11px * var(--text-scale, 1));">{{ __('messages.masuk_ke_akun') ?? 'Masuk ke Akun' }}</p>
                        <h1 style="margin-bottom: 4px; font-size: calc(22px * var(--text-scale, 1));">Login</h1>
                        <p class="lede" style="margin-bottom: 18px; font-size: calc(12px * var(--text-scale, 1));">{{ __('messages.masukkan_email_sandi') ?? 'Masukkan email dan kata sandi Anda untuk melanjutkan' }}</p>

                        {{-- Tampilkan error login --}}
                        @if($errors->any())
                            <div id="login-error" class="alert-dismiss" style="text-align: left; margin-bottom: 14px; font-weight: 500; color: var(--amber); background: var(--amber-soft); padding: 10px 12px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start; transition: opacity 0.6s ease, transform 0.6s ease;">
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                                <button type="button" onclick="document.getElementById('login-error').style.display='none'" style="background: none; border: none; color: var(--amber); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="field">
                                <label style="font-size: calc(12px * var(--text-scale, 1));">Email</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; opacity: 0.7;">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <input type="email" name="email" placeholder="support@skk.co.id" style="padding-left: 42px; font-size: calc(13px * var(--text-scale, 1));" required>
                                </div>
                            </div>
                            <div class="field">
                                <label style="font-size: calc(12px * var(--text-scale, 1));">{{ __('messages.password') }}</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; opacity: 0.7;">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    <input type="password" name="password" id="password-input" placeholder="••••••••" style="padding-left: 42px; padding-right: 42px; font-size: calc(13px * var(--text-scale, 1)); width: 100%; box-sizing: border-box;" required>
                                    <button type="button" id="toggle-password" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center; opacity: 0.7; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Adding remember me and forgot password -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; font-size: calc(11px * var(--text-scale, 1));">
                                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: var(--ink-soft); font-weight: 500;">
                                    <input type="checkbox" name="remember" style="width: 14px; height: 14px; accent-color: var(--brand-primary); margin:0; padding:0; border: 1.5px solid var(--line); border-radius: 4px; cursor: pointer;">
                                    {{ __('messages.ingat_saya') ?? 'Ingat saya' }}
                                </label>
                                <a href="#" id="forgot-password-trigger" style="color: var(--brand-primary); font-weight: 600; text-decoration: underline;">{{ __('messages.lupa_kata_sandi') ?? 'Lupa kata sandi?' }}</a>
                            </div>

                            <button type="submit" class="btn" style="width: 100%; justify-content: center; background: #17447e; border: none; color: white; padding: 11px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; box-shadow: 0 4px 14px rgba(23, 68, 126, 0.35); transition: all 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.background='#123566'; this.style.boxShadow='0 6px 20px rgba(23, 68, 126, 0.45)';" onmouseout="this.style.transform='translateY(0)'; this.style.background='#17447e'; this.style.boxShadow='0 4px 14px rgba(23, 68, 126, 0.35)';">{{ __('messages.masuk_ke_dashboard') ?? 'Masuk ke Dashboard' }} &rarr;</button>
                        </form>

                        <div class="register-prompt" style="position: relative; margin-top: 20px; border-top: none; padding-top: 18px; font-size: calc(11px * var(--text-scale, 1));">
                            <div style="position: absolute; top: 0; left: 50%; transform: translate(-50%, -50%); background: var(--paper-raised); padding: 0 10px; font-size: calc(9px * var(--text-scale, 1)); color: var(--ink-soft); font-family: var(--font-mono); z-index: 2;">{{ __('messages.atau') ?? 'atau' }}</div>
                            <div style="position: absolute; top: 0; left: 0; right: 0; border-top: 1px solid var(--line); z-index: 1;"></div>
                            {{ __('messages.belum_mempunyai_akun') ?? 'Belum mempunyai akun?' }} <a href="#" id="register-modal-trigger" style="color: var(--brand-primary); font-weight: 700; text-decoration: underline;">{{ __('messages.daftar') ?? 'Daftar' }}</a>
                        </div>
                    </div>
                </div>

                <div class="bottom-text fade-up" style="animation-delay: 0.25s;">
                    {{ __('messages.butuh_bantuan_masuk') ?? 'Butuh bantuan masuk?' }} <a href="#" id="support-modal-trigger">{{ __('messages.hubungi_kami') ?? 'Hubungi kami' }}</a>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bantuan Support -->
<div id="support-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
    <div class="modal-card" style="background: var(--paper-raised, #fff); border: 1px solid var(--line); border-radius: 16px; width: 90%; max-width: 440px; padding: 28px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); transform: scale(0.95); transition: transform 0.3s ease; position: relative;">
        <!-- Close Button -->
        <button id="close-support-modal" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; color: var(--ink-soft); cursor: pointer; line-height: 1; padding: 0;">&times;</button>
        
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 id="support-modal-title" style="margin: 0 0 8px 0; font-family: var(--font-display); font-size: calc(20px * var(--text-scale, 1)); font-weight: 700; color: var(--ink);">{{ __('messages.hubungi_tim_support') ?? 'Hubungi Tim Support' }}</h2>
            <p id="support-modal-text" style="margin: 0; font-size: calc(13px * var(--text-scale, 1)); color: var(--ink-soft); line-height: 1.5;">
                {{ __('messages.jika_mengalami_kendala_saat_login') ?? 'Jika Anda mengalami kendala saat login, silakan hubungi tim support kami melalui kontak di bawah ini:' }}
            </p>
        </div>

        <!-- Contact List -->
        <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;">
            <!-- Telepon -->
            <div style="border: 1px solid var(--line); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 16px; background: var(--paper, #fff);">
                <div style="background: var(--brand-primary-soft, rgba(229, 57, 53, 0.05)); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <img src="{{ asset('telephone.png') }}" alt="Telepon" style="width: 20px; height: 20px; object-fit: contain;">
                </div>
                <div style="flex-grow: 1;">
                    <div style="font-weight: 700; font-size: calc(12px * var(--text-scale, 1)); color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('messages.nomor_telepon') ?? 'Nomor Telepon' }}</div>
                    <div style="font-family: var(--font-mono); font-size: calc(14px * var(--text-scale, 1)); color: var(--ink); font-weight: 600; margin-top: 2px;">+62 853-1780-5165</div>
                </div>
            </div>

            <!-- Email -->
            <div style="border: 1px solid var(--line); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 16px; background: var(--paper, #fff);">
                <div style="background: var(--brand-primary-soft, rgba(229, 57, 53, 0.05)); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <img src="{{ asset('mail.png') }}" alt="Email" style="width: 20px; height: 20px; object-fit: contain;">
                </div>
                <div style="flex-grow: 1;">
                    <div style="font-weight: 700; font-size: calc(12px * var(--text-scale, 1)); color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('messages.email_support') ?? 'Email Support' }}</div>
                    <div style="font-family: var(--font-mono); font-size: calc(14px * var(--text-scale, 1)); color: var(--ink); font-weight: 600; margin-top: 2px;">info@ptskk.id</div>
                </div>
            </div>

            <!-- WhatsApp -->
            <a href="https://wa.me/{{ formatNomorWa(config('services.whatsapp_support_number') ?? '085317805165') }}?text=Halo,%20saya%20ingin%20bertanya%20terkait%20SAKTI%20Desk" target="_blank" style="text-decoration: none; border: 1px solid var(--line); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 16px; background: var(--paper, #fff); transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='var(--paper-sunken, #f8f9fa)'" onmouseout="this.style.background='var(--paper, #fff)'">
                <div style="background: var(--brand-primary-soft, rgba(229, 57, 53, 0.05)); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <img src="{{ asset('whatsapp.png') }}" alt="WhatsApp" style="width: 20px; height: 20px; object-fit: contain;">
                </div>
                <div style="flex-grow: 1;">
                    <div style="font-weight: 700; font-size: calc(12px * var(--text-scale, 1)); color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('messages.whatsapp_chat') ?? 'WhatsApp Chat' }}</div>
                    <div style="font-size: calc(13px * var(--text-scale, 1)); color: var(--brand-primary); font-weight: 600; margin-top: 2px; text-decoration: underline;">{{ __('messages.hubungi_via_whatsapp') ?? 'Hubungi via WhatsApp' }} &rarr;</div>
                </div>
            </a>
        </div>

        <!-- Operating Hours Note -->
        <div style="text-align: center; font-size: calc(11px * var(--text-scale, 1)); color: var(--ink-soft); font-family: var(--font-mono); text-transform: uppercase; letter-spacing: 0.05em;">
            {{ __('messages.jam_operasional') ?? 'Jam Operasional: Senin - Jumat (09:00 - 17:00 WIB)' }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const skeleton = document.getElementById('skeleton-loading');
        const content  = document.getElementById('actual-content');
        setTimeout(function () {
            skeleton.style.display = 'none';
            content.style.display = 'flex';
            content.classList.add('loaded');
        }, 800);

        // Modal Bantuan Support logic
        const supportTrigger = document.getElementById('support-modal-trigger');
        const registerTrigger = document.getElementById('register-modal-trigger');
        const forgotPasswordTrigger = document.getElementById('forgot-password-trigger');
        const supportModal = document.getElementById('support-modal');
        const closeSupportModal = document.getElementById('close-support-modal');
        const modalId = document.querySelector('#support-modal .modal-card');
        const modalTitle = document.getElementById('support-modal-title');
        const modalText = document.getElementById('support-modal-text');

        const defaultTitle = 'Hubungi Tim Support';
        const defaultText = 'Jika Anda mengalami kendala saat login, silakan hubungi tim support kami melalui kontak di bawah ini:';
        const registerText = 'Hubungi Tim Support/Admin. Pembuatan akun (registrasi) baru sepenuhnya dialihkan oleh Super Admin untuk keperluan validasi mitra/koperasi.';
        const forgotPasswordText = 'Hubungi Tim Support/Admin. Penyetelan ulang kata sandi sepenuhnya dialihkan oleh Super Admin demi alasan keamanan.';

        function openModal(e, mode = 'support') {
            e.preventDefault();
            
            if(mode === 'register') {
                modalTitle.textContent = 'Pembuatan Akun Baru';
                modalText.textContent = registerText;
            } else if(mode === 'forgot') {
                modalTitle.textContent = 'Lupa Kata Sandi';
                modalText.textContent = forgotPasswordText;
            } else {
                modalTitle.textContent = defaultTitle;
                modalText.textContent = defaultText;
            }

            supportModal.style.display = 'flex';
            setTimeout(() => {
                supportModal.style.opacity = '1';
                if (modalId) modalId.style.transform = 'scale(1)';
            }, 10);
        }

        function closeModal() {
            supportModal.style.opacity = '0';
            if (modalId) modalId.style.transform = 'scale(0.95)';
            setTimeout(() => {
                supportModal.style.display = 'none';
            }, 300);
        }

        if (supportTrigger) supportTrigger.addEventListener('click', (e) => openModal(e, 'support'));
        if (registerTrigger) registerTrigger.addEventListener('click', (e) => openModal(e, 'register'));
        if (forgotPasswordTrigger) forgotPasswordTrigger.addEventListener('click', (e) => openModal(e, 'forgot'));
        if (closeSupportModal) closeSupportModal.addEventListener('click', closeModal);
        
        // Close modal when clicking outside modal-card
        if (supportModal) {
            supportModal.addEventListener('click', (e) => {
                if (e.target === supportModal) {
                    closeModal();
                }
            });
        }

        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password-input');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeOffIcon = document.getElementById('eye-off-icon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                } else {
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection
