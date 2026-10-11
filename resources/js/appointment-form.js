/**
 * RHU MIS - Appointment Booking Form Component Logic
 * Matches resources/views/appointments/create.blade.php
 */

export function appointmentForm(config = {}) {
  const {
    oldData = {},
    routes = {
      verifyFollowUp: '/appointment/verify-follow-up',
      checkDuplicate: '/appointment/check-duplicate',
      checkAvailability: '/appointment/check-availability',
      timeSlots: '/appointment/time-slots',
      sendOtp: '/appointment/send-otp',
      verifyOtp: '/appointment/verify-otp',
    },
    fetchFn = (typeof window !== 'undefined' && window.fetch) ? window.fetch.bind(window) : global.fetch,
  } = config;

  return {
    get isFollowUp() {
      return this.formData.type === 'adult' || (this.formData.type === 'pedia' && this.formData.is_follow_up);
    },
    step: 1,
    maxStepReached: 1,
    progress: 0,
    isLoading: false,
    selectedDate: oldData.preferred_date || '',
    selectedTime: oldData.preferred_time || '',
    timeSlots: [],
    loadingSlots: false,
    followUpDoctorId: null,
    otpCode: '',
    availability: {},
    doctorSchedule: '',
    doctorName: '',
    showPrivacy: false,
    showDuplicateModal: Boolean(oldData.duplicate_booking),
    validDays: null,

    // Calendar Variables
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear(),
    daysInMonth: 0,
    startDay: 0,
    monthName: '',
    months: [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ],

    initCalendar() {
      this.updateCalendar();
    },
    updateCalendar() {
      const d = new Date(this.currentYear, this.currentMonth, 1);
      this.monthName = this.months[this.currentMonth];
      this.startDay = d.getDay();
      this.daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
    },
    nextMonth() {
      if (this.isNextMonthDisabled()) return;
      this.currentMonth++;
      if (this.currentMonth > 11) {
        this.currentMonth = 0;
        this.currentYear++;
      }
      this.updateCalendar();
      this.fetchAvailability();
    },
    prevMonth() {
      if (this.isPrevMonthDisabled()) return;
      this.currentMonth--;
      if (this.currentMonth < 0) {
        this.currentMonth = 11;
        this.currentYear--;
      }
      this.updateCalendar();
      this.fetchAvailability();
    },
    isPrevMonthDisabled() {
      const today = new Date();
      return this.currentYear === today.getFullYear() && this.currentMonth === today.getMonth();
    },
    isNextMonthDisabled() {
      const today = new Date();
      let maxMonth = today.getMonth() + 1;
      let maxYear = today.getFullYear();
      if (maxMonth > 11) {
        maxMonth = 0;
        maxYear++;
      }
      return this.currentYear > maxYear || (this.currentYear === maxYear && this.currentMonth >= maxMonth);
    },
    getDateString(day) {
      const d = new Date(this.currentYear, this.currentMonth, day);
      const offset = d.getTimezoneOffset();
      const adjusted = new Date(d.getTime() - (offset * 60 * 1000));
      return adjusted.toISOString().split('T')[0];
    },
    isPastDate(day) {
      const dateToCheck = new Date(this.currentYear, this.currentMonth, day);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return dateToCheck < today;
    },
    isDayFull(day) {
      const ds = this.getDateString(day);

      if (this.isFollowUp) {
        if (!this.validDays) return false;
        const d = new Date(this.currentYear, this.currentMonth, day);
        const dayShort = d.toLocaleString('en-US', { weekday: 'short' });
        return !this.validDays.includes(dayShort);
      } else {
        const slotData = this.availability[ds];
        if (!slotData) return false;
        if (slotData.capacity <= 0) return true;
        return slotData.available <= 0;
      }
    },
    isDayLimited(day) {
      if (this.isFollowUp) return false;

      const ds = this.getDateString(day);
      const slotData = this.availability[ds];
      if (!slotData || slotData.capacity <= 0) return false;

      const limitThreshold = slotData.capacity * 0.25;
      return slotData.available > 0 && slotData.available <= limitThreshold;
    },

    // Form Data
    formData: {
      first_name: oldData.first_name || '',
      middle_name: oldData.middle_name || '',
      last_name: oldData.last_name || '',
      suffix: oldData.suffix || '',
      sex: oldData.sex || '',
      dob: oldData.dob || '',
      civil_status: oldData.civil_status || '',
      blood_type: oldData.blood_type || '',
      address: oldData.address || '',
      barangay: oldData.barangay || '',
      philhealth_number: oldData.philhealth_number || '',
      education: oldData.education || '',
      religion: oldData.religion || '',
      occupation: oldData.occupation || '',
      mother_first_name: oldData.mother_first_name || '',
      mother_middle_name: oldData.mother_middle_name || '',
      mother_last_name: oldData.mother_last_name || '',
      mother_suffix: oldData.mother_suffix || '',
      mothers_maiden_name: oldData.mothers_maiden_name || '',
      classification: oldData.classification || '',
      email: oldData.email || '',
      type: oldData.type || 'pedia',
      is_follow_up: Boolean(oldData.is_follow_up),
      guardian_first_name: oldData.guardian_first_name || '',
      guardian_middle_name: oldData.guardian_middle_name || '',
      guardian_last_name: oldData.guardian_last_name || '',
      guardian_suffix: oldData.guardian_suffix || '',
      guardian_relation: oldData.guardian_relation || '',
      guardian_contact: oldData.guardian_contact || '',
      guardian_email: oldData.guardian_email || '',
      symptoms: oldData.symptoms || [],
      other_symptom: oldData.other_symptom || '',
      data_privacy_agreed: Boolean(oldData.data_privacy_agreed),
    },

    formatPhilHealth(value) {
      if (!value) return '';
      const raw = value.replace(/\D/g, '').substring(0, 12);
      let formatted = '';
      if (raw.length > 0) {
        formatted = raw.substring(0, 2);
      }
      if (raw.length > 2) {
        formatted += '-' + raw.substring(2, 11);
      }
      if (raw.length > 11) {
        formatted += '-' + raw.substring(11, 12);
      }
      return formatted;
    },

    get combinedComplaint() {
      const parts = [...this.formData.symptoms];
      if (this.formData.other_symptom && this.formData.other_symptom.trim() !== '') {
        parts.push('Others: ' + this.formData.other_symptom.trim());
      }
      return parts.join(', ');
    },

    errors: {},
    motherIsGuardian: false,

    toggleMotherAsGuardian() {
      if (this.motherIsGuardian) {
        this.syncMotherToGuardian();
      }
    },

    syncMotherToGuardian() {
      const mFirst = (this.formData.mother_first_name || (typeof document !== 'undefined' ? document.querySelector('input[x-model="formData.mother_first_name"]')?.value : '') || '').trim().toUpperCase();
      const mMid = (this.formData.mother_middle_name || (typeof document !== 'undefined' ? document.querySelector('input[x-model="formData.mother_middle_name"]')?.value : '') || '').trim().toUpperCase();
      const mLast = (this.formData.mother_last_name || (typeof document !== 'undefined' ? document.querySelector('input[x-model="formData.mother_last_name"]')?.value : '') || '').trim().toUpperCase();
      const mSuffix = (this.formData.mother_suffix || (typeof document !== 'undefined' ? document.querySelector('input[x-model="formData.mother_suffix"]')?.value : '') || '').trim().toUpperCase();

      this.formData.mother_first_name = mFirst;
      this.formData.mother_middle_name = mMid;
      this.formData.mother_last_name = mLast;
      this.formData.mother_suffix = mSuffix;

      this.formData.guardian_first_name = mFirst;
      this.formData.guardian_middle_name = mMid;
      this.formData.guardian_last_name = mLast;
      this.formData.guardian_suffix = mSuffix;

      if (typeof document !== 'undefined') {
        const gFirst = document.querySelector('input[name="guardian_first_name"]');
        if (gFirst) gFirst.value = mFirst;
        const gMid = document.querySelector('input[name="guardian_middle_name"]');
        if (gMid) gMid.value = mMid;
        const gLast = document.querySelector('input[name="guardian_last_name"]');
        if (gLast) gLast.value = mLast;
        const gSuffix = document.querySelector('input[name="guardian_suffix"]');
        if (gSuffix) gSuffix.value = mSuffix;
      }

      if (this.formData.guardian_first_name && this.formData.guardian_last_name) {
        delete this.errors.guardian_first_name;
        delete this.errors.guardian_last_name;
      }
    },

    setService(type) {
      this.formData.type = type;
      this.formData.is_follow_up = (type === 'adult');
      this.fetchAvailability();
    },

    syncFormDataFromDOM() {
      if (typeof document === 'undefined') return;
      const syncVal = (selector, setter) => {
        const el = document.querySelector(selector);
        if (el && el.value !== undefined && el.value !== null && el.value.toString().trim() !== '') {
          setter(el.value.toString().trim());
        }
      };

      syncVal('input[name="first_name"]', v => { this.formData.first_name = v.toUpperCase(); });
      syncVal('input[name="middle_name"]', v => { this.formData.middle_name = v.toUpperCase(); });
      syncVal('input[name="last_name"]', v => { this.formData.last_name = v.toUpperCase(); });
      syncVal('input[name="suffix"]', v => { this.formData.suffix = v.toUpperCase(); });
      syncVal('select[name="sex"]', v => { this.formData.sex = v; });
      syncVal('input[name="dob"]', v => { this.formData.dob = v; });
      syncVal('select[name="civil_status"]', v => { this.formData.civil_status = v; });
      syncVal('select[name="blood_type"]', v => { this.formData.blood_type = v; });
      syncVal('select[name="education"]', v => { this.formData.education = v; });
      syncVal('input[name="email"]', v => { this.formData.email = v; });
      syncVal('input[name="contact_number"]', v => { this.formData.contact_number = v; });
      syncVal('input[name="philhealth_number"]', v => { this.formData.philhealth_number = this.formatPhilHealth(v); });

      syncVal('input[x-model="formData.mother_first_name"]', v => { this.formData.mother_first_name = v.toUpperCase(); });
      syncVal('input[x-model="formData.mother_middle_name"]', v => { this.formData.mother_middle_name = v.toUpperCase(); });
      syncVal('input[x-model="formData.mother_last_name"]', v => { this.formData.mother_last_name = v.toUpperCase(); });
      syncVal('input[x-model="formData.mother_suffix"]', v => { this.formData.mother_suffix = v.toUpperCase(); });

      syncVal('select[x-model="barangay"]', v => { this.formData.barangay = v; });
      syncVal('input[name="address"]', v => { this.formData.address = v; });

      if (this.motherIsGuardian) {
        this.syncMotherToGuardian();
      } else {
        syncVal('input[name="guardian_first_name"]', v => { this.formData.guardian_first_name = v.toUpperCase(); });
        syncVal('input[name="guardian_middle_name"]', v => { this.formData.guardian_middle_name = v.toUpperCase(); });
        syncVal('input[name="guardian_last_name"]', v => { this.formData.guardian_last_name = v.toUpperCase(); });
        syncVal('input[name="guardian_suffix"]', v => { this.formData.guardian_suffix = v.toUpperCase(); });
      }

      syncVal('select[x-model="selectedGuardianRelation"]', v => {
        const custom = document.querySelector('input[x-model="customGuardianRelation"]')?.value?.trim();
        this.formData.guardian_relation = (v === 'Others (Please Specify)' && custom) ? custom.toUpperCase() : v;
      });
      syncVal('input[name="guardian_contact"]', v => { this.formData.guardian_contact = v; });
    },

    validateInfo() {
      this.syncFormDataFromDOM();
      let isValid = true;
      if (!this.formData.first_name) { this.errors.first_name = 'Required'; isValid = false; }
      if (!this.formData.last_name) { this.errors.last_name = 'Required'; isValid = false; }
      if (!this.formData.sex) { this.errors.sex = 'Required'; isValid = false; }
      if (!this.formData.dob) { this.errors.dob = 'Required'; isValid = false; }

      if (this.isFollowUp) {
        // Follow-ups only need name + DOB
      } else {
        if (!this.formData.barangay) { this.errors.address = 'Barangay is required'; isValid = false; }
        if (!this.formData.blood_type) { this.errors.blood_type = 'Blood type is required'; isValid = false; }
        if (this.formData.type !== 'pedia' && !this.formData.religion) { this.errors.religion = 'Religion is required'; isValid = false; }

        if (!this.formData.philhealth_number) {
          this.errors.philhealth = 'PhilHealth Number is required'; isValid = false;
        } else if (!/^\d{2}-\d{9}-\d{1}$/.test(this.formData.philhealth_number)) {
          this.errors.philhealth = 'Must follow 12-123456789-0 format'; isValid = false;
        }
      }

      if (!this.combinedComplaint || this.combinedComplaint.trim() === '') {
        this.errors.complaint = 'Please specify the Reason for Visit'; isValid = false;
      }

      if (!this.formData.email) {
        this.errors.email = 'Required';
        isValid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email.toLowerCase())) {
        this.errors.email = 'Must be a valid email address';
        isValid = false;
      }

      if (this.formData.dob) {
        const dobDate = new Date(this.formData.dob);
        let age = new Date().getFullYear() - dobDate.getFullYear();
        const month = new Date().getMonth() - dobDate.getMonth();
        if (month < 0 || (month === 0 && new Date().getDate() < dobDate.getDate())) {
          age--;
        }

        if (this.formData.type === 'pedia') {
          if (age > 12) {
            this.errors.dob = 'Pediatric patients must be 12 years old or below';
            isValid = false;
          }
          if (!this.isFollowUp) {
            if (!this.formData.guardian_first_name || !this.formData.guardian_last_name) {
              this.errors.guardian_first_name = 'First and Last Name are required';
              isValid = false;
            }
            if (!this.formData.guardian_relation) {
              this.errors.guardian_relation = 'Required';
              isValid = false;
            }
            if (!this.formData.guardian_contact) {
              this.errors.guardian_contact = 'Required';
              isValid = false;
            } else if (!/^\d{11}$/.test(this.formData.guardian_contact)) {
              this.errors.guardian_contact = 'Must be exactly 11 digits';
              isValid = false;
            }
          }
        } else {
          if (age <= 12) {
            this.errors.dob = 'Patients 12 years old and below must use the Pediatrics booking flow';
            isValid = false;
          }
        }
      }

      if (!this.isFollowUp) {
        if (!this.formData.mother_first_name || !this.formData.mother_last_name) {
          this.errors.mothers_maiden_name = "Mother's First and Last Name are required";
          isValid = false;
        }
      }

      return isValid;
    },

    async nextStep() {
      this.errors = {};
      let canProceed = false;

      if (this.step === 1) {
        canProceed = true;
      } else if (this.step === 2) {
        if (this.validateInfo()) {
          try {
            this.isLoading = true;

            if (this.isFollowUp) {
              const csrf = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') : '';
              const followUpRes = await fetchFn(routes.verifyFollowUp, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrf || '',
                },
                body: JSON.stringify({
                  first_name: this.formData.first_name,
                  last_name: this.formData.last_name,
                  dob: this.formData.dob,
                }),
              });

              if (followUpRes.status === 419) {
                if (typeof alert === 'function') alert('Your session has expired. Please refresh the page and try again.');
                return;
              }

              const followUpData = await followUpRes.json();
              if (!followUpData.valid) {
                if (typeof window !== 'undefined') {
                  window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: followUpData.message } }));
                }
                this.isLoading = false;
                return;
              }

              this.doctorSchedule = followUpData.doctor_schedule;
              this.validDays = followUpData.valid_days;
              this.doctorName = followUpData.doctor_name;
              this.followUpDoctorId = followUpData.doctor_id || null;
            }

            const csrf = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') : '';
            const response = await fetchFn(routes.checkDuplicate, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf || '',
              },
              body: JSON.stringify({
                first_name: this.formData.first_name,
                last_name: this.formData.last_name,
                email: this.formData.email,
              }),
            });

            if (response.status === 419) {
              if (typeof alert === 'function') alert('Your session has expired. Please refresh the page and try again.');
              return;
            }

            const data = await response.json();
            if (data.exists) {
              this.showDuplicateModal = true;
              canProceed = false;
            } else {
              canProceed = true;
            }
          } catch (e) {
            console.error('Error checking duplicate', e);
            canProceed = true;
          } finally {
            this.isLoading = false;
          }
        } else {
          if (typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please complete or correct the required fields.' } }));
          }
        }
      } else if (this.step === 3) {
        if (!this.selectedDate) {
          if (typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please select a preferred date.' } }));
          }
        } else if (!this.selectedTime) {
          if (typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please select a preferred arrival time.' } }));
          }
        } else {
          await this.sendOtp();
          canProceed = false;
        }
      } else if (this.step === 4) {
        await this.verifyOtp();
        canProceed = false;
      }

      if (canProceed) {
        this.step++;
        if (this.step > this.maxStepReached) this.maxStepReached = this.step;
        if (this.step === 3) {
          this.initCalendar();
          this.fetchAvailability();
        }
        this.updateProgress();
      }
    },

    prevStep() {
      if (this.step > 1) {
        this.step--;
        this.updateProgress();
      }
    },

    goToStep(targetStep) {
      if (targetStep < this.step || targetStep <= this.maxStepReached) {
        this.step = targetStep;
        if (this.step === 3) {
          this.initCalendar();
          this.fetchAvailability();
        }
        this.updateProgress();
      }
    },

    updateProgress() {
      this.progress = (this.step - 1) * 25;
      if (typeof window !== 'undefined' && typeof window.scrollTo === 'function') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },

    selectDate(date) {
      this.selectedDate = date;
      this.selectedTime = '';
      this.fetchTimeSlots(date);
    },

    async fetchTimeSlots(date) {
      this.loadingSlots = true;
      this.timeSlots = [];
      try {
        const params = new URLSearchParams({ date, type: this.formData.type });
        if (this.followUpDoctorId) {
          params.append('doctor_id', this.followUpDoctorId);
        }
        const response = await fetchFn(`${routes.timeSlots}?${params}`);
        const data = await response.json();
        this.timeSlots = data.slots || [];
      } catch (e) {
        console.error('Failed to fetch time slots', e);
      } finally {
        this.loadingSlots = false;
      }
    },

    async fetchAvailability() {
      const d1 = new Date(this.currentYear, this.currentMonth, 1);
      const d2 = new Date(this.currentYear, this.currentMonth + 1, 0);
      const offset1 = d1.getTimezoneOffset();
      const offset2 = d2.getTimezoneOffset();
      const adj1 = new Date(d1.getTime() - (offset1 * 60 * 1000));
      const adj2 = new Date(d2.getTime() - (offset2 * 60 * 1000));

      const start = adj1.toISOString().split('T')[0];
      const end = adj2.toISOString().split('T')[0];

      try {
        const params = new URLSearchParams({ start, end });
        const response = await fetchFn(`${routes.checkAvailability}?${params}`);
        const data = await response.json();
        this.availability = { ...this.availability, ...data };
      } catch (e) {
        console.error('Failed to fetch availability', e);
      }
    },

    async sendOtp() {
      this.isLoading = true;
      try {
        const csrf = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') : '';
        const response = await fetchFn(routes.sendOtp, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf || '',
          },
          body: JSON.stringify({ email: this.formData.email }),
        });

        if (response.status === 419) {
          if (typeof alert === 'function') alert('Your session has expired. Please refresh the page and try again.');
          return;
        }

        const data = await response.json();
        if (data.success) {
          this.step = 4;
          if (this.step > this.maxStepReached) this.maxStepReached = this.step;
          this.updateProgress();
        } else {
          if (typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: data.message || 'Failed to send OTP' } }));
          }
        }
      } catch (e) {
        if (typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'An error occurred. Please try again.' } }));
        }
      } finally {
        this.isLoading = false;
      }
    },

    async verifyOtp() {
      if (!this.otpCode || this.otpCode.length < 6) {
        this.errors.otp = 'Please enter a valid 6-digit code';
        return;
      }

      this.isLoading = true;
      try {
        const csrf = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') : '';
        const response = await fetchFn(routes.verifyOtp, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf || '',
          },
          body: JSON.stringify({
            email: this.formData.email,
            otp: this.otpCode,
          }),
        });

        if (response.status === 419) {
          if (typeof alert === 'function') alert('Your session has expired. Please refresh the page and try again.');
          return;
        }

        const data = await response.json();
        if (data.success) {
          this.step = 5;
          if (this.step > this.maxStepReached) this.maxStepReached = this.step;
          this.updateProgress();
        } else {
          this.errors.otp = data.message || 'Invalid OTP';
          if (typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: data.message || 'Invalid OTP' } }));
          }
        }
      } catch (e) {
        this.errors.otp = 'Verification failed. Try again.';
        if (typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Verification failed. Try again.' } }));
        }
      } finally {
        this.isLoading = false;
      }
    },
  };
}
