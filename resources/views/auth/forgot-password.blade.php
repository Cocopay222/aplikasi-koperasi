@extends('layouts.app')

@section('content')
<div class="login-wrap" style="min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; background: #ffffff;">
    
    {{-- Top Header Logo --}}
    <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 28px; text-decoration: none;" class="fade-up">
        <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 44px; height: 44px; object-fit: contain;">
        <div>
            <h2 style="margin: 0; font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--ink); line-height: 1.2;">SAKTI Desk</h2>
            <div style="font-family: var(--font-mono); font-size: 10px; font-weight: 700; color: var(--ink-soft); letter-spacing: 1px; text-transform: uppercase;">PT SAKTI KINERJA KOLABORASINDO</div>
        </div>
    </div>


    {{-- Main Card --}}
    <div class="login-panel-card fade-up" style="background: var(--paper-raised); border: 1px solid var(--line); border-radius: 24px; padding: 36px 32px; max-width: 460px; width: 100%; box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08); text-align: center; position: relative;">

        {{-- Eyebrow --}}
        <div style="font-family: var(--font-mono); font-size: 11px; font-weight: 700; color: #17447e; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 8px;">
            PEMULIHAN AKUN
        </div>

        {{-- Heading --}}
        <h1 style="font-family: var(--font-display); font-size: 26px; font-weight: 700; color: var(--ink); margin: 0 0 12px 0; line-height: 1.2;">
            Lupa Kata Sandi?
        </h1>

        {{-- Subtitle Description --}}
        <p style="font-size: 13.5px; color: var(--ink-soft); line-height: 1.6; margin: 0 0 28px 0; max-width: 380px; margin-left: auto; margin-right: auto;">
            Masukkan email terdaftar Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi ke email tersebut
        </p>

        {{-- Success message --}}
        @if(session('success'))
            <div id="forgot-success" class="alert-dismiss" style="text-align: left; margin-bottom: 18px; font-size: 13px; font-weight: 500; color: #166534; background: #dcfce7; padding: 10px 14px; border-radius: 8px; border: 1px solid #bbf7d0;">
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- Error message --}}
        @if($errors->any())
            <div id="forgot-error" class="alert-dismiss" style="text-align: left; margin-bottom: 18px; font-size: 13px; font-weight: 500; color: #dc2626; background: #fee2e2; padding: 10px 14px; border-radius: 8px; border: 1px solid #fca5a5;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('password.email') }}" method="POST" style="text-align: left;">
            @csrf
            <div class="field" style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--ink-soft); margin-bottom: 8px;">Email Terdaftar</label>
                <div style="position: relative;">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@koperasi.id" style="width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; color: var(--ink); background: var(--paper-raised); outline: none; transition: border-color 0.2s;" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn" style="width: 100%; justify-content: center; background: #17447e; border: none; color: white; padding: 12px 16px; border-radius: 10px; font-size: 14px; font-weight: 600; box-shadow: 0 4px 14px rgba(23, 68, 126, 0.35); transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#123566'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='#17447e'; this.style.transform='translateY(0)';">
                Kirim Tautan Reset Password
            </button>
        </form>

        {{-- Footer link --}}
        <div style="margin-top: 24px; font-size: 13px; color: var(--ink-soft);">
            Ingat kata sandi Anda? <a href="{{ route('login') }}" style="color: #17447e; font-weight: 700; text-decoration: none;">Masuk di sini</a>
        </div>

    </div>

    {{-- Bottom Brand Footer --}}
    <div style="margin-top: 32px; font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--ink-soft); letter-spacing: 2px; text-transform: uppercase; display: flex; gap: 24px; opacity: 0.7;">
        <span>SAKTI ONLINE</span>
        <span>SICUNDO</span>
        <span>ISO 27001:2022</span>
    </div>

</div>
@endsection
