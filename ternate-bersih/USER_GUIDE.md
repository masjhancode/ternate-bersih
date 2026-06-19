# Panduan Pengguna & Alur Sistem Ternate Bersih

Dokumen ini berisi panduan penggunaan sistem **Ternate Bersih (SIPAS Ternate)**, platform pelaporan tumpukan sampah berbasis geolokasi untuk menjembatani masyarakat Kota Ternate dengan Dinas Lingkungan Hidup (DLH).

## 1. Flowchart Sistem

Berikut adalah representasi visual dari alur kerja (workflow) laporan sampah dari awal hingga selesai:

```mermaid
graph TD
    %% Entitas
    M([Masyarakat / Pelapor])
    A([Administrator DLH])
    D([Petugas Armada / Driver])
    
    %% Proses
    subgraph Fase 1: Pelaporan
        M -- "Buka Mobile App" --> L1[Kunci Lokasi GPS]
        L1 -- "Ambil Foto & Detail" --> L2[Kirim Laporan]
        L2 --> S1((Status: Menunggu))
    end
    
    subgraph Fase 2: Verifikasi & Penugasan
        S1 -- "Notifikasi Realtime" --> A
        A -- "Cek Validitas" --> V1{Valid?}
        V1 -- "Tidak" --> S2((Status: Ditolak))
        V1 -- "Ya" --> S3((Status: Diverifikasi))
        S3 -- "Pilih Armada" --> T1[Tugaskan Truk]
        T1 --> S4((Status: Ditugaskan))
    end
    
    subgraph Fase 3: Eksekusi Lapangan
        S4 -- "Notifikasi Tugas" --> D
        D -- "Buka Peta & Rute" --> E1[Menuju Lokasi]
        E1 -- "Angkut Sampah" --> E2[Ambil Foto Bukti Bersih]
        E2 -- "Tekan Selesai" --> S5((Status: Selesai))
    end
    
    %% Styling
    classDef statusMenunggu fill:#f59e0b,stroke:#fff,stroke-width:2px,color:#fff;
    classDef statusDitolak fill:#ef4444,stroke:#fff,stroke-width:2px,color:#fff;
    classDef statusDiverifikasi fill:#3b82f6,stroke:#fff,stroke-width:2px,color:#fff;
    classDef statusDitugaskan fill:#8b5cf6,stroke:#fff,stroke-width:2px,color:#fff;
    classDef statusSelesai fill:#10b981,stroke:#fff,stroke-width:2px,color:#fff;
    
    class S1 statusMenunggu;
    class S2 statusDitolak;
    class S3 statusDiverifikasi;
    class S4 statusDitugaskan;
    class S5 statusSelesai;
```

---

## 2. Alur Kerja (Workflow) Detail

### Langkah 1: Masyarakat Melapor (Mobile)
Masyarakat yang menemukan tumpukan sampah liar membuka aplikasi mobile Ternate Bersih.
1. Menekan tombol **Lapor**.
2. Mengambil foto bukti tumpukan sampah.
3. Menekan tombol **"Kunci Lokasi (GPS)"**. Sistem akan otomatis mendeteksi koordinat dan mengubahnya menjadi alamat (Jalan, Kelurahan, Kecamatan).
4. Mengisi catatan tambahan dan memilih Kategori Laporan.
5. Menekan **Kirim Laporan**. Status awal adalah **Menunggu Verifikasi**. 
> [!NOTE]
> Pada tahap ini, masyarakat masih memiliki tombol "Hapus Laporan" jika mereka berubah pikiran atau salah melapor.

### Langkah 2: Verifikasi Admin (Web)
Laporan masuk ke Dashboard Web Administrator DLH.
1. Admin menerima indikator Lonceng Notifikasi secara realtime.
2. Admin mengecek validitas foto dan kejelasan lokasi.
3. Jika laporan dianggap tidak relevan (iseng), Admin berhak mengubah status menjadi **Ditolak**.
4. Jika valid, Admin menyetujui dan mengubah status menjadi **Diverifikasi**.
> [!IMPORTANT]
> Setelah status Diverifikasi, pelapor asli (masyarakat) sudah tidak bisa lagi menghapus laporan tersebut dari aplikasi mobile mereka.

### Langkah 3: Penugasan Armada (Web)
Setelah laporan diverifikasi, sampah harus segera diangkut.
1. Admin menekan tombol **"Tugaskan Armada"**.
2. Admin melihat daftar Truk/Driver yang berstatus aktif.
3. Admin memilih Driver yang dituju.
4. Status berubah menjadi **Ditugaskan** dan Driver yang bersangkutan mendapat notifikasi.

### Langkah 4: Penyelesaian Laporan (Mobile - Driver)
Driver yang sedang bertugas membuka aplikasi mobile mereka.
1. Driver melihat tugas baru di tab **Peta & Tugas**.
2. Driver mengikuti panduan Peta (G-Maps/OpenStreetMap) menuju titik koordinat.
3. Setelah sampai dan sampah selesai diangkut, Driver membuka **Detail Tugas**.
4. Driver wajib mengambil **Foto Bukti Bersih** di lokasi.
5. Menekan tombol **Selesaikan Tugas**. Status laporan secara permanen menjadi **Selesai**.

---

## 3. Panduan Fungsional Administrator

Sistem ini didesain sebagai **Closed System** (Sistem Tertutup) demi keamanan. Hal ini berarti pendaftaran publik secara terbuka dinonaktifkan.

### Manajemen Pengguna
Karena tidak ada halaman "Daftar" (Register), Admin memiliki kewajiban mutlak:
* Membuatkan akun secara manual untuk setiap Petugas Armada (Driver).
* Membuatkan akun untuk Operator atau Staf DLH lainnya.
* Menjaga kerahasiaan kata sandi bawaan (default) yang diberikan kepada Driver sebelum mereka bisa mengubahnya.

### Manajemen Master Data (Wilayah & Kategori)
Aplikasi Mobile sangat bergantung pada data yang dikendalikan oleh Web Backend:
* Admin harus memastikan ketersediaan data **Kecamatan** dan **Kelurahan**.
* Jika kelurahan tempat masyarakat melapor tidak ada dalam database, form aplikasi mobile akan *error*. Selalu mutakhirkan master data.

### Pemantauan (Dispatching)
Admin DLH berfungsi layaknya seorang *Dispatcher*.
* Selalu perhatikan ikon Lonceng Notifikasi di pojok kanan atas Dashboard.
* Jangan biarkan laporan menumpuk di status "Menunggu Verifikasi" atau "Diverifikasi". Semakin cepat Armada ditugaskan, semakin efisien penanganan sampah di kota Ternate.

### Ekspor Data Laporan
Sistem menyimpan rekam jejak setiap laporan.
* Gunakan menu **Ekspor Data** untuk mengunduh seluruh aktivitas harian, bulanan, maupun tahunan.
* File format Excel ini dapat digunakan sebagai laporan resmi pertanggungjawaban DLH.

---

## 4. Desain Database & Relasi (ERD)

Sistem Ternate Bersih didukung oleh arsitektur database relasional untuk menjaga integritas data pelaporan, wilayah, hingga penugasan armada. Berikut adalah diagram entitas beserta kardinalitas relasinya:

```mermaid
erDiagram
    USERS ||--o{ REPORTS : "melapor (Citizen)"
    USERS ||--o{ FLEETS : "mengemudikan (Driver)"
    USERS ||--o{ ASSIGNMENTS : "menugaskan (Admin)"
    USERS ||--o{ REPORT_PROGRESSES : "mencatat progres"
    
    REPORT_CATEGORIES ||--o{ REPORTS : "memiliki"
    REGIONS ||--o{ REGIONS : "parent_id (Kec -> Kel)"
    REGIONS ||--o{ REPORTS : "lokasi kejadian"
    
    REPORTS ||--o{ ASSIGNMENTS : "ditangani oleh"
    REPORTS ||--o{ REPORT_PROGRESSES : "memiliki riwayat"
    
    FLEETS ||--o{ ASSIGNMENTS : "menerima tugas"

    USERS {
        bigint id PK
        string name
        string email
        string password
        enum role "Administrator, Driver, Citizen"
        timestamp created_at
    }

    REGIONS {
        bigint id PK
        bigint parent_id FK "Kecamatan -> Kelurahan"
        string name
        enum type "district, village"
    }

    REPORT_CATEGORIES {
        bigint id PK
        string name
        text description
    }

    REPORTS {
        bigint id PK
        bigint user_id FK "Pelapor"
        bigint category_id FK
        bigint district_id FK
        bigint village_id FK
        text description
        string address
        decimal lat
        decimal lng
        string photo_path
        enum status "Menunggu, Diverifikasi, Ditugaskan, Selesai, Ditolak"
    }

    FLEETS {
        bigint id PK
        bigint user_id FK "Sopir/Driver"
        string fleet_number "Plat / Nomor Pintu"
        string type "Dump Truck, Pick Up, Amrol"
        integer capacity
    }

    ASSIGNMENTS {
        bigint id PK
        bigint report_id FK
        bigint fleet_id FK
        bigint assigned_by FK "Admin"
        enum status "menunggu, dalam_perjalanan, selesai"
    }

    REPORT_PROGRESSES {
        bigint id PK
        bigint report_id FK
        bigint created_by FK "Pelaku Perubahan"
        enum status
        text notes
        string photo_path "Foto bukti penanganan"
    }
```

### Penjelasan Relasi Utama:
1. **Users & Reports (1:N)**: Satu pengguna (Masyarakat) dapat membuat banyak laporan pengaduan sampah.
2. **Users & Fleets (1:N)**: Satu pengguna (Driver) mengemudikan satu armada secara spesifik (atau bergantian).
3. **Reports & Progresses (1:N)**: Satu laporan memiliki banyak tahapan (riwayat waktu pelaporan, kapan diverifikasi, dan kapan selesai ditangani). 
4. **Reports & Assignments (1:N/1:1)**: Satu laporan yang valid ditugaskan kepada suatu Armada melalui tabel *Assignments* sebagai bukti administratif.
5. **Regions Hierarchy (1:N)**: Tabel region merelasikan dirinya sendiri di mana satu *District* (Kecamatan) memiliki banyak *Village* (Kelurahan).

