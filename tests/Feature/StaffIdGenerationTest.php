<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StaffIdGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'rhu-mis',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        parent::tearDown();
    }

    public function test_new_staff_gets_unique_role_coded_staff_id(): void
    {
        $doctor = User::create([
            'name' => 'Staff Id Doctor',
            'email' => 'sid_doc_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'regular_doctor',
        ]);
        $nurse = User::create([
            'name' => 'Staff Id Nurse',
            'email' => 'sid_nrs_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'clinical_nurse',
        ]);

        $yy = now()->format('ym');
        $this->assertMatchesRegularExpression("/^DOC-{$yy}-[23456789ABCDEFGHJKMNPQRSTUVWXYZ]{4}$/", $doctor->staff_id);
        $this->assertMatchesRegularExpression("/^NRS-{$yy}-[23456789ABCDEFGHJKMNPQRSTUVWXYZ]{4}$/", $nurse->staff_id);
        $this->assertNotEquals($doctor->staff_id, $nurse->staff_id);
        $this->assertEquals($doctor->staff_id, User::find($doctor->id)->staff_id);
    }

    public function test_staff_id_is_not_mass_assignable_and_shown_in_settings(): void
    {
        $user = User::create([
            'name' => 'Staff Id Lab',
            'email' => 'sid_lab_' . uniqid() . '@rhu.gov.ph',
            'password' => bcrypt('password'),
            'role' => 'laboratory',
        ]);
        $original = $user->staff_id;

        $user->fill(['staff_id' => 'HACK-00-0000'])->save();
        $this->assertEquals($original, $user->fresh()->staff_id);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($original);
    }

    public function test_all_existing_staff_have_unique_staff_ids(): void
    {
        $this->assertEquals(0, User::withTrashed()->whereNull('staff_id')->count());
        $total = User::withTrashed()->count();
        $distinct = User::withTrashed()->distinct()->count('staff_id');
        $this->assertEquals($total, $distinct);
    }
}
