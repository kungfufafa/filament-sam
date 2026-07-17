# Filament SAM (Sales Assistant Mobile)

Aplikasi panel admin dan fondasi API untuk sistem **Sales Assistant Mobile (SAM)** — platform manajemen sales force yang mencakup pengelolaan outlet, kunjungan (visit), registrasi outlet baru (NOO), dan hierarki organisasi multi-level. Dibangun menggunakan Laravel & Filament dengan tema **Mekaya Admin Panel**.

---

## Tech Stack

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| Bahasa | PHP | 8.4 |
| Framework | Laravel | 13 |
| Admin Panel | Filament | 5 |
| Tema UI | Mekaya Theme (`kungfufafa/mekaya-theme`) | dev |
| Frontend | Vite, Tailwind CSS | 8 / 4 |
| Auth API | Laravel Sanctum | 4 |
| RBAC | Spatie Permission / Filament Shield | 7.4 / 4.2 |
| Push Notification | OneSignal (custom channel) | — |
| Testing | PHPUnit | 12 |
| Dev Tooling | Laravel Boost, Pail, Pint | — |

---

## Fitur Utama

- **Filament Admin Panel** dengan tema Mekaya (sidebar kustom, topbar, Blade components, UntitledUI icons)
- **Login fleksibel** — mendukung autentikasi via username atau email
- **Hierarki organisasi multi-level**: Business Entity → Division → Region → Cluster
- **Organizational Data Scoping** — pembatasan data otomatis berdasarkan level role pengguna (All / Business Entity / Division / Region / Cluster)
- **Manajemen Outlet** lengkap dengan geotag multi-lokasi, arsip perubahan (change archive), dan soft delete
- **Registrasi Outlet (NOO)** dengan workflow approval multi-tahap: Pending → Confirmed → Approved / Rejected
- **Plan Visit & Visit** — penjadwalan dan pencatatan kunjungan sales dengan check-in/check-out, koordinat GPS, foto, dan durasi
- **Import & Export** data (User, Outlet, Plan Visit) via Filament Import/Export
- **Push Notification** via OneSignal untuk notifikasi export selesai
- **WhatsApp OTP** — infrastruktur verifikasi nomor WhatsApp
- **System Settings** — konfigurasi per-scope (global, per business entity, division, region, cluster)
- **Sanctum API** dengan endpoint authenticated user sebagai fondasi integrasi mobile

---

## Arsitektur & Struktur Direktori

```
app/
├── Enums/                          # Enum classes (status, scope level, dll.)
│   ├── OrganizationalScopeLevel    # All, BusinessEntity, Division, Region, Cluster
│   ├── OutletRegistrationStatus    # Pending, Confirmed, Approved, Rejected
│   ├── OutletRegistrationType      # NOO, dll.
│   ├── OutletStatus                # Status outlet aktif
│   ├── ScheduleScope               # Daily, Weekly, Monthly
│   ├── SystemSettingScopeLevel     # Global, BusinessEntity, Division, Region, Cluster
│   └── TransactionStatus           # Status transaksi kunjungan
├── Filament/
│   ├── Exports/                    # Exporter classes (Outlet, PlanVisit, User)
│   ├── Imports/                    # Importer classes (Outlet, PlanVisit, User)
│   ├── Pages/Auth/                 # Custom Login (username/email)
│   └── Resources/                  # 11 Filament Resources:
│       ├── BusinessEntities/
│       ├── Clusters/
│       ├── Divisions/
│       ├── OutletRegistrations/
│       ├── Outlets/
│       ├── PlanVisits/
│       ├── Regions/
│       ├── Roles/
│       ├── SystemSettings/
│       ├── Users/
│       ├── Visits/
│       └── Concerns/               # HasOrganizationalDataScope trait
├── Http/Controllers/               # OneSignalSubscriptionController
├── Models/                         # 16 Eloquent Models
│   ├── BusinessEntity, Division, Region, Cluster
│   ├── Outlet, OutletGeotag, OutletChangeArchive, OutletRegistration
│   ├── Visit, PlanVisit
│   ├── User, Role, Permission
│   ├── SystemSetting
│   ├── OneSignalSubscription
│   └── WhatsappOtp
├── Notifications/
│   ├── Channels/OneSignalChannel   # Custom OneSignal notification channel
│   └── ExportCompletedPush         # Push notification saat export selesai
├── Policies/                       # 11 Policy classes (Shield-based)
├── Providers/
│   └── Filament/AdminPanelProvider # Panel config, plugins, middleware
└── Support/
    └── OrganizationalDataScope     # Engine pembatasan data per-hierarki
```

---

## Model & Relasi

### Hierarki Organisasi

```
BusinessEntity (1) ──→ (*) Division ──→ (*) Region ──→ (*) Cluster
```

### User

- Memiliki `username` (unique), `email` (unique, nullable), `whatsapp_number` (unique, nullable)
- `BelongsTo` TM (Territory Manager — self-referencing)
- `BelongsToMany` BusinessEntity, Division, Region, Cluster (pivot tables)
- `HasMany` Visit, PlanVisit, OneSignalSubscription
- Menggunakan `HasRoles` (Spatie), `SoftDeletes`, `HasAvatar`, `HasName`

### Outlet & Registrasi

- **Outlet**: terhubung ke hierarki organisasi, memiliki geotag multi-lokasi dan change archive
- **OutletRegistration**: workflow NOO dengan tahapan approval (created_by → confirmed_by → approved_by / rejected_by)
- **Visit / PlanVisit**: polymorphic (`visitable`) terhadap Outlet dan OutletRegistration

### Lainnya

- **SystemSetting**: konfigurasi scoped (global / per business entity / division / region / cluster)
- **WhatsappOtp**: OTP verifikasi WhatsApp dengan expiry dan attempt count
- **OneSignalSubscription**: subscription push notification per-user

---

## Organizational Data Scoping

Setiap role memiliki `organizational_scope_level` yang menentukan level akses data:

| Scope Level | Akses Data |
|-------------|------------|
| `All` | Seluruh data tanpa filter |
| `BusinessEntity` | Hanya data pada business entity yang di-assign |
| `Division` | Hanya data pada divisi yang di-assign |
| `Region` | Hanya data pada region yang di-assign |
| `Cluster` | Hanya data pada cluster yang di-assign |

Implementasi melalui:
- `App\Support\OrganizationalDataScope` — core engine yang menerapkan filter query
- `App\Filament\Resources\Concerns\HasOrganizationalDataScope` — trait untuk Filament Resource

---

## Instalasi & Setup

### Prasyarat

- PHP 8.3+ (direkomendasikan PHP 8.4)
- Composer
- Node.js & npm
- SQLite (default) atau MySQL/PostgreSQL

### Langkah Instalasi

1. **Clone repositori & install dependencies:**
   ```bash
   git clone <repository-url>
   cd filament-sam
   composer install
   npm install
   ```

2. **Konfigurasi environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database & migrasi:**
   ```bash
   php artisan migrate
   ```

4. **Seed data awal (roles, permissions, demo user):**
   ```bash
   php artisan db:seed
   ```
   > Demo user: `username: demo` / `password: password` (hanya untuk environment `local` / `testing`)

5. **Generate permissions (Filament Shield):**
   ```bash
   php artisan shield:generate --all --no-interaction
   ```

6. **Kompilasi frontend assets:**
   ```bash
   npm run build
   ```

7. **Jalankan server development:**
   ```bash
   composer run dev
   ```
   Perintah ini menjalankan secara paralel: Laravel server, queue listener, Pail log viewer, dan Vite dev server.

### Quick Setup (alternatif)

```bash
composer run setup
```

Perintah tersebut menyiapkan environment, application key, database, dan frontend assets. Seed data awal dan generate permissions tetap dijalankan terpisah:

```bash
php artisan db:seed
php artisan shield:generate --all --no-interaction
```

---

## Integrasi Mekaya Theme

Proyek ini menggunakan package `kungfufafa/mekaya-theme`. Tema dimuat melalui `MekayaPlugin::make()` di [AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php).

Aset Vite yang dikompilasi (lihat [vite.config.js](vite.config.js)):
- `resources/css/app.css`
- `resources/js/app.js`
- `vendor/kungfufafa/mekaya-theme/resources/css/theme.css`
- `vendor/kungfufafa/mekaya-theme/resources/js/mekaya.js`
- `resources/js/filament-onesignal.js`

---

## Push Notification (OneSignal)

Konfigurasi melalui `.env`:
```env
ONESIGNAL_APP_ID=your-app-id
ONESIGNAL_API_KEY=your-api-key
```

Fitur:
- Subscription management via web endpoint `POST /one-signal/subscriptions` dan `DELETE /one-signal/subscriptions` dengan middleware session `auth`
- Push notification otomatis saat export selesai (`ExportCompletedPush`)
- Custom notification channel (`OneSignalChannel`)

---

## Testing

Proyek menggunakan **PHPUnit** dengan 13 feature test:

```bash
# Jalankan semua test
php artisan test --compact

# Jalankan test tertentu
php artisan test --compact --filter=OrganizationalDataScopeTest
```

Test yang tersedia mencakup:
- `DemoUserSeederTest` — validasi seeder demo user
- `ExportCompletedPushTest` — push notification export
- `FilamentImportExportTest` — import & export data
- `FilamentUsernameAuthenticationTest` — login username/email
- `OneSignalSubscriptionEndpointTest` — web endpoint subscription OneSignal
- `OneSignalSubscriptionMigrationTest` — migrasi tabel subscription
- `OrganizationalCodeUniquenessTest` — uniqueness constraint kode organisasi
- `OrganizationalDataScopeTest` — data scoping per-level
- `OutletGeotagTest` — geotag multi-lokasi outlet
- `RoleResourceTest` — manajemen role Filament
- `UserProfilePhotoTest` — avatar/foto profil user
- `WhatsappVerificationTest` — verifikasi WhatsApp

---

## Kode Konvensi

- PHP 8.3+ dengan type hints, return type declarations, dan constructor property promotion
- Guarded mass assignment (`$guarded = ['id']`)
- Enum dengan `HasLabel` interface untuk integrasi Filament
- PHPDoc blocks dengan array shape type definitions
- Formatting otomatis dengan Laravel Pint (`vendor/bin/pint --dirty --format agent`)

---

## Lisensi

MIT
