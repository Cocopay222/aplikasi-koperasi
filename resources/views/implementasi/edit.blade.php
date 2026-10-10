@extends('layouts.app')

@section('page_title', 'Edit Data Implementasi')
@section('page_subtitle', $implementasi->instansi->nama_instansi ?? 'Koperasi')

@section('topbar_right')
    <a href="{{ route('implementasi.index') }}" style="background-color: #64748b; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: background 0.2s;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
        Batal Edit
    </a>
@endsection

@section('content')
<style>
    .form-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 20px;
        max-width: 800px;
    }
    .dark-mode .form-card { background: #1e293b; color: #f8fafc; }
    
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px; color: #334155; }
    .dark-mode .form-label { color: #cbd5e1; }
    
    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-family: inherit;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .form-control:focus { outline: none; border-color: #2563eb; }
    .dark-mode .form-control {
        background: #0f172a;
        border-color: #334155;
        color: #f8fafc;
    }
    
    .btn-secondary { background: #e2e8f0; color: #475569; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; text-decoration: none; display: inline-block;}
    .dark-mode .btn-secondary { background: #334155; color: #cbd5e1; }
    .btn-primary { background: #2563eb; color: #fff; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500;}
    
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    .form-footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }
    .dark-mode .form-footer { border-color: #334155; }
</style>

<div class="form-card">
    <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 18px;">{{ __('messages.form_edit_implementasi') }}</h2>
    
    @if ($errors->any())
        <div style="background-color: #fef2f2; color: #b91c1c; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;">
            <strong style="display: block; margin-bottom: 5px;">{{ __('messages.terjadi_kesalahan') }}</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('implementasi.update', $implementasi->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">{{ __('messages.koperasi_label') }}</label>
            <select name="instansi_id" class="form-control searchable-select allow-create" required>
                <option value="">{{ __('messages.pilih_koperasi') }}</option>
                @foreach($instansis as $instansi)
                    <option value="{{ $instansi->instansi_id }}" {{ (old('instansi_id', $implementasi->instansi_id) == $instansi->instansi_id) ? 'selected' : '' }}>
                        {{ $instansi->nama_instansi }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="kantor_cabang" class="form-control mt-2" placeholder="Kantor / Cabang (Opsional)" value="{{ old('kantor_cabang', $implementasi->kantor_cabang) }}" style="margin-top: 8px;">
        </div>
        
        <div class="form-group">
            <label class="form-label">{{ __('messages.aplikasi_modul') }}</label>
            <div id="aplikasi-container">
                @php
                    $savedAplikasis = $implementasi->aplikasis->pluck('aplikasi_id')->toArray();
                    if(empty($savedAplikasis) && $implementasi->aplikasi_id) {
                        $savedAplikasis = [$implementasi->aplikasi_id];
                    }
                    $oldAplikasis = old('aplikasi_id', $savedAplikasis);
                    if(empty($oldAplikasis)) $oldAplikasis = [''];
                @endphp
                @foreach($oldAplikasis as $index => $oldAppId)
                <div class="aplikasi-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <select name="aplikasi_id[]" class="form-control searchable-select" required style="flex-grow: 1;">
                        <option value="" disabled {{ empty($oldAppId) ? 'selected' : '' }}>{{ __('messages.pilih_aplikasi') }}</option>
                        @foreach($aplikasis as $app)
                            <option value="{{ $app->aplikasi_id }}" {{ $oldAppId == $app->aplikasi_id ? 'selected' : '' }}>
                                {{ $app->nama_aplikasi }}
                            </option>
                        @endforeach
                    </select>
                    @if($index === 0)
                        <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="addAplikasiInput()">+</button>
                    @else
                        <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; flex-shrink: 0; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="removeInput(this)">-</button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">{{ __('messages.tanggal_pelatihan') }}</label>
                <input type="date" name="tanggal_pelatihan" class="form-control" value="{{ old('tanggal_pelatihan', $implementasi->tanggal_pelatihan ? $implementasi->tanggal_pelatihan->format('Y-m-d') : '') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.metode_pelatihan') }}</label>
                <select name="metode_pelatihan" class="form-control" required>
                    <option value="Online (Zoom/Meet)" {{ old('metode_pelatihan', $implementasi->metode_pelatihan) == 'Online (Zoom/Meet)' ? 'selected' : '' }}>Online (Zoom/Meet)</option>
                    <option value="Offline (Kunjungan)" {{ old('metode_pelatihan', $implementasi->metode_pelatihan) == 'Offline (Kunjungan)' ? 'selected' : '' }}>Offline (Kunjungan)</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="background: var(--paper-sunken, #f8fafc); padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 20px;">
            <label class="form-label" style="font-weight: 600; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Masa Berlaku Tim Support</span>
            </label>
            <div class="grid-2">
                <div>
                    <label class="form-label" style="font-weight: normal; font-size: 12px; color: #64748b;">Mulai Support</label>
                    <input type="date" name="tanggal_mulai_support" class="form-control" value="{{ old('tanggal_mulai_support', $implementasi->tanggal_mulai_support ? $implementasi->tanggal_mulai_support->format('Y-m-d') : '') }}">
                </div>
                <div>
                    <label class="form-label" style="font-weight: normal; font-size: 12px; color: #64748b;">Hingga Tanggal (Selesai Support)</label>
                    <input type="date" name="tanggal_selesai_support" class="form-control" value="{{ old('tanggal_selesai_support', $implementasi->tanggal_selesai_support ? $implementasi->tanggal_selesai_support->format('Y-m-d') : '') }}">
                </div>
            </div>
            <div style="margin-top: 8px;">
                <input type="text" name="durasi_support" class="form-control" value="{{ old('durasi_support', $implementasi->durasi_support ?? '') }}" placeholder="Durasi Support (Opsional, misal: 6 Bulan / 1 Tahun)">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">{{ __('messages.berita_acara_pdf') }}</label>
                <input type="file" name="berita_acara" class="form-control" accept=".pdf">
                @if($implementasi->berita_acara)
                    <small style="display: block; margin-top: 5px;"><a href="{{ Storage::url($implementasi->berita_acara) }}" target="_blank">Lihat file saat ini</a></small>
                @endif
            </div>
            <div></div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">{{ __('messages.anggota_hadir') }}</label>
                <div id="anggota-container">
                    @php
                        $savedAnggota = $implementasi->anggota_hadir ? array_map('trim', explode(',', $implementasi->anggota_hadir)) : [];
                        $oldAnggota = old('anggota_hadir', $savedAnggota);
                        if(empty($oldAnggota)) $oldAnggota = [''];
                        $rolesList = ['Manager', 'Sekretaris', 'Bendahara', 'Pengawas', 'Admin', 'Akuntansi', 'IT', 'Staff'];
                    @endphp
                    @foreach($oldAnggota as $index => $anggotaStr)
                    @php
                        $namaVal = $anggotaStr;
                        $posVal = '';
                        if (preg_match('/^(.*?)(?:\s*\((.*?)\))?$/', $anggotaStr, $matches)) {
                            $namaVal = trim($matches[1]);
                            $posVal = isset($matches[2]) ? trim($matches[2]) : '';
                        }
                    @endphp
                    <div class="anggota-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="anggota_hadir[]" class="form-control" placeholder="{{ __('messages.nama_anggota') }}" value="{{ $namaVal }}" required style="flex: 1;">
                        <select name="posisi_anggota[]" class="form-control" onchange="this.style.color = this.value ? '' : '#94a3b8';" style="flex: 1; color: {{ $posVal ? 'inherit' : '#94a3b8' }};">
                            <option value="" disabled {{ empty($posVal) ? 'selected hidden' : '' }} style="color: #94a3b8;">{{ __('messages.posisi') }}</option>
                            @foreach($rolesList as $role)
                                <option value="{{ $role }}" {{ $posVal == $role ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
                        @if($index === 0)
                            <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="addAnggotaInput()">+</button>
                        @else
                            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; flex-shrink: 0; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="removeInput(this)">-</button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">{{ __('messages.nama_trainer') }}</label>
                <div id="trainer-container">
                    @php
                        $savedTrainer = $implementasi->nama_trainer ? array_map('trim', explode(',', $implementasi->nama_trainer)) : [];
                        $oldTrainer = old('nama_trainer', $savedTrainer);
                        if(empty($oldTrainer)) $oldTrainer = [''];
                    @endphp
                    @foreach($oldTrainer as $index => $trainer)
                    <div class="trainer-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <select name="nama_trainer[]" class="form-control searchable-select">
                            <option value="" disabled {{ empty($trainer) ? 'selected' : '' }} hidden>{{ __('messages.pilih_trainer') }}</option>
                            @foreach($usersSupport as $user)
                                <option value="{{ $user->nama }}" {{ $trainer === $user->nama ? 'selected' : '' }}>{{ $user->nama }}</option>
                            @endforeach
                        </select>
                        @if($index === 0)
                            <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="addTrainerInput()">+</button>
                        @else
                            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; flex-shrink: 0; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="removeInput(this)">-</button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">{{ __('messages.whatsapp_pic') }}</label>
                <input type="text" name="kontak_pic" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('kontak_pic', $implementasi->kontak_pic) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.email_pic') }}</label>
                <input type="email" name="email_pic" class="form-control" placeholder="Email PIC Koperasi" value="{{ old('email_pic', $implementasi->email_pic) }}">
            </div>
        </div>
        
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">{{ __('messages.tanggal_go_live') }}</label>
                <input type="date" name="target_go_live" class="form-control" value="{{ old('target_go_live', $implementasi->target_go_live ? $implementasi->target_go_live->format('Y-m-d') : '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.waktu_go_live') }}</label>
                <input type="text" name="waktu_go_live" class="form-control" placeholder="13:50" value="{{ old('waktu_go_live', $implementasi->waktu_go_live ? \Carbon\Carbon::parse($implementasi->waktu_go_live)->format('H:i') : '') }}">
            </div>
        </div>
        
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">{{ __('messages.tempat_go_live') }}</label>
                @php
                    $currentTempatEdit = old('tempat_go_live', $implementasi->tempat_go_live);
                    $isPresetEdit = in_array($currentTempatEdit, ['Zoom', 'Gmeet', '']);
                    $isCustomEdit = !$isPresetEdit || $currentTempatEdit === 'Lokasi';
                @endphp
                <select name="tempat_go_live" onchange="toggleDetailLokasi(this, 'detail_lokasi_edit')" class="form-control">
                    <option value="">{{ __('messages.pilih_tempat') }}</option>
                    <option value="Zoom" {{ $currentTempatEdit == 'Zoom' ? 'selected' : '' }}>Zoom</option>
                    <option value="Gmeet" {{ $currentTempatEdit == 'Gmeet' ? 'selected' : '' }}>Gmeet</option>
                    <option value="Lokasi" {{ $isCustomEdit ? 'selected' : '' }}>Lokasi (Kunjungan Offline)</option>
                </select>
                <div id="detail_lokasi_edit" style="margin-top: 8px; display: {{ $isCustomEdit ? 'block' : 'none' }};">
                    <input type="text" name="detail_lokasi" class="form-control" placeholder="Tuliskan alamat / lokasi kunjungan..." value="{{ !$isPresetEdit ? $currentTempatEdit : '' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.status_go_live') }}</label>
                <select name="status_go_live" class="form-control">
                    <option value="Belum Siap Go Live" {{ old('status_go_live', $implementasi->status_go_live) == 'Belum Siap Go Live' ? 'selected' : '' }}>Belum Siap Go Live</option>
                    <option value="Siap Go Live" {{ old('status_go_live', $implementasi->status_go_live) == 'Siap Go Live' ? 'selected' : '' }}>Siap Go Live</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">{{ __('messages.catatan_pelatihan') }}</label>
            <textarea name="catatan_pelatihan" class="form-control" rows="3" placeholder="Tuliskan catatan tambahan jika ada">{{ old('catatan_pelatihan', $implementasi->catatan_pelatihan) }}</textarea>
        </div>

        <div class="form-footer">
            <a href="{{ route('implementasi.index') }}" class="btn-secondary">{{ __('messages.batal') }}</a>
            <button type="submit" class="btn-primary">{{ __('messages.simpan_perubahan') }}</button>
        </div>
    </form>
</div>

<script>
    function addAplikasiInput() {
        const container = document.getElementById('aplikasi-container');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'aplikasi-input-group';
        inputGroup.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
        
        inputGroup.innerHTML = `
            <select name="aplikasi_id[]" class="form-control searchable-select" required style="flex-grow: 1;">
                <option value="" disabled selected>{{ __('messages.pilih_aplikasi') }}</option>
                @foreach($aplikasis as $app)
                    <option value="{{ $app->aplikasi_id }}">{{ $app->nama_aplikasi }}</option>
                @endforeach
            </select>
            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; flex-shrink: 0; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="removeInput(this)">-</button>
        `;
        container.appendChild(inputGroup);
        
        const newSelect = inputGroup.querySelector('select');
        if (typeof TomSelect !== 'undefined') {
            new TomSelect(newSelect, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: newSelect.getAttribute('placeholder') || '{{ __('messages.pilih_aplikasi') }}',
                onChange: function(value) {
                    if (typeof updateAplikasiOptions === 'function') updateAplikasiOptions();
                }
            });
        }
        updateAplikasiOptions();
    }
    
    function updateAplikasiOptions() {
        const selects = document.querySelectorAll('select[name="aplikasi_id[]"]');
        const selectedValues = Array.from(selects).map(s => s.value).filter(v => v);

        selects.forEach(select => {
            let ts = select.tomselect;
            
            if (ts) {
                Array.from(select.options).forEach(option => {
                    if (!option.value) return; 
                    let shouldDisable = selectedValues.includes(option.value) && select.value !== option.value;
                    let optionData = ts.options[option.value];
                    if (optionData && optionData.disabled !== shouldDisable) {
                        ts.updateOption(option.value, Object.assign({}, optionData, { disabled: shouldDisable }));
                    }
                });
            } else {
                Array.from(select.options).forEach(option => {
                    if (!option.value) return; 
                    let shouldDisable = selectedValues.includes(option.value) && select.value !== option.value;
                    option.disabled = shouldDisable;
                });
            }
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.name === 'aplikasi_id[]') {
            updateAplikasiOptions();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(updateAplikasiOptions, 500);
    });
    
    function addAnggotaInput() {
        const container = document.getElementById('anggota-container');
        const originalGroup = container.querySelector('.anggota-input-group');
        const newGroup = originalGroup.cloneNode(true);
        newGroup.querySelector('input').value = '';
        
        const btn = newGroup.querySelector('button');
        btn.textContent = '-';
        btn.style.backgroundColor = '#ef4444';
        btn.onclick = function() { removeInput(this); };
        
        container.appendChild(newGroup);
    }
    
    function addTrainerInput() {
        const container = document.getElementById('trainer-container');
        const originalGroup = container.querySelector('.trainer-input-group');
        const newGroup = originalGroup.cloneNode(true);
        const selectEl = newGroup.querySelector('select');
        if (selectEl) selectEl.value = '';
        
        const btn = newGroup.querySelector('button');
        btn.textContent = '-';
        btn.style.backgroundColor = '#ef4444';
        btn.onclick = function() { removeInput(this); };
        
        container.appendChild(newGroup);
    }

    function removeInput(btn) {
        const group = btn.closest('.trainer-input-group, .anggota-input-group, .kontak-input-group');
        if (group) group.remove();
    }

    function toggleDetailLokasi(selectEl, targetId) {
        const target = document.getElementById(targetId);
        if (!target) return;
        if (selectEl.value === 'Lokasi') {
            target.style.display = 'block';
        } else {
            target.style.display = 'none';
        }
    }
</script>
@endsection
