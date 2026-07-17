# Dynamic Outlet Fields & Registration Lifecycle Roadmap

**Status:** Reviewed  
**Document type:** Development source of truth (draft for stakeholder validation)  
**Scope:** Outlet Registration, Outlet, System Settings, approval lifecycle, and dynamic custom fields  
**Last updated:** 17 July 2026

## 1. Purpose

Dokumen ini menjadi acuan pengembangan agar penambahan field bisnis pada Outlet Registration dan Outlet tidak selalu membutuhkan migration, perubahan form, build, dan deployment aplikasi.

Target akhirnya adalah aplikasi yang:

- Mendukung field operasional yang dapat dikonfigurasi melalui panel admin.
- Memiliki lifecycle yang konsisten dari Lead/NOO menjadi Outlet.
- Menjaga histori nilai ketika definisi field berubah.
- Memiliki authorization, audit trail, validasi, dan testing yang matang.
- Tetap menggunakan kolom database biasa untuk data inti yang dibutuhkan oleh relasi, query kritis, dan aturan bisnis.

## 2. Current-State Evidence

### Observed

- `OutletRegistration` memiliki type `NOO` dan `LEAD`.
- Status registrasi adalah `PENDING`, `CONFIRMED`, `APPROVED`, dan `REJECTED`.
- `Outlet` dapat terhubung ke sumber registrasi melalui `outlet_registration_id`.
- Outlet memiliki banyak geotag melalui `outlet_geotags`.
- `SystemSetting` sudah mendukung scope global, business entity, division, region, dan cluster.
- Filament Shield digunakan untuk authorization resource.
- Field dinamis dan tabel penyimpan nilainya belum tersedia.
- Proses approval yang otomatis membuat Outlet belum tersedia pada service/action khusus.

### Inferred

- Approval NOO dimaksudkan untuk menghasilkan Outlet aktif.
- Lead dapat berkembang menjadi NOO sebelum dapat menghasilkan Outlet.
- Nilai field tambahan pada registrasi perlu tersedia pada Outlet tanpa menduplikasi definisi field.

### Unknown — Wajib Diputuskan Sebelum Implementasi

- Apakah `LEAD` boleh langsung di-approve menjadi Outlet atau wajib dikonversi menjadi `NOO`.
- Siapa yang boleh mengubah definisi field setelah field digunakan.
- Apakah nilai pada Outlet merupakan snapshot saat approval atau tetap mengikuti perubahan nilai registrasi.
- Apakah field dapat berbeda berdasarkan business entity/division/region/cluster.
- Retention period untuk definisi dan nilai field yang sudah tidak aktif.

## 3. Design Principles

1. **Core fields remain explicit columns.** Identifier, relationships, status, timestamps, coordinates, dan field yang sering dipakai untuk filtering/reporting tetap menjadi kolom database.
2. **Operational fields may be dynamic.** Field tambahan yang berubah berdasarkan kebutuhan bisnis disimpan melalui metadata dan value records.
3. **Never delete used definitions.** Definisi yang sudah memiliki nilai hanya boleh dinonaktifkan.
4. **Stable keys.** `key` tidak boleh berubah setelah definisi digunakan.
5. **Approval creates an immutable snapshot.** Nilai registrasi disalin ke Outlet saat approval agar histori keputusan tetap dapat diaudit.
6. **Configuration changes are audited.**
7. **Dynamic configuration is not executable code.** Admin tidak boleh memasukkan PHP, SQL, JavaScript, atau expression bebas.

## 4. Actors

| ID | Actor | Responsibility |
|---|---|---|
| ACT-001 | Sales/User | Membuat dan memperbarui Lead/NOO serta mengisi field dinamis |
| ACT-002 | Territory Manager | Melakukan konfirmasi dan pemeriksaan kelengkapan |
| ACT-003 | Approver | Menyetujui atau menolak NOO |
| ACT-004 | System Administrator | Mengelola definisi field dinamis dan pengaturan sistem |
| ACT-005 | Auditor | Membaca lifecycle, snapshot, dan audit perubahan |

## 5. Functional Requirements

| ID | Requirement |
|---|---|
| FR-001 | Administrator dapat membuat definisi custom field tanpa deployment aplikasi. |
| FR-002 | Definisi field memiliki stable key, label, help text, type, required state, default value, validation configuration, dan display order. |
| FR-003 | Tipe awal yang didukung: text, textarea, integer, decimal, date, datetime, boolean, select, multi-select, dan file. |
| FR-004 | Definisi dapat dibatasi untuk `LEAD`, `NOO`, atau keduanya. |
| FR-005 | Definisi dapat diberlakukan pada scope global, business entity, division, region, atau cluster. |
| FR-006 | Form Outlet Registration merender field aktif berdasarkan type dan organizational scope record. |
| FR-007 | Input dinamis divalidasi di server berdasarkan definisi yang aktif. |
| FR-008 | Nilai disimpan tanpa mengubah schema tabel setiap kali field baru ditambahkan. |
| FR-009 | Approval NOO membuat Outlet secara atomik dan menghubungkannya melalui `outlet_registration_id`. |
| FR-010 | Nilai field yang dikonfigurasi `carry_to_outlet` disalin menjadi snapshot milik Outlet saat approval. |
| FR-011 | Approval gagal seluruhnya apabila pembuatan Outlet, geotag, atau snapshot field gagal. |
| FR-012 | Definisi yang sudah digunakan tidak dapat dihapus; definisi hanya dapat dinonaktifkan. |
| FR-013 | Nilai lama tetap dapat dibaca setelah definisi dinonaktifkan. |
| FR-014 | Perubahan definisi, approval, rejection, dan snapshot dicatat dalam audit trail. |
| FR-015 | Tabel Outlet Registration dan Outlet dapat menampilkan serta memfilter field dinamis yang ditandai `is_filterable`. |
| FR-016 | Export menyertakan custom field aktif dan dapat menyertakan field nonaktif yang masih memiliki nilai. |
| FR-017 | Import memvalidasi custom field menggunakan stable key, bukan label. |
| FR-018 | Filament Shield menyediakan permission khusus untuk mengelola definisi, melihat nilai sensitif, dan menjalankan approval. |
| FR-019 | Administrator dapat melihat preview form sebelum definisi diaktifkan. |
| FR-020 | Perubahan definisi memiliki status `DRAFT`, `ACTIVE`, atau `INACTIVE`. |

## 6. Non-Functional Requirements

| ID | Requirement |
|---|---|
| NFR-001 | Render form dengan maksimal 50 custom fields memiliki server response target p95 ≤ 500 ms pada environment produksi yang disepakati. |
| NFR-002 | Approval harus menggunakan database transaction dan idempotency guard. |
| NFR-003 | Tidak ada custom field yang dapat mengeksekusi code atau raw query. |
| NFR-004 | File upload mengikuti visibility, MIME type, size limit, dan authorization yang eksplisit. |
| NFR-005 | Cache definisi field harus di-invalidasi setelah perubahan konfigurasi. |
| NFR-006 | Semua perubahan konfigurasi menyimpan actor dan timestamp. |
| NFR-007 | Data organisasi tidak boleh bocor antar-scope. |
| NFR-008 | Field lama tetap dapat dibaca minimal selama retention period yang disepakati. |
| NFR-009 | Approval yang diulang tidak boleh membuat Outlet kedua untuk registrasi yang sama. |

## 7. Proposed Information Architecture

```text
Settings
├── System Settings
├── Custom Field Definitions
│   ├── List
│   ├── Create/Edit
│   ├── Preview
│   └── Audit History
└── Role

Outlet Registration
├── Core Information
├── Location & Documentation
├── Dynamic Fields
├── Approval History
└── Generated Outlet

Outlet
├── Core Information
├── Geotags
├── Dynamic Field Snapshot
└── Source Registration
```

Custom Field Definitions dapat ditempatkan di navigation group `Settings`, sementara `SystemSetting` tetap menyimpan parameter operasional. Definisi field sebaiknya menjadi resource/model terpisah agar versioning, permissions, ordering, dan audit tidak dipaksa masuk ke satu record SystemSetting.

## 8. Proposed Data Model

### ENT-001 `custom_field_definitions`

| Field | Type | Notes |
|---|---|---|
| id | bigint | Primary key |
| key | string, unique | Immutable machine key |
| label | string | User-facing label |
| description | text, nullable | Help text |
| field_type | enum/string | Supported renderer and validator |
| applies_to | enum/string | `OUTLET_REGISTRATION`, `OUTLET`, or `BOTH` |
| registration_types | json, nullable | `LEAD`, `NOO`, or both |
| carry_to_outlet | boolean | Include in approval snapshot |
| is_required | boolean | Server-side requirement |
| is_filterable | boolean | Allow table filter |
| is_sensitive | boolean | Requires sensitive-value permission |
| default_value | json, nullable | Typed default |
| validation_rules | json, nullable | Allow-listed rules only |
| options | json, nullable | Select/multi-select options |
| display_order | integer | Stable ordering |
| status | enum/string | `DRAFT`, `ACTIVE`, `INACTIVE` |
| scope_level | enum/string | Same hierarchy concept as SystemSetting |
| business_entity_id | bigint, nullable | Scope coordinate |
| division_id | bigint, nullable | Scope coordinate |
| region_id | bigint, nullable | Scope coordinate |
| cluster_id | bigint, nullable | Scope coordinate |
| created_by_id | bigint | Audit actor |
| updated_by_id | bigint | Audit actor |
| timestamps | timestamps | Audit time |

### ENT-002 `custom_field_values`

| Field | Type | Notes |
|---|---|---|
| id | bigint | Primary key |
| custom_field_definition_id | bigint | Definition reference |
| fieldable_type | string | Polymorphic owner |
| fieldable_id | bigint | Registration or Outlet |
| value | json | Typed value |
| source_value_id | bigint, nullable | Original value when copied |
| is_snapshot | boolean | True for approval snapshot |
| created_by_id | bigint, nullable | Actor |
| updated_by_id | bigint, nullable | Actor |
| timestamps | timestamps | Audit time |

Recommended constraints:

- Unique: `custom_field_definition_id + fieldable_type + fieldable_id`.
- Index: `fieldable_type + fieldable_id`.
- Index: `custom_field_definition_id`.
- Definitions with values cannot be hard deleted.
- Sensitive values may require encryption depending on data classification.

### ENT-003 `outlet_registration_status_histories`

Recommended for a mature lifecycle:

| Field | Type |
|---|---|
| outlet_registration_id | bigint |
| from_status | string, nullable |
| to_status | string |
| actor_user_id | bigint |
| notes | text, nullable |
| metadata | json, nullable |
| created_at | timestamp |

## 9. Lifecycle and State Rules

Recommended state machine:

```text
LEAD/PENDING
    ├── edit
    └── convert to NOO/PENDING

NOO/PENDING
    ├── confirm → NOO/CONFIRMED
    └── reject  → NOO/REJECTED

NOO/CONFIRMED
    ├── approve → NOO/APPROVED + Outlet created
    └── reject  → NOO/REJECTED

NOO/APPROVED
    └── immutable approval result; correction uses an audited process
```

Business rules:

- BR-001: Satu Outlet Registration maksimal menghasilkan satu Outlet.
- BR-002: Outlet menyimpan `outlet_registration_id` sebagai provenance.
- BR-003: Approval hanya berhasil bila seluruh required dynamic fields valid.
- BR-004: Approval menggunakan transaction dan row lock.
- BR-005: Snapshot Outlet tidak berubah ketika nilai registrasi diedit setelah approval.
- BR-006: Definisi `INACTIVE` tidak muncul untuk input baru tetapi nilai historis tetap terbaca.
- BR-007: Perubahan label tidak mengubah `key`.
- BR-008: Perubahan field type setelah memiliki nilai dilarang; buat definisi versi baru.
- BR-009: File field yang disalin ke Outlet menggunakan reference/path yang terkelola, bukan upload duplikat tanpa kebutuhan.

## 10. User Flows

### UC-001 Manage Dynamic Field Definition

**Actor:** System Administrator  
**Precondition:** Memiliki permission pengelolaan custom fields.

Main flow:

1. Admin membuka Settings → Custom Field Definitions.
2. Admin membuat definisi dalam status `DRAFT`.
3. Sistem memvalidasi stable key, type, scope, options, dan rules.
4. Admin melihat preview untuk Lead dan/atau NOO.
5. Admin mengaktifkan definisi.
6. Sistem menghapus cache definisi dan mencatat audit.

Acceptance criteria:

- AC-001: Field aktif muncul tanpa deployment.
- AC-002: Key duplikat ditolak.
- AC-003: Rule di luar allow-list ditolak.
- AC-004: Definisi yang sudah memiliki nilai tidak dapat dihapus.

### UC-002 Submit Registration with Dynamic Fields

**Actor:** Sales/User

Main flow:

1. Sales memilih type Lead atau NOO.
2. Sistem memuat definisi aktif sesuai type dan organizational scope.
3. Sales mengisi core fields dan dynamic fields.
4. Sistem memvalidasi seluruh input.
5. Core record dan dynamic values disimpan dalam satu transaction.

Acceptance criteria:

- AC-005: Required field kosong ditolak.
- AC-006: Field untuk scope lain tidak diterima walaupun request dimanipulasi.
- AC-007: Nilai tersimpan menggunakan definition ID/key, bukan label.

### UC-003 Approve NOO and Create Outlet

**Actor:** Approver

Main flow:

1. Approver membuka NOO berstatus Confirmed.
2. Sistem memeriksa authorization, kelengkapan, dan apakah Outlet sudah ada.
3. Sistem mengunci registration row.
4. Sistem membuat Outlet dan geotag yang diperlukan.
5. Sistem menyalin dynamic values dengan `carry_to_outlet = true`.
6. Sistem mengubah status menjadi Approved dan mencatat actor/timestamp/history.
7. Transaction di-commit.

Alternative/error flows:

- Data wajib tidak lengkap: approval ditolak tanpa perubahan data.
- Outlet sudah pernah dibuat: tampilkan Outlet yang ada; jangan membuat duplikat.
- Penyimpanan snapshot gagal: seluruh transaction rollback.

Acceptance criteria:

- AC-008: Satu approval menghasilkan tepat satu Outlet.
- AC-009: Snapshot sama dengan nilai registrasi pada waktu approval.
- AC-010: Retry tidak membuat duplikat.
- AC-011: Audit mencatat approver dan perubahan status.

### UC-004 View Outlet Dynamic Snapshot

**Actor:** Authorized panel user

Main flow:

1. User membuka Outlet.
2. Sistem menampilkan core fields, geotags, dan snapshot custom fields.
3. Field sensitif hanya muncul untuk permission yang sesuai.
4. User dapat membuka source registration.

Acceptance criteria:

- AC-012: Snapshot tetap tersedia jika definisi dinonaktifkan.
- AC-013: Nilai sensitif tersembunyi tanpa permission.

## 11. Authorization Plan

Permission Filament Shield yang direkomendasikan:

- `ViewAny:CustomFieldDefinition`
- `View:CustomFieldDefinition`
- `Create:CustomFieldDefinition`
- `Update:CustomFieldDefinition`
- `Activate:CustomFieldDefinition`
- `Deactivate:CustomFieldDefinition`
- `ViewSensitive:CustomFieldValue`
- `Confirm:OutletRegistration`
- `Approve:OutletRegistration`
- `Reject:OutletRegistration`
- `ConvertToNoo:OutletRegistration`

Action khusus tidak boleh menggunakan permission generik `Create` atau `Update`.

## 12. Validation and Security

- Renderer dan validator dipetakan dari enum field type yang dikontrol aplikasi.
- Validation rules disimpan sebagai konfigurasi terstruktur dan harus menggunakan allow-list.
- Select options memiliki stable value dan editable label.
- File field menetapkan MIME types, ukuran maksimum, disk, directory, dan visibility secara eksplisit.
- Server menghitung applicable definitions; jangan percaya daftar field dari browser.
- Mass assignment custom values dilakukan melalui service khusus.
- Scope organisasi diterapkan pada definition query dan value access.
- Sensitive values dipertimbangkan untuk encryption at rest.
- Semua approval dan configuration mutations menggunakan policy/action authorization.

## 13. Service Boundaries

Recommended application services:

- `ResolveApplicableCustomFields`
- `ValidateCustomFieldValues`
- `SaveCustomFieldValues`
- `ApproveOutletRegistration`
- `CreateOutletFromRegistration`
- `SnapshotRegistrationFieldsToOutlet`
- `TransitionOutletRegistrationStatus`

`ApproveOutletRegistration` menjadi transaction boundary dan mengorkestrasi service lainnya. Logic approval tidak ditempatkan langsung di Filament page/action callback.

## 14. Import, Export, and Reporting

### Import

- Header memakai stable key, contoh `custom.potential_revenue`.
- Unknown keys ditolak atau dilaporkan eksplisit.
- Required dynamic fields ikut divalidasi.
- Import permission tetap terpisah melalui `Import:OutletRegistration`.

### Export

- Core columns tetap stabil.
- Custom fields menggunakan prefix `custom.`.
- Export menyimpan label dan key pada metadata job bila format mendukung.
- Permission field sensitif diperiksa sebelum kolom ditambahkan.

### Reporting

JSON polymorphic values cocok untuk fleksibilitas, tetapi filtering lintas banyak record dapat menjadi mahal. Field yang menjadi KPI/report utama harus dipromosikan menjadi:

- explicit indexed column; atau
- typed/indexed projection table.

Keputusan promosi harus berdasarkan bukti penggunaan query dan kebutuhan reporting.

## 15. Caching

Cache key minimal mencakup:

- entity type;
- registration type;
- scope level dan scope ID;
- definition version.

Cache di-invalidasi setelah activate, update, deactivate, atau perubahan ordering. Cache hanya menyimpan definitions, bukan authorization result atau nilai pengguna.

## 16. Testing Strategy

| ID | Test |
|---|---|
| TC-001 | Admin dapat membuat draft definition dan mengaktifkannya. |
| TC-002 | Duplicate stable key ditolak. |
| TC-003 | Form Lead hanya memuat field yang berlaku untuk Lead dan scope user. |
| TC-004 | Form NOO memvalidasi required dynamic fields. |
| TC-005 | Manipulasi field dari scope lain ditolak server. |
| TC-006 | Approval membuat Outlet, relasi source, geotag, dan snapshot dalam satu transaction. |
| TC-007 | Kegagalan snapshot menyebabkan rollback penuh. |
| TC-008 | Retry approval tidak membuat Outlet duplikat. |
| TC-009 | Definition inactive tidak muncul untuk input tetapi nilai lama tetap terbaca. |
| TC-010 | User tanpa permission tidak dapat melihat sensitive values. |
| TC-011 | Import memetakan stable keys dan melaporkan unknown keys. |
| TC-012 | Export menyertakan custom fields sesuai permission. |
| TC-013 | Cache definitions terhapus setelah configuration change. |
| TC-014 | Organizational scope mencegah kebocoran definition dan value. |
| TC-015 | Query/render 50 fields memenuhi target performa yang disepakati. |

## 17. Delivery Phases

### Phase 1 — Lifecycle Foundation

- Definisikan dan validasi state machine Lead/NOO.
- Tambah status history.
- Implementasikan approval service yang idempotent dan transactional.
- Tambah Shield permissions khusus.
- Tambah test lifecycle dan rollback.

### Phase 2 — Dynamic Field Foundation

- Tambah definitions dan values.
- Tambah Settings resource.
- Tambah type renderer/validator registry.
- Tambah dynamic fields ke form Outlet Registration.
- Tambah authorization dan audit.

### Phase 3 — Outlet Snapshot

- Snapshot nilai saat approval.
- Tampilkan custom field snapshot pada Outlet.
- Tambah sensitive field handling.
- Tambah source registration navigation.

### Phase 4 — Operations

- Import/export dynamic fields.
- Filtering dan reporting.
- Cache dan invalidation.
- Performance, security, dan browser tests.

### Phase 5 — Maturity

- Definition versioning.
- Preview dan staged activation.
- Data retention policy.
- Operational monitoring dan audit reporting.
- Promotion workflow untuk field yang menjadi KPI inti.

## 18. Definition of Done

Fitur dianggap matang ketika:

- Requirement dan state machine telah divalidasi stakeholder.
- Tidak ada approval logic tersebar di UI callbacks.
- Approval bersifat transactional dan idempotent.
- Penambahan field operasional standar dapat dilakukan tanpa deployment.
- Seluruh action sensitif memiliki permission khusus.
- Audit trail dapat menjawab siapa, kapan, dan perubahan apa yang dilakukan.
- Dynamic values aman terhadap scope leakage.
- Import/export memiliki kontrak stable key.
- Functional, authorization, rollback, scope, dan performance tests lulus.
- Runbook perubahan definition dan recovery tersedia.

## 19. Decisions Required

| ID | Decision |
|---|---|
| DEC-001 | Apakah Lead wajib dikonversi menjadi NOO sebelum approval? |
| DEC-002 | Apakah snapshot Outlet boleh diedit setelah approval? |
| DEC-003 | Scope definitions: global saja pada versi pertama atau langsung hierarchical? |
| DEC-004 | Field types apa saja yang masuk MVP? |
| DEC-005 | Siapa yang berhak activate/deactivate definition? |
| DEC-006 | Berapa retention period untuk nilai dan definisi inactive? |
| DEC-007 | Apakah sensitive values harus dienkripsi sejak versi pertama? |

## 20. Traceability Matrix

| Requirement | Use Case | Entity | Test |
|---|---|---|---|
| FR-001–FR-005, FR-019–FR-020 | UC-001 | ENT-001 | TC-001, TC-002, TC-013 |
| FR-006–FR-008 | UC-002 | ENT-001, ENT-002 | TC-003–TC-005, TC-014 |
| FR-009–FR-011, FR-014 | UC-003 | ENT-002, ENT-003 | TC-006–TC-008 |
| FR-010, FR-013, FR-018 | UC-004 | ENT-002 | TC-009, TC-010 |
| FR-015–FR-017 | UC-002, UC-004 | ENT-001, ENT-002 | TC-011, TC-012, TC-015 |

## 21. Validation Status

Dokumen ini telah melalui AI review terhadap codebase saat ini, tetapi **belum berstatus Validated**. Implementasi sebaiknya dimulai setelah stakeholder memutuskan `DEC-001` sampai `DEC-007`, minimal untuk scope MVP.

## 22. Methodology Credit

Dokumen ini menggunakan prinsip Chain of Truth dari Farid Suryanto dan Muhammad Ibnu Athoillah untuk menjaga alur requirements → user flow → data model → implementation → testing.

References:

- [Chain of Truth documentation](https://faridsurya-dev.github.io/Vibe-Coding-Research/welcome)
- [Chain of Truth concept](https://faridsurya-dev.github.io/Vibe-Coding-Research/en/1-concept/what-is-chain-of-truth)
- [Research paper DOI](https://doi.org/10.5281/zenodo.20767965)

