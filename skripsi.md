# Dokumentasi Sistem Pengaduan Pelanggan (Sipena)

Dokumen ini berisi rancangan sistem informasi pengaduan pelanggan berbasis web yang meliputi Use Case Diagram, Activity Diagram masing-masing role, Entity Relationship Diagram (ERD), skema tabel database, serta relasi antartabel database lengkap dengan penjelasannya.

---

## 1. Use Case Diagram

Use Case Diagram menggambarkan interaksi antara aktor (pengguna sistem) dengan fungsionalitas yang disediakan oleh sistem informasi pengaduan pelanggan. 

### Diagram Use Case
```mermaid
graph TD
    %% Actors
    ActorPelanggan["Pelanggan (Guest)"]
    ActorAdmin["Admin (Karyawan/Staff)"]
    ActorSuperAdmin["Super Admin (Kepala Shift)"]

    %% Boundaries
    subgraph Sistem Pengaduan Pelanggan [Sipena]
        UC1((Mengajukan Pengaduan))
        UC2((Melacak Tiket Pengaduan))
        UC3((Melihat Tanggapan Publik))
        UC4((Autentikasi/Login))
        UC5((Mengelola Pengaduan))
        UC6((Eskalasi Tiket))
        UC7((Export Laporan))
        UC8((Mengelola User))
        UC9((Mengelola Pengaturan Landing Page))
        UC10((Menghapus Pengaduan))
    end

    %% Connections Pelanggan
    ActorPelanggan --> UC1
    ActorPelanggan --> UC2
    ActorPelanggan --> UC3

    %% Connections Admin
    ActorAdmin --> UC4
    ActorAdmin --> UC5
    ActorAdmin --> UC6
    ActorAdmin --> UC7

    %% Connections Super Admin
    ActorSuperAdmin --> UC4
    ActorSuperAdmin --> UC5
    ActorSuperAdmin --> UC7
    ActorSuperAdmin --> UC8
    ActorSuperAdmin --> UC9
    ActorSuperAdmin --> UC10

    %% Style
    style ActorPelanggan fill:#f9f,stroke:#333,stroke-width:2px
    style ActorAdmin fill:#bbf,stroke:#333,stroke-width:2px
    style ActorSuperAdmin fill:#fbf,stroke:#333,stroke-width:2px
    style Sistem Pengaduan Pelanggan fill:#fff,stroke:#333,stroke-width:1px
```

### Penjelasan Use Case Diagram
Berdasarkan diagram di atas, terdapat 3 (tiga) aktor utama yang berinteraksi dengan sistem dengan deskripsi hak akses sebagai berikut:
1. **Pelanggan (Guest)**: 
   - **Mengajukan Pengaduan**: Pelanggan dapat mengisi formulir keluhan (nama, nomor WhatsApp, kategori masalah, keterangan, dan mengunggah dokumen/struk pendukung).
   - **Melacak Tiket Pengaduan**: Pelanggan dapat melihat perkembangan status pengaduannya secara real-time dengan memasukkan nomor tiket dan nomor WhatsApp yang valid.
   - **Melihat Tanggapan Publik**: Pelanggan dapat melihat respon atau balasan publik yang ditulis oleh Admin/Super Admin terkait tiket yang mereka ajukan.
2. **Admin (Karyawan/Staff)**:
   - **Autentikasi/Login**: Mengakses dashboard admin melalui proses verifikasi username dan password.
   - **Mengelola Pengaduan**: Memperbarui status keluhan (seperti mengubah status menjadi `diproses`, `ditanggapi`, `selesai`, atau `ditolak`) serta menambahkan tanggapan internal (catatan rahasia staff) maupun tanggapan publik.
   - **Eskalasi Tiket**: Meneruskan pengaduan yang memerlukan persetujuan khusus ke Kepala Shift (Super Admin).
   - **Export Laporan**: Mengunduh rekapitulasi data pengaduan dalam format CSV, dokumen Word, atau mencetaknya secara langsung (Print/PDF).
3. **Super Admin (Kepala Shift)**:
   - Memiliki semua hak akses yang dimiliki oleh Admin.
   - **Mengelola User**: Menambahkan user baru (Admin/Super Admin), mengedit informasi akun, serta menonaktifkan akun karyawan yang sudah tidak aktif.
   - **Mengelola Pengaturan Landing Page**: Mengubah konten dinamis pada halaman publik seperti Running Text, tautan video edukasi/YouTube, Judul/Deskripsi Hero section, serta informasi jam operasional layanan.
   - **Menghapus Pengaduan**: Memiliki wewenang eksklusif untuk menghapus tiket pengaduan tertentu dari sistem jika terjadi duplikasi atau kesalahan fatal.

---

## 2. Activity Diagram

Activity Diagram menggambarkan alur kerja (workflow) atau urutan aktivitas yang terjadi di dalam sistem dari sudut pandang masing-masing role.

### A. Activity Diagram: Pelanggan (Guest)
Activity diagram ini menggambarkan proses pengajuan keluhan dan pelacakan tiket pengaduan oleh pelanggan pada halaman publik.

```mermaid
flowchart TD
    Start([Mulai]) --> MasukWeb[Mengakses Website Sipena]
    MasukWeb --> PilihMenu{Pilih Aktivitas}

    %% Alur Kirim Pengaduan
    PilihMenu -->|Kirim Pengaduan| IsiForm[Mengisi Form Pengaduan]
    IsiForm --> UploadFile[Unggah File Pendukung / Struk Belanja jika Return]
    UploadFile --> Kirim[Klik Tombol Kirim Pengaduan]
    Kirim --> ValidasiInput{Apakah Input Valid?}
    ValidasiInput -->|Tidak| TampilkanError[Tampilkan Pesan Error / Validasi Form]
    TampilkanError --> IsiForm
    ValidasiInput -->|Ya| SimpanDb[Sistem Menyimpan Pengaduan ke Database]
    SimpanDb --> GenerateTiket[Sistem Membuat Nomor Tiket & Token Unik]
    GenerateTiket --> TampilkanTiket[Tampilkan Halaman Sukses & Nomor Tiket]
    TampilkanTiket --> EndPelanggan

    %% Alur Tracking Pengaduan
    PilihMenu -->|Lacak Pengaduan| InputLacak[Masukkan Nomor Tiket & Nomor WhatsApp]
    InputLacak --> CariData[Sistem Mencari Data Tiket]
    CariData --> CekData{Apakah Data Ditemukan?}
    CekData -->|Tidak| MsgError[Tampilkan Pesan: Tiket Tidak Ditemukan]
    MsgError --> InputLacak
    CekData -->|Ya| TampilkanStatus[Tampilkan Status Pengaduan & Log Tanggapan]
    TampilkanStatus --> EndPelanggan([Selesai])
```

### B. Activity Diagram: Admin (Staff)
Activity diagram ini menggambarkan alur kerja Admin mulai dari proses login hingga pengelolaan data keluhan masuk dan eskalasi.

```mermaid
flowchart TD
    StartAdmin([Mulai]) --> FormLogin[Mengakses Halaman Login Admin]
    FormLogin --> InputAuth[Masukkan Username & Password]
    InputAuth --> SubmitLogin[Klik Login]
    SubmitLogin --> CekAuth{Apakah Kredensial Valid?}
    CekAuth -->|Tidak| TampilkanGagal[Tampilkan Pesan Error: Username/Password Salah]
    TampilkanGagal --> FormLogin
    CekAuth -->|Ya| Dashboard[Masuk ke Halaman Dashboard Admin]

    Dashboard --> PilihMenu{Pilih Menu Utama}

    %% Alur Kelola Pengaduan
    PilihMenu -->|Kelola Pengaduan| TampilkanList[Lihat Daftar Pengaduan Masuk]
    TampilkanList --> BukaDetail[Buka Detail Pengaduan]
    BukaDetail --> PilihanAksi{Pilih Aksi Tindakan}

    %% Sub Aksi Kelola
    PilihanAksi -->|Ubah Status| UpdateStatus[Pilih Status Baru: Diproses/Selesai/Ditolak]
    UpdateStatus --> SimpanStatus[Sistem Update Status & Tulis Log Perubahan]
    
    PilihanAksi -->|Tulis Tanggapan| InputTanggapan[Tulis Tanggapan Publik / Catatan Internal]
    InputTanggapan --> SimpanTanggapan[Sistem Menyimpan Tanggapan Baru]

    PilihanAksi -->|Eskalasi| Eskalasi[Klik Eskalasi ke Kepala Shift]
    Eskalasi --> InputCatatan[Tulis Catatan Eskalasi]
    InputCatatan --> SimpanEskalasi[Status berubah menjadi Menunggu Keputusan]

    SimpanStatus --> SelesaiAksi[Kembali ke Halaman Detail / Dashboard]
    SimpanTanggapan --> SelesaiAksi
    SimpanEskalasi --> SelesaiAksi

    %% Alur Export Laporan
    PilihMenu -->|Export Laporan| FilterLaporan[Filter Berdasarkan Kategori/Status/Bulan/Tahun]
    FilterLaporan --> CetakData[Unduh file CSV / Word / Cetak PDF]
    CetakData --> Dashboard

    PilihMenu -->|Logout| KeluarSistem[Logout dari Dashboard]
    KeluarSistem --> EndAdmin([Selesai])
    SelesaiAksi --> PilihMenu
```

### C. Activity Diagram: Super Admin (Kepala Shift)
Activity diagram ini menggambarkan aktivitas Super Admin yang memiliki hak akses penuh terhadap konfigurasi sistem, data pengguna, dan penanganan tiket eskalasi.

```mermaid
flowchart TD
    StartSuper([Mulai]) --> FormLoginSuper[Mengakses Halaman Login]
    FormLoginSuper --> InputAuthSuper[Masukkan Kredensial Akun]
    InputAuthSuper --> CekAuthSuper{Apakah Valid?}
    CekAuthSuper -->|Tidak| GagalSuper[Kembali ke Form Login]
    GagalSuper --> FormLoginSuper
    CekAuthSuper -->|Ya| DashboardSuper[Masuk ke Dashboard Super Admin]

    DashboardSuper --> PilihMenuSuper{Pilih Menu}

    %% Kelola User
    PilihMenuSuper -->|Kelola User| ListUser[Lihat Daftar Karyawan]
    ListUser --> AksiUser{Pilih Aksi}
    AksiUser -->|Tambah Baru| FormUser[Isi Data Karyawan & Tentukan Role]
    AksiUser -->|Ubah Status| StatusUser[Edit Informasi / Aktifkan / Nonaktifkan Akun]
    FormUser --> SimpanUser[Simpan Data ke Database]
    StatusUser --> SimpanUser
    SimpanUser --> DashboardSuper

    %% Kelola Pengaturan
    PilihMenuSuper -->|Landing Page Settings| FormSetting[Ubah Konten Running Text, Tautan YouTube, Hero Section]
    FormSetting --> SimpanSetting[Update Setting Keys di Database]
    SimpanSetting --> DashboardSuper

    %% Proses Eskalasi & Hapus
    PilihMenuSuper -->|Penanganan Tiket| BukaTiket[Buka Tiket Berstatus Menunggu Keputusan]
    BukaTiket --> AksiTiket{Tentukan Keputusan}
    AksiTiket -->|Selesaikan / Tolak| UpdateTiket[Update Status Akhir & Beri Catatan]
    AksiTiket -->|Hapus Tiket| HapusTiket[Hapus Tiket Permanen dari Sistem]
    UpdateTiket --> SimpanTiket[Sistem Update Database]
    HapusTiket --> SimpanTiket
    SimpanTiket --> DashboardSuper

    PilihMenuSuper -->|Logout| LogoutSuper[Keluar dari Sistem]
    LogoutSuper --> EndSuper([Selesai])
```

---

## 3. Entity Relationship Diagram (ERD)

ERD di bawah ini menggunakan notasi Crow's Foot untuk menggambarkan entitas, atribut-atribut pembentuknya, tipe data, serta kardinalitas relasi antar entitas dalam sistem database Sipena.

### Diagram ERD
```mermaid
erDiagram
    USERS {
        bigint id PK
        string nama
        string username UK
        string password
        enum role
        boolean is_active
        datetime last_login
        timestamp created_at
        timestamp updated_at
    }
    COMPLAINTS {
        bigint id PK
        string ticket_no UK
        string public_token UK
        string nama_pelanggan
        string nomor_wa
        string nomor_wa_clean
        enum kategori
        string kategori_lain
        text keterangan
        string struk_file
        string dokumen_file
        enum status
        bigint assigned_admin_id FK
        bigint escalated_to_id FK
        timestamp created_at
        timestamp updated_at
        datetime closed_at
    }
    COMPLAINT_RESPONSES {
        bigint id PK
        bigint complaint_id FK
        bigint user_id FK
        enum sender_role
        enum visibility
        text message
        string attachment_file
        timestamp created_at
        timestamp updated_at
    }
    STATUS_LOGS {
        bigint id PK
        bigint complaint_id FK
        string old_status
        string new_status
        bigint changed_by FK
        text note
        timestamp created_at
        timestamp updated_at
    }
    LANDING_SETTINGS {
        bigint id PK
        string setting_key UK
        text setting_value
        timestamp created_at
        timestamp updated_at
    }

    USERS ||--o{ COMPLAINTS : "menangani (assigned_admin)"
    USERS ||--o{ COMPLAINTS : "menerima eskalasi (escalated_to)"
    USERS ||--o{ COMPLAINT_RESPONSES : "memberikan respons"
    USERS ||--o{ STATUS_LOGS : "mengubah status (changed_by)"
    COMPLAINTS ||--o{ COMPLAINT_RESPONSES : "memiliki tanggapan"
    COMPLAINTS ||--o{ STATUS_LOGS : "memiliki riwayat status"
```

---

## 4. Struktur Tabel Database

Berikut adalah spesifikasi teknis dari masing-masing tabel database yang digunakan oleh sistem berdasarkan migration database Laravel:

### A. Tabel `users`
Tabel ini digunakan untuk menyimpan data akun pengguna sistem (Admin dan Super Admin).

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik pengguna |
| `nama` | string(255) | Not Null | Nama lengkap pengguna |
| `username` | string(255) | Unique, Not Null | Nama pengguna untuk login |
| `password` | string(255) | Not Null | Kata sandi yang telah di-hash (Bcrypt) |
| `role` | enum('admin', 'super_admin') | Not Null, Default: 'admin' | Hak akses level pengguna |
| `is_active` | boolean | Default: true | Status keaktifan akun pengguna |
| `last_login` | datetime | Nullable | Catatan waktu login terakhir |
| `created_at` | timestamp | Nullable | Waktu pembuatan baris data |
| `updated_at` | timestamp | Nullable | Waktu perubahan terakhir data |

### B. Tabel `complaints`
Tabel ini menyimpan seluruh informasi data pengaduan/keluhan yang diajukan oleh pelanggan.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik pengaduan |
| `ticket_no` | string(30) | Unique, Index, Not Null | Nomor tiket keluhan (Format: SPN-YYYYMMDD-XXXX) |
| `public_token` | string(100) | Unique, Not Null | Token rahasia untuk melacak pengaduan publik |
| `nama_pelanggan`| string(100) | Not Null | Nama lengkap pelapor |
| `nomor_wa` | string(30) | Not Null | Nomor WhatsApp pelapor |
| `nomor_wa_clean`| string(30) | Index, Not Null | Nomor WhatsApp pelapor (hanya angka) untuk optimasi pencarian |
| `kategori` | enum(...) | Index, Not Null | Kategori keluhan: 'pelayanan', 'return_produk', 'produk', 'masalah_lain' |
| `kategori_lain` | string(150) | Nullable | Spesifikasi keluhan jika memilih kategori 'masalah_lain' |
| `keterangan` | text | Nullable | Uraian lengkap mengenai kronologi keluhan |
| `struk_file` | string(255) | Nullable | Path lokasi file unggah struk belanja (wajib untuk return_produk) |
| `dokumen_file` | string(255) | Nullable | Path lokasi file unggah dokumen bukti pendukung lainnya |
| `status` | enum(...) | Index, Not Null, Default: 'diajukan' | Status pengaduan: 'diajukan', 'diproses', 'diteruskan', 'menunggu_keputusan', 'ditanggapi', 'selesai', 'ditolak' |
| `assigned_admin_id` | bigint | Foreign Key (users), Nullable | ID Admin yang ditugaskan menangani pengaduan |
| `escalated_to_id` | bigint | Foreign Key (users), Nullable | ID Super Admin tempat tiket ini dieskalasikan |
| `created_at` | timestamp | Index, Nullable | Tanggal pengaduan diajukan oleh pelanggan |
| `updated_at` | timestamp | Nullable | Tanggal perubahan data pengaduan |
| `closed_at` | datetime | Nullable | Tanggal keluhan dinyatakan selesai/ditutup |

### C. Tabel `complaint_responses`
Tabel ini digunakan untuk menyimpan seluruh riwayat percakapan/tanggapan dari keluhan yang terdaftar.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik tanggapan |
| `complaint_id` | bigint | Foreign Key (complaints), Not Null | ID pengaduan yang ditanggapi |
| `user_id` | bigint | Foreign Key (users), Nullable | ID pengguna (staff) yang membalas |
| `sender_role` | enum('admin','super_admin','system') | Default: 'admin' | Peran dari pengirim tanggapan |
| `visibility` | enum('internal','public') | Index, Default: 'internal' | Visibilitas tanggapan: 'internal' (rahasia staff) atau 'public' (bisa dilihat pelanggan) |
| `message` | text | Not Null | Isi teks tanggapan |
| `attachment_file` | string(255) | Nullable | Path lokasi file dokumen lampiran pendukung tanggapan |
| `created_at` | timestamp | Index, Nullable | Tanggal tanggapan dikirimkan |
| `updated_at` | timestamp | Nullable | Tanggal perubahan tanggapan |

### D. Tabel `status_logs`
Tabel ini digunakan untuk mencatat riwayat perubahan status tiket pengaduan secara transparan.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik log status |
| `complaint_id` | bigint | Foreign Key (complaints), Not Null | ID pengaduan yang mengalami perubahan status |
| `old_status` | string(50) | Nullable | Status sebelum diubah (kosong pada pembuatan awal) |
| `new_status` | string(50) | Not Null | Status baru setelah diubah |
| `changed_by` | bigint | Foreign Key (users), Nullable | ID pengguna (staff) yang melakukan perubahan status |
| `note` | text | Nullable | Alasan atau catatan tambahan terkait perubahan status |
| `created_at` | timestamp | Index, Nullable | Tanggal log status dicatat |
| `updated_at` | timestamp | Nullable | Tanggal log status diperbarui |

### E. Tabel `landing_settings`
Tabel ini menampung data konfigurasi antarmuka landing page dinamis yang dikelola oleh Super Admin.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik pengaturan |
| `setting_key` | string(100) | Unique, Not Null | Kata kunci pengaturan (contoh: `running_text`, `youtube_url`) |
| `setting_value`| text | Nullable | Nilai teks atau konten dari pengaturan |
| `created_at` | timestamp | Nullable | Tanggal konfigurasi dibuat |
| `updated_at` | timestamp | Nullable | Tanggal konfigurasi diperbarui |

---

## 5. Relasi Database dan Penjelasannya

Relasi antartabel di dalam database Sipena dibangun dengan merujuk pada integritas data referensial (referential integrity) sebagai berikut:

### A. Relasi Antara Tabel `users` dengan `complaints`
Hubungan antara tabel `users` dengan tabel `complaints` terbagi menjadi dua jalur relasi kunci asing (Foreign Key):
1. **Relasi Penugasan (`assigned_admin_id` -> `users.id`)**:
   - Satu pengguna (`users`) dengan peran Admin atau Super Admin dapat ditugaskan untuk menangani banyak tiket pengaduan pelanggan (**One-to-Many**). Setiap pengaduan (`complaints`) hanya ditangani oleh maksimal satu admin pada satu waktu.
   - *Kalimat Representasi*: "Satu admin dapat memproses nol hingga banyak pengaduan pelanggan, sedangkan setiap pengaduan pelanggan hanya dapat dialokasikan penanganannya kepada maksimal satu admin."
2. **Relasi Eskalasi (`escalated_to_id` -> `users.id`)**:
   - Satu pengguna dengan tingkat otoritas Kepala Shift/Super Admin dapat menerima eskalasi dari banyak pengaduan (**One-to-Many**). Sebaliknya, setiap pengaduan hanya dapat diteruskan ke satu Super Admin tertentu.
   - *Kalimat Representasi*: "Satu Kepala Shift dapat ditunjuk untuk menerima eskalasi keputusan dari nol hingga banyak keluhan pelanggan, sedangkan satu keluhan pelanggan hanya dapat dieskalasikan statusnya kepada maksimal satu Kepala Shift."

### B. Relasi Antara Tabel `complaints` dengan `complaint_responses`
Hubungan antara tabel `complaints` dengan tabel `complaint_responses` mengikat setiap tanggapan ke pengaduan induknya.
- **Relasi Kepemilikan Tanggapan (`complaint_responses.complaint_id` -> `complaints.id`)**:
   - Satu pengaduan dapat memuat banyak tanggapan atau respons, baik berupa diskusi internal sesama staff maupun balasan resmi kepada pelanggan (**One-to-Many**). Setiap baris tanggapan wajib merujuk secara eksklusif pada satu nomor pengaduan saja. Jika data induk pengaduan dihapus (`ON DELETE CASCADE`), maka seluruh riwayat tanggapan yang terikat juga akan terhapus secara otomatis.
   - *Kalimat Representasi*: "Satu tiket pengaduan pelanggan dapat memiliki banyak tanggapan pesan, sedangkan setiap pesan tanggapan hanya dapat merujuk ke satu tiket pengaduan yang bersangkutan."

### C. Relasi Antara Tabel `users` dengan `complaint_responses`
Hubungan ini mencatat siapa pembuat dari masing-masing tanggapan.
- **Relasi Penulis Tanggapan (`complaint_responses.user_id` -> `users.id`)**:
   - Satu pengguna (`users`) dapat menulis banyak pesan tanggapan di berbagai tiket pengaduan (**One-to-Many**). Sebaliknya, setiap pesan tanggapan ditulis oleh satu user terdaftar (atau bernilai null jika tanggapan dibuat otomatis oleh sistem).
   - *Kalimat Representasi*: "Satu user sistem dapat menulis nol hingga banyak pesan tanggapan pengaduan, sedangkan setiap pesan tanggapan hanya dapat diidentifikasi kepemilikan penulisnya oleh satu user sistem saja."

### D. Relasi Antara Tabel `complaints` dengan `status_logs`
Hubungan ini mencatat linimasa perubahan status yang dialami oleh suatu tiket pengaduan.
- **Relasi Log Perubahan Status (`status_logs.complaint_id` -> `complaints.id`)**:
   - Satu pengaduan dapat melalui beberapa tahapan siklus status (contoh dari `diajukan` -> `diproses` -> `ditanggapi` -> `selesai`) sehingga menghasilkan banyak catatan status log (**One-to-Many**). Setiap baris log status wajib terikat secara eksklusif ke satu pengaduan. Hubungan ini menggunakan aturan penghapusan berjenjang (`ON DELETE CASCADE`) untuk mempermudah kebersihan data.
   - *Kalimat Representasi*: "Satu pengaduan dapat menghasilkan banyak log riwayat perubahan status seiring berjalannya proses penanganan, sedangkan setiap log riwayat status hanya mendokumentasikan perubahan pada satu pengaduan saja."

### E. Relasi Antara Tabel `users` dengan `status_logs`
Hubungan ini mencatat pertanggungjawaban aktor yang mengubah status tiket.
- **Relasi Aktor Pengubah Status (`status_logs.changed_by` -> `users.id`)**:
   - Satu user (`users`) dapat melakukan pengubahan status pada berbagai keluhan pelanggan sebanyak beberapa kali (**One-to-Many**). Setiap log status mencatat satu user ID penanggung jawab tindakan tersebut.
   - *Kalimat Representasi*: "Satu admin dapat memicu pencatatan nol hingga banyak log perubahan status akibat tindakannya memperbarui tiket pengaduan, sedangkan setiap baris log perubahan status hanya mencatat satu admin yang melakukan tindakan perubahan tersebut."
