<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users
        $admin = User::create([
            'name' => 'Dr. Aris Sudarmono',
            'email' => 'admin@biotrace.io',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
            'organization' => 'BioTrace Global Control Center',
            'phone' => '+62 811-9988-771',
        ]);

        $operator = User::create([
            'name' => 'Siti Rahmawati, S.Tr.Kes',
            'email' => 'operator@biotrace.io',
            'password' => Hash::make('password'),
            'role' => 'OPERATOR',
            'organization' => 'BioCourier Rapid Dispatch Hub',
            'phone' => '+62 812-4455-667',
        ]);

        $customer1 = User::create([
            'name' => 'Prof. Dr. Budi Santoso, Sp.PK',
            'email' => 'rscm.lab@biotrace.io',
            'password' => Hash::make('password'),
            'role' => 'CUSTOMER',
            'organization' => 'RSUP Nasional Dr. Cipto Mangunkusumo',
            'phone' => '+62 813-1122-334',
        ]);

        $customer2 = User::create([
            'name' => 'Dr. Cheryl Tan, Ph.D.',
            'email' => 'genomics.sg@biotrace.io',
            'password' => Hash::make('password'),
            'role' => 'CUSTOMER',
            'organization' => 'Singapore National Precision Medicine Institute',
            'phone' => '+65 6778-9900',
        ]);

        $customer3 = User::create([
            'name' => 'Dr. Klaus Weber',
            'email' => 'charite.de@biotrace.io',
            'password' => Hash::make('password'),
            'role' => 'CUSTOMER',
            'organization' => 'Charité Comprehensive Cancer Center Berlin',
            'phone' => '+49 30 45050',
        ]);

        // 2. Seed Carriers
        $wck = Carrier::create([
            'name' => 'World Courier Global Bio-Logistics',
            'code' => 'WCK',
            'contact_phone' => '+1 800-223-4457',
            'contact_email' => 'ops@worldcourier.com',
            'is_cold_chain_certified' => true,
            'notes' => 'Certified IATA CEIV Pharma & Liquid Nitrogen Cryo-shippers provider.',
        ]);

        $mrk = Carrier::create([
            'name' => 'Marken Bio-Pharma Solutions',
            'code' => 'MRK',
            'contact_phone' => '+1 919-474-6890',
            'contact_email' => 'support@marken.com',
            'is_cold_chain_certified' => true,
            'notes' => 'Global clinical trials supply chain leader with smart active tracking sensors.',
        ]);

        $dhlm = Carrier::create([
            'name' => 'DHL Medical Express Priority',
            'code' => 'DHLM',
            'contact_phone' => '+62 21 7917-3333',
            'contact_email' => 'medical.id@dhl.com',
            'is_cold_chain_certified' => true,
            'notes' => 'Dedicated dry-ice replenishment points at international transit hubs.',
        ]);

        // 3. Seed Locations
        $locRscm = Location::create([
            'facility_name' => 'RSUP Nasional Dr. Cipto Mangunkusumo (RSCM)',
            'facility_code' => 'JKT-RSCM-01',
            'facility_type' => 'HOSPITAL',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'latitude' => -6.1970230,
            'longitude' => 106.8480350,
        ]);

        $locCgk = Location::create([
            'facility_name' => 'Soekarno-Hatta Cold-Chain Cargo Terminal (CGK)',
            'facility_code' => 'CGK-AIR-CARGO',
            'facility_type' => 'AIRPORT_HUB',
            'city' => 'Tangerang',
            'country' => 'Indonesia',
            'latitude' => -6.1275000,
            'longitude' => 106.6537000,
        ]);

        $locChangi = Location::create([
            'facility_name' => 'Singapore Changi Coolport Hub (SIN)',
            'facility_code' => 'SIN-CHG-COOL',
            'facility_type' => 'AIRPORT_HUB',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'latitude' => 1.3644200,
            'longitude' => 103.9915300,
        ]);

        $locSgLab = Location::create([
            'facility_name' => 'Singapore Precision Genomics Institute',
            'facility_code' => 'SIN-GENOMICS-LAB',
            'facility_type' => 'RESEARCH_LAB',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'latitude' => 1.2966430,
            'longitude' => 103.7763940,
        ]);

        $locFra = Location::create([
            'facility_name' => 'Frankfurt Airport Perishable & Cool Center (FRA)',
            'facility_code' => 'FRA-COOL-CTR',
            'facility_type' => 'AIRPORT_HUB',
            'city' => 'Frankfurt',
            'country' => 'Germany',
            'latitude' => 50.0379330,
            'longitude' => 8.5621520,
        ]);

        $locCharite = Location::create([
            'facility_name' => 'Charité Comprehensive Cancer Biobank',
            'facility_code' => 'BER-CHARITE-BB',
            'facility_type' => 'BIOBANK',
            'city' => 'Berlin',
            'country' => 'Germany',
            'latitude' => 52.5256420,
            'longitude' => 13.3780340,
        ]);

        // 4. Seed Shipment 1: Normal In-Transit LN2 Cryo Stem Cells (RSCM -> SG Lab)
        $now = Carbon::now();
        $shipment1 = Shipment::create([
            'shipment_number' => 'BIO-2026-0001',
            'customer_id' => $customer1->id,
            'carrier_id' => $wck->id,
            'origin_id' => $locRscm->id,
            'destination_id' => $locSgLab->id,
            'storage_temp' => 'CRYO_LN2',
            'status' => 'IN_TRANSIT',
            'total_weight_kg' => 14.50,
            'expected_departure' => $now->copy()->subHours(8),
            'expected_arrival' => $now->copy()->addHours(12),
            'estimated_arrival' => $now->copy()->addHours(12),
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'delay_reason' => null,
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'specimen_type' => 'Allogeneic Hematopoietic Stem Cells (HSC)',
            'specimen_identifier' => 'STEM-HSC-B901',
            'quantity_vials' => 4,
            'biohazard_level' => 'BSL_2',
            'required_temp_celsius' => -196.00,
            'storage_instructions' => 'Liquid nitrogen vapor phase shipper (Dewar vessel). Do not invert or tilt > 30 degrees.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locRscm->id,
            'event_type' => 'CRYOPACKED',
            'recorded_temp_celsius' => -195.4,
            'latitude' => $locRscm->latitude,
            'longitude' => $locRscm->longitude,
            'event_time' => $now->copy()->subHours(8),
            'description' => 'Biospecimen harvested, cryopreserved, and sealed inside cryogenic dry vapor container.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locCgk->id,
            'event_type' => 'AIRPORT_DEPARTURE',
            'recorded_temp_celsius' => -195.2,
            'latitude' => $locCgk->latitude,
            'longitude' => $locCgk->longitude,
            'event_time' => $now->copy()->subHours(4),
            'description' => 'Cleared special medical cargo inspection at CGK. Loaded onto flight SQ957 to Singapore.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locChangi->id,
            'event_type' => 'HUB_SCAN',
            'recorded_temp_celsius' => -194.8,
            'latitude' => $locChangi->latitude,
            'longitude' => $locChangi->longitude,
            'event_time' => $now->copy()->subHour(),
            'description' => 'Arrived at Singapore Changi Coolport Hub. Nitrogen level verified adequate.',
        ]);

        // 5. Seed Shipment 2: Delayed with High Severity Exception (Dry Ice Biopsy: RSCM -> Berlin)
        $shipment2 = Shipment::create([
            'shipment_number' => 'BIO-2026-0002',
            'customer_id' => $customer1->id,
            'carrier_id' => $mrk->id,
            'origin_id' => $locRscm->id,
            'destination_id' => $locCharite->id,
            'storage_temp' => 'DRY_ICE',
            'status' => 'IN_TRANSIT',
            'total_weight_kg' => 22.00,
            'expected_departure' => $now->copy()->subHours(30),
            'expected_arrival' => $now->copy()->subHours(2), // Expected already
            'estimated_arrival' => $now->copy()->addHours(14), // Delayed by 16 hours
            'is_delayed' => true,
            'delay_hours' => 16,
            'delay_severity' => 'HIGH',
            'delay_reason' => 'Severe blizzard grounding cargo connections at Frankfurt Airport (FRA) & temperature spike detected.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment2->id,
            'specimen_type' => 'Frozen Tumor Biopsy Core Extracts (RNA-Seq)',
            'specimen_identifier' => 'BIOPSY-TMR-772',
            'quantity_vials' => 8,
            'biohazard_level' => 'BSL_2',
            'required_temp_celsius' => -80.00,
            'storage_instructions' => 'Requires dry-ice pelleted packaging. Critical viability shelf life: 48 hours without replenishment.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locRscm->id,
            'event_type' => 'CRYOPACKED',
            'recorded_temp_celsius' => -79.8,
            'latitude' => $locRscm->latitude,
            'longitude' => $locRscm->longitude,
            'event_time' => $now->copy()->subHours(30),
            'description' => 'Biopsy specimens packed with 15kg certified dry ice pellets.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locFra->id,
            'event_type' => 'HUB_SCAN',
            'recorded_temp_celsius' => -64.2,
            'latitude' => $locFra->latitude,
            'longitude' => $locFra->longitude,
            'event_time' => $now->copy()->subHours(6),
            'description' => 'Sensor flagged temperature excursion (-64.2°C). Dry ice sublimation accelerated due to flight delay.',
        ]);

        ShipmentException::create([
            'shipment_id' => $shipment2->id,
            'exception_type' => 'TEMPERATURE_EXCURSION',
            'severity' => 'HIGH',
            'status' => 'OPEN',
            'detected_at' => $now->copy()->subHours(6),
            'description' => 'Internal sensor temperature climbed from -78°C to -64.2°C. Critical RNA degradation threshold (-60°C) approaching.',
            'resolution_notes' => 'Emergency re-icing dispatched to Frankfurt Cool Center station 4B.',
        ]);

        ShipmentException::create([
            'shipment_id' => $shipment2->id,
            'exception_type' => 'WEATHER_DELAY',
            'severity' => 'MEDIUM',
            'status' => 'INVESTIGATING',
            'detected_at' => $now->copy()->subHours(8),
            'description' => 'Frankfurt airport ground operations halted due to sub-zero freezing blizzard.',
            'resolution_notes' => 'Re-routed via scheduled specialized thermal van directly to Berlin.',
        ]);

        Notification::create([
            'user_id' => $customer1->id,
            'shipment_id' => $shipment2->id,
            'title' => 'Critical Warning: Shipment BIO-2026-0002 Thermal Alert',
            'message' => 'Your shipment has encountered a temperature rise to -64.2°C at Frankfurt Hub. Emergency re-icing is in progress.',
            'alert_type' => 'CRITICAL',
            'is_read' => false,
        ]);

        // 6. Seed Shipment 3: DELIVERED (Clinical Blood: Jakarta -> Singapore Lab)
        $shipment3 = Shipment::create([
            'shipment_number' => 'BIO-2026-0003',
            'customer_id' => $customer1->id,
            'carrier_id' => $dhlm->id,
            'origin_id' => $locRscm->id,
            'destination_id' => $locSgLab->id,
            'storage_temp' => 'CHILLED',
            'status' => 'DELIVERED',
            'total_weight_kg' => 6.20,
            'expected_departure' => $now->copy()->subDays(2),
            'expected_arrival' => $now->copy()->subDay(),
            'estimated_arrival' => $now->copy()->subDay(),
            'is_delayed' => false,
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'delay_reason' => null,
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'specimen_type' => 'Phase 3 Multi-Center Clinical Trial EDTA Plasma Samples',
            'specimen_identifier' => 'CT-EDTA-PK-102',
            'quantity_vials' => 12,
            'biohazard_level' => 'BSL_1',
            'required_temp_celsius' => 4.00,
            'storage_instructions' => 'Refrigerated pack (2°C - 8°C). Protect from freezing.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment3->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locRscm->id,
            'event_type' => 'PICKED_UP',
            'recorded_temp_celsius' => 4.1,
            'latitude' => $locRscm->latitude,
            'longitude' => $locRscm->longitude,
            'event_time' => $now->copy()->subDays(2),
            'description' => 'Courier collected chilled shippers from RSCM Central Pathology Lab.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment3->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locSgLab->id,
            'event_type' => 'DELIVERED',
            'recorded_temp_celsius' => 4.3,
            'latitude' => $locSgLab->latitude,
            'longitude' => $locSgLab->longitude,
            'event_time' => $now->copy()->subDay(),
            'description' => 'Successfully delivered to Singapore Genomics Lab reception. Temperature log verified within 2-8°C limit.',
        ]);

        // 7. Seed Shipment 4: CUSTOMS_HOLD (CAR-T Cell Therapy: Singapore -> Jakarta)
        $shipment4 = Shipment::create([
            'shipment_number' => 'BIO-2026-0004',
            'customer_id' => $customer2->id,
            'carrier_id' => $wck->id,
            'origin_id' => $locSgLab->id,
            'destination_id' => $locRscm->id,
            'storage_temp' => 'CRYO_LN2',
            'status' => 'CUSTOMS_HOLD',
            'total_weight_kg' => 18.00,
            'expected_departure' => $now->copy()->subHours(18),
            'expected_arrival' => $now->copy()->addHours(6),
            'estimated_arrival' => $now->copy()->addHours(14), // Delay 8 hours
            'is_delayed' => true,
            'delay_hours' => 8,
            'delay_severity' => 'MEDIUM',
            'delay_reason' => 'Indonesian Quarantine & BPOM Special Import Clearance pending biological safety documents.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment4->id,
            'specimen_type' => 'Autologous CAR-T Cell Therapy Infusion Batch',
            'specimen_identifier' => 'CART-CD19-041',
            'quantity_vials' => 2,
            'biohazard_level' => 'BSL_2',
            'required_temp_celsius' => -196.00,
            'storage_instructions' => 'Single patient specific live cell product. Keep in LN2 vapor dewar at all times.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment4->id,
            'recorded_by_user_id' => $operator->id,
            'location_id' => $locCgk->id,
            'event_type' => 'CUSTOMS_HOLD',
            'recorded_temp_celsius' => -195.1,
            'latitude' => $locCgk->latitude,
            'longitude' => $locCgk->longitude,
            'event_time' => $now->copy()->subHours(5),
            'description' => 'Karantina Kesehatan Bandara Soekarno-Hatta requested physical inspection of Material Transfer Agreement (MTA).',
        ]);

        ShipmentException::create([
            'shipment_id' => $shipment4->id,
            'exception_type' => 'CUSTOMS_BIOHAZARD_HOLD',
            'severity' => 'MEDIUM',
            'status' => 'OPEN',
            'detected_at' => $now->copy()->subHours(5),
            'description' => 'Indonesian Customs & Karantina Kesehatan hold awaiting BPOM SAS permit verification.',
            'resolution_notes' => 'Medical expedite clearance form submitted with Hospital Director authorization.',
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'INITIAL_SYSTEM_SETUP',
            'entity_type' => 'System',
            'entity_id' => 1,
            'old_payload' => null,
            'new_payload' => ['message' => 'BioTrace Global platform initialized with 4 baseline biospecimen shipments.'],
            'ip_address' => '127.0.0.1',
        ]);
    }
}
