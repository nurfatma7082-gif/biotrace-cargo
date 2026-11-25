# BioTrace — Global Cold-Chain Biospecimen & Clinical Trial Visibility Platform
> **Web Programming 2 — Final Project (UAS)**
> Focused on Biomedical Logistics, Cold-Chain Viability & Global Shipment Visibility

---

## 1. Domain & Problem Statement
Dalam riset biomedis global, uji klinis multi-senter (*multi-center clinical trials*), dan terapi berbasis sel/gen (*cell & gene therapy*), pengiriman sampel biologis (seperti sel punca, kultur jaringan, plasma darah uji klinis, dan spesimen DNA/RNA) memerlukan pengawetan rantai dingin (*cold-chain*) yang sangat ketat (-196°C Liquid Nitrogen, -80°C Dry Ice, atau 2–8°C).

Keterlambatan (*delay*) atau gangguan suhu transit bukan sekadar masalah operasional logistik biasa, melainkan dapat **merusak integritas molekuler dan viabilitas sel hidup**.

**BioTrace** hadir sebagai platform visibilitas pengiriman global terpusat yang:
- Melacak perjalanan spesimen biomedis antar rumah sakit, hub transit kargo medis, dan laboratorium uji klinis di peta interaktif.
- Mengontrol transisi status pengiriman dengan aturan bisnis yang ketat (*state machine*).
- Menghitung risiko viabilitas sampel berdasarkan estimasi waktu tiba (ETA) vs durasi ketahanan pendingin (*cold-chain shelf life*).
- Mencatat dan mengelola insiden khusus seperti lonjakan suhu (*temperature excursion*), kehabisan es kering (*dry ice depletion*), dan penahanan karantina hayati (*customs biohazard inspection*).

---

## 2. User Roles & Permissions (RBAC)

| Role | Tanggung Jawab & Hak Akses |
| :--- | :--- |
| **Administrator** | - Mengelola pengguna & peran.<br>- Mengelola master data carrier medis (Marken, World Courier, DHL Medical Express) dan lokasi fasilitas kesehatan / hub transit.<br>- Konfigurasi parameter viabilitas & threshold keterlambatan.<br>- Mengakses audit log dan seluruh dashboard metrik. |
| **Logistics Operator** | - Membuat dan memperbarui shipment spesimen biomedis.<br>- Memasukkan *tracking event* (lokasi, suhu saat ini, status transit, catatan).<br>- Mencatat dan memperbarui exception (misal: pengisian ulang es kering / *re-icing*).<br>- Memantau status keterlambatan operasional harian. |
| **Customer (Lab / RS)** | - Melihat daftar spesimen yang dikirim atau ditujukan ke fasilitas mereka.<br>- Melihat timeline kronologis dan posisi kargo di peta (Leaflet.js).<br>- Menerima alert peringatan jika ada ancaman degradasi sampel akibat keterlambatan. |

---

## 3. Shipment Lifecycle & State Machine

Status pengiriman dikendalikan oleh validasi backend ketat (tidak boleh loncat sembarangan atau kembali dari status akhir `DELIVERED`):

```mermaid
stateDiagram-v2
    [*] --> REGISTERED : Order Dibuat oleh Lab/RS
    REGISTERED --> CRYOPACKED : Spesimen Dikemas & Diinspeksi Suhu
    CRYOPACKED --> PICKED_UP : Diambil Kurir Medis Khusus
    PICKED_UP --> IN_TRANSIT : Dalam Perjalanan Udara / Darat
    IN_TRANSIT --> CUSTOMS_HOLD : Tertahan Karantina Hayati (Opsional Exception)
    CUSTOMS_HOLD --> IN_TRANSIT : Dokumen Disetujui
    IN_TRANSIT --> OUT_FOR_DELIVERY : Menuju Lab Tujuan
    OUT_FOR_DELIVERY --> DELIVERED : Diterima & Diverifikasi Lab (Terminal)
    IN_TRANSIT --> CANCELLED : Batal / Degradasi Fatal (Terminal)
```

---

## 4. Biomedical Cargo Classification

Setiap pengiriman memiliki item spesimen dengan karakteristik biomedis:
1. **Specimen Categories:**
   - `STEM_CELLS` (Sel Punca / Terapi Regeneratif)
   - `GENOMIC_DNA_RNA` (Sampel Genomik & PCR)
   - `BIOPSY_TISSUE` (Kultur Jaringan & Patologi Anatomi)
   - `CLINICAL_TRIAL_BLOOD` (Sampel Darah Uji Klinis Fase 2/3)
2. **Temperature Storage Tiers:**
   - `CRYO_LN2`: -196°C s/d -150°C (Liquid Nitrogen Vapor Shipper)
   - `DRY_ICE`: -80°C s/d -20°C (Dry Ice Insulated Shipper)
   - `CHILLED`: 2°C s/d 8°C (Gel Pack Shipper)
3. **Biohazard Levels:**
   - `BSL-1` (Low Risk)
   - `BSL-2` (Moderate Risk / Diagnostic Samples)
   - `BSL-3` (High Containment / Controlled Pathogens)

---

## 5. Delay Calculation & Exception Rules

### Aturan Keterlambatan (ETA vs Viability Threshold):
- $\text{Delay Duration} = \text{Current Estimated Arrival} - \text{Expected Arrival}$
- **Tingkat Keparahan Keterlambatan (Severity):**
  - **LOW (0 – 6 Jam):** Masih dalam batas cadangan pendingin; aman.
  - **MEDIUM (>6 – 12 Jam):** Membutuhkan penjadwalan *re-icing* (penambahan es kering) di hub transit terdekat.
  - **HIGH (>12 Jam):** Risiko tinggi degradasi integritas biomolekuler; peringatan kritis dikirim ke operator dan customer.

### Exception Types (Gangguan Pengiriman):
- `TEMPERATURE_EXCURSION`: Sensor membaca kenaikan suhu melebihi ambang batas toleransi tier pendingin.
- `DRY_ICE_DEPLETION`: Volume es kering menyusut mendekati batas aman sebelum tiba di tujuan.
- `CUSTOMS_BIOHAZARD_HOLD`: Penahanan dokumen karantina biologis oleh bea cukai bandara internasional.
- `FLIGHT_WEATHER_DELAY`: Keterlambatan penerbangan transit akibat cuaca buruk.
- `PACKAGING_BREACH`: Kerusakan fisik pada wadah luar (*outer shipper*).

Status Exception: `OPEN` $\rightarrow$ `INVESTIGATING` $\rightarrow$ `RESOLVED`.

---

## 6. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ SHIPMENTS : "places as customer / registers"
    USERS ||--o{ TRACKING_EVENTS : "recorded by operator"
    USERS ||--o{ AUDIT_LOGS : "performed action"
    
    CARRIERS ||--o{ SHIPMENTS : "transports"
    
    LOCATIONS ||--o{ SHIPMENTS : "origin"
    LOCATIONS ||--o{ SHIPMENTS : "destination"
    LOCATIONS ||--o{ TRACKING_EVENTS : "event facility"
    
    SHIPMENTS ||--|{ SHIPMENT_ITEMS : "contains"
    SHIPMENTS ||--o{ TRACKING_EVENTS : "timeline history"
    SHIPMENTS ||--o{ SHIPMENT_EXCEPTIONS : "encounters"
    SHIPMENTS ||--o{ NOTIFICATIONS : "triggers alerts"

    USERS {
        bigint id PK
        string name
        string email
        string password
        enum role "ADMIN, OPERATOR, CUSTOMER"
        string organization "Nama RS / Laboratorium"
    }

    CARRIERS {
        bigint id PK
        string name "World Courier, Marken, etc."
        string code "WCK, MRK"
        string contact_phone
        boolean is_cold_chain_certified
    }

    LOCATIONS {
        bigint id PK
        string facility_name
        string facility_type "HOSPITAL, LAB, AIRPORT_HUB"
        string city
        string country
        decimal latitude
        decimal longitude
    }

    SHIPMENTS {
        bigint id PK
        string shipment_number UK "e.g. BIO-2026-0089"
        bigint customer_id FK
        bigint carrier_id FK
        bigint origin_id FK
        bigint destination_id FK
        enum storage_temp "CRYO_LN2, DRY_ICE, CHILLED"
        enum status "REGISTERED, CRYOPACKED, PICKED_UP, IN_TRANSIT, CUSTOMS_HOLD, OUT_FOR_DELIVERY, DELIVERED, CANCELLED"
        decimal total_weight_kg
        datetime expected_departure
        datetime expected_arrival
        datetime estimated_arrival
        boolean is_delayed
        integer delay_hours
        enum delay_severity "NONE, LOW, MEDIUM, HIGH"
    }

    SHIPMENT_ITEMS {
        bigint id PK
        bigint shipment_id FK
        string specimen_type "STEM_CELLS, GENOMIC_DNA, etc."
        string specimen_identifier "Sample Tube / Batch Code"
        integer quantity_vials
        enum biohazard_level "BSL_1, BSL_2, BSL_3"
        decimal required_temp_celsius
        text storage_instructions
    }

    TRACKING_EVENTS {
        bigint id PK
        bigint shipment_id FK
        bigint recorded_by_user_id FK
        bigint location_id FK
        string event_type "PICKED_UP, HUB_SCAN, REICED, CUSTOMS_INSPECT, ARRIVED"
        decimal current_temp_celsius
        decimal latitude
        decimal longitude
        datetime recorded_at
        text description
    }

    SHIPMENT_EXCEPTIONS {
        bigint id PK
        bigint shipment_id FK
        enum exception_type "TEMPERATURE_EXCURSION, DRY_ICE_DEPLETION, CUSTOMS_BIOHAZARD_HOLD, FLIGHT_WEATHER_DELAY"
        enum severity "LOW, MEDIUM, HIGH"
        enum status "OPEN, INVESTIGATING, RESOLVED"
        datetime detected_at
        datetime resolved_at
        text description
        text resolution_notes
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK
        string action "CREATE_SHIPMENT, UPDATE_STATUS, RESOLVE_EXCEPTION"
        string entity_type
        bigint entity_id
        json old_values
        json new_values
        datetime created_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        bigint shipment_id FK
        string title
        text message
        boolean is_read
        datetime created_at
    }
```

