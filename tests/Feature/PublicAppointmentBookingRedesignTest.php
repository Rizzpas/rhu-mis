<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicAppointmentBookingRedesignTest extends TestCase
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
        DB::rollBack();
        parent::tearDown();
    }

    public function test_appointment_booking_page_renders_successfully()
    {
        $response = $this->get(route('appointment.create'));

        $response->assertStatus(200);
        $response->assertSee('Book Consultation');
        // Step 2 has the updated section titles matching Register New Patient
        $response->assertSee('Patient Demographics (Permanent Record)');
        $response->assertSee('Contact &amp; Residence Address', false);
        $response->assertSee('Social &amp; Health Background', false);
        $response->assertSee('Family &amp; Guardian Information', false);
        $response->assertSee('Chief Complaint', false);
        // Mother as guardian toggle
        $response->assertSee("Mother is also the patient's guardian", false);
        // Step 3 queue policy banner
        $response->assertSee('First-Come, First-Served Queue Policy');
        // Must NOT have arrival time selection
        $response->assertDontSee('Select Preferred Arrival Time');
    }

    public function test_booking_submission_succeeds_without_preferred_time()
    {
        // Session requires OTP verification for the email
        $email = 'patient.booking.' . uniqid() . '@example.com';
        session(['otp_verified_' . $email => true]);

        $postData = [
            'type' => 'pedia',
            'first_name' => 'JUAN',
            'middle_name' => 'SANTOS',
            'last_name' => 'DELA CRUZ',
            'suffix' => 'JR',
            'sex' => 'Male',
            'dob' => Carbon::now()->subYears(5)->format('Y-m-d'),
            'civil_status' => 'Single',
            'blood_type' => 'O+',
            'email' => $email,
            'contact_number' => '09123456789',
            'address' => '123 Rizal St, Biga 1, Silang, Cavite',
            'barangay' => 'Biga 1',
            'house_no' => '123',
            'street' => 'Rizal St',
            'building' => '',
            'city_province' => 'Silang, Cavite',
            'philhealth_number' => '12-345678901-2',
            'education' => 'Elementary',
            'religion' => 'Roman Catholic',
            'occupation' => 'None',
            'mother_first_name' => 'MARIA',
            'mother_middle_name' => 'LOPEZ',
            'mother_last_name' => 'DELA CRUZ',
            'mother_suffix' => '',
            'mothers_maiden_name' => 'MARIA LOPEZ DELA CRUZ',
            'guardian_first_name' => 'MARIA',
            'guardian_middle_name' => 'LOPEZ',
            'guardian_last_name' => 'DELA CRUZ',
            'guardian_suffix' => '',
            'guardian_relation' => 'Mother',
            'guardian_contact' => '09123456789',
            'guardian_email' => $email,
            'preferred_date' => Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d'),
            // preferred_time is intentionally omitted / null
            'complaint' => 'Fever, Cough',
            'symptoms' => ['Fever', 'Cough'],
            'other_symptom' => '',
            'data_privacy_agreed' => '1',
            'facility_id' => 1,
        ];

        $response = $this->post(route('appointment.store'), $postData);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('appointments', [
            'first_name' => 'JUAN',
            'last_name' => 'DELA CRUZ',
            'email' => $email,
            'preferred_time' => null,
        ]);
    }

    public function test_appointment_dashboard_reschedule_modal_renders_queue_policy_and_no_arrival_window_picker()
    {
        $appointment = Appointment::create([
            'reference_number' => 'APT-TEST-' . strtoupper(uniqid()),
            'first_name' => 'JUAN',
            'last_name' => 'DELA CRUZ',
            'sex' => 'Male',
            'dob' => '2000-01-01',
            'civil_status' => 'Single',
            'blood_type' => 'O+',
            'email' => 'juan.test@example.com',
            'contact_number' => '09123456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Biga 1',
            'data_privacy_agreed' => true,
            'type' => 'adult',
            'preferred_date' => Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d'),
            'status' => 'approved',
            'complaint' => 'Headache',
            'facility_id' => 1,
        ]);

        $response = $this->withSession(['manage_appointment_id' => $appointment->id])
            ->get(route('appointment.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Reschedule Appointment');
        $response->assertSee('First-Come, First-Served Queue Policy');
        $response->assertSee('RHU Operating Hours:');
        $response->assertSee('8:00 AM – 5:00 PM');
        $response->assertSee('8:00 AM – 11:00 AM');
        $response->assertDontSee('Select your arrival window:');
        $response->assertDontSee('Step 2 • Arrival Window');
    }

    public function test_appointment_reschedule_succeeds_without_new_time()
    {
        $appointment = Appointment::create([
            'reference_number' => 'APT-TEST-' . strtoupper(uniqid()),
            'first_name' => 'JUAN',
            'last_name' => 'DELA CRUZ',
            'sex' => 'Male',
            'dob' => '2000-01-01',
            'civil_status' => 'Single',
            'blood_type' => 'O+',
            'email' => 'juan.test2@example.com',
            'contact_number' => '09123456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Biga 1',
            'data_privacy_agreed' => true,
            'type' => 'adult',
            'preferred_date' => Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d'),
            'status' => 'approved',
            'complaint' => 'Headache',
            'facility_id' => 1,
        ]);

        $nextDate = Carbon::now()->next(Carbon::THURSDAY)->format('Y-m-d');

        $response = $this->withSession(['manage_appointment_id' => $appointment->id])
            ->post(route('appointment.reschedule'), [
                'new_date' => $nextDate,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $appointment->refresh();
        $this->assertEquals('rescheduled', $appointment->status);
        $this->assertEquals($nextDate, $appointment->preferred_date->format('Y-m-d'));
        $this->assertNull($appointment->preferred_time);
    }

    public function test_booking_fails_for_pedia_consultation_if_patient_younger_than_6_months()
    {
        $email = 'baby.under6mo.' . uniqid() . '@example.com';
        session(['otp_verified_' . $email => true]);

        $postData = [
            'type' => 'pedia',
            'first_name' => 'BABY',
            'middle_name' => 'SANTOS',
            'last_name' => 'DELA CRUZ',
            'suffix' => '',
            'sex' => 'Male',
            'dob' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'civil_status' => 'Single',
            'blood_type' => 'O+',
            'email' => $email,
            'contact_number' => '09123456789',
            'address' => '123 Rizal St, Biga 1, Silang, Cavite',
            'barangay' => 'Biga 1',
            'house_no' => '123',
            'street' => 'Rizal St',
            'building' => '',
            'city_province' => 'Silang, Cavite',
            'philhealth_number' => '12-345678901-2',
            'education' => 'None',
            'religion' => 'Roman Catholic',
            'occupation' => 'None',
            'mother_first_name' => 'MARIA',
            'mother_middle_name' => 'LOPEZ',
            'mother_last_name' => 'DELA CRUZ',
            'mother_suffix' => '',
            'mothers_maiden_name' => 'MARIA LOPEZ DELA CRUZ',
            'guardian_first_name' => 'MARIA',
            'guardian_middle_name' => 'LOPEZ',
            'guardian_last_name' => 'DELA CRUZ',
            'guardian_suffix' => '',
            'guardian_relation' => 'Mother',
            'guardian_contact' => '09123456789',
            'guardian_email' => $email,
            'preferred_date' => Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d'),
            'complaint' => 'Fever',
            'symptoms' => ['Fever'],
            'other_symptom' => '',
            'data_privacy_agreed' => '1',
            'facility_id' => 1,
        ];

        $response = $this->post(route('appointment.store'), $postData);

        $response->assertSessionHasErrors(['dob']);
        $this->assertEquals(
            'The Pedia Consultation service is only available for patients aged 6 months and above. Patients younger than 6 months require specialized neonatal or infant healthcare.',
            session('errors')->first('dob')
        );
    }

    public function test_booking_succeeds_for_pedia_consultation_if_patient_at_least_6_months_old()
    {
        $email = 'baby.over6mo.' . uniqid() . '@example.com';
        session(['otp_verified_' . $email => true]);

        $postData = [
            'type' => 'pedia',
            'first_name' => 'BABY',
            'middle_name' => 'SANTOS',
            'last_name' => 'DELA CRUZ',
            'suffix' => '',
            'sex' => 'Male',
            'dob' => Carbon::now()->subMonths(7)->format('Y-m-d'),
            'civil_status' => 'Single',
            'blood_type' => 'O+',
            'email' => $email,
            'contact_number' => '09123456789',
            'address' => '123 Rizal St, Biga 1, Silang, Cavite',
            'barangay' => 'Biga 1',
            'house_no' => '123',
            'street' => 'Rizal St',
            'building' => '',
            'city_province' => 'Silang, Cavite',
            'philhealth_number' => '12-345678901-2',
            'education' => 'None',
            'religion' => 'Roman Catholic',
            'occupation' => 'None',
            'mother_first_name' => 'MARIA',
            'mother_middle_name' => 'LOPEZ',
            'mother_last_name' => 'DELA CRUZ',
            'mother_suffix' => '',
            'mothers_maiden_name' => 'MARIA LOPEZ DELA CRUZ',
            'guardian_first_name' => 'MARIA',
            'guardian_middle_name' => 'LOPEZ',
            'guardian_last_name' => 'DELA CRUZ',
            'guardian_suffix' => '',
            'guardian_relation' => 'Mother',
            'guardian_contact' => '09123456789',
            'guardian_email' => $email,
            'preferred_date' => Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d'),
            'complaint' => 'Fever',
            'symptoms' => ['Fever'],
            'other_symptom' => '',
            'data_privacy_agreed' => '1',
            'facility_id' => 1,
        ];

        $response = $this->post(route('appointment.store'), $postData);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('appointments', [
            'first_name' => 'BABY',
            'last_name' => 'DELA CRUZ',
            'email' => $email,
        ]);
    }

    public function test_reschedule_fails_for_pedia_consultation_if_patient_younger_than_6_months()
    {
        $appointment = Appointment::create([
            'reference_number' => 'APT-TEST-' . strtoupper(uniqid()),
            'first_name' => 'BABY',
            'last_name' => 'DELA CRUZ',
            'sex' => 'Male',
            'dob' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            'civil_status' => 'Single',
            'blood_type' => 'O+',
            'email' => 'baby.reschedule@example.com',
            'contact_number' => '09123456789',
            'address' => 'Silang, Cavite',
            'barangay' => 'Biga 1',
            'data_privacy_agreed' => true,
            'type' => 'pedia',
            'preferred_date' => Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d'),
            'status' => 'approved',
            'complaint' => 'Fever',
            'facility_id' => 1,
        ]);

        $nextDate = Carbon::now()->next(Carbon::THURSDAY)->format('Y-m-d');

        $response = $this->withSession(['manage_appointment_id' => $appointment->id])
            ->post(route('appointment.reschedule'), [
                'new_date' => $nextDate,
            ]);

        $response->assertSessionHasErrors(['new_date']);
        $this->assertEquals(
            'The Pedia Consultation service is only available for patients aged 6 months and above. Patients younger than 6 months require specialized neonatal or infant healthcare.',
            session('errors')->first('new_date')
        );
    }
}
