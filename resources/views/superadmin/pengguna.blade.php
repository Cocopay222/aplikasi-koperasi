@extends('layouts.app')

@section('page_title', __('messages.manajemen_pengguna'))
@section('page_subtitle', __('messages.fitur_eksklusif_super_admin'))

@section('content')
<div class="pelapor-panel">

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

    @if($errors->any())
        <div class="alert-dismiss fade-up" style="animation-delay: 0.1s; display: flex; align-items: flex-start; gap: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 500; font-size: 0.9rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="glass-panel fade-up" style="padding: 0; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 24px; border-bottom: 1px solid var(--line); flex-wrap: wrap; gap: 16px;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--ink); margin: 0;">{{ __('messages.daftar_pengguna_sistem') }}</h2>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <form action="{{ route('superadmin.pengguna') }}" method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." style="padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem; min-width: 250px;">
                    <button type="submit" class="btn btn-secondary" style="display: flex; align-items: center; justify-content: center; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-sunken); cursor: pointer;" onmouseover="this.style.background='var(--line)'" onmouseout="this.style.background='var(--paper-sunken)'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--ink);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('superadmin.pengguna') }}" class="btn btn-ghost" style="display: flex; align-items: center; justify-content: center; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); color: var(--ink); text-decoration: none;" onmouseover="this.style.background='var(--line)'" onmouseout="this.style.background='transparent'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </a>
                    @endif
                </form>
                <button onclick="openModal('modal-add-user')" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    {{ __('messages.tambah_pengguna') }}
                </button>
            </div>
        </div>

        <div class="table-scroll-wrapper" style="overflow: auto; border: none; border-radius: 0; background: transparent; margin: 0; padding-bottom: 0;">
            <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
                <thead style="background: var(--paper-sunken); position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.nama_pengguna') }}</th>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.email') }}</th>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.peran') }}</th>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.posisi') }}</th>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.instansi') }}</th>
                        <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">{{ __('messages.aksi') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 1.25rem 1.5rem; color: var(--ink); font-size: 0.95rem; font-weight: 600;">{{ $user->nama }}</td>
                        <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.95rem;">{{ $user->email }}</td>
                        <td style="padding: 1.25rem 1.5rem; text-align: left;">
                            @if($user->role === \App\Enums\UserRole::SUPERADMIN)
                                <span class="badge" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; white-space: nowrap;">Super Admin</span>
                            @elseif($user->role === \App\Enums\UserRole::SUPPORT)
                                <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; white-space: nowrap;">Support</span>
                            @else
                                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; white-space: nowrap;">Pelapor</span>
                            @endif
                        </td>
                        <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.95rem;">
                            {{ $user->posisi ?: '-' }}
                        </td>
                        <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.95rem;">
                            {{ $user->instansi ? $user->instansi->nama_instansi : '-' }}
                        </td>
                        <td style="padding: 1.25rem 1.5rem; text-align: center; position: relative;">
                            @if($user->user_id !== Auth::id())
                            <div style="position: relative; display: inline-block;">
                                <button type="button" onclick="toggleDropdown(event, 'dropdown-user-{{ $user->user_id }}')" style="background: var(--paper-raised); border: 1.5px solid var(--line); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; color: var(--ink); display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--border-hover)'; this.style.background='var(--paper-sunken)'" onmouseout="this.style.borderColor='var(--line)'; this.style.background='var(--paper-raised)'" title="Aksi">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1.5"></circle><circle cx="12" cy="5" r="1.5"></circle><circle cx="12" cy="19" r="1.5"></circle></svg>
                                </button>
                                
                                <div id="dropdown-user-{{ $user->user_id }}" class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 36px; background: var(--paper-raised); border: 1px solid var(--line); border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 140px; z-index: 100; text-align: left; padding: 4px; overflow: hidden;">
                                    <button type="button" onclick="openModal('modal-edit-user-{{ $user->user_id }}')" style="display: flex; width: 100%; align-items: center; gap: 8px; padding: 10px 12px; font-size: 0.85rem; font-weight: 500; color: var(--ink); background: transparent; border: none; cursor: pointer; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='var(--paper-sunken)'" onmouseout="this.style.background='transparent'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Edit
                                    </button>
                                    <button type="button" onclick="if(confirm('{{ __('messages.yakin_hapus_akun') }}')) { document.getElementById('form-delete-{{ $user->user_id }}').submit(); }" style="display: flex; width: 100%; align-items: center; gap: 8px; padding: 10px 12px; font-size: 0.85rem; font-weight: 500; color: #ef4444; background: transparent; border: none; cursor: pointer; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='transparent'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        {{ __('messages.hapus') }}
                                    </button>
                                    <form id="form-delete-{{ $user->user_id }}" action="{{ route('superadmin.pengguna.destroy', $user->user_id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                            @else
                                <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">Anda (Saat Ini)</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 3rem 1.5rem; text-align: center; color: var(--text-muted);">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <div>
                                    <div style="font-weight: 600; color: var(--ink); font-size: 1.1rem; margin-bottom: 4px;">{{ __('messages.tidak_ditemukan') ?? 'Pengguna tidak ditemukan' }}</div>
                                    <div style="font-size: 0.9rem;">Pencarian Anda tidak membuahkan hasil. Coba kata kunci lain.</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div class="overlay" id="modal-add-user">
    <div class="modal w-sm modal-centered">
        <div class="modal-head">
            <div>
                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.tambah_pengguna_baru') }}</h3>
            </div>
            <button type="button" class="modal-x" onclick="closeModal('modal-add-user')">✕</button>
        </div>
        <form action="{{ route('superadmin.pengguna.store') }}" method="POST">
            @csrf
            <div class="modal-body" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                <div class="field">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.peran_role') }} <span style="color:var(--danger)">*</span></label>
                    <select name="role" id="role-select" required onchange="toggleInstansi(this, 'add')" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                        <option value="">{{ __('messages.pilih_peran') }}</option>
                        <option value="{{ \App\Enums\UserRole::SUPPORT->value }}">{{ __('messages.tim_support_internal') }}</option>
                        <option value="{{ \App\Enums\UserRole::PELAPOR->value }}">{{ __('messages.mitra_koperasi_pelapor') }}</option>
                        <option value="{{ \App\Enums\UserRole::SUPERADMIN->value }}">{{ __('messages.super_admin') }}</option>
                    </select>
                </div>
                
                <div id="common-fields-add" style="display: none; flex-direction: column; gap: 16px;">
                    <div class="field">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.nama_lengkap') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama" required placeholder="Masukkan nama" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                    </div>
                    <div class="field">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.email') }} <span style="color:var(--danger)">*</span></label>
                        <input type="email" name="email" required placeholder="Email aktif" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                    </div>
                    <div class="field" id="posisi-group-add" style="display: none;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.posisi') }}</label>
                        <select name="posisi" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                            <option value="">{{ __('messages.pilih_posisi') }}</option>
                            <option value="Manager">Manager</option>
                            <option value="Sekretaris">Sekretaris</option>
                            <option value="Bendahara">Bendahara</option>
                            <option value="Pengawas">Pengawas</option>
                            <option value="Admin">Admin</option>
                            <option value="Akuntansi">Akuntansi</option>
                            <option value="IT">IT</option>
                        </select>
                    </div>
                    <div class="field" id="whatsapp-group-add" style="display: none;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.no_whatsapp') }}</label>
                        <input type="text" name="whatsapp" placeholder="{{ __('messages.contoh_whatsapp') }}" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                    </div>
                    <div class="field" id="nik-group-add" style="display: none;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.no_anggota') }}</label>
                        <input type="text" name="nik" placeholder="Contoh: KSS-2026-00005" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                    </div>
                    <div class="field" id="instansi-group-add" style="display: none;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.instansi_koperasi') }} <span style="color:var(--danger)">*</span></label>
                        <select name="instansi_id" id="instansi-select-add" onchange="toggleInstansiBaru(this, 'add')" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                            <option value="">{{ __('messages.pilih_instansi') }}</option>
                            <option value="new" style="font-weight: bold; color: var(--primary);">{{ __('messages.buat_koperasi_baru') }}</option>
                            @foreach($instansis as $instansi)
                                <option value="{{ $instansi->instansi_id }}">{{ $instansi->nama_instansi }}</option>
                            @endforeach
                        </select>
                        
                        <input type="text" name="instansi_baru" id="instansi-baru-add" placeholder="{{ __('messages.masukkan_nama_koperasi_baru') }}" style="display: none; width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem; margin-top: 8px;">
                        
                        <div id="btn-edit-koperasi-wrapper-add" style="display: none; margin-top: 8px;">
                            <button type="button" class="btn btn-ghost btn-sm" onclick="showEditKoperasiInput('add')" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid var(--line); border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Edit Nama Koperasi
                            </button>
                        </div>
                        <input type="text" name="edit_nama_instansi" id="edit-nama-instansi-add" placeholder="Ubah nama koperasi (opsional)" style="display: none; width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem; margin-top: 8px;">
                    </div>
                    <div class="field">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.kata_sandi') }} <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="password" required placeholder="{{ __('messages.minimal_8_karakter') }}" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-add-user')">{{ __('messages.batal') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.simpan_pengguna') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Modals Edit Pengguna -->
@foreach($users as $user)
<div class="overlay" id="modal-edit-user-{{ $user->user_id }}">
    <div class="modal w-sm modal-centered">
        <div class="modal-head">
            <div>
                <h3 style="margin: 0; font-size: 1.1rem; color: var(--ink);">{{ __('messages.edit_pengguna') }}</h3>
            </div>
            <button type="button" class="modal-x" onclick="closeModal('modal-edit-user-{{ $user->user_id }}')">✕</button>
        </div>
        <form action="{{ route('superadmin.pengguna.update', $user->user_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                <div class="field">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.peran_role') }} <span style="color:var(--danger)">*</span></label>
                    <select disabled style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper-sunken); color: var(--ink-soft); font-size: 0.9rem; cursor: not-allowed;">
                        <option value="{{ \App\Enums\UserRole::SUPPORT->value }}" {{ $user->role === \App\Enums\UserRole::SUPPORT ? 'selected' : '' }}>{{ __('messages.tim_support_internal') }}</option>
                        <option value="{{ \App\Enums\UserRole::PELAPOR->value }}" {{ $user->role === \App\Enums\UserRole::PELAPOR ? 'selected' : '' }}>{{ __('messages.mitra_koperasi_pelapor') }}</option>
                        <option value="{{ \App\Enums\UserRole::SUPERADMIN->value }}" {{ $user->role === \App\Enums\UserRole::SUPERADMIN ? 'selected' : '' }}>{{ __('messages.super_admin') }}</option>
                    </select>
                    <input type="hidden" name="role" value="{{ $user->role }}">
                </div>
                <div class="field">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.nama_lengkap') }} <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama" required value="{{ $user->nama }}" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                </div>
                <div class="field">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.email') }} <span style="color:var(--danger)">*</span></label>
                    <input type="email" name="email" required value="{{ $user->email }}" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                </div>
                <div class="field" id="posisi-group-edit-{{ $user->user_id }}" style="display: {{ $user->role === \App\Enums\UserRole::PELAPOR ? 'block' : 'none' }};">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.posisi') }}</label>
                    <select name="posisi" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                        <option value="">{{ __('messages.pilih_posisi') }}</option>
                        <option value="Manager" {{ $user->posisi == 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="Sekretaris" {{ $user->posisi == 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                        <option value="Bendahara" {{ $user->posisi == 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                        <option value="Pengawas" {{ $user->posisi == 'Pengawas' ? 'selected' : '' }}>Pengawas</option>
                        <option value="Admin" {{ $user->posisi == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Akuntansi" {{ $user->posisi == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                        <option value="IT" {{ $user->posisi == 'IT' ? 'selected' : '' }}>IT</option>
                    </select>
                </div>
                <div class="field" id="whatsapp-group-edit-{{ $user->user_id }}" style="display: {{ in_array($user->role, [\App\Enums\UserRole::PELAPOR, \App\Enums\UserRole::SUPPORT]) ? 'block' : 'none' }};">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.no_whatsapp') }}</label>
                    <input type="text" name="whatsapp" value="{{ $user->whatsapp ?? ($user->instansi->no_telp ?? '') }}" placeholder="{{ __('messages.contoh_whatsapp') }}" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                </div>
                <div class="field" id="nik-group-edit-{{ $user->user_id }}" style="display: {{ $user->role === \App\Enums\UserRole::PELAPOR ? 'block' : 'none' }};">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.no_anggota') }}</label>
                    <input type="text" name="nik" value="{{ $user->nik ?: 'KSS-' . date('Y') . '-' . str_pad($user->user_id, 5, '0', STR_PAD_LEFT) }}" placeholder="Contoh: KSS-2026-00005" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                </div>
                <div class="field" id="instansi-group-edit-{{ $user->user_id }}" style="display: {{ $user->role === \App\Enums\UserRole::PELAPOR ? 'block' : 'none' }};">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.instansi_koperasi') }} <span style="color:var(--danger)">*</span></label>
                    <select name="instansi_id" id="instansi-select-edit-{{ $user->user_id }}" {{ $user->role === \App\Enums\UserRole::PELAPOR ? 'required' : '' }} onchange="toggleInstansiBaru(this, 'edit-{{ $user->user_id }}')" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                        <option value="">{{ __('messages.pilih_instansi') }}</option>
                        <option value="new" style="font-weight: bold; color: var(--primary);">{{ __('messages.buat_koperasi_baru') }}</option>
                        @foreach($instansis as $instansi)
                            <option value="{{ $instansi->instansi_id }}" {{ $user->instansi_id == $instansi->instansi_id ? 'selected' : '' }}>{{ $instansi->nama_instansi }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="instansi_baru" id="instansi-baru-edit-{{ $user->user_id }}" placeholder="{{ __('messages.masukkan_nama_koperasi_baru') }}" style="display: none; width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem; margin-top: 8px;">
                    
                    <div id="btn-edit-koperasi-wrapper-edit-{{ $user->user_id }}" style="display: {{ ($user->role === \App\Enums\UserRole::PELAPOR && $user->instansi_id) ? 'block' : 'none' }}; margin-top: 8px;">
                        <button type="button" class="btn btn-ghost btn-sm" onclick="showEditKoperasiInput('edit-{{ $user->user_id }}')" style="padding: 6px 12px; font-size: 0.8rem; border: 1px solid var(--line); border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            Edit Nama Koperasi
                        </button>
                    </div>
                    <input type="text" name="edit_nama_instansi" id="edit-nama-instansi-edit-{{ $user->user_id }}" placeholder="Ubah nama koperasi (opsional)" style="display: none; width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem; margin-top: 8px;" value="{{ $user->instansi ? $user->instansi->nama_instansi : '' }}">
                </div>
                <div class="field">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--ink);">{{ __('messages.ubah_kata_sandi') }}</label>
                    <input type="text" name="password" placeholder="{{ __('messages.kosongkan_jika_tidak_ubah') }}" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--line); background: var(--paper); color: var(--ink); font-size: 0.9rem;">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit-user-{{ $user->user_id }}')">{{ __('messages.batal') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.simpan_perubahan') }}</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
    function toggleInstansi(selectElement, suffix) {
        let role = selectElement.value;
        let commonFields = document.getElementById('common-fields-' + suffix);
        let groupInstansi = document.getElementById('instansi-group-' + suffix);
        let instansiSelect = document.getElementById('instansi-select-' + suffix);
        let inputBaru = document.getElementById('instansi-baru-' + suffix);
        let groupWhatsapp = document.getElementById('whatsapp-group-' + suffix);
        let groupNik = document.getElementById('nik-group-' + suffix);
        let groupPosisi = document.getElementById('posisi-group-' + suffix);

        if (role === '') {
            if(commonFields) commonFields.style.display = 'none';
            return;
        } else {
            if(commonFields) commonFields.style.display = 'flex';
        }
        
        if (role === '{{\App\Enums\UserRole::PELAPOR->value}}') {
            if(groupInstansi) groupInstansi.style.display = 'block';
            if(instansiSelect) instansiSelect.setAttribute('required', 'required');
            if(groupWhatsapp) groupWhatsapp.style.display = 'block';
            if(groupNik) groupNik.style.display = 'block';
            if(groupPosisi) groupPosisi.style.display = 'block';
        } else if (role === '{{\App\Enums\UserRole::SUPPORT->value}}') {
            if(groupInstansi) groupInstansi.style.display = 'none';
            if(instansiSelect) {
                instansiSelect.removeAttribute('required');
                instansiSelect.value = "";
            }
            if(inputBaru) { inputBaru.style.display = 'none'; inputBaru.removeAttribute('required'); inputBaru.value = ""; }
            let inputEdit = document.getElementById('edit-nama-instansi-' + suffix);
            if(inputEdit) { inputEdit.style.display = 'none'; inputEdit.value = ""; }
            let btnEditWrapper = document.getElementById('btn-edit-koperasi-wrapper-' + suffix);
            if(btnEditWrapper) { btnEditWrapper.style.display = 'none'; }
            if(groupWhatsapp) groupWhatsapp.style.display = 'block';
            if(groupNik) groupNik.style.display = 'none';
            if(groupPosisi) groupPosisi.style.display = 'none';
        } else {
            // Super Admin
            if(groupInstansi) groupInstansi.style.display = 'none';
            if(instansiSelect) {
                instansiSelect.removeAttribute('required');
                instansiSelect.value = "";
            }
            if(inputBaru) { inputBaru.style.display = 'none'; inputBaru.removeAttribute('required'); inputBaru.value = ""; }
            let inputEditSA = document.getElementById('edit-nama-instansi-' + suffix);
            if(inputEditSA) { inputEditSA.style.display = 'none'; inputEditSA.value = ""; }
            let btnEditWrapperSA = document.getElementById('btn-edit-koperasi-wrapper-' + suffix);
            if(btnEditWrapperSA) { btnEditWrapperSA.style.display = 'none'; }
            if(groupWhatsapp) groupWhatsapp.style.display = 'none';
            if(groupNik) groupNik.style.display = 'none';
            if(groupPosisi) groupPosisi.style.display = 'none';
        }
    }

    function toggleInstansiBaru(selectElement, suffix) {
        let inputBaru = document.getElementById('instansi-baru-' + suffix);
        let inputEdit = document.getElementById('edit-nama-instansi-' + suffix);
        let btnEditWrapper = document.getElementById('btn-edit-koperasi-wrapper-' + suffix);
        
        if (selectElement.value === 'new') {
            inputBaru.style.display = 'block';
            inputBaru.setAttribute('required', 'required');
            if (inputEdit) { inputEdit.style.display = 'none'; inputEdit.value = ""; }
            if (btnEditWrapper) { btnEditWrapper.style.display = 'none'; }
        } else if (selectElement.value !== "") {
            inputBaru.style.display = 'none';
            inputBaru.removeAttribute('required');
            inputBaru.value = "";
            if (btnEditWrapper) { btnEditWrapper.style.display = 'block'; }
            if (inputEdit) {
                inputEdit.style.display = 'none';
                inputEdit.value = selectElement.options[selectElement.selectedIndex].text;
            }
        } else {
            inputBaru.style.display = 'none';
            inputBaru.removeAttribute('required');
            inputBaru.value = "";
            if (inputEdit) { inputEdit.style.display = 'none'; inputEdit.value = ""; }
            if (btnEditWrapper) { btnEditWrapper.style.display = 'none'; }
        }
    }

    function showEditKoperasiInput(suffix) {
        let inputEdit = document.getElementById('edit-nama-instansi-' + suffix);
        if(inputEdit) {
            inputEdit.style.display = inputEdit.style.display === 'none' ? 'block' : 'none';
            if(inputEdit.style.display === 'block') {
                inputEdit.focus();
            }
        }
    }

    function toggleDropdown(event, id) {
        event.stopPropagation();
        let menu = document.getElementById(id);
        let allMenus = document.querySelectorAll('.dropdown-menu');
        
        allMenus.forEach(m => {
            if(m.id !== id) m.style.display = 'none';
        });
        
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
        } else {
            menu.style.display = 'none';
        }
    }

    // Tutup dropdown kalau klik di luar
    document.addEventListener('click', function() {
        let allMenus = document.querySelectorAll('.dropdown-menu');
        allMenus.forEach(m => {
            m.style.display = 'none';
        });
    });
</script>
@endsection
