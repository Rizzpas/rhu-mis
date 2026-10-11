import { appointmentForm } from '../../resources/js/appointment-form.js';

describe('Appointment Form Component Logic', () => {
  let form;

  beforeEach(() => {
    document.body.innerHTML = '';
    form = appointmentForm();
  });

  describe('formatPhilHealth', () => {
    it('formats 12 numeric digits to XX-XXXXXXXXX-X format', () => {
      expect(form.formatPhilHealth('012345678901')).toBe('01-234567890-1');
    });

    it('strips non-digit characters before formatting', () => {
      expect(form.formatPhilHealth('01-234567890-1')).toBe('01-234567890-1');
      expect(form.formatPhilHealth('abc01def234567890ghi1xyz')).toBe('01-234567890-1');
    });

    it('handles partial input lengths gracefully', () => {
      expect(form.formatPhilHealth('01')).toBe('01');
      expect(form.formatPhilHealth('012')).toBe('01-2');
      expect(form.formatPhilHealth('01234567890')).toBe('01-234567890');
    });

    it('truncates inputs longer than 12 digits', () => {
      expect(form.formatPhilHealth('01234567890199999')).toBe('01-234567890-1');
    });

    it('returns empty string for falsy values', () => {
      expect(form.formatPhilHealth('')).toBe('');
      expect(form.formatPhilHealth(null)).toBe('');
      expect(form.formatPhilHealth(undefined)).toBe('');
    });
  });

  describe('combinedComplaint', () => {
    it('returns empty string when symptoms are empty', () => {
      form.formData.symptoms = [];
      form.formData.other_symptom = '';
      expect(form.combinedComplaint).toBe('');
    });

    it('joins selected symptoms with comma and space', () => {
      form.formData.symptoms = ['Fever', 'Cough', 'Body Pain'];
      form.formData.other_symptom = '';
      expect(form.combinedComplaint).toBe('Fever, Cough, Body Pain');
    });

    it('formats custom other_symptom prefixed with Others:', () => {
      form.formData.symptoms = [];
      form.formData.other_symptom = 'Skin rash on arm';
      expect(form.combinedComplaint).toBe('Others: Skin rash on arm');
    });

    it('combines preset symptoms and trimmed other_symptom', () => {
      form.formData.symptoms = ['Fever'];
      form.formData.other_symptom = '  Persistent dizziness  ';
      expect(form.combinedComplaint).toBe('Fever, Others: Persistent dizziness');
    });
  });

  describe('Service Selection & isFollowUp status', () => {
    it('sets adult service as follow-up automatically', () => {
      form.fetchAvailability = jest.fn();
      form.setService('adult');

      expect(form.formData.type).toBe('adult');
      expect(form.formData.is_follow_up).toBe(true);
      expect(form.isFollowUp).toBe(true);
      expect(form.fetchAvailability).toHaveBeenCalled();
    });

    it('sets pedia service as non-followup by default', () => {
      form.fetchAvailability = jest.fn();
      form.setService('pedia');

      expect(form.formData.type).toBe('pedia');
      expect(form.formData.is_follow_up).toBe(false);
      expect(form.isFollowUp).toBe(false);
      expect(form.fetchAvailability).toHaveBeenCalled();
    });

    it('evaluates isFollowUp as true for pedia if marked as follow up', () => {
      form.formData.type = 'pedia';
      form.formData.is_follow_up = true;
      expect(form.isFollowUp).toBe(true);
    });
  });

  describe('Calendar Operations', () => {
    it('initializes and calculates days in month correctly', () => {
      form.currentYear = 2026;
      form.currentMonth = 1; // February 2026 (non-leap year)
      form.initCalendar();

      expect(form.monthName).toBe('February');
      expect(form.daysInMonth).toBe(28);
    });

    it('navigates next month and wraps around year', () => {
      form.isNextMonthDisabled = jest.fn().mockReturnValue(false);
      form.fetchAvailability = jest.fn();
      form.currentYear = 2026;
      form.currentMonth = 11; // December

      form.nextMonth();

      expect(form.currentMonth).toBe(0); // January
      expect(form.currentYear).toBe(2027);
      expect(form.fetchAvailability).toHaveBeenCalled();
    });

    it('navigates prev month and wraps back to previous year', () => {
      form.isPrevMonthDisabled = jest.fn().mockReturnValue(false);
      form.fetchAvailability = jest.fn();
      form.currentYear = 2026;
      form.currentMonth = 0; // January

      form.prevMonth();

      expect(form.currentMonth).toBe(11); // December
      expect(form.currentYear).toBe(2025);
      expect(form.fetchAvailability).toHaveBeenCalled();
    });

    it('formats date string as YYYY-MM-DD', () => {
      form.currentYear = 2026;
      form.currentMonth = 9; // October (0-indexed: 9)
      const dateStr = form.getDateString(8);
      expect(dateStr).toBe('2026-10-08');
    });

    it('identifies dates in the past correctly', () => {
      const today = new Date();
      form.currentYear = today.getFullYear();
      form.currentMonth = today.getMonth();

      // Yesterday
      if (today.getDate() > 1) {
        expect(form.isPastDate(today.getDate() - 1)).toBe(true);
      }
      // Today is not past
      expect(form.isPastDate(today.getDate())).toBe(false);
    });
  });

  describe('Availability & Day Status', () => {
    it('determines if day is full for standard pedia appointments', () => {
      form.formData.type = 'pedia';
      form.formData.is_follow_up = false;
      form.currentYear = 2026;
      form.currentMonth = 9; // October

      const dateStr = '2026-10-15';
      form.getDateString = jest.fn().mockReturnValue(dateStr);

      // No capacity at all
      form.availability = { [dateStr]: { capacity: 0, available: 0 } };
      expect(form.isDayFull(15)).toBe(true);

      // Capacity exists but 0 slots left
      form.availability = { [dateStr]: { capacity: 20, available: 0 } };
      expect(form.isDayFull(15)).toBe(true);

      // Slots available
      form.availability = { [dateStr]: { capacity: 20, available: 5 } };
      expect(form.isDayFull(15)).toBe(false);
    });

    it('determines if day is limited (<= 25% capacity available)', () => {
      form.formData.type = 'pedia';
      form.formData.is_follow_up = false;
      const dateStr = '2026-10-20';
      form.getDateString = jest.fn().mockReturnValue(dateStr);

      // 4 out of 20 available (20% <= 25%) -> limited
      form.availability = { [dateStr]: { capacity: 20, available: 4 } };
      expect(form.isDayLimited(20)).toBe(true);

      // 10 out of 20 available (50% > 25%) -> not limited
      form.availability = { [dateStr]: { capacity: 20, available: 10 } };
      expect(form.isDayLimited(20)).toBe(false);
    });

    it('checks doctor schedule valid days for follow-up appointments', () => {
      form.formData.type = 'adult'; // follow-up
      form.currentYear = 2026;
      form.currentMonth = 9; // October 2026
      // In October 2026, Oct 8 is Thursday, Oct 11 is Sunday
      form.validDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

      // Thursday Oct 8 should be available (not full)
      expect(form.isDayFull(8)).toBe(false);
      // Sunday Oct 11 should be full/unavailable
      expect(form.isDayFull(11)).toBe(true);
    });
  });

  describe('syncMotherToGuardian', () => {
    it('syncs mother name fields to guardian fields in uppercase', () => {
      form.formData.mother_first_name = 'maria';
      form.formData.mother_middle_name = 'santos';
      form.formData.mother_last_name = 'cruz';
      form.formData.mother_suffix = '';

      form.syncMotherToGuardian();

      expect(form.formData.guardian_first_name).toBe('MARIA');
      expect(form.formData.guardian_middle_name).toBe('SANTOS');
      expect(form.formData.guardian_last_name).toBe('CRUZ');
      expect(form.formData.guardian_suffix).toBe('');
    });

    it('clears guardian name validation errors after syncing', () => {
      form.errors.guardian_first_name = 'First and Last Name are required';
      form.errors.guardian_last_name = 'First and Last Name are required';
      form.formData.mother_first_name = 'MARIA';
      form.formData.mother_last_name = 'CRUZ';

      form.syncMotherToGuardian();

      expect(form.errors.guardian_first_name).toBeUndefined();
      expect(form.errors.guardian_last_name).toBeUndefined();
    });
  });

  describe('validateInfo (Step 2 Validation)', () => {
    it('fails when common required fields are missing', () => {
      expect(form.validateInfo()).toBe(false);
      expect(form.errors.first_name).toBe('Required');
      expect(form.errors.last_name).toBe('Required');
      expect(form.errors.sex).toBe('Required');
      expect(form.errors.dob).toBe('Required');
      expect(form.errors.email).toBe('Required');
      expect(form.errors.complaint).toBe('Please specify the Reason for Visit');
    });

    it('validates email format correctly', () => {
      form.formData.first_name = 'JUAN';
      form.formData.last_name = 'DELA CRUZ';
      form.formData.sex = 'Male';
      form.formData.dob = '2020-05-10';
      form.formData.symptoms = ['Fever'];

      form.formData.email = 'not-an-email';
      expect(form.validateInfo()).toBe(false);
      expect(form.errors.email).toBe('Must be a valid email address');

      form.errors = {};
      form.formData.email = 'valid.user@example.com';
      form.validateInfo();
      expect(form.errors.email).toBeUndefined();
    });

    it('enforces pediatric age restriction (<= 12 years)', () => {
      form.formData.type = 'pedia';
      form.formData.first_name = 'CARLO';
      form.formData.last_name = 'SANTOS';
      form.formData.sex = 'Male';
      form.formData.symptoms = ['Cough'];
      form.formData.email = 'test@example.com';

      // 15 years old (DOB 2011)
      form.formData.dob = '2011-01-01';
      expect(form.validateInfo()).toBe(false);
      expect(form.errors.dob).toBe('Pediatric patients must be 12 years old or below');

      // 5 years old (DOB 2021)
      form.errors = {};
      form.formData.dob = '2021-01-01';
      form.validateInfo();
      expect(form.errors.dob).toBeUndefined();
    });

    it('enforces adult age restriction (> 12 years)', () => {
      form.formData.type = 'adult';
      form.formData.is_follow_up = true;
      form.formData.first_name = 'PEDRO';
      form.formData.last_name = 'REYES';
      form.formData.sex = 'Male';
      form.formData.symptoms = ['Checkup'];
      form.formData.email = 'adult@example.com';

      // 8 years old
      form.formData.dob = '2018-01-01';
      expect(form.validateInfo()).toBe(false);
      expect(form.errors.dob).toBe('Patients 12 years old and below must use the Pediatrics booking flow');
    });

    it('validates PhilHealth format for non-followup appointments', () => {
      form.formData.type = 'pedia';
      form.formData.is_follow_up = false;
      form.formData.first_name = 'LUCAS';
      form.formData.last_name = 'GARCIA';
      form.formData.sex = 'Male';
      form.formData.dob = '2022-01-01';
      form.formData.email = 'lucas@example.com';
      form.formData.symptoms = ['Checkup'];
      form.formData.barangay = 'Biga I';
      form.formData.blood_type = 'O+';
      form.formData.mother_first_name = 'ANNA';
      form.formData.mother_last_name = 'GARCIA';
      form.formData.guardian_first_name = 'ANNA';
      form.formData.guardian_last_name = 'GARCIA';
      form.formData.guardian_relation = 'Mother';
      form.formData.guardian_contact = '09123456789';

      form.formData.philhealth_number = 'invalid-philhealth';
      expect(form.validateInfo()).toBe(false);
      expect(form.errors.philhealth).toBe('Must follow 12-123456789-0 format');

      form.errors = {};
      form.formData.philhealth_number = '12-123456789-0';
      expect(form.validateInfo()).toBe(true);
      expect(form.errors.philhealth).toBeUndefined();
    });

    it('validates 11-digit guardian contact number for pediatric patients', () => {
      form.formData.type = 'pedia';
      form.formData.is_follow_up = false;
      form.formData.first_name = 'LUCAS';
      form.formData.last_name = 'GARCIA';
      form.formData.sex = 'Male';
      form.formData.dob = '2022-01-01';
      form.formData.email = 'lucas@example.com';
      form.formData.symptoms = ['Checkup'];
      form.formData.barangay = 'Biga I';
      form.formData.blood_type = 'O+';
      form.formData.philhealth_number = '12-123456789-0';
      form.formData.mother_first_name = 'ANNA';
      form.formData.mother_last_name = 'GARCIA';
      form.formData.guardian_first_name = 'ANNA';
      form.formData.guardian_last_name = 'GARCIA';
      form.formData.guardian_relation = 'Mother';

      form.formData.guardian_contact = '12345'; // Not 11 digits
      expect(form.validateInfo()).toBe(false);
      expect(form.errors.guardian_contact).toBe('Must be exactly 11 digits');

      form.formData.guardian_contact = '09171234567';
      expect(form.validateInfo()).toBe(true);
    });
  });

  describe('Multi-Step Progression & API Calls', () => {
    it('progresses from Step 1 to Step 2 without API validation', async () => {
      form.step = 1;
      await form.nextStep();
      expect(form.step).toBe(2);
    });

    it('validates duplicate booking on Step 2 and blocks if duplicate exists', async () => {
      form.step = 2;
      form.validateInfo = jest.fn().mockReturnValue(true);

      const mockFetch = jest.fn().mockResolvedValue({
        status: 200,
        json: async () => ({ exists: true }),
      });

      form = appointmentForm({ fetchFn: mockFetch });
      form.step = 2;
      form.validateInfo = jest.fn().mockReturnValue(true);

      await form.nextStep();

      expect(form.showDuplicateModal).toBe(true);
      expect(form.step).toBe(2); // Did not advance
    });

    it('verifies follow-up doctor schedule on Step 2 if follow-up service', async () => {
      const mockFetch = jest.fn()
        .mockResolvedValueOnce({
          status: 200,
          json: async () => ({
            valid: true,
            doctor_id: 14,
            doctor_name: 'Dr. Santos',
            doctor_schedule: 'Mon, Wed, Fri',
            valid_days: ['Mon', 'Wed', 'Fri'],
          }),
        })
        .mockResolvedValueOnce({
          status: 200,
          json: async () => ({ exists: false }),
        });

      form = appointmentForm({ fetchFn: mockFetch });
      form.step = 2;
      form.formData.type = 'adult'; // follow up
      form.validateInfo = jest.fn().mockReturnValue(true);

      await form.nextStep();

      expect(form.doctorName).toBe('Dr. Santos');
      expect(form.validDays).toEqual(['Mon', 'Wed', 'Fri']);
      expect(form.followUpDoctorId).toBe(14);
      expect(form.step).toBe(3); // Advanced to calendar step
    });

    it('sends OTP on Step 3 when date and time are selected', async () => {
      const mockFetch = jest.fn().mockResolvedValue({
        status: 200,
        json: async () => ({ success: true }),
      });

      form = appointmentForm({ fetchFn: mockFetch });
      form.step = 3;
      form.selectedDate = '2026-10-15';
      form.selectedTime = '09:00 AM';
      form.formData.email = 'patient@example.com';

      await form.nextStep();

      expect(mockFetch).toHaveBeenCalledWith(
        '/appointment/send-otp',
        expect.objectContaining({ method: 'POST' })
      );
      expect(form.step).toBe(4); // Advanced to OTP step
    });

    it('verifies OTP on Step 4 and progresses to Step 5 (confirmation)', async () => {
      const mockFetch = jest.fn().mockResolvedValue({
        status: 200,
        json: async () => ({ success: true }),
      });

      form = appointmentForm({ fetchFn: mockFetch });
      form.step = 4;
      form.formData.email = 'patient@example.com';
      form.otpCode = '123456';

      await form.nextStep();

      expect(mockFetch).toHaveBeenCalledWith(
        '/appointment/verify-otp',
        expect.objectContaining({ method: 'POST' })
      );
      expect(form.step).toBe(5); // Final confirmation step
    });

    it('rejects OTP verification on Step 4 if OTP is invalid', async () => {
      const mockFetch = jest.fn().mockResolvedValue({
        status: 200,
        json: async () => ({ success: false, message: 'Invalid or expired code' }),
      });

      form = appointmentForm({ fetchFn: mockFetch });
      form.step = 4;
      form.formData.email = 'patient@example.com';
      form.otpCode = '999999';

      await form.nextStep();

      expect(form.errors.otp).toBe('Invalid or expired code');
      expect(form.step).toBe(4); // Remains on OTP step
    });

    it('navigates backwards via prevStep', () => {
      form.step = 3;
      form.prevStep();
      expect(form.step).toBe(2);
    });

    it('allows jumping only to previously reached steps via goToStep', () => {
      form.step = 3;
      form.maxStepReached = 3;

      // Jump back to step 1
      form.goToStep(1);
      expect(form.step).toBe(1);

      // Jump forward to previously reached step 3
      form.goToStep(3);
      expect(form.step).toBe(3);

      // Attempt jump forward to unreached step 5 is blocked
      form.goToStep(5);
      expect(form.step).toBe(3);
    });

    it('fetches time slots when selecting a calendar date', async () => {
      const mockSlots = ['08:00 AM', '09:00 AM', '10:00 AM'];
      const mockFetch = jest.fn().mockResolvedValue({
        json: async () => ({ slots: mockSlots }),
      });

      form = appointmentForm({ fetchFn: mockFetch });
      form.selectDate('2026-10-15');

      await new Promise(resolve => setTimeout(resolve, 10));

      expect(form.selectedDate).toBe('2026-10-15');
      expect(form.selectedTime).toBe('');
      expect(form.timeSlots).toEqual(mockSlots);
      expect(form.loadingSlots).toBe(false);
    });
  });
});
