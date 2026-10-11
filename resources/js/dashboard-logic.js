/**
 * Dashboard & Patient Classification Logic
 * Rural Health Unit Management Information System (RHU MIS)
 */

export const CLASSIFICATIONS = {
  PEDIATRIC: 'Pediatric',
  ADULT: 'Regular Adult',
  SENIOR: 'Senior Citizen',
  PWD: 'PWD',
};

export const MAX_PEDIATRIC_AGE = 12;
export const MIN_SENIOR_AGE = 60;

/**
 * Calculates accurate age based on Date of Birth string (YYYY-MM-DD)
 * @param {string|Date} dob 
 * @param {Date} [referenceDate=new Date()]
 * @returns {number}
 */
export function calculateAge(dob, referenceDate = new Date()) {
  if (!dob) return 0;
  const birthDate = new Date(dob);
  if (isNaN(birthDate.getTime())) return 0;

  let age = referenceDate.getFullYear() - birthDate.getFullYear();
  const monthDiff = referenceDate.getMonth() - birthDate.getMonth();
  if (monthDiff < 0 || (monthDiff === 0 && referenceDate.getDate() < birthDate.getDate())) {
    age--;
  }
  return Math.max(0, age);
}

/**
 * Classifies a patient based on age and PWD status.
 * Note: PWD is not age-based and takes precedence over DOB age grouping.
 * @param {string|Date} dob 
 * @param {boolean} isPWD 
 * @param {Date} [referenceDate]
 * @returns {string}
 */
export function classifyPatientByAge(dob, isPWD = false, referenceDate) {
  if (isPWD) {
    return CLASSIFICATIONS.PWD;
  }
  const age = calculateAge(dob, referenceDate);
  if (age <= MAX_PEDIATRIC_AGE) {
    return CLASSIFICATIONS.PEDIATRIC;
  }
  if (age >= MIN_SENIOR_AGE) {
    return CLASSIFICATIONS.SENIOR;
  }
  return CLASSIFICATIONS.ADULT;
}

/**
 * Calculates total patient count.
 * Accepts either an array of patient records or an object mapping classification -> count.
 * @param {Array<Object>|Record<string, number>} data 
 * @returns {number}
 */
export function calculateTotalPatients(data) {
  if (!data) return 0;
  if (Array.isArray(data)) {
    return data.length;
  }
  if (typeof data === 'object') {
    return Object.values(data).reduce((acc, curr) => acc + (Number(curr) || 0), 0);
  }
  return 0;
}

/**
 * Counts patients grouped by classification category.
 * @param {Array<{classification?: string, dob?: string, is_pwd?: boolean}>} patients 
 * @returns {Record<string, number>}
 */
export function calculateClassificationCounts(patients = []) {
  const counts = {
    [CLASSIFICATIONS.PEDIATRIC]: 0,
    [CLASSIFICATIONS.ADULT]: 0,
    [CLASSIFICATIONS.SENIOR]: 0,
    [CLASSIFICATIONS.PWD]: 0,
  };

  if (!Array.isArray(patients)) return counts;

  for (const patient of patients) {
    let cls = patient.classification;
    if (!cls && patient.dob) {
      cls = classifyPatientByAge(patient.dob, Boolean(patient.is_pwd));
    } else if (cls === 'Adult') {
      cls = CLASSIFICATIONS.ADULT;
    } else if (cls === 'Senior') {
      cls = CLASSIFICATIONS.SENIOR;
    }

    if (cls && counts.hasOwnProperty(cls)) {
      counts[cls]++;
    } else if (cls) {
      counts[cls] = (counts[cls] || 0) + 1;
    }
  }

  return counts;
}

/**
 * Computes full classification statistics: counts, total, and percentage share.
 * @param {Array<Object>|Record<string, number>} data 
 * @returns {{total: number, counts: Record<string, number>, percentages: Record<string, number>}}
 */
export function calculateClassificationStats(data) {
  const counts = Array.isArray(data) ? calculateClassificationCounts(data) : { ...data };
  const total = calculateTotalPatients(counts);
  const percentages = {};

  for (const [key, count] of Object.entries(counts)) {
    const val = Number(count) || 0;
    percentages[key] = total > 0 ? Math.round((val / total) * 1000) / 10 : 0;
  }

  return {
    total,
    counts,
    percentages,
  };
}

/**
 * Calculates percentage trend between previous and current period consultations.
 * @param {number} current 
 * @param {number} previous 
 * @returns {number}
 */
export function calculateTrendPercentage(current = 0, previous = 0) {
  const curr = Number(current) || 0;
  const prev = Number(previous) || 0;

  if (prev > 0) {
    return Math.round(((curr - prev) / prev) * 1000) / 10;
  }
  if (curr > 0) {
    return 100;
  }
  return 0;
}

/**
 * Factory for creating the Alpine dashboard store object
 * @param {Object} options 
 * @returns {Object}
 */
export function createDashboardStore(options = {}) {
  const {
    timeFilter = 'monthly',
    statsUrl = '/admin/dashboard/stats-data',
    initialFiltered = {},
    fetchFn = global.fetch,
  } = options;

  return {
    timeFilter,
    loading: false,
    filtered: {
      currentPeriodConsultations: '0',
      currentPeriodConsultationsRaw: 0,
      trendPercentage: 0,
      avgWaitTime: 0,
      complianceRate: 0,
      totalFollowupsNeeded: '0',
      ...initialFiltered,
    },
    get filterLabel() {
      const labels = {
        today: 'today',
        weekly: 'weekly',
        monthly: 'monthly',
        yearly: 'yearly',
        all: 'all-time',
      };
      return labels[this.timeFilter] || this.timeFilter;
    },
    async refresh() {
      this.loading = true;
      try {
        const url = `${statsUrl}?time_filter=${this.timeFilter}`;
        const res = await fetchFn(url);
        const stats = await res.json();
        this.filtered = { ...this.filtered, ...stats };
        if (typeof window !== 'undefined' && typeof window.__refreshVolumeChart === 'function') {
          await window.__refreshVolumeChart(this.timeFilter);
        }
        if (typeof window !== 'undefined' && typeof window.__refreshPeakChart === 'function') {
          await window.__refreshPeakChart(this.timeFilter);
        }
        if (typeof window !== 'undefined' && typeof window.__refreshReturnRate === 'function') {
          window.__refreshReturnRate(this.filtered.complianceRate);
        }
      } catch (err) {
        console.error('Dashboard refresh error:', err);
      } finally {
        this.loading = false;
      }
    },
  };
}
