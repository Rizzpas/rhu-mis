<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\User;
use App\Models\InventoryLog;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FlowEInventoryManagementTest extends TestCase
{
    protected User $pharmacist;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'rhu-mis',
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
        \Illuminate\Support\Facades\DB::reconnect('mysql');
        \Illuminate\Support\Facades\DB::beginTransaction();

        $this->pharmacist = User::where('role', 'pharmacy')->first()
            ?? User::create([
                'name' => 'Pharmacist Test',
                'email' => 'pharm_test_' . uniqid() . '@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'pharmacy',
                'is_active' => true,
            ]);
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\DB::rollBack();
        parent::tearDown();
    }

    public function test_pharmacist_can_store_medicine_with_initial_batch()
    {
        $response = $this->actingAs($this->pharmacist)->post(route('pharmacy.medicines.store'), [
            'name' => 'Amoxicillin 500mg',
            'generic_name' => 'Amoxicillin',
            'category' => 'Antibiotic',
            'form' => 'Capsule',
            'unit' => 'Piece',
            'batch_number' => '  rhu-202610-xyz1.  ',
            'expiration_date' => now()->addYear()->format('Y-m-d'),
            'quantity' => 150,
        ]);

        $response->assertSessionHas('success');

        $medicine = Medicine::where('name', 'Amoxicillin 500mg')->first();
        $this->assertNotNull($medicine);
        $this->assertTrue($medicine->is_active);

        $batch = $medicine->batches()->first();
        $this->assertNotNull($batch);
        $this->assertEquals('RHU-202610-XYZ1', $batch->batch_number);
        $this->assertEquals(150, $batch->quantity);
        $this->assertEquals(150, $medicine->total_stock);

        $this->assertDatabaseHas('inventory_logs', [
            'medicine_id' => $medicine->id,
            'batch_id' => $batch->id,
            'action' => 'Added',
            'quantity_changed' => 150,
        ]);
    }

    public function test_pharmacist_can_add_stock_batch()
    {
        $medicine = Medicine::create([
            'name' => 'Paracetamol 500mg',
            'generic_name' => 'Paracetamol',
            'form' => 'Tablet',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->pharmacist)->post(route('pharmacy.medicines.add-stock', $medicine->id), [
            'batch_number' => 'LOT-8877A',
            'expiration_date' => now()->addMonths(8)->format('Y-m-d'),
            'quantity' => 100,
        ]);

        $response->assertSessionHas('success');

        $batch = MedicineBatch::where('batch_number', 'LOT-8877A')->first();
        $this->assertNotNull($batch);
        $this->assertEquals(100, $batch->quantity);
        $this->assertEquals('active', $batch->status);
    }

    public function test_pharmacist_can_adjust_batch_stock_count()
    {
        $medicine = Medicine::create([
            'name' => 'Cetirizine 10mg',
            'generic_name' => 'Cetirizine',
            'form' => 'Tablet',
            'is_active' => true,
        ]);

        $batch = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'CET-001',
            'expiration_date' => now()->addYear(),
            'quantity' => 50,
            'original_quantity' => 50,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->pharmacist)->post(route('pharmacy.batches.adjust', $batch->id), [
            'new_quantity' => 45,
            'adjustment_reason' => 'Physical Recount',
            'adjustment_notes' => 'Found 5 broken blisters during weekly audit',
        ]);

        $response->assertSessionHas('success');

        $batch->refresh();
        $this->assertEquals(45, $batch->quantity);
        $this->assertEquals('active', $batch->status);

        $this->assertDatabaseHas('inventory_logs', [
            'medicine_id' => $medicine->id,
            'batch_id' => $batch->id,
            'quantity_changed' => -5,
        ]);
    }

    public function test_pharmacist_can_dispose_and_write_off_batch()
    {
        $medicine = Medicine::create([
            'name' => 'Cough Syrup 60ml',
            'generic_name' => 'Dextromethorphan',
            'form' => 'Syrup',
            'is_active' => true,
        ]);

        $batch = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'EXP-2026',
            'expiration_date' => now()->addDays(5),
            'quantity' => 20,
            'original_quantity' => 20,
            'status' => 'active',
        ]);

        $this->assertEquals(20, $medicine->total_stock);

        $response = $this->actingAs($this->pharmacist)->post(route('pharmacy.batches.dispose', $batch->id), [
            'disposal_reason' => 'Damaged / Broken',
            'disposal_notes' => 'Vials broken in transit, wrote off per policy',
        ]);

        $response->assertSessionHas('success');

        $batch->refresh();
        $this->assertEquals(0, $batch->quantity);
        $this->assertEquals('disposed', $batch->status);
        $this->assertNotNull($batch->disposed_at);
        $this->assertEquals($this->pharmacist->id, $batch->disposed_by);
        $this->assertEquals('Damaged / Broken', $batch->disposal_reason);

        // Disposed batch should no longer count in medicine total_stock
        $this->assertEquals(0, $medicine->total_stock);

        // Inventory log deduction recorded
        $this->assertDatabaseHas('inventory_logs', [
            'medicine_id' => $medicine->id,
            'batch_id' => $batch->id,
            'action' => 'Deducted',
            'quantity_changed' => -20,
        ]);
    }

    public function test_pharmacist_can_toggle_medicine_active_status()
    {
        $medicine = Medicine::create([
            'name' => 'Old Drug 100mg',
            'generic_name' => 'Old Drug',
            'form' => 'Tablet',
            'is_active' => true,
        ]);

        // Archive
        $response = $this->actingAs($this->pharmacist)->post(route('pharmacy.medicines.toggle-status', $medicine->id));
        $response->assertSessionHas('success');

        $medicine->refresh();
        $this->assertFalse($medicine->is_active);

        // Should be excluded from active scope
        $this->assertFalse(Medicine::active()->where('id', $medicine->id)->exists());

        // Re-activate
        $response = $this->actingAs($this->pharmacist)->post(route('pharmacy.medicines.toggle-status', $medicine->id));
        $response->assertSessionHas('success');

        $medicine->refresh();
        $this->assertTrue($medicine->is_active);
        $this->assertTrue(Medicine::active()->where('id', $medicine->id)->exists());
    }

    public function test_pharmacist_can_view_written_off_medicines_grouped_by_medicine()
    {
        $medicine = Medicine::create([
            'name' => 'Amoxicillin 250mg Susp',
            'generic_name' => 'Amoxicillin',
            'form' => 'Suspension',
            'unit' => 'Bottle',
            'is_active' => true,
        ]);

        $batch1 = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'AMX-DISP-01',
            'expiration_date' => now()->subMonth(),
            'quantity' => 0,
            'original_quantity' => 15,
            'status' => 'disposed',
            'disposed_at' => now()->subDays(2),
            'disposed_by' => $this->pharmacist->id,
            'disposal_reason' => 'Expired',
            'disposal_notes' => 'Passed expiry date',
        ]);

        $batch2 = MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => 'AMX-DISP-02',
            'expiration_date' => now()->addMonths(6),
            'quantity' => 0,
            'original_quantity' => 10,
            'status' => 'disposed',
            'disposed_at' => now()->subDay(),
            'disposed_by' => $this->pharmacist->id,
            'disposal_reason' => 'Damaged / Broken',
            'disposal_notes' => 'Cracked glass container',
        ]);

        $response = $this->actingAs($this->pharmacist)->get(route('pharmacy.written-off'));

        $response->assertStatus(200);
        $response->assertSee('Written-Off & Disposed Inventory', false);
        $response->assertSee('Amoxicillin 250mg Susp');
        $response->assertSee('AMX-DISP-01');
        $response->assertSee('AMX-DISP-02');
        $response->assertSee('Expired');
        $response->assertSee('Damaged / Broken');

        // Test filtering by reason
        $filterResponse = $this->actingAs($this->pharmacist)->get(route('pharmacy.written-off', ['reason' => 'Expired']));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('AMX-DISP-01');

        // Test search by lot number
        $searchResponse = $this->actingAs($this->pharmacist)->get(route('pharmacy.written-off', ['search' => 'AMX-DISP-02']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('AMX-DISP-02');
    }
}

