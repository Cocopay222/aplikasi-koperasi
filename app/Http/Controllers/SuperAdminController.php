<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instansi;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function pengguna(Request $request)
    {
        // Pastikan hanya Super Admin yang bisa mengakses
        if (Auth::user()->role !== UserRole::SUPERADMIN) {
            return redirect('/login')->with('error', __('messages.akses_ditolak'));
        }

        $query = User::with('instansi');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();
        $instansis = Instansi::all();
        
        return view('superadmin.pengguna', compact('users', 'instansis'));
    }

    public function storePengguna(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', new Enum(UserRole::class)],
            'instansi_id' => 'nullable|string',
            'instansi_baru' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'posisi' => 'nullable|string|max:50',
        ]);

        $data = $request->only(['nama', 'email', 'role', 'instansi_id', 'whatsapp', 'nik', 'posisi']);
        $data['password'] = Hash::make($request->password);
        $data['is_verified'] = true; // Selalu aktif karena ditambahkan oleh Super Admin

        // Bersihkan data yang tidak sesuai role
        if ($data['role'] === UserRole::PELAPOR->value) {
            if (empty($data['instansi_id'])) {
                return back()->with('error', __('messages.instansi_wajib'));
            }
            
            if ($data['instansi_id'] === 'new') {
                if (empty($request->instansi_baru)) {
                    return back()->with('error', __('messages.nama_instansi_baru_wajib'));
                }
                $newInstansi = Instansi::create(['nama_instansi' => $request->instansi_baru]);
                $data['instansi_id'] = $newInstansi->instansi_id;
            } else {
                $instansi = Instansi::where('instansi_id', $data['instansi_id'])->first();
                if (!$instansi) {
                    return back()->with('error', __('messages.instansi_tidak_valid'));
                }
                
                if ($request->filled('edit_nama_instansi') && $request->edit_nama_instansi !== $instansi->nama_instansi) {
                    $instansi->update(['nama_instansi' => $request->edit_nama_instansi]);
                }
            }
        } elseif ($data['role'] === UserRole::SUPPORT->value) {
            $data['instansi_id'] = null;
        } elseif ($data['role'] === UserRole::SUPERADMIN->value) {
            $data['instansi_id'] = null;
            $data['whatsapp'] = null;
        }

        User::create($data);

        return back()->with('success', __('messages.pengguna_berhasil_ditambah'));
    }

    public function updatePengguna(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'role' => ['required', new Enum(UserRole::class)],
            'instansi_id' => 'nullable|string',
            'instansi_baru' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
            'whatsapp' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'posisi' => 'nullable|string|max:50',
        ]);

        $data = $request->only(['nama', 'email', 'role', 'instansi_id', 'whatsapp', 'nik', 'posisi']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Bersihkan data yang tidak sesuai role
        if ($data['role'] === UserRole::PELAPOR->value) {
            if (empty($data['instansi_id'])) {
                return back()->with('error', __('messages.instansi_wajib'));
            }

            if ($data['instansi_id'] === 'new') {
                if (empty($request->instansi_baru)) {
                    return back()->with('error', __('messages.nama_instansi_baru_wajib'));
                }
                $newInstansi = Instansi::create(['nama_instansi' => $request->instansi_baru]);
                $data['instansi_id'] = $newInstansi->instansi_id;
            } else {
                $instansi = Instansi::where('instansi_id', $data['instansi_id'])->first();
                if (!$instansi) {
                    return back()->with('error', __('messages.instansi_tidak_valid'));
                }
                
                if ($request->filled('edit_nama_instansi') && $request->edit_nama_instansi !== $instansi->nama_instansi) {
                    $instansi->update(['nama_instansi' => $request->edit_nama_instansi]);
                }
            }
        } elseif ($data['role'] === UserRole::SUPPORT->value) {
            $data['instansi_id'] = null;
        } elseif ($data['role'] === UserRole::SUPERADMIN->value) {
            $data['instansi_id'] = null;
            $data['whatsapp'] = null;
        }

        $user->update($data);

        return back()->with('success', __('messages.data_pengguna_berhasil_diperbarui'));
    }

    public function destroyPengguna(User $user)
    {
        // Mencegah super admin menghapus dirinya sendiri
        if ($user->user_id === Auth::id()) {
            return back()->with('error', __('messages.tidak_dapat_hapus_akun_sendiri'));
        }

        $user->delete();
        return back()->with('success', __('messages.pengguna_berhasil_dihapus'));
    }
}
