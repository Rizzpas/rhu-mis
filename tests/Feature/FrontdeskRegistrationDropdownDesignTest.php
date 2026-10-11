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
        $response->assertSee('Highest Education Attained', false);
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
        $response->assertSee('Highest Education Attained', false);
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

    public function test_new_patient_guardian_information_renders_mother_toggle_button()
    {
        $response = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['new_patient' => '1']));

        $response->assertStatus(200);

        // Verify Mother as Guardian toggle card elements
        $response->assertSee("Mother is also the patient's guardian", false);
        $response->assertSee("motherIsGuardian", false);
        $response->assertSee("toggleMotherAsGuardian()", false);
        $response->assertSee("Linked", false);
        $response->assertSee("Auto-filled and synced with Mother's Name", false);
        $response->assertSee("Click to auto-fill guardian name using mother's information from above", false);
    }

    public function test_edit_patient_guardian_information_renders_mother_toggle_button()
    {
        $patient = Patient::create([
            'patient_id' => 'PAT-' . uniqid(),
            'first_name' => 'Maria',
            'middle_name' => 'Clara',
            'last_name' => 'Santos',
            'suffix' => null,
            'sex' => 'Female',
            'civil_status' => 'Single',
            'blood_type' => 'A+',
            'classification' => 'Pediatric',
            'dob' => now()->subYears(5)->format('Y-m-d'),
            'contact_number' => null,
            'address' => 'San Miguel II, Silang, Cavite',
            'education' => 'Elementary Student',
            'occupation' => 'Student',
            'religion' => 'Roman Catholic',
            'philhealth_number' => null,
            'mothers_maiden_name' => 'ANASTACIA EULALIO BANTIGUE',
            'guardian_first_name' => 'ANASTACIA',
            'guardian_middle_name' => 'EULALIO',
            'guardian_last_name' => 'BANTIGUE',
            'guardian_suffix' => null,
            'guardian_relation' => 'Mother',
            'guardian_contact' => '09123456789',
            'guardian_philhealth' => '65-465465465-4',
        ]);

        $response = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['selected_id' => $patient->patient_id]));

        $response->assertStatus(200);

        // Verify Mother as Guardian toggle card is rendered on edit form
        $response->assertSee("Mother is also the patient's guardian", false);
        $response->assertSee("motherIsGuardian", false);
        $response->assertSee("toggleMotherAsGuardian()", false);
        $response->assertSee("Linked", false);
    }

    public function test_guardian_relationship_renders_styled_custom_dropdown_in_new_and_edit_forms()
    {
        $patient = Patient::create([
            'patient_id' => 'PAT-' . uniqid(),
            'first_name' => 'Carlo',
            'last_name' => 'Santos',
            'sex' => 'Male',
            'civil_status' => 'Single',
            'classification' => 'Pediatric',
            'dob' => now()->subYears(7)->format('Y-m-d'),
            'address' => 'Biga 1, Silang, Cavite',
            'guardian_first_name' => 'MARIA',
            'guardian_last_name' => 'SANTOS',
            'guardian_relation' => 'Mother',
            'guardian_contact' => '09123456789',
        ]);

        // 1. Verify New Patient registration form
        $newResponse = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['new_patient' => '1']));

        $newResponse->assertStatus(200);
        $newResponse->assertDontSee('<select x-model="selectedGuardianRelation"', false);
        $newResponse->assertSee('Biological or adoptive mother', false);
        $newResponse->assertSee('Biological or adoptive father', false);
        $newResponse->assertSee('Grandmother or grandfather', false);
        $newResponse->assertSee('Brother or sister of legal age', false);
        $newResponse->assertSee('Aunt, uncle, legal guardian, caregiver', false);
        $newResponse->assertSee('name="guardian_relation"', false);

        // 2. Verify Edit Patient demographics form
        $editResponse = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['selected_id' => $patient->patient_id]));

        $editResponse->assertStatus(200);
        $editResponse->assertDontSee('<select x-model="selectedGuardianRelation"', false);
        $editResponse->assertSee('Biological or adoptive mother', false);
        $editResponse->assertSee('Biological or adoptive father', false);
        $editResponse->assertSee('name="guardian_relation"', false);
    }

    public function test_new_patient_registration_renders_error_banner_when_session_errors_present()
    {
        $response = $this->actingAs($this->frontdesk)
            ->withSession([
                'error' => 'Pediatric classification is restricted to patients 12 years old and below. Please choose another classification.',
                'errors' => new \Illuminate\Support\ViewErrorBag(),
            ])
            ->get(route('frontdesk.registration.index', ['new_patient' => '1']));

        $response->assertStatus(200);
        $response->assertSee('Registration Could Not Be Completed', false);
        $response->assertSee('Pediatric classification is restricted to patients 12 years old and below.', false);
    }

    public function test_new_patient_registration_renders_age_mismatch_helpers_and_corrected_sublabels()
    {
        $response = $this->actingAs($this->frontdesk)
            ->get(route('frontdesk.registration.index', ['new_patient' => '1']));

        $response->assertStatus(200);

        // Verify corrected Pediatric sublabel (12 years, not 17)
        $response->assertSee('Ages 0 to 12 years old with Guardian', false);
        $response->assertDontSee('Ages 0 to 17 years old with Guardian', false);

        // Verify Alpine mismatch watcher & UI elements
        $response->assertSee('ageMismatchWarning', false);
        $response->assertSee('syncClassificationWithDob', false);
        $response->assertSee('Classification Mismatch Detected', false);
        $response->assertSee('Auto-Fix Classification to Match Age', false);
    }

    public function test_submitting_adult_patient_with_pediatric_classification_is_rejected_by_controller()
    {
        $payload = [
            'first_name' => 'AIME',
            'middle_name' => 'BANTIGUE',
            'last_name' => 'RIPAS',
            'sex' => 'Female',
            'civil_status' => 'Married',
            'blood_type' => 'A+',
            'classification' => 'Pediatric',
            'dob' => '1972-03-22',
            'education' => 'Secondary Education (High School)',
            'religion' => 'Roman Catholic',
            'address' => 'BLOCK 61, ZONE 3, SITE 3, Pulong Bunga, Silang, Cavite',
            'mothers_maiden_name' => 'ANASTACIA EULALIO BANTIGUE',
            'philhealth_number' => '12-345678901-2',
            'guardian_first_name' => 'ANASTACIA',
            'guardian_last_name' => 'BANTIGUE',
            'guardian_relation' => 'Mother',
            'guardian_contact' => '09123456789',
        ];

        $response = $this->actingAs($this->frontdesk)
            ->from(route('frontdesk.registration.index', ['new_patient' => 1]))
            ->post(route('frontdesk.patients.store'), $payload);

        $response->assertRedirect(route('frontdesk.registration.index', ['new_patient' => 1]));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Pediatric classification is restricted to patients 12 years old and below', session('error'));
    }

    public function test_submitting_patient_from_screenshot_as_regular_adult_succeeds()
    {
        Patient::where('first_name', 'AIME')->where('last_name', 'RIPAS')->forceDelete();

        $payload = [
            'first_name' => 'AIME',
            'middle_name' => 'BANTIGUE',
            'last_name' => 'RIPAS',
            'suffix' => null,
            'sex' => 'Female',
            'civil_status' => 'Married',
            'blood_type' => 'A+',
            'classification' => 'Regular Adult',
            'dob' => '1972-03-22',
            'contact_number' => '09123456789',
            'email' => 'aimeripas@gmail.com',
            'education' => 'Secondary Education (High School)',
            'religion' => 'Roman Catholic',
            'house_no' => 'BLOCK 61',
            'street' => 'ZONE 3',
            'building' => 'SITE 3',
            'barangay' => 'Pulong Bunga',
            'city_province' => 'Silang, Cavite',
            'address' => 'BLOCK 61, ZONE 3, SITE 3, Pulong Bunga, Silang, Cavite',
            'philhealth_number' => '12-345678901-2',
            'mothers_maiden_name' => 'ANASTACIA EULALIO BANTIGUE',
        ];

        $response = $this->actingAs($this->frontdesk)
            ->from(route('frontdesk.registration.index', ['new_patient' => 1]))
            ->post(route('frontdesk.patients.store'), $payload);

        $response->assertSessionHas('success', 'Patient registered successfully.');
        $response->assertRedirect();

        $this->assertDatabaseHas('patients', [
            'first_name' => 'AIME',
            'last_name' => 'RIPAS',
            'classification' => 'Regular Adult',
        ]);
    }
}


