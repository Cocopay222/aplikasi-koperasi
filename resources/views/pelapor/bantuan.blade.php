@extends('layouts.app')

@section('page_title', __('messages.title_pusat_bantuan'))
@section('page_subtitle', 'PELAPOR')

@section('content')
<div class="pelapor-panel">
    <div style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 12px; padding: 24px;">
        <h3 style="margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; font-family: var(--font-display); font-size: 19px; font-weight: 600; color: var(--ink);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                <circle cx="8" cy="12" r="1.5" fill="var(--brand-primary)" stroke="none"/>
                <circle cx="12" cy="12" r="1.5" fill="var(--brand-primary)" stroke="none"/>
                <circle cx="16" cy="12" r="1.5" fill="var(--brand-primary)" stroke="none"/>
            </svg>
            {{ __('messages.hubungi_tim_support') }}
        </h3>
        <p style="color: var(--ink-soft); font-size: 14px; margin-bottom: 24px; margin-top: 0;">
            {{ __('messages.desc_pusat_bantuan') }}
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <!-- Telepon -->
            <div style="border: 1px solid var(--line); border-radius: 10px; padding: 16px; display: flex; align-items: center; gap: 16px;">
                <div style="background: var(--brand-primary-soft); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('telephone.png') }}" alt="Telepon" style="width: 22px; height: 22px; object-fit: contain;">
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 14px; color: var(--ink);">{{ __('messages.telepon') }}</div>
                    <div style="font-family: var(--font-mono); font-size: 13px; color: var(--ink-soft); margin-top: 4px;">+62 853-1780-5165</div>
                </div>
            </div>
            
            <!-- Email -->
            <div style="border: 1px solid var(--line); border-radius: 10px; padding: 16px; display: flex; align-items: center; gap: 16px;">
                <div style="background: var(--brand-primary-soft); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('mail.png') }}" alt="Email" style="width: 22px; height: 22px; object-fit: contain;">
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 14px; color: var(--ink);">{{ __('messages.email') }}</div>
                    <div style="font-family: var(--font-mono); font-size: 13px; color: var(--ink-soft); margin-top: 4px;">info@ptskk.id</div>
                </div>
            </div>
            
            <!-- WhatsApp -->
            <a href="https://wa.me/6285317805165?text=Halo,%20saya%20ingin%20bertanya%20terkait%20SAKTI%20Desk" target="_blank" style="text-decoration: none; border: 1px solid var(--line); border-radius: 10px; padding: 16px; display: flex; align-items: center; gap: 16px; cursor: pointer; transition: 0.2s; background: var(--paper);" onmouseover="this.style.background='var(--paper-sunken)'" onmouseout="this.style.background='var(--paper)'">
                <div style="background: var(--brand-primary-soft); width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('whatsapp.png') }}" alt="WhatsApp" style="width: 22px; height: 22px; object-fit: contain;">
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 14px; color: var(--ink);">WhatsApp</div>
                    <div style="font-size: 13px; color: var(--ink-soft); margin-top: 4px;">{{ __('messages.klik_untuk_chat') }}</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
