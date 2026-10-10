    <form action="{{ route('implementasi.update', $implementasi->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="grid-2">
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
                    <label class="form-label">{{ __('messages.status_implementasi') }}</label>
                    <select name="status" class="form-control" required>
                        @php
                            $statuses = [
                                'Pelatihan Dijadwalkan' => 'status_impl_pelatihan_dijadwalkan',
                                'Pelatihan Berlangsung' => 'status_impl_pelatihan_berlangsung',
                                'Pelatihan Selesai' => 'status_impl_pelatihan_selesai',
                                'Persiapan Data' => 'status_impl_persiapan_data',
                                'Olah data' => 'status_impl_olah_data',
                                'Migrasi data' => 'status_impl_migrasi_data',
                                'Menunggu Validasi Data' => 'status_impl_menunggu_validasi_data',
                                'Pengecekan' => 'status_impl_pengecekan',
                                'Update/Perbaikan Aplikasi' => 'status_impl_update_perbaikan_aplikasi',
                                'Monitoring' => 'status_impl_monitoring',
                                'Pendampingan' => 'status_impl_pendampingan',
                                'Running' => 'status_impl_running',
                                'Go-Live' => 'status_impl_go_live',
                                'Pending' => 'status_impl_pending',
                                'Follow-up' => 'status_impl_follow_up',
                                'Pelatihan Ulang' => 'status_impl_pelatihan_ulang',
                                'Implementasi Selesai' => 'status_impl_implementasi_selesai',
                                'Dibatalkan' => 'status_impl_dibatalkan',
                            ];
                            $currentStatus = old('status', $implementasi->status);
                        @endphp
                        @foreach($statuses as $stValue => $stKey)
                            @php
                                $translated = __('messages.' . $stKey);
                                if ($translated === 'messages.' . $stKey) {
                                    $translated = $stValue;
                                }
                            @endphp
                            <option value="{{ $stValue }}" {{ $currentStatus == $stValue ? 'selected' : '' }}>{{ $translated }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">{{ __('messages.aplikasi_modul') }}</label>
                <div id="edit-aplikasi-container">
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
                            <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addEditAplikasiInput()">+</button>
                        @else
                            <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0;" onclick="removeEditInput(this)">-</button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('messages.tanggal_pelatihan') }}</label>
                <div class="grid-2">
                    <div>
                        <label class="form-label" style="font-weight: normal; font-size: 12px; color: #64748b;">{{ __('messages.mulai') }}</label>
                        <input type="date" name="tanggal_pelatihan" class="form-control" value="{{ old('tanggal_pelatihan', $implementasi->tanggal_pelatihan ? $implementasi->tanggal_pelatihan->format('Y-m-d') : '') }}" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 13px; color: #64748b; font-weight: normal; margin-bottom: 4px;">{{ __('messages.selesai_label') }}</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $implementasi->tanggal_selesai ? $implementasi->tanggal_selesai->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>

            <div class="form-group" style="background: var(--paper-sunken, #f8fafc); padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 15px;">
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
                    <label class="form-label">{{ __('messages.metode_pelatihan') }}</label>
                    <select name="metode_pelatihan" class="form-control" required>
                        <option value="Online (Zoom/Meet)" {{ old('metode_pelatihan', $implementasi->metode_pelatihan) == 'Online (Zoom/Meet)' ? 'selected' : '' }}>Online (Zoom/Meet)</option>
                        <option value="Offline (Kunjungan)" {{ old('metode_pelatihan', $implementasi->metode_pelatihan) == 'Offline (Kunjungan)' ? 'selected' : '' }}>Offline (Kunjungan)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('messages.berita_acara_pdf') }}</label>
                    <input type="file" name="berita_acara" class="form-control" accept=".pdf">
                    @if($implementasi->berita_acara)
                        <small style="display: block; margin-top: 5px;"><a href="{{ Storage::url($implementasi->berita_acara) }}" target="_blank">Lihat file saat ini</a></small>
                    @endif
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('messages.anggota_hadir') }}</label>
                    <div id="edit-anggota-container">
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
                                <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addEditAnggotaInput()">+</button>
                            @else
                                <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0;" onclick="removeEditInput(this)">-</button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">{{ __('messages.nama_trainer') }}</label>
                    <div id="edit-trainer-container">
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
                                <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addEditTrainerInput()">+</button>
                            @else
                                <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0;" onclick="removeEditInput(this)">-</button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">{{ __('messages.whatsapp_pic') }}</label>
                    <div id="edit-kontak-container">
                        @php
                            $savedKontak = $implementasi->kontak_pic ? array_map('trim', explode(',', $implementasi->kontak_pic)) : [];
                            $oldKontak = old('kontak_pic', $savedKontak);
                            if(empty($oldKontak)) $oldKontak = [''];
                        @endphp
                        @foreach($oldKontak as $index => $kontak)
                        <div class="kontak-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="kontak_pic[]" class="form-control" placeholder="Contoh: 081234567890" value="{{ $kontak }}" required>
                            @if($index === 0)
                                <button type="button" class="btn-action" style="background-color: #10b981; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; flex-shrink: 0;" onclick="addEditKontakInput()">+</button>
                            @else
                                <button type="button" class="btn-action" style="background-color: #ef4444; padding: 0; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0;" onclick="removeEditInput(this)">-</button>
                            @endif
                        </div>
                        @endforeach
                    </div>
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
                        $currentTempatPartial = old('tempat_go_live', $implementasi->tempat_go_live);
                        $isPresetPartial = in_array($currentTempatPartial, ['Zoom', 'Gmeet', '']);
                        $isCustomPartial = !$isPresetPartial || $currentTempatPartial === 'Lokasi';
                    @endphp
                    <select name="tempat_go_live" onchange="toggleDetailLokasi(this, 'detail_lokasi_partial')" class="form-control">
                        <option value="">{{ __('messages.pilih_tempat') }}</option>
                        <option value="Zoom" {{ $currentTempatPartial == 'Zoom' ? 'selected' : '' }}>Zoom</option>
                        <option value="Gmeet" {{ $currentTempatPartial == 'Gmeet' ? 'selected' : '' }}>Gmeet</option>
                        <option value="Lokasi" {{ $isCustomPartial ? 'selected' : '' }}>Lokasi (Kunjungan Offline)</option>
                    </select>
                    <div id="detail_lokasi_partial" style="margin-top: 8px; display: {{ $isCustomPartial ? 'block' : 'none' }};">
                        <input type="text" name="detail_lokasi" class="form-control" placeholder="Tuliskan alamat / lokasi kunjungan..." value="{{ !$isPresetPartial ? $currentTempatPartial : '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('messages.status_go_live') }}</label>
                    <select name="status_go_live" class="form-control">
                        <option value="Belum Done" {{ old('status_go_live', $implementasi->status_go_live) == 'Belum Done' ? 'selected' : '' }}>Belum Done</option>
                        <option value="Done" {{ old('status_go_live', $implementasi->status_go_live) == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('messages.catatan_pelatihan') }}</label>
                <textarea name="catatan_pelatihan" class="form-control" rows="3" placeholder="Tuliskan catatan tambahan jika ada">{{ old('catatan_pelatihan', $implementasi->catatan_pelatihan) }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('modalEditData')">{{ __('messages.batal') }}</button>
            <button type="submit" class="btn-primary" style="background-color: #f59e0b; color: white; padding: 8px 15px; border-radius: 4px; border: none; cursor: pointer; font-weight: 500;">{{ __('messages.simpan_perubahan') }}</button>
        </div>
    </form>
