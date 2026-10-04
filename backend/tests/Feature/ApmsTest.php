<?php
namespace Tests\Feature;
use App\Domains\Inventory\Models\Batch;
use App\Domains\Inventory\Models\Medicine;
use App\Domains\Sales\Models\Sale;
use Tests\TestCase;
class ApmsTest extends TestCase
{
    public function test_health(): void { $this->getJson('/api/v1/health')->assertOk()->assertJsonPath('service', 'apms-api'); }

    public function test_login(): void {
        $this->postJson('/api/v1/auth/login', ['email' => 'owner@ahmedpharma.local', 'password' => 'password123'])->assertOk()->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_medicines_list(): void {
        $token = $this->loginAs('owner@ahmedpharma.local');
        $res = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/v1/medicines');
        $res->assertOk(); $this->assertGreaterThan(0, count($res->json('data')));
    }

    public function test_fefo_deduction(): void {
        $token = $this->loginAs('cashier@ahmedpharma.local');
        $med = Medicine::where('name', 'Panadol Extra')->firstOrFail();
        // Get batches in FEFO order
        $batches = Batch::where('medicine_id', $med->id)->orderBy('expiry_date')->get();
        $firstBatch = $batches[0];
        $firstQtyBefore = (float) $firstBatch->quantity;
        $secondQtyBefore = (float) $batches[1]->quantity;

        // Sell 50 units — should deduct from first (earliest expiry) batch only
        $res = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/v1/pos/sales', [
            'items' => [['medicine_id' => $med->id, 'quantity' => 50]],
            'payment_method' => 'cash',
        ]);
        $res->assertStatus(201);

        $firstBatch->refresh(); $batches[1]->refresh();
        $this->assertEquals($firstQtyBefore - 50, (float) $firstBatch->quantity, 'FEFO: earliest batch deducted first');
        $this->assertEquals($secondQtyBefore, (float) $batches[1]->quantity, 'FEFO: later batch untouched');
    }

    public function test_insufficient_stock_rejected(): void {
        $token = $this->loginAs('cashier@ahmedpharma.local');
        $med = Medicine::where('name', 'Disprin')->firstOrFail();
        $this->withHeader('Authorization', "Bearer $token")->postJson('/api/v1/pos/sales', [
            'items' => [['medicine_id' => $med->id, 'quantity' => 999999]],
        ])->assertStatus(422);
    }

    public function test_idempotent_sale(): void {
        $token = $this->loginAs('cashier@ahmedpharma.local');
        $med = Medicine::where('name', 'Disprin')->firstOrFail();
        $key = 'test-' . uniqid();
        $payload = ['items' => [['medicine_id' => $med->id, 'quantity' => 5]], 'idempotency_key' => $key];
        $r1 = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/v1/pos/sales', $payload);
        $r1->assertStatus(201);
        $r2 = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/v1/pos/sales', $payload);
        $r2->assertStatus(201);
        $this->assertEquals($r1->json('data.id'), $r2->json('data.id'), 'Idempotent: same sale returned');
        $this->assertEquals(1, Sale::where('idempotency_key', $key)->count());
    }

    public function test_cashier_cannot_view_inventory(): void {
        $token = $this->loginAs('cashier@ahmedpharma.local');
        // Cashier has pos.use but NOT inventory.view
        $this->withHeader('Authorization', "Bearer $token")->getJson('/api/v1/medicines')->assertStatus(403);
        // But CAN use POS
        $this->withHeader('Authorization', "Bearer $token")->getJson('/api/v1/sales')->assertOk();
    }

    public function test_auditor_readonly(): void {
        $token = $this->loginAs('auditor@ahmedpharma.local');
        $med = Medicine::first();
        $this->withHeader('Authorization', "Bearer $token")->postJson('/api/v1/pos/sales', [
            'items' => [['medicine_id' => $med->id, 'quantity' => 1]],
        ])->assertStatus(403);
    }

    public function test_low_stock_alert(): void {
        $token = $this->loginAs('owner@ahmedpharma.local');
        $this->withHeader('Authorization', "Bearer $token")->getJson('/api/v1/medicines/low-stock')->assertOk();
    }

    public function test_near_expiry_alert(): void {
        $token = $this->loginAs('owner@ahmedpharma.local');
        $res = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/v1/medicines/near-expiry?days=365');
        $res->assertOk(); $this->assertGreaterThan(0, count($res->json('data')));
    }

    public function test_daily_sales_report(): void {
        $token = $this->loginAs('owner@ahmedpharma.local');
        $this->withHeader('Authorization', "Bearer $token")->getJson('/api/v1/reports/daily-sales')->assertOk();
    }
}
