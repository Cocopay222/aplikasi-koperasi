@extends('layouts.app')

@section('page_title', __('messages.profil_koperasi'))
@section('page_subtitle', 'SAKTI Desk')


@section('content')
<div class="pelapor-panel">

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
            <a href="{{ Auth::user()->role === 'support' ? route('support.dashboard') : route('pelapor.dashboard') }}" class="btn btn-ghost" style="padding: 8px 16px; border: 1px solid var(--line); background: var(--paper-raised);">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:inline-block; margin-right: 6px; vertical-align:-2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('messages.kembali_ke_dashboard') }}
            </a>
        </div>
        
        <div class="panel fade-up" style="padding: 30px; max-width: 100%; animation-delay: 0.15s;" id="instansi-panel">

            
            <!-- KARTU IDENTITAS ANGGOTA DIGITAL -->
            <div id="kartu-identitas" style="margin-bottom: 32px; background: linear-gradient(135deg, #1e3a8a, #1e40af); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);">
                <div style="padding: 24px; position: relative; color: white;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <!-- Avatar -->
                            <div style="position: relative; flex-shrink: 0;">
                                @php
                                    $nama = Auth::user()->nama ?? 'User';
                                    $initials = collect(explode(' ', $nama))->map(function($w){return strtoupper(substr($w,0,1));})->take(2)->join('');
                                @endphp
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" style="width: 70px; height: 70px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.4); object-fit: cover; display: block;">
                                @else
                                    <div style="width: 70px; height: 70px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.4); display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); font-size: 28px; font-weight: bold; color: white;">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <!-- Info -->
                            <div>
                                <div style="font-size: 11px; letter-spacing: 1px; color: rgba(255,255,255,0.7); text-transform: uppercase; margin-bottom: 4px; font-weight: 600;">
                                    {{ __('messages.kartu_identitas_digital') }}
                                </div>
                                <h2 style="margin: 0; font-size: 22px; font-weight: 700; color: white; margin-bottom: 2px;">
                                    {{ Auth::user()->nama }}
                                </h2>
                                <div style="font-size: 13px; color: rgba(255,255,255,0.85); margin-bottom: 6px; font-weight: 500;">
                                    {{ Auth::user()->posisi ?? 'Anggota' }} di {{ Auth::user()->instansi->nama_instansi ?? 'Koperasi' }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: rgba(255,255,255,0.9); font-family: monospace;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #fcd34d;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    {{ __('messages.no_anggota') }}: {{ Auth::user()->nik ?: 'KSS-' . date('Y') . '-' . str_pad(Auth::user()->user_id, 5, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                        </div>

                        <!-- Badge Status -->
                        <div>
                            @if(Auth::user()->is_verified)
                                <div style="background: rgba(255,255,255,0.9); color: #15803d; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <span style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block;"></span>
                                    {{ __('messages.aktif_terverifikasi') }}
                                </div>
                            @else
                                <div style="background: rgba(255,255,255,0.9); color: #b45309; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <span style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; display: inline-block;"></span>
                                    {{ __('messages.menunggu_verifikasi') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="background: #f1f5f9; padding: 12px 24px; font-size: 12px; color: #64748b; border-top: 1px dashed rgba(0,0,0,0.1);">
                    {{ __('messages.footer_kartu') }}
                </div>
            </div>
            
            @if(session('success'))
                <div id="success-alert" class="alert-dismiss fade-up" style="animation-delay: 0.2s; display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: var(--sage-soft); color: var(--sage); border-radius: 8px; margin-bottom: 24px; font-size: calc(13.5px * var(--text-scale, 1)); font-weight: 600; border: 1px solid rgba(46, 125, 82, 0.2); transition: opacity 0.6s ease, transform 0.6s ease;">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="document.getElementById('success-alert').style.display='none'" style="background: none; border: none; color: var(--sage); cursor: pointer; font-size: calc(18px * var(--text-scale, 1)); font-weight: bold; line-height: 1; padding: 0 4px; margin-left: 10px;">&times;</button>
                </div>
            @endif

            <!-- VIEW MODE -->
            <div id="view-mode" class="fade-up" style="animation-delay: 0.25s;">
                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.nama_koperasi') }}</label>
                    <h4 style="font-size: calc(16px * var(--text-scale, 1)); color: var(--ink); margin: 0;">{{ Auth::user()->instansi->nama_instansi ?? '-' }}</h4>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.alamat_lengkap') }}</label>
                    <p style="color: var(--ink); line-height: 1.6; margin: 0; font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->instansi->alamat ?? '-' }}</p>
                </div>
                <div style="margin-bottom: 32px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.nomor_telepon') }}</label>
                    <div class="mono" style="color: var(--ink); font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->instansi->no_telp ?? '-' }}</div>
                </div>
                
                <div style="margin-bottom: 32px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.nama_pic') }}</label>
                    <div class="mono" style="color: var(--ink); font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->nama ?? '-' }}</div>
                </div>

                <div style="margin-bottom: 32px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.posisi_koperasi') }}</label>
                    <div class="mono" style="color: var(--ink); font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->posisi ?? '-' }}</div>
                </div>

                <div style="margin-bottom: 32px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:6px;">{{ __('messages.email_pic') }}</label>
                    <div class="mono" style="color: var(--ink); font-size: calc(14.5px * var(--text-scale, 1));">{{ Auth::user()->email ?? '-' }}</div>
                </div>

                <div style="margin-bottom: 32px;">
                    <label style="display:block; font-size: calc(12px * var(--text-scale, 1)); font-weight:600; color:var(--ink-soft); margin-bottom:8px;">{{ __('messages.aplikasi_digunakan') }}</label>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @if(Auth::user()->instansi && Auth::user()->instansi->aplikasis->count() > 0)
                            @foreach(Auth::user()->instansi->aplikasis as $app)
                                <span style="background: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 20px; font-size: calc(13px * var(--text-scale, 1)); font-weight: 600;">{{ $app->nama_aplikasi }}</span>
                            @endforeach
                        @else
                            <span style="color: var(--text-muted); font-size: calc(14px * var(--text-scale, 1));">{{ __('messages.belum_ada_aplikasi_dipilih') }}</span>
                        @endif
                    </div>
                </div>
                
                <div style="border-top: 1px solid var(--line); padding-top: 20px;">
                    <button type="button" class="btn btn-ghost" onclick="toggleEditMode(true)" style="padding: 9px 18px;">
                        <span class="ic"><img src="{{ asset('edit.png') }}" alt="Edit" style="width: 14px; height: 14px; object-fit: contain; vertical-align: middle; margin-right: 4px; margin-top: -2px;"></span> {{ __('messages.edit_data_koperasi') }}
                    </button>
                </div>
            </div>

            <!-- EDIT MODE -->
            <div id="edit-mode" style="display: none;">
                <form action="{{ route('profil.instansi.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- FOTO PROFIL UPLOAD -->
                    <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 28px;">
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
                    
                    <div class="field">
                        <label>{{ __('messages.nama_koperasi') }}</label>
                        <input type="text" value="{{ Auth::user()->instansi->nama_instansi ?? '-' }}" readonly style="background: var(--paper-sunken); color: var(--ink-soft); cursor: not-allowed;" title="Nama Koperasi tidak dapat diubah">
                        <div class="helper">{{ __('messages.nama_koperasi_tetap') }}</div>
                    </div>
                    
                    <div class="field" style="margin-top: 18px;">
                        <label>{{ __('messages.alamat_lengkap') }}</label>
                        <textarea name="alamat" rows="3" placeholder="{{ __('messages.masukkan_alamat_koperasi') }}" required>{{ Auth::user()->instansi->alamat ?? '' }}</textarea>
                    </div>
                    
                    <div class="field" style="margin-top: 18px;">
                        <label>{{ __('messages.nomor_telepon') }}</label>
                        <input type="text" name="no_telp" value="{{ Auth::user()->instansi->no_telp ?? '' }}" placeholder="{{ __('messages.contoh_nomor') }}" required>
                    </div>

                    <div class="field" style="margin-top: 18px;">
                        <label>{{ __('messages.nama_pic') }}</label>
                        <input type="text" value="{{ Auth::user()->nama ?? '' }}" readonly style="background: var(--paper-sunken); color: var(--ink-soft); cursor: not-allowed;" title="{{ __('messages.nama_pic_ubah_pengaturan') }}">
                        <div class="helper">{{ __('messages.nama_pic_ubah_pengaturan') }}</div>
                    </div>

                    <div class="field" style="margin-top: 18px;">
                        <label>{{ __('messages.email_pic') }}</label>
                        <input type="email" value="{{ Auth::user()->email ?? '' }}" readonly style="background: var(--paper-sunken); color: var(--ink-soft); cursor: not-allowed;" title="{{ __('messages.email_pic_ubah_pengaturan') }}">
                        <div class="helper">{{ __('messages.email_pic_ubah_pengaturan') }}</div>
                    </div>

                    <div class="field" style="margin-top: 18px;">
                        <label>{{ __('messages.aplikasi_digunakan') }}</label>
                        <div style="background: var(--paper-sunken); padding: 16px; border-radius: 8px; border: 1px solid var(--line); display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                            @php
                                $selectedAplikasi = Auth::user()->instansi ? Auth::user()->instansi->aplikasis->pluck('aplikasi_id')->toArray() : [];
                            @endphp
                            @forelse($aplikasis ?? [] as $app)
                                <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; cursor: pointer; color: var(--ink);">
                                    <input type="checkbox" name="aplikasis[]" value="{{ $app->aplikasi_id }}" {{ in_array($app->aplikasi_id, $selectedAplikasi) ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer;">
                                    {{ $app->nama_aplikasi }}
                                </label>
                            @empty
                                <div style="color: var(--text-muted); font-size: calc(13px * var(--text-scale, 1));">{{ __('messages.belum_ada_master_aplikasi') }}</div>
                            @endforelse
                        </div>
                        <div class="helper">{{ __('messages.pilih_aplikasi_koperasi') }}</div>
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
                    }, 800);
                }
            });
        </script>
        
    </div>
</div>
@endsection
