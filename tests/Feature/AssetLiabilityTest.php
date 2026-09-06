<?php

namespace Tests\Feature;

use App\Livewire\Admin\AssetLiabilityManager;
use App\Models\AssetLiability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssetLiabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_assets_liabilities_page(): void
    {
        $response = $this->get(route('admin.assets-liabilities'));
        $response->assertRedirect(route('login'));
    }

    public function test_sellers_cannot_access_assets_liabilities_page(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'is_active' => true]);
        $this->actingAs($seller);

        $response = $this->get(route('admin.assets-liabilities'));
        $response->assertRedirect(route('seller.quick-sell'));
    }

    public function test_admin_can_access_assets_liabilities_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.assets-liabilities'));
        $response->assertStatus(200);
        $response->assertSee('Assets & Liabilities');
        $response->assertSee('Assets');
        $response->assertSee('Liabilities');
    }

    public function test_admin_can_switch_tabs(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        Livewire::test(AssetLiabilityManager::class)
            ->assertSet('activeTab', 'asset')
            ->call('switchTab', 'liability')
            ->assertSet('activeTab', 'liability')
            ->call('switchTab', 'asset')
            ->assertSet('activeTab', 'asset');
    }

    public function test_admin_can_add_asset_record(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        Livewire::test(AssetLiabilityManager::class)
            ->call('openAddModal', 'asset')
            ->assertSet('showRecordModal', true)
            ->assertSet('recordType', 'asset')
            ->set('name', 'Freezer')
            ->set('amount', 25000)
            ->set('record_date', '2026-09-06')
            ->set('record_time', '10:30')
            ->call('saveRecord')
            ->assertHasNoErrors()
            ->assertSet('showRecordModal', false);

        $this->assertDatabaseHas('asset_liabilities', [
            'type' => 'asset',
            'name' => 'Freezer',
            'amount' => 25000,
        ]);

        $record = AssetLiability::where('name', 'Freezer')->first();
        $this->assertNotNull($record);
        $this->assertEquals('2026-09-06', $record->record_date->format('Y-m-d'));
        $this->assertEquals('10:30 AM', $record->formatted_time);
    }

    public function test_admin_can_add_liability_record(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        Livewire::test(AssetLiabilityManager::class)
            ->call('openAddModal', 'liability')
            ->assertSet('showRecordModal', true)
            ->assertSet('recordType', 'liability')
            ->set('name', 'Supplier Due')
            ->set('amount', 8000)
            ->set('record_date', '2026-09-06')
            ->set('record_time', '12:10')
            ->call('saveRecord')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('asset_liabilities', [
            'type' => 'liability',
            'name' => 'Supplier Due',
            'amount' => 8000,
        ]);

        $record = AssetLiability::where('name', 'Supplier Due')->first();
        $this->assertNotNull($record);
        $this->assertEquals('2026-09-06', $record->record_date->format('Y-m-d'));
        $this->assertEquals('12:10 PM', $record->formatted_time);
    }

    public function test_total_assets_and_liabilities_are_automatically_calculated(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        AssetLiability::create([
            'type' => 'asset',
            'name' => 'Freezer',
            'amount' => 25000,
            'record_date' => '2026-09-06',
            'record_time' => '10:30:00',
        ]);

        AssetLiability::create([
            'type' => 'asset',
            'name' => 'Cash',
            'amount' => 10000,
            'record_date' => '2026-09-06',
            'record_time' => '11:15:00',
        ]);

        AssetLiability::create([
            'type' => 'liability',
            'name' => 'Supplier Due',
            'amount' => 8000,
            'record_date' => '2026-09-06',
            'record_time' => '12:10:00',
        ]);

        AssetLiability::create([
            'type' => 'liability',
            'name' => 'Business Loan',
            'amount' => 20000,
            'record_date' => '2026-09-05',
            'record_time' => '16:30:00',
        ]);

        Livewire::test(AssetLiabilityManager::class)
            ->assertViewHas('totalAssets', 35000.0)
            ->assertViewHas('totalLiabilities', 28000.0)
            ->assertSee('Freezer')
            ->assertSee('35,000')
            ->call('switchTab', 'liability')
            ->assertSee('Supplier Due')
            ->assertSee('Business Loan')
            ->assertSee('28,000');
    }

    public function test_admin_can_edit_record(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $record = AssetLiability::create([
            'type' => 'asset',
            'name' => 'Old Refrigerator',
            'amount' => 15000,
            'record_date' => '2026-09-06',
            'record_time' => '09:00:00',
        ]);

        Livewire::test(AssetLiabilityManager::class)
            ->call('editRecord', $record->id)
            ->assertSet('editingRecordId', $record->id)
            ->assertSet('name', 'Old Refrigerator')
            ->set('name', 'Commercial Deep Freezer')
            ->set('amount', 22000)
            ->call('saveRecord')
            ->assertHasNoErrors()
            ->assertSet('showRecordModal', false);

        $this->assertDatabaseHas('asset_liabilities', [
            'id' => $record->id,
            'name' => 'Commercial Deep Freezer',
            'amount' => 22000,
        ]);
    }

    public function test_admin_can_delete_record(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $record = AssetLiability::create([
            'type' => 'liability',
            'name' => 'Temporary Advance',
            'amount' => 5000,
            'record_date' => '2026-09-06',
            'record_time' => '09:00:00',
        ]);

        Livewire::test(AssetLiabilityManager::class)
            ->call('deleteRecord', $record->id);

        $this->assertDatabaseMissing('asset_liabilities', [
            'id' => $record->id,
        ]);
    }

    public function test_records_are_sorted_by_newest_date_and_time_first(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $old = AssetLiability::create([
            'type' => 'asset',
            'name' => 'Old Asset',
            'amount' => 1000,
            'record_date' => '2026-09-01',
            'record_time' => '10:00:00',
        ]);

        $newerDate = AssetLiability::create([
            'type' => 'asset',
            'name' => 'Newer Date Asset',
            'amount' => 2000,
            'record_date' => '2026-09-06',
            'record_time' => '09:00:00',
        ]);

        $newestTime = AssetLiability::create([
            'type' => 'asset',
            'name' => 'Newest Time Asset',
            'amount' => 3000,
            'record_date' => '2026-09-06',
            'record_time' => '15:00:00',
        ]);

        Livewire::test(AssetLiabilityManager::class)
            ->assertSeeInOrder([
                'Newest Time Asset',
                'Newer Date Asset',
                'Old Asset',
            ]);
    }

    public function test_records_remain_saved_after_page_reload(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        AssetLiability::create([
            'type' => 'asset',
            'name' => 'Beverage Cooler',
            'amount' => 18000,
            'record_date' => '2026-09-06',
            'record_time' => '11:00:00',
        ]);

        // First load
        $res1 = $this->get(route('admin.assets-liabilities'));
        $res1->assertSee('Beverage Cooler');
        $res1->assertSee('18,000');

        // Subsequent reload
        $res2 = $this->get(route('admin.assets-liabilities'));
        $res2->assertSee('Beverage Cooler');
        $res2->assertSee('18,000');
    }
}
