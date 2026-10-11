import {
  CLASSIFICATIONS,
  MAX_PEDIATRIC_AGE,
  MIN_SENIOR_AGE,
  calculateAge,
  classifyPatientByAge,
  calculateTotalPatients,
  calculateClassificationCounts,
  calculateClassificationStats,
  calculateTrendPercentage,
  createDashboardStore,
} from '../../resources/js/dashboard-logic.js';

describe('Dashboard & Patient Classification Logic', () => {
  describe('calculateAge', () => {
    it('accurately calculates age considering month and day boundaries', () => {
      const refDate = new Date('2026-10-08');
      
      // Exact birthday today
      expect(calculateAge('2016-10-08', refDate)).toBe(10);
      
      // Birthday hasn't happened yet this year
      expect(calculateAge('2016-11-01', refDate)).toBe(9);
      
      // Birthday passed earlier this year
      expect(calculateAge('2016-01-15', refDate)).toBe(10);
      
      // Newborn (< 1 year)
      expect(calculateAge('2026-05-01', refDate)).toBe(0);
    });

    it('returns 0 for empty or invalid date strings', () => {
      expect(calculateAge('')).toBe(0);
      expect(calculateAge(null)).toBe(0);
      expect(calculateAge('invalid-date')).toBe(0);
    });
  });

  describe('classifyPatientByAge', () => {
    const refDate = new Date('2026-10-08');

    it('classifies patients 12 and below as Pediatric', () => {
      // 5 years old
      expect(classifyPatientByAge('2021-01-01', false, refDate)).toBe(CLASSIFICATIONS.PEDIATRIC);
      // Exactly 12 years old
      expect(classifyPatientByAge('2014-10-08', false, refDate)).toBe(CLASSIFICATIONS.PEDIATRIC);
    });

    it('classifies patients between 13 and 59 as Regular Adult', () => {
      // 13 years old
      expect(classifyPatientByAge('2013-10-08', false, refDate)).toBe(CLASSIFICATIONS.ADULT);
      // 35 years old
      expect(classifyPatientByAge('1991-05-12', false, refDate)).toBe(CLASSIFICATIONS.ADULT);
      // 59 years old
      expect(classifyPatientByAge('1967-01-01', false, refDate)).toBe(CLASSIFICATIONS.ADULT);
    });

    it('classifies patients 60 and above as Senior Citizen', () => {
      // Exactly 60 years old
      expect(classifyPatientByAge('1966-10-08', false, refDate)).toBe(CLASSIFICATIONS.SENIOR);
      // 75 years old
      expect(classifyPatientByAge('1951-03-20', false, refDate)).toBe(CLASSIFICATIONS.SENIOR);
    });

    it('retains PWD classification regardless of age', () => {
      // Pediatric age with PWD
      expect(classifyPatientByAge('2020-01-01', true, refDate)).toBe(CLASSIFICATIONS.PWD);
      // Adult age with PWD
      expect(classifyPatientByAge('1995-01-01', true, refDate)).toBe(CLASSIFICATIONS.PWD);
      // Senior age with PWD
      expect(classifyPatientByAge('1950-01-01', true, refDate)).toBe(CLASSIFICATIONS.PWD);
    });
  });

  describe('calculateTotalPatients', () => {
    it('returns total count from an array of patient records', () => {
      const patients = [
        { patient_id: 'P1', classification: 'Pediatric' },
        { patient_id: 'P2', classification: 'Regular Adult' },
        { patient_id: 'P3', classification: 'Senior Citizen' },
      ];
      expect(calculateTotalPatients(patients)).toBe(3);
    });

    it('returns total count from a classification count dictionary', () => {
      const counts = {
        'Pediatric': 15,
        'Regular Adult': 45,
        'Senior Citizen': 20,
        'PWD': 8,
      };
      expect(calculateTotalPatients(counts)).toBe(88);
    });

    it('handles empty or null data safely', () => {
      expect(calculateTotalPatients([])).toBe(0);
      expect(calculateTotalPatients({})).toBe(0);
      expect(calculateTotalPatients(null)).toBe(0);
      expect(calculateTotalPatients(undefined)).toBe(0);
    });
  });

  describe('calculateClassificationCounts', () => {
    it('accurately counts patients per classification category', () => {
      const patients = [
        { classification: 'Pediatric' },
        { classification: 'Pediatric' },
        { classification: 'Regular Adult' },
        { classification: 'Adult' }, // normalizes Adult -> Regular Adult
        { classification: 'Senior Citizen' },
        { classification: 'Senior' }, // normalizes Senior -> Senior Citizen
        { classification: 'PWD' },
      ];

      const counts = calculateClassificationCounts(patients);
      expect(counts).toEqual({
        [CLASSIFICATIONS.PEDIATRIC]: 2,
        [CLASSIFICATIONS.ADULT]: 2,
        [CLASSIFICATIONS.SENIOR]: 2,
        [CLASSIFICATIONS.PWD]: 1,
      });
    });

    it('auto-classifies patients from DOB if classification is missing', () => {
      const refDate = new Date('2026-10-08');
      const patients = [
        { dob: '2020-01-01' }, // Pediatric (6yo)
        { dob: '1990-01-01' }, // Adult (36yo)
        { dob: '1950-01-01' }, // Senior (76yo)
        { dob: '1980-01-01', is_pwd: true }, // PWD
      ];

      // Temporarily mock Date to ensure deterministic age calculation
      jest.useFakeTimers().setSystemTime(refDate);

      const counts = calculateClassificationCounts(patients);
      expect(counts[CLASSIFICATIONS.PEDIATRIC]).toBe(1);
      expect(counts[CLASSIFICATIONS.ADULT]).toBe(1);
      expect(counts[CLASSIFICATIONS.SENIOR]).toBe(1);
      expect(counts[CLASSIFICATIONS.PWD]).toBe(1);

      jest.useRealTimers();
    });

    it('returns all zeros for empty patient list', () => {
      const counts = calculateClassificationCounts([]);
      expect(counts).toEqual({
        [CLASSIFICATIONS.PEDIATRIC]: 0,
        [CLASSIFICATIONS.ADULT]: 0,
        [CLASSIFICATIONS.SENIOR]: 0,
        [CLASSIFICATIONS.PWD]: 0,
      });
    });
  });

  describe('calculateClassificationStats', () => {
    it('computes total, counts, and accurate percentage shares', () => {
      const input = {
        'Pediatric': 20,
        'Regular Adult': 50,
        'Senior Citizen': 20,
        'PWD': 10,
      };

      const stats = calculateClassificationStats(input);
      expect(stats.total).toBe(100);
      expect(stats.counts['Pediatric']).toBe(20);
      expect(stats.percentages['Pediatric']).toBe(20.0);
      expect(stats.percentages['Regular Adult']).toBe(50.0);
      expect(stats.percentages['Senior Citizen']).toBe(20.0);
      expect(stats.percentages['PWD']).toBe(10.0);
    });

    it('handles zero total without dividing by zero', () => {
      const input = {
        'Pediatric': 0,
        'Regular Adult': 0,
        'Senior Citizen': 0,
        'PWD': 0,
      };

      const stats = calculateClassificationStats(input);
      expect(stats.total).toBe(0);
      expect(stats.percentages['Pediatric']).toBe(0);
      expect(stats.percentages['Regular Adult']).toBe(0);
    });

    it('rounds percentage shares to 1 decimal place', () => {
      const input = {
        'Pediatric': 1,
        'Regular Adult': 1,
        'Senior Citizen': 1,
      };

      const stats = calculateClassificationStats(input);
      expect(stats.total).toBe(3);
      expect(stats.percentages['Pediatric']).toBe(33.3);
      expect(stats.percentages['Regular Adult']).toBe(33.3);
      expect(stats.percentages['Senior Citizen']).toBe(33.3);
    });
  });

  describe('calculateTrendPercentage', () => {
    it('calculates positive trend correctly', () => {
      expect(calculateTrendPercentage(120, 100)).toBe(20.0);
    });

    it('calculates negative trend correctly', () => {
      expect(calculateTrendPercentage(80, 100)).toBe(-20.0);
    });

    it('returns 100% when previous is zero and current has volume', () => {
      expect(calculateTrendPercentage(50, 0)).toBe(100);
    });

    it('returns 0% when both are zero', () => {
      expect(calculateTrendPercentage(0, 0)).toBe(0);
    });
  });

  describe('createDashboardStore (Alpine Store)', () => {
    it('initializes with default monthly filter and matching labels', () => {
      const store = createDashboardStore();
      expect(store.timeFilter).toBe('monthly');
      expect(store.filterLabel).toBe('monthly');
      expect(store.loading).toBe(false);
      expect(store.filtered.trendPercentage).toBe(0);
    });

    it('maps all filter options correctly', () => {
      const store = createDashboardStore({ timeFilter: 'today' });
      expect(store.filterLabel).toBe('today');

      store.timeFilter = 'weekly';
      expect(store.filterLabel).toBe('weekly');

      store.timeFilter = 'yearly';
      expect(store.filterLabel).toBe('yearly');

      store.timeFilter = 'all';
      expect(store.filterLabel).toBe('all-time');
    });

    it('refreshes stats via mocked API call and updates filtered values', async () => {
      const mockStats = {
        currentPeriodConsultations: '142',
        currentPeriodConsultationsRaw: 142,
        trendPercentage: 15.4,
        avgWaitTime: 18.5,
        complianceRate: 92.4,
        totalFollowupsNeeded: '8',
      };

      const mockFetch = jest.fn().mockResolvedValue({
        ok: true,
        json: async () => mockStats,
      });

      const store = createDashboardStore({
        statsUrl: '/admin/dashboard/stats-data',
        fetchFn: mockFetch,
      });

      // Mock chart triggers on window
      window.__refreshVolumeChart = jest.fn().mockResolvedValue(true);
      window.__refreshPeakChart = jest.fn().mockResolvedValue(true);
      window.__refreshReturnRate = jest.fn();

      await store.refresh();

      expect(mockFetch).toHaveBeenCalledWith('/admin/dashboard/stats-data?time_filter=monthly');
      expect(store.loading).toBe(false);
      expect(store.filtered.currentPeriodConsultationsRaw).toBe(142);
      expect(store.filtered.trendPercentage).toBe(15.4);
      expect(window.__refreshVolumeChart).toHaveBeenCalledWith('monthly');
      expect(window.__refreshPeakChart).toHaveBeenCalledWith('monthly');
      expect(window.__refreshReturnRate).toHaveBeenCalledWith(92.4);
    });

    it('handles refresh fetch failure gracefully without crashing', async () => {
      const mockFetch = jest.fn().mockRejectedValue(new Error('Network error'));
      const consoleErrorSpy = jest.spyOn(console, 'error').mockImplementation(() => {});

      const store = createDashboardStore({ fetchFn: mockFetch });
      await store.refresh();

      expect(store.loading).toBe(false);
      expect(consoleErrorSpy).toHaveBeenCalled();
      consoleErrorSpy.mockRestore();
    });
  });
});
