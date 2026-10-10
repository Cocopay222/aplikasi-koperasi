<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Instansi;
use App\Enums\UserRole;
use App\Http\Requests\StoreRegisterRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Cek apakah akun Pelapor sudah diverifikasi oleh Tim Support
            if (Auth::user()->role === UserRole::PELAPOR && !Auth::user()->is_verified) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda belum diverifikasi oleh Tim Support. Silakan tunggu proses verifikasi.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            if (in_array(Auth::user()->role, [UserRole::SUPPORT, UserRole::SUPERADMIN])) {
                return redirect()->intended('/support/dashboard');
            }
            return redirect()->intended('/pelapor/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan form registrasi Pelapor.
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Memproses form registrasi Pelapor.
     * Business Logic:
     * 1. Buat record Instansi (nama_instansi + no_hp)
     * 2. Buat record User (nama PIC, email, password hash, instansi_id, role=Pelapor, is_verified=false)
     * 3. User baru harus diverifikasi oleh Tim Support sebelum bisa login.
     */
    public function storeRegister(StoreRegisterRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $role = $validated['role'] ?? UserRole::PELAPOR->value;
            $instansiId = null;
            $isVerified = true;

            if ($role === UserRole::PELAPOR->value) {
                $instansi = Instansi::firstOrCreate(
                    ['nama_instansi' => $validated['nama_instansi']],
                    ['no_telp'       => $validated['no_hp']]
                );
                $instansiId = $instansi->instansi_id;
                $isVerified = false; // Akun Pelapor perlu diverifikasi oleh Support
            }

            User::create([
                'nama'        => $validated['nama'],
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
                'role'        => $role,
                'posisi'      => $validated['posisi'] ?? null,
                'whatsapp'    => $validated['no_hp'],
                'instansi_id' => $instansiId,
                'is_verified' => $isVerified,
            ]);
        });

        return redirect('/login')->with('success', __('messages.reg_success'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function updateInstansi(Request $request)
    {
        $request->validate([
            'alamat' => ['nullable', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:50'],
            'aplikasis' => ['nullable', 'array'],
            'aplikasis.*' => ['exists:master_aplikasis,aplikasi_id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = Auth::user();
        if ($user) {
            if ($request->hasFile('avatar')) {
                // Hapus avatar lama jika ada
                if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
                $user->save();
            }

            if ($user->instansi) {
                $user->instansi->update([
                    'alamat' => $request->alamat,
                    'no_telp' => $request->no_telp,
                ]);

                if ($request->has('aplikasis')) {
                    $user->instansi->aplikasis()->sync($request->aplikasis);
                } else {
                    $user->instansi->aplikasis()->sync([]);
                }
            }
        }

        return back()->with('success', __('messages.koperasi_updated'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini harus diisi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', __('messages.password_updated'));
    }

    public function showProfilSaya()
    {
        return view('support.profil-saya');
    }

    public function updateProfilSaya(Request $request)
    {
        $user = Auth::user();

        $rules = [];
        if ($request->has('nama') || $request->has('email')) {
            $rules['nama'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email,' . $user->user_id . ',user_id'];
            $rules['nik'] = ['nullable', 'string', 'max:50'];
            $rules['whatsapp'] = ['nullable', 'string', 'max:50'];
            $rules['avatar'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }
        if ($request->filled('password') || $request->filled('current_password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }
        if ($request->has('spesialisasi')) {
            $rules['spesialisasi'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules, [
            'current_password.required' => 'Kata sandi saat ini harus diisi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
        ]);

        if (isset($validated['nama'])) $user->nama = $validated['nama'];
        if (isset($validated['email'])) $user->email = $validated['email'];
        if (isset($validated['nik'])) $user->nik = $validated['nik'];
        if (isset($validated['whatsapp'])) $user->whatsapp = $validated['whatsapp'];
        if (isset($validated['spesialisasi'])) $user->spesialisasi = $validated['spesialisasi'];

        if ($request->has('two_factor_present')) {
            $user->two_factor = $request->has('two_factor');
            $user->otp_method = $request->input('otp_method', 'WhatsApp');
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', __('messages.profil_updated'));
    }

    public function showPelaporProfile(User $user)
    {
        return view('support.pelapor-profile', compact('user'));
    }

    public function pengaturan()
    {
        return view('pengaturan');
    }

    public function updatePengaturan(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->user_id . ',user_id'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required_with' => 'Kata sandi saat ini harus diisi jika Anda ingin mengubah kata sandi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', __('messages.akun_saved'));
    }

    public function updateLanguage(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:id,en'],
        ]);

        $user = Auth::user();
        if ($user) {
            $user->locale = $validated['locale'];
            $user->save();
        }
        session()->put('locale', $validated['locale']);

        App::setLocale($validated['locale']);

        return back()->with('success', __('messages.bahasa_updated'));
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar dalam sistem.',
            ])->withInput();
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email));

        return back()->with('success', __('messages.reset_link_sent'));
    }

    public function showResetPassword($token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $resetData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetData) {
            // Debug: cek nilai yang diterima
            $dbToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();
            $debugInfo = 'Email diterima: ' . $request->email . ' | ';
            $debugInfo .= 'Token diterima (20): ' . substr($request->token, 0, 20) . ' | ';
            $debugInfo .= 'Token di DB (20): ' . ($dbToken ? substr($dbToken->token, 0, 20) : 'TIDAK ADA') . ' | ';
            $debugInfo .= 'Cocok: ' . ($dbToken && $dbToken->token === $request->token ? 'YA' : 'TIDAK');
            return back()->withErrors(['email' => 'Token tidak valid. DEBUG: ' . $debugInfo]);
        }

        $tokenAge = Carbon::parse($resetData->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 10080) { // 7 hari = 10080 menit
            return back()->withErrors(['email' => 'Tautan reset kata sandi telah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', __('messages.password_reset_success'));
    }
}
