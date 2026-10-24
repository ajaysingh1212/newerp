<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthGates;
use App\Models\BusinessDevice;
use App\Models\ManualParty;
use App\Models\ManualProduct;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\BusinessManagementPermissionsSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class BusinessManagementTest extends TestCase
{
    private ManualParty $party;

    private ManualProduct $product;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('manual_parties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('manual_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
        foreach (['roles', 'permissions'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->timestamps();
                $table->softDeletes();
            });
        }
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
        });
        Schema::create('qa_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('receiver_id');
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_24_000001_create_business_devices_table.php'))->up();
        (require database_path('migrations/2026_10_24_000002_add_business_import_ledger.php'))->up();
        DB::table('users')->insert(['id' => 1, 'name' => 'Operator']);
        $this->actingAs(User::find(1));
        $this->withoutMiddleware(AuthGates::class);
        foreach (['access', 'create', 'show', 'edit', 'delete', 'import', 'activate', 'report'] as $action) {
            Gate::define('business_management_'.$action, fn () => true);
        }
        app('view.finder')->prependLocation(base_path('tests/Fixtures/views'));
        app('view')->getFinder()->prependLocation(base_path('tests/Fixtures/views'));
        app('view')->getFinder()->flush();
        $this->party = ManualParty::create(['name' => 'Party A', 'state' => 'Bihar', 'district' => 'Patna', 'city' => 'Patna']);
        $this->product = ManualProduct::create(['name' => 'Tracker']);
    }

    private function payload(array $devices = []): array
    {
        $devices = $devices ?: [['manual_product_id' => $this->product->id, 'vts_number' => '000123456789012345', 'sim_number' => '001234567890', 'sales_price' => '125.50']];

        return ['manual_party_id' => $this->party->id, 'assigned_at' => '2026-10-24', 'quantity' => count($devices), 'devices' => $devices];
    }

    public function test_crud_and_pending_status(): void
    {
        $this->post(route('admin.business-management.store'), $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        $device = BusinessDevice::firstOrFail();
        $this->assertSame('pending', $device->status);
        $this->assertSame(1, $device->created_by);
        $this->get(route('admin.business-management.create'))->assertOk()->assertSee('device-row-template');
        $this->get(route('admin.business-management.show', $device))->assertOk()->assertSee('000123456789012345');
        $this->get(route('admin.business-management.edit', $device))->assertOk();
        $data = $this->payload();
        $data['devices'][0]['sales_price'] = '200.25';
        $data['status'] = 'activated';
        $this->put(route('admin.business-management.update', $device), $data)->assertSessionHasNoErrors();
        $this->assertSame('200.25', $device->fresh()->sales_price);
        $this->assertSame('pending', $device->fresh()->status);
        $this->delete(route('admin.business-management.destroy', $device))->assertRedirect();
        $this->assertDatabaseCount('business_devices', 0);
    }

    public function test_bulk_create_quantity_duplicates_and_total(): void
    {
        $row = $this->payload()['devices'][0];
        $other = array_merge($row, ['vts_number' => 'second', 'sales_price' => '74.50']);
        $this->post(route('admin.business-management.store'), $this->payload([$row, $other]))->assertSessionHasNoErrors();
        $this->get(route('admin.business-management.index'))->assertOk()->assertViewHas('total', fn ($total) => (float) $total === 200.0);
        $this->post(route('admin.business-management.store'), $this->payload([$row, $row]))->assertSessionHasErrors('devices.0.vts_number');
        $data = $this->payload([$other]);
        $data['quantity'] = 2;
        $this->post(route('admin.business-management.store'), $data)->assertSessionHasErrors('devices');
        $this->assertDatabaseCount('business_devices', 2);
    }

    public function test_index_only_displays_selected_party(): void
    {
        $this->post(route('admin.business-management.store'), $this->payload());
        $party = ManualParty::create(['name' => 'Party B']);
        $this->get(route('admin.business-management.index', ['party_id' => $party->id]))->assertOk()->assertDontSee('000123456789012345');
    }

    public function test_activation_keeps_original_actor_and_time(): void
    {
        $this->post(route('admin.business-management.store'), $this->payload());
        $device = BusinessDevice::firstOrFail();
        $this->post(route('admin.business-management.activate', $device))->assertRedirect();
        $device->refresh();
        $this->assertSame('activated', $device->status);
        $this->assertSame(1, $device->activated_by);
        $time = $device->activated_at->toDateTimeString();
        DB::table('users')->insert(['id' => 2, 'name' => 'Second operator']);
        $this->actingAs(User::find(2));
        $this->travel(1)->hours();
        $this->post(route('admin.business-management.activate', $device));
        $this->assertSame(1, $device->fresh()->activated_by);
        $this->assertSame($time, $device->fresh()->activated_at->toDateTimeString());
    }

    public function test_csv_import_preserves_numbers_and_rejects_whole_invalid_file(): void
    {
        $header = "manual_product_id,vts_number,sim_number,sales_price\n";
        $valid = $this->product->id.",000123456789012345,001234567890,125.50\n";
        $base = ['manual_party_id' => $this->party->id, 'assigned_at' => '2026-10-24'];
        $this->post(route('admin.business-management.import'), $base + ['file' => UploadedFile::fake()->createWithContent('devices.csv', $header.$valid."999,invalid,123,10\n")])->assertSessionHasErrors('devices.1.manual_product_id');
        $this->assertDatabaseCount('business_devices', 0);
        $this->post(route('admin.business-management.import'), $base + ['file' => UploadedFile::fake()->createWithContent('devices.csv', $header.$valid)])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('business_devices', ['vts_number' => '000123456789012345', 'sim_number' => '001234567890', 'status' => 'pending']);
    }

    public function test_report_grouping_filters_and_exports(): void
    {
        $this->post(route('admin.business-management.store'), $this->payload());
        $device = BusinessDevice::firstOrFail();
        $this->post(route('admin.business-management.activate', $device));
        foreach (['party', 'model', 'state', 'district', 'city'] as $group) {
            $this->get(route('admin.business-management.report', ['group_by' => $group, 'state' => 'Bihar', 'from' => '2026-10-01', 'to' => '2026-10-31']))
                ->assertOk()->assertViewHas('totals', fn ($totals) => $totals->devices === 1 && (int) $totals->activated === 1)
                ->assertViewHas('summaries', fn ($summary) => $summary->count() === 1);
        }
        $this->get(route('admin.business-management.report', ['status' => 'pending']))->assertViewHas('totals', fn ($totals) => $totals->devices === 0);
        $this->get(route('admin.business-management.report', ['to' => '2026-10-31']))->assertOk();
        foreach (['csv', 'xlsx'] as $format) {
            $this->get(route('admin.business-management.report', ['export' => $format]))->assertOk()->assertDownload('business-report.'.$format);
        }
    }

    public function test_permission_checks_protect_mutations_reports_and_import(): void
    {
        foreach (['access', 'create', 'show', 'edit', 'delete', 'import', 'activate', 'report'] as $action) {
            Gate::define('business_management_'.$action, fn () => false);
        }
        $this->get(route('admin.business-management.index'))->assertForbidden();
        $this->get(route('admin.business-management.report'))->assertForbidden();
        $this->get(route('admin.business-management.template'))->assertForbidden();
        $this->post(route('admin.business-management.store'), $this->payload())->assertForbidden();
        $this->post(route('admin.business-management.import'))->assertForbidden();
        $device = BusinessDevice::create($this->payload()['devices'][0] + ['manual_party_id' => $this->party->id, 'assigned_at' => '2026-10-24']);
        $this->post(route('admin.business-management.activate', $device))->assertForbidden();
        $this->put(route('admin.business-management.update', $device), $this->payload())->assertForbidden();
        $this->delete(route('admin.business-management.destroy', $device))->assertForbidden();
    }

    public function test_permission_seeder_is_repeatable_and_grants_admin_access(): void
    {
        Role::create(['title' => 'Admin']);
        $this->seed(BusinessManagementPermissionsSeeder::class);
        $this->seed(BusinessManagementPermissionsSeeder::class);
        $this->assertDatabaseCount('permissions', 8);
        $this->assertSame(8, Role::find(1)->permissions()->count());
    }

    public function test_seeded_admin_can_use_real_permission_middleware(): void
    {
        Role::create(['title' => 'Admin']);
        $this->seed(BusinessManagementPermissionsSeeder::class);
        DB::table('role_user')->insert(['role_id' => 1, 'user_id' => 1]);
        $this->actingAs(User::find(1));
        $this->withMiddleware(AuthGates::class);
        $this->get(route('admin.business-management.index'))->assertOk()->assertSee('Business Management');
    }

    public function test_excel_import_and_large_form_payload(): void
    {
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->fromArray([
            ['manual_product_id', 'vts_number', 'sim_number', 'sales_price'],
            [$this->product->id, '000111222333444555', '000777888999', '500.25'],
        ]);
        $sheet->getActiveSheet()->setCellValueExplicit('B2', '000111222333444555', DataType::TYPE_STRING);
        $sheet->getActiveSheet()->setCellValueExplicit('C2', '000777888999', DataType::TYPE_STRING);
        ob_start();
        (new Xlsx($sheet))->save('php://output');
        $content = ob_get_clean();
        $this->post(route('admin.business-management.import'), [
            'manual_party_id' => $this->party->id, 'assigned_at' => '2026-10-24',
            'file' => UploadedFile::fake()->createWithContent('devices.xlsx', $content),
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('business_devices', ['vts_number' => '000111222333444555', 'sim_number' => '000777888999']);
        $data = $this->payload();
        $data['devices_payload'] = json_encode($data['devices']);
        unset($data['devices']);
        $this->post(route('admin.business-management.store'), $data)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('business_devices', 2);
    }

    public function test_import_page_and_preview_expose_original_headers_without_saving(): void
    {
        $this->get(route('admin.business-management.importForm'))->assertOk()->assertSee('Match spreadsheet columns')->assertSee('business-import.js');
        $file = UploadedFile::fake()->createWithContent('custom.csv', "Price,Device,IMEI,SIM\n125.50,Tracker,000123456789012345,001234567890\n");
        $this->postJson(route('admin.business-management.importPreview'), ['file' => $file])
            ->assertOk()->assertJsonPath('count', 1)->assertJsonPath('headers.0', 'Price')
            ->assertJsonPath('rows.0.2', '000123456789012345')->assertJsonPath('mapping.vts_number', 2);
        $this->assertDatabaseCount('business_devices', 0);
        Gate::define('business_management_import', fn () => false);
        $this->postJson(route('admin.business-management.importPreview'), ['file' => $file])->assertForbidden();
    }

    public function test_custom_column_mapping_imports_exact_values_and_rejects_duplicate_mapping(): void
    {
        $content = "Cost,Serial,Network,Hardware,Extra\n125.50,000123456789012345,001234567890,Tracker,ignore me\n";
        $base = ['manual_party_id' => $this->party->id, 'assigned_at' => '2026-10-24',
            'mapping' => ['manual_product_id' => 3, 'vts_number' => 1, 'sim_number' => 2, 'sales_price' => 0]];
        $bad = $base;
        $bad['mapping']['sim_number'] = 1;
        $bad['file'] = UploadedFile::fake()->createWithContent('custom.csv', $content);
        $this->postJson(route('admin.business-management.import'), $bad)->assertUnprocessable()->assertJsonValidationErrors('mapping');
        $this->assertDatabaseCount('business_devices', 0);
        $base['file'] = UploadedFile::fake()->createWithContent('custom.csv', $content);
        $this->postJson(route('admin.business-management.import'), $base)->assertOk()->assertJsonStructure(['redirect']);
        $this->assertDatabaseHas('business_devices', ['manual_product_id' => $this->product->id, 'vts_number' => '000123456789012345', 'sim_number' => '001234567890', 'sales_price' => '125.50']);
    }

    public function test_import_rejects_missing_mapping_and_oversize_row_count(): void
    {
        $file = UploadedFile::fake()->createWithContent('unknown.csv', "A,B,C,D\n1,2,3,4\n");
        $this->postJson(route('admin.business-management.import'), ['file' => $file])->assertUnprocessable()->assertJsonValidationErrors('mapping.manual_product_id');
        $large = "Device,VTS,SIM,Price\n".str_repeat("Tracker,test,123,10\n", 1001);
        $this->postJson(route('admin.business-management.importPreview'), ['file' => UploadedFile::fake()->createWithContent('large.csv', $large)])->assertUnprocessable()->assertJsonValidationErrors('file');
        $this->assertDatabaseCount('business_devices', 0);
    }

    public function test_multi_party_csv_import_assigns_each_row_and_previews_party_mapping(): void
    {
        $otherParty = ManualParty::create(['name' => 'Party B']);
        $content = "Customer,Device,VTS,SIM,Price\nParty A,Tracker,000111,000222,100.50\n".$otherParty->id.",Tracker,000333,000444,200.25\nparty a,Tracker,000555,000666,50\n";
        $this->postJson(route('admin.business-management.importPreview'), ['file' => UploadedFile::fake()->createWithContent('parties.csv', $content)])
            ->assertOk()->assertJsonPath('count', 3)->assertJsonPath('mapping.manual_party_id', 0)->assertJsonPath('rows.1.0', (string) $otherParty->id);
        $this->postJson(route('admin.business-management.import'), [
            'party_source' => 'file', 'assigned_at' => '2026-10-24',
            'file' => UploadedFile::fake()->createWithContent('parties.csv', $content),
            'mapping' => ['manual_party_id' => 0, 'manual_product_id' => 1, 'vts_number' => 2, 'sim_number' => 3, 'sales_price' => 4],
        ])->assertOk()->assertJsonPath('devices', 3)->assertJsonPath('parties', 2);
        $this->assertDatabaseHas('business_devices', ['vts_number' => '000111', 'manual_party_id' => $this->party->id, 'sim_number' => '000222', 'status' => 'pending']);
        $this->assertDatabaseHas('business_devices', ['vts_number' => '000333', 'manual_party_id' => $otherParty->id]);
        $this->assertDatabaseHas('business_devices', ['vts_number' => '000555', 'manual_party_id' => $this->party->id]);
        $this->assertSame(2, BusinessDevice::where('manual_party_id', $this->party->id)->count());
    }

    public function test_unknown_ambiguous_deleted_or_empty_party_rejects_the_whole_file(): void
    {
        ManualParty::create(['name' => 'Same name']);
        ManualParty::create(['name' => 'Same name']);
        $deleted = ManualParty::create(['name' => 'Deleted party']);
        $deleted->delete();
        foreach (['Unknown party', 'Same name', '999999', (string) $deleted->id, ''] as $invalidParty) {
            $content = "Party,Device,VTS,SIM,Price\nParty A,Tracker,valid,001,10\n".$invalidParty.",Tracker,invalid,002,20\n";
            $this->postJson(route('admin.business-management.import'), [
                'party_source' => 'file', 'assigned_at' => '2026-10-24',
                'file' => UploadedFile::fake()->createWithContent('invalid-parties.csv', $content),
            ])->assertUnprocessable()->assertJsonValidationErrors('devices.1.manual_party_id');
            $this->assertDatabaseCount('business_devices', 0);
        }
    }

    public function test_multi_party_requires_party_mapping_and_still_validates_every_device(): void
    {
        $otherParty = ManualParty::create(['name' => 'Party B']);
        $content = "Party,Device,VTS,SIM,Price\nParty A,Tracker,repeated,001,10\nParty B,Tracker,repeated,002,20\n";
        $this->postJson(route('admin.business-management.import'), [
            'party_source' => 'file', 'assigned_at' => '2026-10-24',
            'file' => UploadedFile::fake()->createWithContent('duplicates.csv', $content),
        ])->assertUnprocessable()->assertJsonValidationErrors('devices.0.vts_number');
        $content = "Device,VTS,SIM,Price\nTracker,test,001,10\n";
        $this->postJson(route('admin.business-management.import'), [
            'party_source' => 'file', 'manual_party_id' => $otherParty->id, 'assigned_at' => '2026-10-24',
            'file' => UploadedFile::fake()->createWithContent('missing-party.csv', $content),
        ])->assertUnprocessable()->assertJsonValidationErrors('mapping.manual_party_id');
        $this->assertDatabaseCount('business_devices', 0);
        $this->postJson(route('admin.business-management.import'), [
            'party_source' => 'single', 'manual_party_id' => $otherParty->id, 'assigned_at' => '2026-10-24',
            'file' => UploadedFile::fake()->createWithContent('single-party.csv', $content),
        ])->assertOk()->assertJsonPath('parties', 1);
        $this->assertDatabaseHas('business_devices', ['vts_number' => 'test', 'manual_party_id' => $otherParty->id]);
    }

    public function test_multi_party_excel_import_and_updated_template(): void
    {
        $otherParty = ManualParty::create(['name' => 'Party B']);
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->fromArray([
            ['manual_party_id', 'manual_product_id', 'vts_number', 'sim_number', 'sales_price'],
            [$this->party->id, $this->product->id, 'excel-first', 'first-sim', 100],
            [$otherParty->id, $this->product->id, 'excel-second', 'second-sim', 200],
        ]);
        ob_start();
        (new Xlsx($sheet))->save('php://output');
        $content = ob_get_clean();
        $this->postJson(route('admin.business-management.import'), [
            'party_source' => 'file', 'assigned_at' => '2026-10-24',
            'file' => UploadedFile::fake()->createWithContent('parties.xlsx', $content),
        ])->assertOk()->assertJsonPath('parties', 2);
        $this->assertDatabaseHas('business_devices', ['vts_number' => 'excel-first', 'manual_party_id' => $this->party->id]);
        $this->assertDatabaseHas('business_devices', ['vts_number' => 'excel-second', 'manual_party_id' => $otherParty->id]);
        $template = $this->get(route('admin.business-management.template'))->assertOk();
        $this->assertStringContainsString('manual_party_id,manual_product_id,vts_number,sim_number,sales_price', $template->streamedContent());
    }

    public function test_device_datatables_paginate_search_and_sort_all_records(): void
    {
        $rows = [];
        for ($index = 0; $index < 32; $index++) {
            $rows[] = ['manual_product_id' => $this->product->id, 'vts_number' => sprintf('VTS-%03d', $index), 'sim_number' => '000123', 'sales_price' => (string) ($index + 1)];
        }
        $this->post(route('admin.business-management.store'), $this->payload($rows))->assertSessionHasNoErrors();
        $parameters = ['party_id' => $this->party->id, 'draw' => 1, 'start' => 25, 'length' => 10,
            'columns' => [['data' => 'vts_number', 'name' => 'vts_number', 'searchable' => 'true', 'orderable' => 'true']],
            'order' => [['column' => 0, 'dir' => 'asc']]];
        $this->getJson(route('admin.business-management.index', $parameters), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertJsonPath('recordsTotal', 32)->assertJsonCount(7, 'data')->assertJsonPath('data.0.vts_number', 'VTS-025');
        $parameters['start'] = 0;
        $parameters['search'] = ['value' => 'VTS-031', 'regex' => 'false'];
        $this->getJson(route('admin.business-management.index', $parameters), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertJsonPath('recordsFiltered', 1)->assertJsonPath('data.0.vts_number', 'VTS-031');
        $parameters['party_id'] = ManualParty::create(['name' => 'Empty party'])->id;
        $this->getJson(route('admin.business-management.index', $parameters), ['X-Requested-With' => 'XMLHttpRequest'])->assertJsonPath('recordsTotal', 0);
        $parameters['party_id'] = $this->party->id;
        $this->getJson(route('admin.business-management.report', $parameters), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertJsonPath('recordsFiltered', 1)->assertJsonPath('data.0.party_name', 'Party A')->assertJsonMissingPath('data.0.party');
        $device = BusinessDevice::where('vts_number', 'VTS-031')->firstOrFail();
        $this->post(route('admin.business-management.activate', $device));
        $parameters['columns'] = [['data' => 'model_name', 'name' => 'product.name', 'searchable' => 'true', 'orderable' => 'true']];
        $parameters['search']['value'] = 'Tracker';
        $parameters['status'] = 'activated';
        $response = $this->getJson(route('admin.business-management.index', $parameters), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertJsonPath('recordsFiltered', 1)->assertJsonPath('data.0.model_name', 'Tracker')->assertJsonMissingPath('data.0.activator');
        $this->assertStringContainsString('Operator', $response->json('data.0.activation'));
    }
}
