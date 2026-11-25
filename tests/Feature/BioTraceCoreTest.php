<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\EtaViabilityService;
use App\Services\ShipmentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BioTraceCoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;
    protected User $customer;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.test@biotrace.io',
            'password' => bcrypt('password'),
            'role' => 'ADMIN',
            'organization' => 'BioTrace HQ',
        ]);

        $this->operator = User::create([
            'name' => 'Operator User',
            'email' => 'operator.test@biotrace.io',
            'password' => bcrypt('password'),
            'role' => 'OPERATOR',
            'organization' => 'Cold Logistics Dispatch',
        ]);

        $this->customer = User::create([
            'name' => 'Hospital Lab',
            'email' => 'lab.test@biotrace.io',
            'password' => bcrypt('password'),
            'role' => 'CUSTOMER',
            'organization' => 'RSCM Jakarta',
        ]);

        $this->carrier = Carrier::create([
            'name' => 'World Courier Test',
            'code' => 'WCK-T',
            'is_cold_chain_certified' => true,
        ]);

        $this->origin = Location::create([
            'facility_name' => 'Jakarta Lab',
            'facility_code' => 'JKT-LAB',
            'facility_type' => 'HOSPITAL',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'latitude' => -6.2000,
            'longitude' => 106.8166,
        ]);

        $this->destination = Location::create([
            'facility_name' => 'Singapore Lab',
            'facility_code' => 'SIN-LAB',
            'facility_type' => 'RESEARCH_LAB',
            'city' => 'Singapore',
            'country' => 'Singapore',
            'latitude' => 1.3521,
            'longitude' => 103.8198,
        ]);
    }

    /**
     * Test 1: Authentication & RBAC Login
     */
    public function test_user_can_authenticate_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => $this->customer->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->customer);
    }

    public function test_unauthenticated_user_cannot_access_shipments(): void
    {
        $response = $this->get('/shipments');
        $response->assertRedirect('/login');
    }

    /**
     * Test 2: Valid and Invalid Status Transition Machine
     */
    public function test_shipment_follows_strict_biomedical_state_machine(): void
    {
        $shipmentService = app(ShipmentService::class);

        $now = Carbon::now();
        $shipment = Shipment::create([
            'shipment_number' => 'BIO-TEST-001',
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'storage_temp' => 'DRY_ICE',
            'status' => 'REGISTERED',
            'expected_departure' => $now,
            'expected_arrival' => $now->copy()->addHours(24),
            'estimated_arrival' => $now->copy()->addHours(24),
        ]);

        // Valid transition: REGISTERED -> CRYOPACKED
        $shipmentService->updateStatus($shipment, 'CRYOPACKED', $this->operator);
        $this->assertEquals('CRYOPACKED', $shipment->fresh()->status);

        // Invalid transition: CRYOPACKED cannot jump directly to DELIVERED
        $this->expectException(Exception::class);
        $shipmentService->updateStatus($shipment, 'DELIVERED', $this->operator);
    }

    /**
     * Test 3: Terminal Status Cannot Be Mutated
     */
    public function test_terminal_delivered_status_cannot_be_reverted(): void
    {
        $shipmentService = app(ShipmentService::class);
        $now = Carbon::now();

        $shipment = Shipment::create([
            'shipment_number' => 'BIO-TEST-002',
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'storage_temp' => 'CRYO_LN2',
            'status' => 'DELIVERED',
            'expected_departure' => $now->subDays(2),
            'expected_arrival' => $now->subDay(),
            'estimated_arrival' => $now->subDay(),
        ]);

        $this->expectException(Exception::class);
        $shipmentService->updateStatus($shipment, 'IN_TRANSIT', $this->operator);
    }

    /**
     * Test 4: ETA & Viability Delay Severity Calculation
     */
    public function test_eta_delay_severity_rules(): void
    {
        $etaService = app(EtaViabilityService::class);
        $expected = Carbon::now();

        // 3 hours delay -> LOW
        $resLow = $etaService->evaluateDelay($expected, $expected->copy()->addHours(3));
        $this->assertTrue($resLow['is_delayed']);
        $this->assertEquals(3, $resLow['delay_hours']);
        $this->assertEquals('LOW', $resLow['severity']);

        // 9 hours delay -> MEDIUM
        $resMed = $etaService->evaluateDelay($expected, $expected->copy()->addHours(9));
        $this->assertEquals('MEDIUM', $resMed['severity']);

        // 18 hours delay -> HIGH (Viability critical)
        $resHigh = $etaService->evaluateDelay($expected, $expected->copy()->addHours(18));
        $this->assertEquals('HIGH', $resHigh['severity']);
    }

    /**
     * Test 5: Customer Isolation (Customer only sees their own manifests)
     */
    public function test_customer_cannot_inspect_other_clients_shipment(): void
    {
        $otherCustomer = User::create([
            'name' => 'Other Lab',
            'email' => 'other@biotrace.io',
            'password' => bcrypt('password'),
            'role' => 'CUSTOMER',
        ]);

        $now = Carbon::now();
        $shipment = Shipment::create([
            'shipment_number' => 'BIO-SECRET-001',
            'customer_id' => $otherCustomer->id,
            'carrier_id' => $this->carrier->id,
            'origin_id' => $this->origin->id,
            'destination_id' => $this->destination->id,
            'storage_temp' => 'DRY_ICE',
            'status' => 'IN_TRANSIT',
            'expected_departure' => $now,
            'expected_arrival' => $now->copy()->addHours(24),
            'estimated_arrival' => $now->copy()->addHours(24),
        ]);

        $this->actingAs($this->customer);
        $response = $this->get("/shipments/{$shipment->id}");
        $response->assertStatus(403);
    }

    /**
     * Test 6: REST API Endpoint Check
     */
    public function test_api_shipment_listing_authenticated(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson('/api/shipments');
        $response->assertStatus(200);
    }
}

