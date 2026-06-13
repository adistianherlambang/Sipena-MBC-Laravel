# Dokumentasi Perancangan Sistem Pengaduan Pelanggan (Sipena) MBC Swalayan

Berikut adalah perancangan sistem yang diusulkan oleh penulis sebagai acuan sebelum proses pembuatan kode program dilakukan.

---

## 2. Desain Sistem
Pada tahap ini, penulis menerjemahkan sistem yang dapat menentukan proses dan data yang diperlukan pada sistem pengaduan yang sudah dirancang sebelum pembuatan koding.

### 1) Use Case Diagram
Use Case Diagram menggambarkan fungsionalitas sistem dari sudut pandang interaksi antara pengguna atau aktor dengan sistem pengaduan pelanggan yang dinamakan Sipena pada MBC Swalayan. Diagram use case tersebut adalah sebagai berikut:

```mermaid
graph TD
    %% Actors
    ActorPelanggan["Pelanggan (Konsumen)"]
    ActorAdmin["Admin (Karyawan/Staff)"]
    ActorSuperAdmin["Super Admin (Kepala Shift)"]

    subgraph Sipena [Sistem Pengaduan Pelanggan MBC Swalayan]
        UC1((Mengajukan Pengaduan))
        UC2((Melacak Tiket Pengaduan))
        UC3((Melihat Tanggapan Publik))
        UC4((Autentikasi/Login))
        UC5((Mengelola Pengaduan))
        UC6((Eskalasi Tiket))
        UC7((Export Laporan))
        UC8((Mengelola User Karyawan))
        UC9((Mengatur Landing Page))
        UC10((Menghapus Pengaduan))
    end

    %% Connections
    ActorPelanggan --> UC1
    ActorPelanggan --> UC2
    ActorPelanggan --> UC3

    ActorAdmin --> UC4
    ActorAdmin --> UC5
    ActorAdmin --> UC6
    ActorAdmin --> UC7

    ActorSuperAdmin --> UC4
    ActorSuperAdmin --> UC5
    ActorSuperAdmin --> UC7
    ActorSuperAdmin --> UC8
    ActorSuperAdmin --> UC9
    ActorSuperAdmin --> UC10

    %% Styles
    style ActorPelanggan fill:#fff,stroke:#000,stroke-width:2px
    style ActorAdmin fill:#fff,stroke:#000,stroke-width:2px
    style ActorSuperAdmin fill:#fff,stroke:#000,stroke-width:2px
    style Sipena fill:#fff,stroke:#000,stroke-width:1px
```

*Gambar 2.1. Use Case Diagram Sistem Informasi Pengaduan Pelanggan (Sipena) Pada MBC Swalayan.*

---

### 2) Activity Diagram
Activity diagram menunjukkan aliran kerja dari use case diagram untuk masing-masing role pengguna.

#### (1) Activity Diagram Admin
Pada Gambar 2.2 berikut merupakan activity diagram admin dalam mengelola data pada sistem informasi pengaduan pelanggan MBC Swalayan mulai dari proses autentikasi login hingga melakukan aksi pengelolaan.

```mermaid
flowchart TD
    StartAdmin([Mulai]) --> FormLogin[Mengakses Halaman Login Admin]
    FormLogin --> InputAuth[Masukkan Username & Password]
    InputAuth --> SubmitLogin[Klik Login]
    SubmitLogin --> CekAuth{Kredensial Valid?}
    
    CekAuth -->|Tidak| FormLogin
    CekAuth -->|Ya| Dashboard[Masuk Halaman Dashboard Admin]

    Dashboard --> PilihAksi{Pilih Aktivitas}

    %% Kelola
    PilihAksi -->|Kelola Pengaduan| DetailPengaduan[Buka Detail Pengaduan]
    DetailPengaduan --> AksiTindakan{Pilih Tindakan}
    
    AksiTindakan -->|Ubah Status| UpdateStatus[Simpan Perubahan Status & Riwayat]
    AksiTindakan -->|Tulis Balasan| SimpanBalasan[Simpan Tanggapan Publik / Internal]
    AksiTindakan -->|Eskalasi| SimpanEskalasi[Eskalasi Status ke Kepala Shift]
    
    UpdateStatus --> Dashboard
    SimpanBalasan --> Dashboard
    SimpanEskalasi --> Dashboard

    %% Laporan
    PilihAksi -->|Export Laporan| CetakLaporan[Cetak PDF / Unduh Word & CSV]
    CetakLaporan --> Dashboard

    %% Logout
    PilihAksi -->|Logout| Logout[Keluar dari Sistem]
    Logout --> EndAdmin([Selesai])

    classDef default fill:#fff,stroke:#000,stroke-width:1px,color:#000;
```
*Gambar 2.2. Activity Diagram Admin Sistem Informasi Pengaduan Pelanggan (Sipena).*

---

#### (2) Activity Diagram Konsumen (Pelanggan)
Pada Gambar 2.3 berikut merupakan activity diagram pada konsumen dalam menerima informasi, melacak status pengaduan, dan mengirimkan keluhan pada sistem informasi pengaduan pelanggan MBC Swalayan.

```mermaid
flowchart TD
    StartKonsumen([Mulai]) --> MasukWeb[Mengakses Website Sipena]
    MasukWeb --> PilihMenu{Pilih Menu}

    %% Kirim Pengaduan
    PilihMenu -->|Kirim Keluhan| FormComplaint[Isi Form Pengaduan]
    FormComplaint --> UploadBukti[Unggah Struk Belanja / Bukti Foto]
    UploadBukti --> SubmitComplaint[Kirim Pengaduan]
    SubmitComplaint --> Validasi{Input Valid?}
    
    Validasi -->|Tidak| FormComplaint
    Validasi -->|Ya| SimpanData[Simpan & Generate Nomor Tiket & Token]
    SimpanData --> TampilHalamanTiket[Tampilkan Tiket untuk Pelanggan]
    TampilHalamanTiket --> EndKonsumen

    %% Melacak Status
    PilihMenu -->|Tracking Status| InputLacak[Masukkan Nomor Tiket & Nomor WhatsApp]
    InputLacak --> CariTiket[Cari Data Pengaduan]
    CariTiket --> CekTiket{Data Ditemukan?}
    
    CekTiket -->|Tidak| InputLacak
    CekTiket -->|Ya| TampilkanStatus[Tampilkan Status & Riwayat Tanggapan]
    TampilkanStatus --> EndKonsumen([Selesai])

    classDef default fill:#fff,stroke:#000,stroke-width:1px,color:#000;
```
*Gambar 2.3. Activity Diagram Konsumen Sistem Informasi Pengaduan Pelanggan (Sipena).*

---

#### (3) Activity Diagram Super Admin
Pada Gambar 2.4 berikut merupakan activity diagram Super Admin dalam melakukan pengelolaan data karyawan, konfigurasi landing page, serta menangani keluhan tingkat lanjut yang membutuhkan eskalasi di dalam sistem pengaduan pelanggan.

```mermaid
flowchart TD
    StartSuper([Mulai]) --> FormLoginSuper[Mengakses Halaman Login]
    FormLoginSuper --> InputAuthSuper[Masukkan Kredensial Akun]
    InputAuthSuper --> CekAuthSuper{Apakah Valid?}
    CekAuthSuper -->|Tidak| FormLoginSuper
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

    classDef default fill:#fff,stroke:#000,stroke-width:1px,color:#000;
```
*Gambar 2.4. Activity Diagram Super Admin Sistem Informasi Pengaduan Pelanggan (Sipena).*

---

## 3. Desain Prosedur Sistem Yang Diusulkan
Proses desain akan menerjemahkan sebuah perancangan perangkat lunak yang di mana sebelumnya diperkirakan untuk diimplementasikan ke koding. Berikut adalah langkah untuk melakukan desain sistem: desain database dan desain interface.

### a. Desain Database
Desain database terbagi menjadi dua bagian, yaitu Entity Relationship Diagram sistem informasi pengaduan pelanggan pada MBC Swalayan serta tabel.

#### 1. Entity Relationship Diagram sistem informasi pengaduan pelanggan pada MBC Swalayan
Berdasarkan Gambar 3.1, Entity Relationship Diagram sistem informasi pengaduan pelanggan pada MBC Swalayan terbagi menjadi lima tabel, yaitu tabel users, complaints, complaint_responses, status_logs, serta landing_settings, di mana setiap entitas memiliki beberapa atribut.

```mermaid
flowchart TD
    %% ENTITIES
    U[users]
    C[complaints]
    CR[complaint_responses]
    SL[status_logs]
    LS[landing_settings]

    %% RELATIONSHIPS (Belah Ketupat)
    R1{menangani}
    R2{menerima}
    R3{menulis}
    R4{mengubah}
    R5{memiliki}
    R6{memiliki}

    %% ATTRIBUTES (Bulat Lonjong)
    u_id(["<u>id</u>"])
    u_nama(["nama"])
    u_username(["username"])
    u_password(["password"])
    u_role(["role"])
    u_is_active(["is_active"])
    u_last_login(["last_login"])

    U --- u_id
    U --- u_nama
    U --- u_username
    U --- u_password
    U --- u_role
    U --- u_is_active
    U --- u_last_login

    c_id(["<u>id</u>"])
    c_ticket(["ticket_no"])
    c_token(["public_token"])
    c_pelanggan(["nama_pelanggan"])
    c_wa(["nomor_wa"])
    c_kat(["kategori"])
    c_kat_lain(["kategori_lain"])
    c_ket(["keterangan"])
    c_struk(["struk_file"])
    c_dok(["dokumen_file"])
    c_status(["status"])
    c_closed(["closed_at"])

    C --- c_id
    C --- c_ticket
    C --- c_token
    C --- c_pelanggan
    C --- c_wa
    C --- c_kat
    C --- c_kat_lain
    C --- c_ket
    C --- c_struk
    C --- c_dok
    C --- c_status
    C --- c_closed

    cr_id(["<u>id</u>"])
    cr_sender(["sender_role"])
    cr_vis(["visibility"])
    cr_msg(["message"])
    cr_att(["attachment_file"])

    CR --- cr_id
    CR --- cr_sender
    CR --- cr_vis
    CR --- cr_msg
    CR --- cr_att

    sl_id(["<u>id</u>"])
    sl_old(["old_status"])
    sl_new(["new_status"])
    sl_note(["note"])

    SL --- sl_id
    SL --- sl_old
    SL --- sl_new
    SL --- sl_note

    ls_id(["<u>id</u>"])
    ls_key(["setting_key"])
    ls_val(["setting_value"])

    LS --- ls_id
    LS --- ls_key
    LS --- ls_val

    %% CONNECTIONS
    U ---|1| R1
    R1 -->|N| C
    
    U ---|1| R2
    R2 -->|N| C
    
    U ---|1| R3
    R3 -->|N| CR
    
    U ---|1| R4
    R4 -->|N| SL
    
    C ---|1| R5
    R5 -->|N| CR
    
    C ---|1| R6
    R6 -->|N| SL

    %% STYLING
    classDef default fill:#fff,stroke:#000,stroke-width:1px,color:#000;
    classDef entity fill:#fff,stroke:#000,stroke-width:2px,color:#000;
    classDef relationship fill:#fff,stroke:#000,stroke-width:2px,color:#000;

    class U,C,CR,SL,LS entity;
    class R1,R2,R3,R4,R5,R6 relationship;
    linkStyle default stroke:#000,stroke-width:1.5px;
```
*Gambar 3.1. Entity Relationship Diagram (ERD) Sipena MBC Swalayan.*

---

#### 2) Tabel
Tabel database atau basis data adalah kumpulan file yang berkaitan dengan program, yang di mana untuk menyimpan data sistem informasi pengaduan pelanggan pada MBC Swalayan dibutuhkan database. Berikut ini adalah tabel – tabel yang berada dalam database:

##### (1) Tabel `users`
Tabel `users` diperlukan untuk mendaftarkan akun administrator sistem dan berfungsi untuk memproses otentikasi login serta identifikasi hak akses level pengguna, baik Admin maupun Super Admin.

Nama Tabel	: users
Attribute   	: id, nama, username, password, role, is_active, last_login, created_at, updated_at
Primary key 	: id
Jumlah field	: 9

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint unsigned | NO | PRI | NULL | auto_increment |
| `nama` | varchar(255) | NO | | NULL | |
| `username` | varchar(255) | NO | UNI | NULL | |
| `password` | varchar(255) | NO | | NULL | |
| `role` | enum('admin','super_admin') | NO | | 'admin' | |
| `is_active` | tinyint(1) | NO | | 1 | |
| `last_login` | datetime | YES | | NULL | |
| `created_at` | timestamp | YES | | NULL | |
| `updated_at` | timestamp | YES | | NULL | |

##### (2) Tabel `complaints`
Tabel `complaints` diperlukan untuk merekam seluruh rincian keluhan masuk yang diajukan oleh konsumen.

Nama Tabel	: complaints
Attribute   	: id, ticket_no, public_token, nama_pelanggan, nomor_wa, nomor_wa_clean, kategori, kategori_lain, keterangan, struk_file, dokumen_file, status, assigned_admin_id, escalated_to_id, created_at, updated_at, closed_at
Primary key 	: id
Jumlah field	: 17

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint unsigned | NO | PRI | NULL | auto_increment |
| `ticket_no` | varchar(30) | NO | UNI | NULL | |
| `public_token` | varchar(100) | NO | UNI | NULL | |
| `nama_pelanggan` | varchar(100) | NO | | NULL | |
| `nomor_wa` | varchar(30) | NO | | NULL | |
| `nomor_wa_clean` | varchar(30) | NO | | NULL | |
| `kategori` | enum('pelayanan','return_produk','produk','masalah_lain') | NO | | NULL | |
| `kategori_lain` | varchar(150) | YES | | NULL | |
| `keterangan` | text | YES | | NULL | |
| `struk_file` | varchar(255) | YES | | NULL | |
| `dokumen_file` | varchar(255) | YES | | NULL | |
| `status` | enum('diajukan','diproses','diteruskan','menunggu_keputusan','ditanggapi','selesai','ditolak') | NO | | 'diajukan' | |
| `assigned_admin_id` | bigint unsigned | YES | MUL | NULL | |
| `escalated_to_id` | bigint unsigned | YES | MUL | NULL | |
| `created_at` | timestamp | YES | | NULL | |
| `updated_at` | timestamp | YES | | NULL | |
| `closed_at` | datetime | YES | | NULL | |

##### (3) Tabel `complaint_responses`
Tabel `complaint_responses` diperlukan untuk mendata percakapan dan respon balasan dari staff admin maupun sistem.

Nama Tabel	: complaint_responses
Attribute   	: id, complaint_id, user_id, sender_role, visibility, message, attachment_file, created_at, updated_at
Primary key 	: id
Jumlah field	: 9

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint unsigned | NO | PRI | NULL | auto_increment |
| `complaint_id` | bigint unsigned | NO | MUL | NULL | |
| `user_id` | bigint unsigned | YES | MUL | NULL | |
| `sender_role` | enum('admin','super_admin','system') | NO | | 'admin' | |
| `visibility` | enum('internal','public') | NO | | 'internal' | |
| `message` | text | NO | | NULL | |
| `attachment_file` | varchar(255) | YES | | NULL | |
| `created_at` | timestamp | YES | | NULL | |
| `updated_at` | timestamp | YES | | NULL | |

##### (4) Tabel `status_logs`
Tabel `status_logs` diperlukan untuk mencatat riwayat perubahan status pelaporan keluhan secara berurutan.

Nama Tabel	: status_logs
Attribute   	: id, complaint_id, old_status, new_status, changed_by, note, created_at, updated_at
Primary key 	: id
Jumlah field	: 8

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint unsigned | NO | PRI | NULL | auto_increment |
| `complaint_id` | bigint unsigned | NO | MUL | NULL | |
| `old_status` | varchar(50) | YES | | NULL | |
| `new_status` | varchar(50) | NO | | NULL | |
| `changed_by` | bigint unsigned | YES | MUL | NULL | |
| `note` | text | YES | | NULL | |
| `created_at` | timestamp | YES | | NULL | |
| `updated_at` | timestamp | YES | | NULL | |

##### (5) Tabel `landing_settings`
Tabel `landing_settings` diperlukan untuk menampung pengaturan data antarmuka halaman beranda secara dinamis.

Nama Tabel	: landing_settings
Attribute   	: id, setting_key, setting_value, created_at, updated_at
Primary key 	: id
Jumlah field	: 5

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | bigint unsigned | NO | PRI | NULL | auto_increment |
| `setting_key` | varchar(100) | NO | UNI | NULL | |
| `setting_value` | text | YES | | NULL | |
| `created_at` | timestamp | YES | | NULL | |
| `updated_at` | timestamp | YES | | NULL | |

---

#### 3) Relasi Tabel
Berdasarkan Gambar 3.2 relasi tabel ini memiliki 5 tabel, yaitu tabel `users`, `complaints`, `complaint_responses`, `status_logs`, dan `landing_settings`. Berikut relasi tabel:

```mermaid
erDiagram
    users {
        bigint id PK
        string nama
        string username
        string password
        enum role
        boolean is_active
        datetime last_login
    }
    complaints {
        bigint id PK
        string ticket_no
        string public_token
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
    }
    complaint_responses {
        bigint id PK
        bigint complaint_id FK
        bigint user_id FK
        enum sender_role
        enum visibility
        text message
        string attachment_file
    }
    status_logs {
        bigint id PK
        bigint complaint_id FK
        string old_status
        string new_status
        bigint changed_by FK
        text note
    }
    landing_settings {
        bigint id PK
        string setting_key
        text setting_value
    }

    users ||--o{ complaints : "menangani (assigned_admin_id)"
    users ||--o{ complaints : "menerima (escalated_to_id)"
    users ||--o{ complaint_responses : "menulis (user_id)"
    users ||--o{ status_logs : "mengubah (changed_by)"
    complaints ||--o{ complaint_responses : "memiliki (complaint_id)"
    complaints ||--o{ status_logs : "memiliki (complaint_id)"
```
*Gambar 3.2. Relasi Tabel Database Sipena MBC Swalayan.*

---

### b. Desain Interface

#### 1) Rancangan Form Login Admin
Tampilan form login digunakan untuk memberikan hak akses kepada administrator, yaitu Admin atau Super Admin, untuk masuk ke halaman dashboard internal sistem. Form tersebut dirancang sebagai berikut:

##### Halaman Form Login Admin
```
+-------------------------------------------------------------------+
|                            LOGIN ADMIN                            |
+-------------------------------------------------------------------+
|                                                                   |
|   Username : [_________________________________________________]  |
|                                                                   |
|   Password : [_________________________________________________]  |
|                                                                   |
|                       [ MASUK ]                                   |
|                                                                   |
+-------------------------------------------------------------------+
```
*Gambar 3.3. Rancangan Form Login Admin.*

##### Tabel 3.1. Rancangan Tombol Form Login Admin
| No | Tombol | Fungsi |
| :--- | :--- | :--- |
| 1 | `MASUK` | Berfungsi untuk mengirimkan kredensial berupa username dan password ke sistem untuk divalidasi. Jika sukses, admin dialihkan ke halaman dashboard. |

---

#### 2) Rancangan Form Pengaduan (Konsumen)
Tampilan halaman pengaduan adalah halaman formulir publik ketika konsumen mengakses website sistem pengaduan untuk menulis keluhan. Rancangan ini adalah sebagai berikut:

##### Halaman Form Pengaduan Konsumen
```
+-------------------------------------------------------------------+
|                   FORMULIR PENGADUAN PELANGGAN                    |
+-------------------------------------------------------------------+
|                                                                   |
|  Nama Pelanggan : [____________________________________________]  |
|                                                                   |
|  Nomor WhatsApp : [____________________________________________]  |
|                                                                   |
|  Kategori       : [ Pelayanan / Produk / Return / Masalah Lain v] |
|                                                                   |
|  Struk Belanja  : [ Pilih File ] (Wajib jika kategori return)     |
|                                                                   |
|  Dokumen Bukti  : [ Pilih File ] (Opsional)                       |
|                                                                   |
|  Keterangan     :                                                 |
|  +-------------------------------------------------------------+  |
|  | Tulis isi keluhan Anda di sini...                           |  |
|  +-------------------------------------------------------------+  |
|                                                                   |
|                       [ KIRIM ADUAN ]                             |
|                                                                   |
+-------------------------------------------------------------------+
```
*Gambar 3.4. Rancangan Form Pengaduan Konsumen.*

##### Tabel 3.2. Rancangan Tombol Form Pengaduan Konsumen
| No | Tombol | Fungsi |
| :--- | :--- | :--- |
| 1 | `Pilih File` | Membuka galeri/penyimpanan perangkat untuk memilih dokumen struk belanja atau dokumen bukti. |
| 2 | `KIRIM ADUAN`| Mengirimkan data formulir keluhan pelanggan ke database dan membuat nomor tiket otomatis. |

---

#### 3) Rancangan Halaman Tracking (Konsumen)
Tampilan halaman tracking digunakan oleh konsumen untuk melacak status aduan dengan nomor tiket mereka. Rancangan ini adalah sebagai berikut:

##### Halaman Tracking Pengaduan
```
+-------------------------------------------------------------------+
|                     CEK STATUS PENGADUAN                          |
+-------------------------------------------------------------------+
|                                                                   |
|  Nomor Tiket    : [ Contoh: SPN-20260612-0001 _________________]  |
|                                                                   |
|  Nomor WhatsApp : [ Contoh: 081234567890 ______________________]  |
|                                                                   |
|                       [ LACAK STATUS ]                            |
|                                                                   |
+-------------------------------------------------------------------+
```
*Gambar 3.5. Rancangan Halaman Tracking.*

##### Tabel 3.3. Rancangan Tombol Halaman Tracking
| No | Tombol | Fungsi |
| :--- | :--- | :--- |
| 1 | `LACAK STATUS` | Melakukan pencarian tiket pengaduan di database. Jika ditemukan, menampilkan halaman riwayat detail dan progress keluhan. |
