<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FrontdeskRegistrationDropdownDesignTest extends TestCase
{
    protected User $frontdesk;

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

        $this->frontdesk = User::where('role', 'information_desk')->first()
            ?? User::create([
                'name' => 'Front Desk Staff',
                'email' => 'frontdesk_' . uniqid() . '@example.com',
                'password' => bcrypt('password123'),
                'role' => 'information_desk',
                'status' => 'Present',
            ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_new_patient_registration_page_renders_styled_custom_dropdowns()
    {
        $response = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['new_patient' => '1']));

        $response->assertStatus(200);

        // Verify that custom-dropdown-container is rendered for demographics
        $response->assertSee('custom-dropdown-container', false);

        // Verify custom options and badge styling are present in the DOM
        $response->assertSee('Biological male', false);
        $response->assertSee('Biological female', false);
        $response->assertSee('Universal plasma donor', false);
        $response->assertSee('Regular Adult', false);
        $response->assertSee('Senior Citizen', false);
        $response->assertSee('PWD', false);
        $response->assertSee('Pediatric', false);

        // Verify native hidden inputs exist with correct names for form post
        $response->assertSee('name="sex"', false);
        $response->assertSee('name="civil_status"', false);
        $response->assertSee('name="blood_type"', false);
        $response->assertSee('name="classification"', false);
        $response->assertSee('name="education"', false);
        $response->assertSee('name="occupation"', false);
        $response->assertSee('name="religion"', false);
    }

    public function test_selected_patient_edit_renders_styled_dropdowns_with_prefilled_values()
    {
        $patient = Patient::create([
            'patient_id' => 'PAT-' . uniqid(),
            'first_name' => 'Juan',
            'middle_name' => 'Protacio',
            'last_name' => 'Rizal',
            'suffix' => null,
            'sex' => 'Male',
            'civil_status' => 'Single',
            'blood_type' => 'O+',
            'classification' => 'Regular Adult',
            'dob' => '1990-06-19',
            'contact_number' => '09123456789',
            'address' => 'Poblacion 1, Silang, Cavite',
            'education' => 'College Graduate',
            'occupation' => 'Employed',
            'religion' => 'Roman Catholic',
            'philhealth_number' => '12-345678901-2',
            'mothers_maiden_name' => 'Teodora Alonso Realonda',
        ]);

        $response = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['selected_id' => $patient->patient_id]));

        $response->assertStatus(200);

        // Demographic dropdowns rendered for patient edit
        $response->assertSee('custom-dropdown-container', false);
        $response->assertSee('Patient Demographics (Permanent)', false);
        $response->assertSee('name="sex"', false);
        $response->assertSee('name="blood_type"', false);
        $response->assertSee('name="classification"', false);
    }

    public function test_date_of_birth_picker_renders_custom_month_and_year_dropdowns()
    {
        $response = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['new_patient' => '1']));

        $response->assertStatus(200);

        // Verify custom month and year popover controls are present
        $response->assertSee('showMonthPicker', false);
        $response->assertSee('showYearPicker', false);
        $response->assertSee('yearSearch', false);

        // Verify native unstyled selects are no longer used for Month and Year
        $response->assertDontSee('<select @change="setMonth', false);
        $response->assertDontSee('<select @change="setYear', false);
    }

    public function test_barangay_dropdown_renders_title_case_regular_weight_and_styled_options()
    {
        $response = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['new_patient' => '1']));

        $response->assertStatus(200);

        // Verify Title Case placeholder is rendered
        $response->assertSee('Select Barangay...', false);

        // Verify native uppercase bold select is removed
        $response->assertDontSee('<select :required="mode === \'edit\'" x-model="barangay"', false);
        $response->assertDontSee('<select :required="addressEditing" x-model="barangay"', false);

        // Verify styled barangay options with sublabel and search, without right-side Barangay badge tag
        $response->assertSee('Silang, Cavite', false);
        $response->assertSee('Search barangay...', false);
        $response->assertDontSee('"badge":"Barangay"', false);

        // Verify regular weight and no uppercase class on the trigger
        $response->assertSee('font-normal', false);
        $response->assertDontSee('font-semibold uppercase transition-colors" :disabled="loading">', false);
    }
}

