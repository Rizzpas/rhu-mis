<?php

namespace Database\Seeders;

use App\Models\AncillaryRequest;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\InventoryLog;
use App\Models\MedicalCase;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Patient;
use App\Models\PractitionerSchedule;
use App\Models\PreTriage;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductionLookSeeder extends Seeder
{
    // ─── Configurable counts ────────────────────────────────────────
    private const STAFF_COUNT          = 40;
    private const PATIENT_COUNT        = 120;
    private const APPOINTMENT_COUNT    = 300;
    private const HISTORY_MONTHS       = 6;
    private const FUTURE_DAYS          = 14;

    // ─── Reference data ─────────────────────────────────────────────
    private const ROLES = [
        'super_admin'      => 1,
        'admin'            => 2,
        'regular_doctor'   => 8,
        'pedia_doctor'     => 3,
        'clinical_nurse'   => 6,
        'vitals_nurse'     => 6,
        'information_desk' => 5,
        'laboratory'       => 4,
        'radiology'        => 2,
        'pharmacy'         => 3,
    ];

    /**
     * Role → email prefix mapping.
     */
    private const ROLE_EMAIL_PREFIX = [
        'super_admin'      => 'superadmin',
        'admin'            => 'admin',
        'regular_doctor'   => 'doctor',
        'pedia_doctor'     => 'pediadoctor',
        'clinical_nurse'   => 'clinicalnurse',
        'vitals_nurse'     => 'vitalsnurse',
        'information_desk' => 'infodesk',
        'laboratory'       => 'lab',
        'radiology'        => 'radiology',
        'pharmacy'         => 'pharmacy',
    ];

    private const FILIPINO_FIRST_NAMES_M = [
        'Juan', 'Jose', 'Andres', 'Marco', 'Rafael', 'Miguel', 'Gabriel', 'Carlos',
        'Antonio', 'Francisco', 'Ricardo', 'Eduardo', 'Fernando', 'Lorenzo', 'Roberto',
        'Enrique', 'Vicente', 'Emilio', 'Santiago', 'Ramon', 'Joaquin', 'Arturo',
        'Ernesto', 'Dominic', 'Benedict', 'Jerome', 'Patrick', 'Christian', 'Angelo',
        'Isaiah', 'Nathan', 'Elijah', 'Adrian', 'Jayden', 'Ethan', 'Liam',
    ];

    private const FILIPINO_FIRST_NAMES_F = [
        'Maria', 'Ana', 'Rosa', 'Carmen', 'Luisa', 'Elena', 'Teresa', 'Isabella',
        'Sofia', 'Gabriela', 'Patricia', 'Victoria', 'Angelica', 'Rosario', 'Catalina',
        'Margarita', 'Concepcion', 'Dolores', 'Esperanza', 'Josefina', 'Remedios',
        'Beatriz', 'Pilar', 'Corazon', 'Estrella', 'Faith', 'Grace', 'Hannah',
        'Julia', 'Kristine', 'Liza', 'Nicole', 'Olivia', 'Samantha', 'Zia',
    ];

    private const FILIPINO_LAST_NAMES = [
        'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres',
        'Villanueva', 'Ramos', 'Aquino', 'Castro', 'Flores', 'Gonzales', 'Hernandez',
        'Lopez', 'Martinez', 'Rivera', 'Rodriguez', 'Pascual', 'Dela Cruz', 'Del Rosario',
        'Dimaculangan', 'Espiritu', 'Fajardo', 'Gutierrez', 'Ilagan', 'Javier',
        'Lacson', 'Magno', 'Navarro', 'Pardo', 'Quijano', 'Salvador', 'Tolentino',
        'Umali', 'Valdez', 'Yuson', 'Zamora', 'Aguilar', 'Corpuz', 'Dizon',
    ];

    private const MIDDLE_NAMES = [
        'Aguilar', 'Bantigue', 'Crisostomo', 'Dizon', 'Evangelista', 'Fernandez',
        'Galang', 'Hidalgo', 'Ignacio', 'Jimenez', 'Katigbak', 'Legaspi',
        'Manalo', 'Natividad', 'Ortega', 'Palacios', 'Quevedo', 'Resurreccion',
    ];

    private const SILANG_BARANGAYS = [
        'Biga I', 'Biga II', 'Kalubkob', 'Bulihan', 'Lucsuhin', 'Balite I', 'Balite II',
        'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balubad', 'Banaba', 'Biluso',
        'Bucal', 'Buho', 'Carmen', 'Hoyo', 'Hukay', 'Iba', 'Inchican',
        'Ipil I', 'Ipil II', 'Kaong', 'Lalaan I', 'Lalaan II', 'Litlit', 'Lumil',
        'Maguyam', 'Malabag', 'Mataas Na Burol', 'Munting Ilog', 'Narra I', 'Narra II',
        'Pasong Langka', 'Pooc I', 'Pulong Bunga', 'Puting Kahoy', 'Sabutan',
        'San Miguel I', 'San Vicente I', 'Tartaria', 'Tibig', 'Toledo', 'Ulat',
    ];

    private const DIAGNOSES = [
        'Acute Upper Respiratory Tract Infection',
        'Urinary Tract Infection',
        'Hypertension, Essential',
        'Type 2 Diabetes Mellitus',
        'Acute Gastroenteritis',
        'Community-Acquired Pneumonia',
        'Bronchial Asthma, Mild Persistent',
        'Allergic Rhinitis',
        'Skin Infection / Cellulitis',
        'Dyslipidemia',
        'Acute Bronchitis',
        'Tension-Type Headache',
        'Osteoarthritis',
        'Iron Deficiency Anemia',
        'Conjunctivitis',
        'Otitis Media',
        'Dengue Fever',
        'Chickenpox (Varicella)',
        'Low Back Pain (Musculoskeletal)',
        'Acid Peptic Disease / GERD',
    ];

    private const CHIEF_COMPLAINTS = [
        'Fever and body malaise for 2 days',
        'Cough and colds for 3 days',
        'Headache and dizziness',
        'Abdominal pain and vomiting',
        'Difficulty of breathing',
        'Painful urination',
        'High blood pressure follow-up',
        'Blood sugar monitoring',
        'Skin rash and itching',
        'Sore throat',
        'Chest pain on exertion',
        'Joint pain and swelling',
        'General weakness',
        'Loose bowel movement for 1 day',
        'Ear pain',
        'Eye redness and discharge',
        'Follow-up consultation',
        'Medical certificate request',
        'Prenatal check-up',
        'Well-child visit',
    ];

    private const SYMPTOMS = [
        'fever,cough,body malaise',
        'headache,dizziness,nausea',
        'abdominal pain,vomiting,diarrhea',
        'cough,phlegm,difficulty breathing',
        'painful urination,frequency,urgency',
        'skin rash,itching,redness',
        'sore throat,difficulty swallowing',
        'chest pain,palpitations',
        'joint pain,swelling,stiffness',
        'loose bowel movement,cramping',
    ];

    private const LAB_TESTS = [
        'Laboratory'  => ['Complete Blood Count (CBC)', 'Urinalysis', 'Fasting Blood Sugar', 'Lipid Profile', 'Liver Function Test', 'Kidney Function Test', 'Thyroid Function Test', 'HbA1c', 'Blood Typing', 'Dengue NS1 Antigen'],
        'Radiology'   => ['Chest X-Ray (PA)', 'Chest X-Ray (AP-Lateral)', 'Abdominal X-Ray', 'Knee X-Ray', 'Lumbar X-Ray', 'Skull X-Ray'],
    ];

    private const CANCELLATION_REASONS = [
        'Scheduling conflict', 'Feeling better', 'Transportation issues',
        'Family emergency', 'Work commitment', 'Weather conditions',
    ];

    private const CIVIL_STATUSES = ['Single', 'Married', 'Widowed', 'Separated'];
    private const BLOOD_TYPES    = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    private const RELIGIONS      = ['Roman Catholic', 'Iglesia Ni Cristo', 'Baptist', 'Born Again Christian', 'Muslim', 'Methodist', 'Seventh Day Adventist'];
    private const EDUCATIONS     = ['Elementary', 'High School', 'College Undergraduate', 'College Graduate', 'Postgraduate'];
    private const OCCUPATIONS    = ['Student', 'Farmer', 'Vendor', 'Teacher', 'Driver', 'Housewife', 'OFW', 'Construction Worker', 'Government Employee', 'Self-Employed', 'Retired'];

    private const SEVERITY_LEVELS = ['mild', 'moderate', 'severe'];

    // Days of the week abbreviation
    private const WEEKDAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    // ─── State ──────────────────────────────────────────────────────
    private array $counts = [];
    private array $staffByRole = [];
    private array $doctorIds = [];
    private array $nurseIds = [];
    private array $vitalsNurseIds = [];
    private array $infoDeskIds = [];
    private array $labIds = [];
    private array $radIds = [];
    private array $pharmacyIds = [];
    private array $patientRecords = [];
    private array $medicineIds = [];

    /**
     * Run the seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 ProductionLookSeeder — Starting...');
        $this->command->newLine();

        // ── Phase 0: Safety check ──────────────────────────────────
        if (app()->environment('production')) {
            $this->command->error('⛔ Refusing to run in production! Set APP_ENV=local first.');
            return;
        }

        // ── Phase 1: Purge existing data ───────────────────────────
        $this->purgeData();

        // ── Phase 2: Staff / Users ─────────────────────────────────
        $this->seedStaff();

        // ── Phase 3: Patients ──────────────────────────────────────
        $this->seedPatients();

        // ── Phase 4: Medicine Batches (inventory) ──────────────────
        $this->seedMedicineBatches();

        // ── Phase 5: Appointments + Consultations + everything ─────
        $this->seedAppointmentsAndConsultations();

        // ── Phase 6: Audit Logs ────────────────────────────────────
        $this->seedAuditLogs();

        // ── Phase 7: Notifications ─────────────────────────────────
        $this->seedNotifications();

        // ── Report ─────────────────────────────────────────────────
        $this->printReport();
    }

    // ================================================================
    //  Phase 1: Purge
    // ================================================================
    private function purgeData(): void
    {
        $this->command->warn('🗑️  Purging existing seed data (preserving announcements, facility_units, site_settings, medicines)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'notifications', 'audit_logs', 'inventory_logs', 'prescription_items', 'prescriptions',
            'ancillary_requests', 'medical_cases', 'queues', 'consultations', 'pre_triages',
            'appointments', 'practitioner_schedules', 'messages', 'conversation_user', 'conversations',
            'medicine_batches', 'password_reset_tokens',
        ];

        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            if ($count > 0) {
                $this->counts["deleted_{$table}"] = $count;
            }
            DB::table($table)->truncate();
        }

        // Soft-delete aware: force-delete patients and users (except keep medicines)
        $patientCount = DB::table('patients')->count();
        if ($patientCount > 0) {
            $this->counts['deleted_patients'] = $patientCount;
        }
        DB::table('patients')->truncate();

        $userCount = DB::table('users')->count();
        if ($userCount > 0) {
            $this->counts['deleted_users'] = $userCount;
        }
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('   ✓ Tables purged.');
    }

    // ================================================================
    //  Phase 2: Staff / Users
    // ================================================================
    private function seedStaff(): void
    {
        $this->command->info('👥 Seeding staff users...');

        $avatarDir = public_path('uploads/staff');
        File::ensureDirectoryExists($avatarDir);

        $staffCreated = 0;
        $now = Carbon::now();

        foreach (self::ROLES as $role => $count) {
            for ($i = 0; $i < $count; $i++) {
                $sex = $this->pickSexForRole($role, $i);
                $firstName = $sex === 'M'
                    ? self::FILIPINO_FIRST_NAMES_M[array_rand(self::FILIPINO_FIRST_NAMES_M)]
                    : self::FILIPINO_FIRST_NAMES_F[array_rand(self::FILIPINO_FIRST_NAMES_F)];
                $middleName = self::MIDDLE_NAMES[array_rand(self::MIDDLE_NAMES)];
                $lastName = self::FILIPINO_LAST_NAMES[array_rand(self::FILIPINO_LAST_NAMES)];

                $fullName = "$firstName $middleName $lastName";

                // Role-based email: superadmin@gmail.com, doctor@gmail.com, doctor1@gmail.com, etc.
                $emailPrefix = self::ROLE_EMAIL_PREFIX[$role] ?? strtolower(str_replace('_', '', $role));
                $email = $i === 0
                    ? "{$emailPrefix}@gmail.com"
                    : "{$emailPrefix}{$i}@gmail.com";

                // Create month spread: staff hired over last 6-24 months
                $monthsAgo = rand(2, 24);
                $createdAt = $now->copy()->subMonths($monthsAgo)->addDays(rand(0, 28));
                $period = $createdAt->format('ym');

                // Generate a simple colored avatar placeholder (100x100 PNG)
                $avatarFilename = $this->generateAvatarPlaceholder($avatarDir, $firstName, $lastName, $sex);

                $staffId = User::generateStaffId($role, $period);

                // Pick schedule
                $status = $this->randomElement(['Present', 'Online', 'Offline'], [0.3, 0.3, 0.4]);

                $user = User::create([
                    'name'             => $fullName,
                    'email'            => $email,
                    'password'         => bcrypt('password'),
                    'role'             => $role,
                    'status'           => $status,
                    'staff_id'         => $staffId,
                    'avatar_path'      => "staff/{$avatarFilename}",
                    'last_activity_at' => $status !== 'Offline' ? $now->copy()->subMinutes(rand(1, 30)) : $now->copy()->subHours(rand(2, 48)),
                    'created_at'       => $createdAt,
                    'updated_at'       => $now,
                ]);

                // Schedules for clinical roles
                if (in_array($role, ['regular_doctor', 'pedia_doctor', 'clinical_nurse', 'vitals_nurse'])) {
                    $this->createSchedule($user);
                }

                // Track staff by role
                $this->staffByRole[$role][] = $user->id;
                $staffCreated++;
            }
        }

        $this->doctorIds      = array_merge($this->staffByRole['regular_doctor'] ?? [], $this->staffByRole['pedia_doctor'] ?? []);
        $this->nurseIds       = $this->staffByRole['clinical_nurse'] ?? [];
        $this->vitalsNurseIds = $this->staffByRole['vitals_nurse'] ?? [];
        $this->infoDeskIds    = $this->staffByRole['information_desk'] ?? [];
        $this->labIds         = $this->staffByRole['laboratory'] ?? [];
        $this->radIds         = $this->staffByRole['radiology'] ?? [];
        $this->pharmacyIds    = $this->staffByRole['pharmacy'] ?? [];

        $this->counts['created_users'] = $staffCreated;
        $this->counts['created_practitioner_schedules'] = PractitionerSchedule::count();
        $this->command->info("   ✓ Created {$staffCreated} staff users");
    }

    private function pickSexForRole(string $role, int $index): string
    {
        // Slight bias to make it realistic, but mixed
        if (in_array($role, ['clinical_nurse', 'vitals_nurse'])) {
            return $index % 4 === 0 ? 'M' : 'F'; // mostly female nurses
        }
        return rand(0, 1) ? 'M' : 'F';
    }

    private function createSchedule(User $user): void
    {
        // All staff: Mon-Fri, 8 AM to 5 PM
        foreach (self::WEEKDAYS as $day) {
            PractitionerSchedule::create([
                'user_id'    => $user->id,
                'day_of_week' => $day,
                'time_in'    => '08:00:00',
                'time_out'   => '17:00:00',
            ]);
        }
    }

    private function generateAvatarPlaceholder(string $dir, string $firstName, string $lastName, string $sex): string
    {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        $colors = $sex === 'M'
            ? ['3B82F6', '6366F1', '8B5CF6', '0EA5E9', '14B8A6', '10B981']
            : ['EC4899', 'F43F5E', 'F97316', 'EAB308', 'A855F7', 'E879F9'];
        $bgColor = $colors[array_rand($colors)];

        // Create a simple SVG avatar and save as file
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
             . '<rect width="200" height="200" fill="#' . $bgColor . '"/>'
             . '<text x="100" y="115" font-family="Arial,sans-serif" font-size="72" font-weight="bold" '
             . 'fill="white" text-anchor="middle">' . htmlspecialchars($initials) . '</text></svg>';

        $filename = 'seed_' . Str::random(20) . '.svg';
        File::put("{$dir}/{$filename}", $svg);

        return $filename;
    }

    // ================================================================
    //  Phase 3: Patients
    // ================================================================
    private function seedPatients(): void
    {
        $this->command->info('🏥 Seeding patients...');

        $now = Carbon::now();

        for ($i = 0; $i < self::PATIENT_COUNT; $i++) {
            $sex = rand(0, 1) ? 'Male' : 'Female';
            $firstName = $sex === 'Male'
                ? self::FILIPINO_FIRST_NAMES_M[array_rand(self::FILIPINO_FIRST_NAMES_M)]
                : self::FILIPINO_FIRST_NAMES_F[array_rand(self::FILIPINO_FIRST_NAMES_F)];
            $middleName = self::MIDDLE_NAMES[array_rand(self::MIDDLE_NAMES)];
            $lastName = self::FILIPINO_LAST_NAMES[array_rand(self::FILIPINO_LAST_NAMES)];

            // Age distribution: Pediatric 15%, Teen 10%, Adult 40%, Middle 20%, Senior 15%
            $ageGroup = $this->randomElement(
                ['pediatric', 'teen', 'adult', 'middle', 'senior'],
                [0.15, 0.10, 0.40, 0.20, 0.15]
            );
            $age = match ($ageGroup) {
                'pediatric' => rand(1, 12),
                'teen'      => rand(13, 17),
                'adult'     => rand(18, 39),
                'middle'    => rand(40, 59),
                'senior'    => rand(60, 85),
            };
            $dob = $now->copy()->subYears($age)->subDays(rand(0, 364));

            // Classification is auto-set by the Patient model boot, but we set a base
            $classification = match (true) {
                $age <= 12 => 'Pediatric',
                $age >= 60 => 'Senior Citizen',
                rand(1, 20) === 1 => 'PWD',
                default => 'Regular Adult',
            };

            $barangay = self::SILANG_BARANGAYS[array_rand(self::SILANG_BARANGAYS)];
            $houseNo = 'Blk ' . rand(1, 99);
            $street  = 'Lot ' . rand(1, 50);

            // Registration dates spread over history period
            $monthsAgo = rand(0, self::HISTORY_MONTHS);
            $createdAt = $now->copy()->subMonths($monthsAgo)->subDays(rand(0, 28));
            if ($createdAt->isAfter($now)) {
                $createdAt = $now->copy()->subDays(rand(1, 30));
            }

            $registeredBy = $this->randomFrom($this->infoDeskIds);

            // Guard: need guardian for pediatric
            $guardianFirst = null;
            $guardianLast  = null;
            $guardianRelation = null;
            $guardianContact = null;
            if ($age <= 12) {
                $guardianFirst = self::FILIPINO_FIRST_NAMES_F[array_rand(self::FILIPINO_FIRST_NAMES_F)];
                $guardianLast  = $lastName; // same surname
                $guardianRelation = $this->randomElement(['Mother', 'Father', 'Guardian']);
                $guardianContact = '09' . rand(100000000, 999999999);
            }

            $patient = Patient::create([
                'first_name'         => $firstName,
                'middle_name'        => $middleName,
                'last_name'          => $lastName,
                'sex'                => $sex,
                'dob'                => $dob->format('Y-m-d'),
                'civil_status'       => $age >= 18 ? self::CIVIL_STATUSES[array_rand(self::CIVIL_STATUSES)] : 'Single',
                'blood_type'         => self::BLOOD_TYPES[array_rand(self::BLOOD_TYPES)],
                'address'            => "{$houseNo}, {$street}, {$barangay}, Silang, Cavite",
                'house_no'           => $houseNo,
                'street'             => $street,
                'barangay'           => $barangay,
                'city_province'      => 'Silang, Cavite',
                'contact_number'     => '09' . rand(100000000, 999999999),
                'email'              => strtolower("{$firstName}.{$lastName}.p{$i}@example.com"),
                'religion'           => self::RELIGIONS[array_rand(self::RELIGIONS)],
                'education'          => self::EDUCATIONS[array_rand(self::EDUCATIONS)],
                'occupation'         => self::OCCUPATIONS[array_rand(self::OCCUPATIONS)],
                'classification'     => $classification,
                'mothers_maiden_name'=> self::FILIPINO_FIRST_NAMES_F[array_rand(self::FILIPINO_FIRST_NAMES_F)] . ' ' . self::FILIPINO_LAST_NAMES[array_rand(self::FILIPINO_LAST_NAMES)],
                'registered_by'      => $registeredBy,
                'guardian_first_name'=> $guardianFirst,
                'guardian_last_name' => $guardianLast,
                'guardian_relation'  => $guardianRelation,
                'guardian_contact'   => $guardianContact,
                'created_at'         => $createdAt,
                'updated_at'         => $now,
            ]);

            $this->patientRecords[] = $patient;
        }

        $this->counts['created_patients'] = self::PATIENT_COUNT;
        $this->command->info('   ✓ Created ' . self::PATIENT_COUNT . ' patients');
    }

    // ================================================================
    //  Phase 4: Medicine Batches
    // ================================================================
    private function seedMedicineBatches(): void
    {
        $this->command->info('💊 Seeding medicine batches...');

        $this->medicineIds = Medicine::pluck('id')->toArray();
        $batchCount = 0;
        $now = Carbon::now();

        foreach ($this->medicineIds as $medId) {
            // 2-4 batches per medicine
            $numBatches = rand(2, 4);
            for ($b = 0; $b < $numBatches; $b++) {
                $originalQty = rand(50, 500);
                $qty = rand(intval($originalQty * 0.2), $originalQty);
                $expirationMonths = rand(3, 24);
                $status = 'active';

                // 10% chance of depleted batch
                if (rand(1, 10) === 1) {
                    $qty = 0;
                    $status = 'depleted';
                }

                $createdAt = $now->copy()->subMonths(rand(1, 6));

                MedicineBatch::create([
                    'medicine_id'      => $medId,
                    'batch_number'     => 'BATCH-' . strtoupper(Str::random(3)) . '-' . str_pad($b + 1, 3, '0', STR_PAD_LEFT),
                    'expiration_date'  => $now->copy()->addMonths($expirationMonths)->format('Y-m-d'),
                    'quantity'         => $qty,
                    'original_quantity'=> $originalQty,
                    'status'           => $status,
                    'created_at'       => $createdAt,
                    'updated_at'       => $now,
                ]);
                $batchCount++;
            }
        }

        $this->counts['created_medicine_batches'] = $batchCount;
        $this->command->info("   ✓ Created {$batchCount} medicine batches");
    }

    // ================================================================
    //  Phase 5: Appointments + full pipeline
    // ================================================================
    private function seedAppointmentsAndConsultations(): void
    {
        $this->command->info('📅 Seeding appointments, consultations, and clinical data...');

        $now = CarbonImmutable::now();
        $historyStart = $now->subMonths(self::HISTORY_MONTHS)->startOfMonth();
        $futureEnd = $now->addDays(self::FUTURE_DAYS);

        // Distribute appointments with upward trend: later months get more
        $totalDays = $historyStart->diffInDays($futureEnd);

        $appointmentCount = 0;
        $consultationCount = 0;
        $preTriageCount = 0;
        $medicalCaseCount = 0;
        $prescriptionCount = 0;
        $prescriptionItemCount = 0;
        $ancillaryCount = 0;
        $inventoryLogCount = 0;
        $queueCount = 0;

        for ($i = 0; $i < self::APPOINTMENT_COUNT; $i++) {
            // Weighted date: use a power curve for upward trend
            // More appointments in recent months
            $fraction = pow(rand(0, 10000) / 10000, 0.6); // skew toward 1.0 = recent
            $dayOffset = intval($fraction * $totalDays);
            $date = $historyStart->addDays($dayOffset);

            // Skip weekends (government clinic)
            while ($date->isWeekend()) {
                $date = $date->addDay();
            }

            $isPast = $date->isBefore($now->startOfDay());
            $isToday = $date->isSameDay($now);
            $isFuture = $date->isAfter($now->startOfDay()) && !$isToday;

            // Pick a business hour (weighted: 8-11 AM busiest)
            $hour = $this->randomElement(
                [7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
                [0.03, 0.15, 0.20, 0.20, 0.15, 0.05, 0.08, 0.07, 0.05, 0.02]
            );
            $minute = rand(0, 59);
            $appointmentDateTime = $date->setTime($hour, $minute, 0);

            // Pick a patient
            $patient = $this->patientRecords[array_rand($this->patientRecords)];
            $isPediatric = $patient->dob && Carbon::parse($patient->dob)->age <= 12;
            $type = $isPediatric ? 'pedia' : 'adult';

            // Reference number
            $ref = 'APT-' . strtoupper(Str::random(10));

            // Determine status based on timeline
            if ($isPast) {
                $status = $this->randomElement(
                    ['done', 'done', 'done', 'done', 'cancelled', 'no_show'],
                    [0.45, 0.25, 0.10, 0.05, 0.08, 0.07]
                );
            } elseif ($isToday) {
                $status = $this->randomElement(
                    ['approved', 'arrived', 'triaged', 'registered', 'done'],
                    [0.20, 0.20, 0.20, 0.20, 0.20]
                );
            } else {
                $status = $this->randomElement(
                    ['approved', 'pending'],
                    [0.7, 0.3]
                );
            }

            $preferredTime = $this->randomElement(
                ['Morning (8AM-12PM)', 'Afternoon (1PM-5PM)'],
                [$hour < 12 ? 0.8 : 0.2, $hour >= 12 ? 0.8 : 0.2]
            );

            $complaint = self::CHIEF_COMPLAINTS[array_rand(self::CHIEF_COMPLAINTS)];
            $barangay = $patient->barangay ?: self::SILANG_BARANGAYS[array_rand(self::SILANG_BARANGAYS)];

            $cancellationReason = null;
            $cancelledBy = null;
            $cancelledAt = null;
            if ($status === 'cancelled') {
                $cancellationReason = self::CANCELLATION_REASONS[array_rand(self::CANCELLATION_REASONS)];
                $cancelledBy = $this->randomFrom($this->infoDeskIds);
                $cancelledAt = $appointmentDateTime->addHours(rand(1, 48));
            }

            $aptCreatedAt = $appointmentDateTime->subDays(rand(1, 14)); // booked in advance
            if ($aptCreatedAt->isAfter($now)) {
                $aptCreatedAt = $now->subDays(rand(1, 5));
            }

            $appointment = Appointment::create([
                'reference_number'  => $ref,
                'first_name'        => $patient->first_name,
                'middle_name'       => $patient->middle_name,
                'last_name'         => $patient->last_name,
                'suffix'            => $patient->suffix,
                'sex'               => $patient->sex,
                'dob'               => $patient->dob,
                'civil_status'      => $patient->civil_status,
                'blood_type'        => $patient->blood_type,
                'address'           => $patient->address,
                'house_no'          => $patient->house_no,
                'street'            => $patient->street,
                'barangay'          => $barangay,
                'city_province'     => 'Silang, Cavite',
                'email'             => $patient->email ?? strtolower($patient->first_name) . '.seed@example.com',
                'contact_number'    => $patient->contact_number,
                'classification'    => $patient->classification,
                'type'              => $type,
                'preferred_date'    => $appointmentDateTime,
                'preferred_time'    => $preferredTime,
                'complaint'         => $complaint,
                'data_privacy_agreed' => true,
                'status'            => $status,
                'cancellation_reason' => $cancellationReason,
                'cancelled_by'      => $cancelledBy,
                'cancelled_at'      => $cancelledAt,
                'reminder_sent_at'  => $isPast ? $appointmentDateTime->subDay() : null,
                'created_at'        => $aptCreatedAt,
                'updated_at'        => $appointmentDateTime,
            ]);
            $appointmentCount++;

            // ── Only create full clinical pipeline for completed/done appointments ──
            if (!in_array($status, ['done', 'arrived', 'triaged', 'registered'])) {
                continue;
            }

            // ── Pre-Triage ──
            $triageTime = $appointmentDateTime->addMinutes(rand(5, 30));
            $vitalsNurse = $this->randomFrom($this->vitalsNurseIds);

            $bp_sys = rand(90, 160);
            $bp_dia = rand(50, 100);
            $temp = round(rand(360, 390) / 10, 1);
            $weight = round(rand(300, 900) / 10, 1);
            $height = round(rand(100, 190), 0);
            $hr = rand(60, 110);
            $rr = rand(14, 24);
            $pr = rand(60, 110);
            $spo2 = rand(94, 100);

            $preTriage = PreTriage::create([
                'patient_name'    => "{$patient->first_name} {$patient->middle_name} {$patient->last_name}",
                'first_name'      => $patient->first_name,
                'last_name'       => $patient->last_name,
                'middle_name'     => $patient->middle_name,
                'dob'             => $patient->dob,
                'blood_pressure'  => "{$bp_sys}/{$bp_dia}",
                'temperature'     => $temp,
                'weight'          => $weight,
                'height'          => $height,
                'heart_rate'      => $hr,
                'respiratory_rate'=> $rr,
                'pulse_rate'      => $pr,
                'spo2'            => (string) $spo2,
                'oxygen_saturation' => $spo2,
                'chief_complaint' => $complaint,
                'symptoms'        => self::SYMPTOMS[array_rand(self::SYMPTOMS)],
                'past_medical_history' => 'No significant past medical history',
                'medicine_taken'  => 'None',
                'known_allergies' => $this->randomElement(['None', 'Penicillin', 'Aspirin', 'Seafood']),
                'classification'  => match (true) {
                    $isPediatric => 'Pediatric',
                    Carbon::parse($patient->dob)->age >= 60 => 'Senior',
                    default => 'Adult',
                },
                'recorded_by'     => $vitalsNurse,
                'patient_id'      => $patient->patient_id,
                'appointment_id'  => $appointment->id,
                'status'          => in_array($status, ['done', 'registered']) ? 'completed' : 'claimed',
                'is_emergency'    => rand(1, 20) === 1 ? 1 : 0,
                'encoding_duration_seconds' => rand(45, 300),
                'created_at'      => $triageTime,
                'updated_at'      => $triageTime->addMinutes(rand(2, 10)),
            ]);
            $preTriageCount++;

            // ── Consultation ──
            $doctorId = $isPediatric
                ? $this->randomFrom($this->staffByRole['pedia_doctor'] ?? $this->doctorIds)
                : $this->randomFrom($this->staffByRole['regular_doctor'] ?? $this->doctorIds);
            $clinicalNurse = $this->randomFrom($this->nurseIds);

            $consultStartTime = $triageTime->addMinutes(rand(10, 60));
            $consultDuration = rand(8, 45); // minutes
            $consultEndTime = $status === 'done' ? $consultStartTime->addMinutes($consultDuration) : null;

            $diagnosis = self::DIAGNOSES[array_rand(self::DIAGNOSES)];
            $severity = self::SEVERITY_LEVELS[array_rand(self::SEVERITY_LEVELS)];

            // Queue number
            $queuePrefix = $isPediatric ? 'PED-' : 'GEN-';
            $queueNum = $queuePrefix . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $isFollowupNeeded = rand(1, 5) === 1; // 20% need follow-up
            $followupDate = $isFollowupNeeded ? $appointmentDateTime->addDays(rand(7, 30))->format('Y-m-d') : null;

            $consultStatus = match ($status) {
                'done' => 'completed',
                'registered' => $this->randomElement(['queued', 'active']),
                'triaged' => 'queued',
                default => 'queued',
            };

            $consultation = Consultation::create([
                'patient_id'           => $patient->patient_id,
                'doctor_id'            => $doctorId,
                'nurse_id'             => $clinicalNurse,
                'pre_triage_id'        => $preTriage->id,
                'consultation_date'    => $appointmentDateTime->format('Y-m-d'),
                'queue_number'         => $queueNum,
                'status'               => $consultStatus,
                'severity'             => $severity,
                'diagnosis'            => $consultStatus === 'completed' ? $diagnosis : null,
                'medical_notes'        => $consultStatus === 'completed' ? "Patient presents with {$complaint}. Vitals stable. {$diagnosis} diagnosed." : null,
                'is_followup_needed'   => $isFollowupNeeded,
                'followup_date'        => $followupDate,
                'followup_doctor_id'   => $isFollowupNeeded ? $doctorId : null,
                'followup_completed_at'=> ($isFollowupNeeded && $isPast && rand(1, 3) <= 2) ? $appointmentDateTime->addDays(rand(7, 28)) : null,
                'blood_pressure'       => "{$bp_sys}/{$bp_dia}",
                'temperature'          => (string) $temp,
                'weight'               => (string) $weight,
                'height'               => (string) $height,
                'heart_rate'           => (string) $hr,
                'respiratory_rate'     => (string) $rr,
                'pulse_rate'           => (string) $pr,
                'spo2'                 => (string) $spo2,
                'consultation_start_time' => $consultStartTime,
                'consultation_end_time'   => $consultEndTime,
                'created_at'           => $consultStartTime,
                'updated_at'           => $consultEndTime ?? $consultStartTime,
            ]);
            $consultationCount++;

            // ── Queue ──
            $queueStatus = match ($consultStatus) {
                'completed' => 'Done',
                'active' => 'In Progress',
                default => 'Waiting',
            };
            Queue::create([
                'patient_id'   => $patient->patient_id,
                'queue_number' => $queueNum,
                'priority_type'=> in_array($patient->classification, ['Senior Citizen', 'PWD']) ? 'Priority' : 'Regular',
                'service_type' => 'Consultation',
                'status'       => $queueStatus,
                'called_at'    => $consultStatus !== 'queued' ? $consultStartTime : null,
                'created_at'   => $triageTime,
                'updated_at'   => $consultEndTime ?? $consultStartTime,
            ]);
            $queueCount++;

            // ── Medical Case (for completed) ──
            if ($consultStatus === 'completed') {
                $caseNumber = 'CASE-' . $appointmentDateTime->format('Ymd') . '-' . str_pad($consultation->id, 5, '0', STR_PAD_LEFT);
                MedicalCase::create([
                    'case_number'     => $caseNumber,
                    'patient_id'      => $patient->patient_id,
                    'consultation_id' => $consultation->id,
                    'pre_triage_id'   => $preTriage->id,
                    'diagnosis'       => $diagnosis,
                    'prescription'    => $consultation->medical_notes,
                    'vitals_snapshot'  => json_encode([
                        'blood_pressure' => "{$bp_sys}/{$bp_dia}",
                        'temperature'    => $temp,
                        'weight'         => $weight,
                        'height'         => $height,
                        'heart_rate'     => $hr,
                        'respiratory_rate' => $rr,
                        'pulse_rate'     => $pr,
                        'spo2'           => $spo2,
                    ]),
                    'closed_at'       => $consultEndTime,
                    'created_at'      => $consultEndTime,
                    'updated_at'      => $consultEndTime,
                ]);
                $medicalCaseCount++;
            }

            // ── Ancillary Requests (30% of completed consultations) ──
            if ($consultStatus === 'completed' && rand(1, 100) <= 30) {
                $labType = rand(0, 1) ? 'Laboratory' : 'Radiology';
                $tests = self::LAB_TESTS[$labType];
                $testName = $tests[array_rand($tests)];

                $labStaff = $labType === 'Laboratory'
                    ? $this->randomFrom($this->labIds)
                    : $this->randomFrom($this->radIds);

                $arStatus = $isPast
                    ? $this->randomElement(['Done', 'Done', 'Done', 'Cancelled'], [0.5, 0.2, 0.2, 0.1])
                    : 'Pending';

                $specimenAt = $arStatus !== 'Pending' ? $consultStartTime->addMinutes(rand(5, 20)) : null;
                $processingAt = in_array($arStatus, ['Done', 'In Progress']) ? ($specimenAt ? $specimenAt->addMinutes(rand(10, 30)) : null) : null;
                $completedAt = $arStatus === 'Done' ? ($processingAt ? $processingAt->addMinutes(rand(15, 60)) : null) : null;

                AncillaryRequest::create([
                    'consultation_id'       => $consultation->id,
                    'type'                  => $labType,
                    'test_name'             => $testName,
                    'status'                => $arStatus,
                    'specimen_collected_at'  => $specimenAt,
                    'specimen_collected_by'  => $specimenAt ? $labStaff : null,
                    'processing_started_at'  => $processingAt,
                    'completed_by'           => $completedAt ? $labStaff : null,
                    'completed_at'           => $completedAt,
                    'result_data'            => $completedAt ? json_encode(['result' => 'Normal', 'notes' => 'Within normal limits']) : null,
                    'cancelled_by'           => $arStatus === 'Cancelled' ? $doctorId : null,
                    'cancelled_at'           => $arStatus === 'Cancelled' ? $consultStartTime->addMinutes(rand(5, 30)) : null,
                    'cancellation_reason'    => $arStatus === 'Cancelled' ? 'No longer clinically indicated' : null,
                    'created_at'             => $consultStartTime,
                    'updated_at'             => $completedAt ?? $consultStartTime,
                ]);
                $ancillaryCount++;
            }

            // ── Prescriptions (60% of completed consultations) ──
            if ($consultStatus === 'completed' && rand(1, 100) <= 60) {
                $numItems = rand(1, 3);
                $prescriptionStatus = $isPast
                    ? $this->randomElement(['dispensed', 'dispensed', 'pending', 'cancelled'], [0.5, 0.2, 0.2, 0.1])
                    : 'pending';

                $pharmacist = $this->randomFrom($this->pharmacyIds);
                $dispensedAt = $prescriptionStatus === 'dispensed' ? $consultEndTime->addMinutes(rand(10, 60)) : null;

                $prescription = Prescription::create([
                    'consultation_id' => $consultation->id,
                    'patient_id'      => $patient->patient_id,
                    'doctor_id'       => $doctorId,
                    'status'          => $prescriptionStatus,
                    'dispensed_by'    => $prescriptionStatus === 'dispensed' ? $pharmacist : null,
                    'dispensed_at'    => $dispensedAt,
                    'cancelled_by'    => $prescriptionStatus === 'cancelled' ? $pharmacist : null,
                    'cancelled_at'    => $prescriptionStatus === 'cancelled' ? $consultEndTime->addMinutes(rand(5, 30)) : null,
                    'cancellation_reason' => $prescriptionStatus === 'cancelled' ? 'Stock unavailable at RHU' : null,
                    'expires_at'      => $consultEndTime->addDays(7),
                    'created_at'      => $consultEndTime,
                    'updated_at'      => $dispensedAt ?? $consultEndTime,
                ]);
                $prescriptionCount++;

                $chosenMeds = array_rand(array_flip($this->medicineIds), min($numItems, count($this->medicineIds)));
                if (!is_array($chosenMeds)) $chosenMeds = [$chosenMeds];

                foreach ($chosenMeds as $medId) {
                    $medicine = Medicine::find($medId);
                    $qty = rand(5, 30);
                    $dispensedQty = $prescriptionStatus === 'dispensed' ? $qty : 0;

                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_id'     => $medId,
                        'medicine_name'   => $medicine->name,
                        'dosage'          => $this->randomElement(['500mg', '250mg', '100mg', '10mg', '5ml']),
                        'frequency'       => $this->randomElement(['3x daily', '2x daily', 'once daily', 'every 8 hours', 'as needed']),
                        'duration'         => $this->randomElement(['3 days', '5 days', '7 days', '14 days']),
                        'quantity'         => $qty,
                        'dispensed_quantity' => $dispensedQty,
                        'created_at'       => $consultEndTime,
                        'updated_at'       => $dispensedAt ?? $consultEndTime,
                    ]);
                    $prescriptionItemCount++;

                    // ── Inventory Log for dispensed items ──
                    if ($dispensedQty > 0) {
                        InventoryLog::create([
                            'medicine_id'      => $medId,
                            'batch_id'         => MedicineBatch::where('medicine_id', $medId)->where('quantity', '>', 0)->first()?->id,
                            'action'           => 'Dispensed',
                            'quantity_changed'  => -$dispensedQty,
                            'remarks'          => "Dispensed for Rx #{$prescription->id}",
                            'performed_by'     => $pharmacist,
                            'created_at'       => $dispensedAt,
                            'updated_at'       => $dispensedAt,
                        ]);
                        $inventoryLogCount++;
                    }
                }
            }
        }

        $this->counts['created_appointments'] = $appointmentCount;
        $this->counts['created_pre_triages'] = $preTriageCount;
        $this->counts['created_consultations'] = $consultationCount;
        $this->counts['created_queues'] = $queueCount;
        $this->counts['created_medical_cases'] = $medicalCaseCount;
        $this->counts['created_ancillary_requests'] = $ancillaryCount;
        $this->counts['created_prescriptions'] = $prescriptionCount;
        $this->counts['created_prescription_items'] = $prescriptionItemCount;
        $this->counts['created_inventory_logs'] = $inventoryLogCount;

        $this->command->info("   ✓ Created {$appointmentCount} appointments, {$consultationCount} consultations");
        $this->command->info("   ✓ Created {$preTriageCount} pre-triages, {$medicalCaseCount} medical cases");
        $this->command->info("   ✓ Created {$prescriptionCount} prescriptions, {$ancillaryCount} ancillary requests");
    }

    // ================================================================
    //  Phase 6: Audit Logs
    // ================================================================
    private function seedAuditLogs(): void
    {
        $this->command->info('📋 Seeding audit logs...');

        $now = Carbon::now();
        $allStaff = User::pluck('id')->toArray();
        $logs = [];
        $logTemplates = [
            ['action' => 'Login', 'model_type' => 'App\\Models\\User'],
            ['action' => 'Logout', 'model_type' => 'App\\Models\\User'],
            ['action' => 'Viewed Patient Master List', 'model_type' => null],
            ['action' => 'Updated Consultation', 'model_type' => 'App\\Models\\Consultation'],
            ['action' => 'Created PreTriage', 'model_type' => 'App\\Models\\PreTriage'],
            ['action' => 'Vitals Recorded', 'model_type' => 'App\\Models\\PreTriage'],
            ['action' => 'Created Consultation', 'model_type' => 'App\\Models\\Consultation'],
            ['action' => 'Consultation Queued', 'model_type' => 'App\\Models\\Consultation'],
            ['action' => 'Consultation Completed', 'model_type' => 'App\\Models\\Consultation'],
            ['action' => 'Updated Patient', 'model_type' => 'App\\Models\\Patient'],
            ['action' => 'Patient Registered', 'model_type' => 'App\\Models\\Patient'],
            ['action' => 'Diagnostic Results Completed', 'model_type' => 'App\\Models\\AncillaryRequest'],
            ['action' => 'Specimen Collected', 'model_type' => 'App\\Models\\AncillaryRequest'],
            ['action' => 'Viewed Patient List (Information Desk)', 'model_type' => null],
            ['action' => 'Viewed Follow-Up Tracker (Front Desk)', 'model_type' => null],
            ['action' => 'Updated Landing Page & CMS Content', 'model_type' => null],
        ];

        // Generate ~1500 logs spread over 6 months with upward trend
        $targetLogs = 1500;
        $ips = ['192.168.1.' . rand(10, 50), '10.0.0.' . rand(5, 25), '172.16.0.' . rand(2, 20)];

        for ($i = 0; $i < $targetLogs; $i++) {
            $template = $logTemplates[array_rand($logTemplates)];
            $daysAgo = intval(pow(rand(0, 10000) / 10000, 0.5) * 180); // last 6 months, recent-heavy
            $logDate = $now->copy()->subDays($daysAgo)->setTime(rand(6, 18), rand(0, 59), rand(0, 59));

            $logs[] = [
                'user_id'    => $allStaff[array_rand($allStaff)],
                'action'     => $template['action'],
                'model_type' => $template['model_type'],
                'model_id'   => $template['model_type'] ? rand(1, 200) : null,
                'changes'    => null,
                'ip_address' => $ips[array_rand($ips)],
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => $logDate,
                'updated_at' => $logDate,
            ];
        }

        // Bulk insert (bypass AuditLog model's boot events)
        foreach (array_chunk($logs, 500) as $chunk) {
            DB::table('audit_logs')->insert($chunk);
        }

        $this->counts['created_audit_logs'] = count($logs);
        $this->command->info('   ✓ Created ' . count($logs) . ' audit logs');
    }

    // ================================================================
    //  Phase 7: Notifications
    // ================================================================
    private function seedNotifications(): void
    {
        $this->command->info('🔔 Seeding notifications...');

        $now = Carbon::now();
        $allStaff = User::pluck('id')->toArray();
        $notifications = [];

        $types = [
            'App\\Notifications\\AppointmentReminder',
            'App\\Notifications\\PrescriptionReady',
            'App\\Notifications\\LabResultReady',
            'App\\Notifications\\FollowUpDue',
        ];

        $messages = [
            'New appointment booked for tomorrow',
            'Prescription is ready for dispensing',
            'Laboratory results are available',
            'Patient follow-up is due today',
            'New patient registered in the system',
            'Consultation completed — discharge ready',
        ];

        for ($i = 0; $i < 50; $i++) {
            $daysAgo = rand(0, 30);
            $notifications[] = [
                'id'              => (string) Str::uuid(),
                'type'            => $types[array_rand($types)],
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $allStaff[array_rand($allStaff)],
                'data'            => json_encode([
                    'message' => $messages[array_rand($messages)],
                    'url'     => '/admin/dashboard',
                ]),
                'read_at'    => rand(0, 1) ? $now->copy()->subDays($daysAgo)->addHours(rand(1, 8)) : null,
                'created_at' => $now->copy()->subDays($daysAgo),
                'updated_at' => $now->copy()->subDays($daysAgo),
            ];
        }

        DB::table('notifications')->insert($notifications);

        $this->counts['created_notifications'] = count($notifications);
        $this->command->info('   ✓ Created ' . count($notifications) . ' notifications');
    }

    // ================================================================
    //  Report
    // ================================================================
    private function printReport(): void
    {
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('  📊 SEED REPORT');
        $this->command->info('═══════════════════════════════════════════════');

        // Deleted
        $deleted = array_filter($this->counts, fn($k) => str_starts_with($k, 'deleted_'), ARRAY_FILTER_USE_KEY);
        if (!empty($deleted)) {
            $this->command->warn('  🗑️  DELETED:');
            foreach ($deleted as $key => $count) {
                $table = str_replace('deleted_', '', $key);
                $this->command->line("     {$table}: {$count} rows");
            }
        }

        $this->command->newLine();

        // Created
        $created = array_filter($this->counts, fn($k) => str_starts_with($k, 'created_'), ARRAY_FILTER_USE_KEY);
        $this->command->info('  ✅ CREATED:');
        foreach ($created as $key => $count) {
            $table = str_replace('created_', '', $key);
            $this->command->line("     {$table}: {$count} rows");
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('  ✨ Dashboard should now look production-ready!');
        $this->command->info('  📌 Login: any user email / password: "password"');
        $this->command->info('═══════════════════════════════════════════════');
    }

    // ================================================================
    //  Helpers
    // ================================================================
    private function randomElement(array $items, array $weights = []): mixed
    {
        if (empty($weights)) {
            return $items[array_rand($items)];
        }

        $rand = mt_rand(0, 10000) / 10000;
        $cumulative = 0;
        foreach ($items as $idx => $item) {
            $cumulative += $weights[$idx] ?? 0;
            if ($rand <= $cumulative) {
                return $item;
            }
        }
        return end($items);
    }

    private function randomFrom(array $ids): mixed
    {
        return !empty($ids) ? $ids[array_rand($ids)] : null;
    }
}
