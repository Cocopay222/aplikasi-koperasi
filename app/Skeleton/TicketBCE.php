<?php

namespace App\Skeleton;

use DateTime;

/**
 * ============================================================================
 * TUGAS BESAR / TUGAS KELOMPOK: IMPLEMENTASI KODE BERDASARKAN SEQUENCE DIAGRAM
 * Pemodelan Kelas Berstereotype BCE (Boundary, Controller, Entity)
 * Use Case : Membuat Tiket Kendala (Create Ticket)
 * Studi Kasus: SupportFlow (SAKTI Desk)
 * ============================================================================
 */

// --- ENUMS & TIPE DATA ---
enum TicketStatus: string {
    case OPEN = 'Open';
    case PENDING = 'Pending';
    case PROSES = 'Proses';
    case DONE = 'Done';
}

enum UrgensiLevel: string {
    case RENDAH = 'Rendah';
    case SEDANG = 'Sedang';
    case TINGGI = 'Tinggi';
}


/**
 * ----------------------------------------------------------------------------
 * 1. <<entity>> MasterAplikasi
 * Merepresentasikan data referensi aplikasi yang dikelola sistem.
 * ----------------------------------------------------------------------------
 */
class MasterAplikasi
{
    private int $aplikasiId;
    private string $namaAplikasi;
    private string $deskripsi;
    private bool $isActive;

    public function __construct(int $aplikasiId, string $namaAplikasi, string $deskripsi = '', bool $isActive = true)
    {
        $this->aplikasiId = $aplikasiId;
        $this->namaAplikasi = $namaAplikasi;
        $this->deskripsi = $deskripsi;
        $this->isActive = $isActive;
    }

    public function getAplikasiId(): int
    {
        return $this->aplikasiId;
    }

    public function getNamaAplikasi(): string
    {
        return $this->namaAplikasi;
    }

    public function isAktif(): bool
    {
        return $this->isActive;
    }
}


/**
 * ----------------------------------------------------------------------------
 * 2. <<entity>> Ticket
 * Entitas transaksional utama yang menyimpan seluruh data tiket kendala.
 * ----------------------------------------------------------------------------
 */
class Ticket
{
    private string $ticketId;
    private int $pelaporId;
    private int $aplikasiId;
    private ?int $kategoriId;
    private ?int $picSupportId;
    private string $permasalahan;
    private ?array $lampiran;
    protected TicketStatus $status;
    private UrgensiLevel $urgensi;
    private ?string $penyelesaian;
    private ?string $pencegahan;
    private DateTime $tanggalInput;
    private ?DateTime $tanggalPenyelesaian;

    public function __construct(
        int $pelaporId,
        int $aplikasiId,
        string $permasalahan,
        UrgensiLevel $urgensi = UrgensiLevel::SEDANG,
        ?array $lampiran = null
    ) {
        $this->ticketId = $this->generateTicketId();
        $this->pelaporId = $pelaporId;
        $this->aplikasiId = $aplikasiId;
        $this->permasalahan = $permasalahan;
        $this->urgensi = $urgensi;
        $this->lampiran = $lampiran;
        $this->status = TicketStatus::OPEN;
        $this->kategoriId = null;
        $this->picSupportId = null;
        $this->penyelesaian = null;
        $this->pencegahan = null;
        $this->tanggalInput = new DateTime();
        $this->tanggalPenyelesaian = null;
    }

    /**
     * Menghasilkan format ID Tiket otomatis: TKT-YYMMDDxxxx
     */
    private function generateTicketId(): string
    {
        $prefix = 'TKT-' . date('ymd');
        $randomSeq = str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $randomSeq;
    }

    // --- GETTER & OPERASI ENTITY ---
    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getPelaporId(): int
    {
        return $this->pelaporId;
    }

    public function getAplikasiId(): int
    {
        return $this->aplikasiId;
    }

    public function getPermasalahan(): string
    {
        return $this->permasalahan;
    }

    public function getStatus(): TicketStatus
    {
        return $this->status;
    }

    public function getUrgensi(): UrgensiLevel
    {
        return $this->urgensi;
    }

    public function getTanggalInput(): DateTime
    {
        return $this->tanggalInput;
    }

    public function updateStatus(TicketStatus $newStatus): bool
    {
        $this->status = $newStatus;
        if ($newStatus === TicketStatus::DONE) {
            $this->tanggalPenyelesaian = new DateTime();
        }
        return true;
    }

    public function assignPic(int $supportId): bool
    {
        $this->picSupportId = $supportId;
        $this->status = TicketStatus::PROSES;
        return true;
    }

    public function resolveTicket(string $solusi, string $pencegahan, int $kategoriId): bool
    {
        $this->penyelesaian = $solusi;
        $this->pencegahan = $pencegahan;
        $this->kategoriId = $kategoriId;
        $this->status = TicketStatus::DONE;
        $this->tanggalPenyelesaian = new DateTime();
        return true;
    }
}


/**
 * ----------------------------------------------------------------------------
 * 3. <<controller>> TicketController
 * Mengorkestrasi logika bisnis: menangani validasi input, koordinasi berkas,
 * dan memanggil Entity Ticket untuk disimpan.
 * ----------------------------------------------------------------------------
 */
class TicketController
{
    /**
     * Memproses penyimpanan tiket kendala baru dari form Boundary
     *
     * @param array $requestPayload Data dari form [aplikasi_id, permasalahan, urgensi, lampiran, pelapor_id]
     * @return array [success => bool, message => string, ticket => Ticket|null, errors => array]
     */
    public function store(array $requestPayload): array
    {
        // 1. Validasi Masukan (Business Validation Rule)
        $errors = $this->validateStoreRequest($requestPayload);
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Validasi formulir gagal.',
                'errors'  => $errors,
                'ticket'  => null,
            ];
        }

        // 2. Pemrosesan File Lampiran (Simulasi Penyimpanan Berkas)
        $uploadedFilePaths = [];
        if (!empty($requestPayload['lampiran']) && is_array($requestPayload['lampiran'])) {
            foreach ($requestPayload['lampiran'] as $file) {
                // Simpan path berkas yang sah
                $uploadedFilePaths[] = 'storage/lampiran_tiket/' . time() . '_' . $file['name'];
            }
        }

        // 3. Konversi Urgensi String ke Enum
        $urgensiEnum = match (strtolower($requestPayload['urgensi'] ?? 'sedang')) {
            'tinggi' => UrgensiLevel::TINGGI,
            'rendah' => UrgensiLevel::RENDAH,
            default  => UrgensiLevel::SEDANG,
        };

        // 4. Instansiasi Entity Ticket
        $newTicket = new Ticket(
            pelaporId: (int) $requestPayload['pelapor_id'],
            aplikasiId: (int) $requestPayload['aplikasi_id'],
            permasalahan: trim($requestPayload['permasalahan']),
            urgensi: $urgensiEnum,
            lampiran: $uploadedFilePaths
        );

        // 5. Kembalikan Respon Sukses ke Boundary
        return [
            'success'   => true,
            'message'   => 'Laporan berhasil dibuat dengan Nomor Tiket: #' . $newTicket->getTicketId(),
            'errors'    => [],
            'ticket'    => $newTicket,
            'ticket_id' => $newTicket->getTicketId(),
        ];
    }

    /**
     * Logika validasi data masukan
     */
    private function validateStoreRequest(array $data): array
    {
        $errors = [];

        if (empty($data['aplikasi_id'])) {
            $errors['aplikasi_id'] = 'Aplikasi bermasalah wajib dipilih.';
        }

        if (empty($data['permasalahan']) || strlen(trim($data['permasalahan'])) < 10) {
            $errors['permasalahan'] = 'Uraian kendala wajib diisi minimal 10 karakter.';
        }

        if (!empty($data['lampiran']) && is_array($data['lampiran'])) {
            foreach ($data['lampiran'] as $file) {
                if (isset($file['size']) && $file['size'] > (5 * 1024 * 1024)) {
                    $errors['lampiran'] = 'Ukuran berkas lampiran tidak boleh melebihi 5 MB.';
                    break;
                }
            }
        }

        return $errors;
    }
}


/**
 * ----------------------------------------------------------------------------
 * 4. <<boundary>> FormBuatTiketView (Dashboard Pelapor Modal Form)
 * Antarmuka pengguna untuk interaksi Aktor Pelapor dengan sistem.
 * ----------------------------------------------------------------------------
 */
class FormBuatTiketView
{
    private TicketController $controller;

    public function __construct(TicketController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Merender form input tiket ke antarmuka pengguna
     */
    public function renderForm(array $daftarAplikasi): string
    {
        $output = "=== FORM BUAT TIKET KENDALA (MODAL UI) ===\n";
        $output .= "Pilihan Aplikasi:\n";
        foreach ($daftarAplikasi as $app) {
            $output .= " - [ID: {$app->getAplikasiId()}] {$app->getNamaAplikasi()}\n";
        }
        $output .= "Input Field: Urgensi (Rendah/Sedang/Tinggi)\n";
        $output .= "Input Field: Deskripsi Permasalahan\n";
        $output .= "Input Field: Upload Berkas Bukti (Max 5MB)\n";
        $output .= "Tombol Aksi: [ Kirim Laporan ]\n";
        return $output;
    }

    /**
     * Menerima event klik tombol "Kirim Laporan" dari Pelapor
     */
    public function onSubmitButtonClicked(array $formData): void
    {
        echo "[Boundary] Mengirim form data ke TicketController...\n";
        $response = $this->controller->store($formData);

        if ($response['success']) {
            echo "[Boundary] Notifikasi Sukses: " . $response['message'] . "\n";
        } else {
            echo "[Boundary] Validasi Error:\n";
            foreach ($response['errors'] as $field => $err) {
                echo " - $field: $err\n";
            }
        }
    }
}

