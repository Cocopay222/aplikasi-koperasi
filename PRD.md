# Product Requirements Document (PRD)
**Project Name:** SAKTI Desk  
**Client / Owner:** PT Sakti Kinerja Kolaborasindo  
**Author:** Muhammad Radith Nurmansyah (NIM: 103012400226)  
**Document Status:** Draft / Ready for Development  

---

## 1. Overview
**SAKTI Desk** adalah sistem manajemen tiket (*ticketing system*) tersentralisasi untuk ekosistem aplikasi PT Sakti Kinerja Kolaborasindo (seperti SAKTI.Link, SiCUNDO, dan SAKTI Online). 

Tujuan utama sistem ini adalah menjembatani pelaporan kendala dari instansi mitra (Koperasi) ke tim *Support Engineer*, memastikan pelacakan siklus hidup tiket secara *real-time*, serta mengotomatisasi rekapitulasi analitik bulanan dan klasifikasi *Knowledge Base* (Smart FAQ).

## 2. User Personas
Sistem ini menggunakan otentikasi berbasis peran (*Role-Based Access Control* / RBAC) dengan dua aktor utama:

1. **Pelapor (Mitra / Instansi)**
   * **Deskripsi:** Perwakilan dari koperasi atau instansi (misal: PIC Koperasi Kredit Sejahtera).
   * **Goals:** Melaporkan *bug* atau kendala, melacak status tiket, dan memberikan data tambahan (lampiran/bukti) jika diminta.
2. **Tim Support (Internal PT SKK)**
   * **Deskripsi:** *Support Engineer* yang bertanggung jawab menyelesaikan kendala.
   * **Goals:** Meninjau tiket, menugaskan PIC, memperbarui status resolusi, mengkategorikan tiket untuk analitik, dan membangun FAQ.

---

## 3. Functional Requirements (FR)

### Epic 1: Authentication & User Management
* **FR-Auth1:** Sistem harus menyediakan halaman *login* yang membedakan rute akses antara Pelapor dan Tim Support.
* **FR-Auth2:** Sistem menyediakan form pendaftaran (Registrasi) untuk Pelapor baru, yang memerlukan validasi (Aktivasi) dari sisi admin/Tim Support sebelum bisa digunakan.

### Epic 2: Manajemen Tiket (Pelapor)
* **FR-01 / FR-02 (Create Ticket):** Pelapor dapat membuat tiket baru dengan memilih "Aplikasi Bermasalah" (divalidasi dari D2-Master Aplikasi), mengisi deskripsi, dan mengunggah lampiran (Maks 5MB).
* **FR-03 (Auto-ID & Timestamp):** Sistem secara otomatis men-*generate* Nomor Tiket (Format: `TKT-[YYMMDD][AutoIncrement]`) dan *timestamp* saat form dikirim. Status awal diatur ke `Open`.
* **FR-04 (Need Info Action):** Jika tiket berstatus `Pending`, Pelapor dapat mengirimkan keterangan atau lampiran tambahan yang diminta oleh Tim Support.

### Epic 3: Penanganan Tiket (Tim Support)
* **FR-05 (Ticket Board):** Tim Support dapat melihat daftar antrean tiket yang dapat di-filter berdasarkan status (`Open`, `Proses`, `Pending`, `Done`) atau *Search Query*.
* **FR-06 (Update & Resolution):** Tim support dapat memperbarui status tiket. Jika status diubah ke `Done`, Tim Support **wajib** mengisi *field* `Penyelesaian` dan `Pencegahan`. *Field* `tanggal_penyelesaian` terisi otomatis.
* **FR-07 (Categorization):** Saat penyelesaian, tiket harus ditautkan ke satu `kategori_id` (divalidasi dari D2-Master Kategori).
* **FR-08 (External Tracking):** Tersedia *field* `link_ticket` opsional untuk menautkan tiket ke platform eksternal (mis. Jira).
* **FR-09 (Knowledge Base / FAQ):** Terdapat *toggle* boolean `is_faq`. Jika diatur ke `True`, deskripsi dan penyelesaian tiket akan masuk ke basis data *Smart FAQ* yang akan direkomendasikan kepada Pelapor saat mereka mengetik kendala serupa.

### Epic 4: Master Data (D2)
* **FR-11 (Master Aplikasi):** CRUD data aplikasi (SAKTI.Link, SiCUNDO, dll) beserta status aktifnya.
* **FR-12 (Master Kategori):** CRUD data kategori tiket (Bug, SOP, Data, dll) untuk keperluan klasifikasi analitik akhir.

### Epic 5: Reporting & Analytics (FR-10)
* **FR-10A (Monthly Bar Chart):** Sistem secara dinamis merekap jumlah tiket berdasarkan tahun dan bulan *input*.
* **FR-10B (Category Crosstab):** Tabel rekap silang (*spreadsheet-style*) yang menjumlahkan tiket per kategori per bulan dalam satu tahun berjalan, yang dihitung dari tiket dengan status `Done` berdasarkan *timestamp* penyelesaian.

---

## 4. Technical & Database Spec (System Design Context)

### 4.1. Core Entities (Draft ERD)
1. **Users:** `user_id`, `role` (Enum: Pelapor, Support), `nama`, `email`, `password_hash`, `instansi_id` (Nullable).
2. **Instansi:** `instansi_id`, `nama_instansi`, `alamat`, `no_telp`.
3. **Master_Aplikasi:** `aplikasi_id`, `nama_aplikasi`, `deskripsi`, `is_active`.
4. **Master_Kategori:** `kategori_id`, `nama_kategori`.
5. **Tickets:** 
   * `ticket_id` (PK, String TKT-...)
   * `pelapor_id` (FK -> Users)
   * `aplikasi_id` (FK -> Master_Aplikasi)
   * `kategori_id` (FK -> Master_Kategori, Nullable until closed)
   * `pic_support_id` (FK -> Users, Nullable)
   * `permasalahan` (Text)
   * `penyelesaian` (Text, Nullable)
   * `pencegahan` (Text, Nullable)
   * `status` (Enum: `Open`, `Pending`, `Proses`, `Done`)
   * `link_ticket` (String, Nullable)
   * `is_faq` (Boolean, Default False)
   * `tanggal_input` (Timestamp)
   * `tanggal_penyelesaian` (Timestamp, Nullable)

### 4.2. API Design Principles
* Menggunakan standar RESTful API untuk seluruh *endpoint*.
* Analitik (FR-10) harus diproses menggunakan agregasi kueri di level *database* (misalnya dengan SQL `GROUP BY` dan `COUNT`) dengan indeks yang dioptimalkan pada kolom tanggal untuk memastikan performa yang efisien, bukan dihitung di *client-side*.

---

## 5. Non-Functional Requirements (NFR)
* **Usability:** Antarmuka harus sangat intuitif dengan mengadopsi prinsip UI/UX modern (mengurangi jumlah klik, memandu *user* secara visual, dan memberikan *feedback* pada setiap interaksi).
* **Security:** *Password* wajib dienkripsi (Bcrypt/Argon2). Implementasi akses kontrol yang ketat (Tim Support tidak boleh mengedit data instansi milik Pelapor, Pelapor hanya bisa melihat tiket miliknya sendiri).
* **Performance:** Waktu *load* halaman analitik tidak boleh melebihi 2 detik, bahkan saat data tiket mencapai ribuan baris per bulan.
