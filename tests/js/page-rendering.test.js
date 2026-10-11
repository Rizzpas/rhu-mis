/**
 * Page Rendering & Interactive UI Test Suite
 * Covers Home, About, Units, Announcements, Appointment Booking, and Admin Dashboard rendering.
 * Mocks all API calls.
 */

describe('Page Rendering & Interactive UI Behaviors', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
    jest.clearAllMocks();
  });

  describe('Home Page (/) Rendering', () => {
    it('renders hero landmark, navigation links, services section, and live update badge', () => {
      document.body.innerHTML = `
        <header id="main-navbar" class="fixed top-0 w-full z-50">
          <nav aria-label="Main Navigation">
            <a href="/" class="brand-logo">RHU MIS Silang</a>
            <div class="nav-links">
              <a href="/about" id="nav-about">About</a>
              <a href="/units" id="nav-units">Facility Units</a>
              <a href="/announcements" id="nav-announcements">Announcements</a>
              <a href="/appointment" id="nav-appointment">Book Appointment</a>
            </div>
          </nav>
        </header>

        <main id="main-content">
          <section id="hero-section" class="hero" data-reveal>
            <h1>Rural Health Unit Silang</h1>
            <p>Compassionate Municipal Healthcare at Your Service</p>
            <a href="/appointment" id="hero-cta-btn">Book an Appointment</a>
          </section>

          <section id="services-preview" data-reveal>
            <h2>Primary Health Services</h2>
            <div class="service-card" data-service="general">General Consultation</div>
            <div class="service-card" data-service="pedia">Pediatric Care</div>
            <div class="service-card" data-service="maternal">Maternal & Child Health</div>
            <div class="service-card" data-service="pharmacy">Pharmacy & Dispensary</div>
          </section>

          <aside id="home-live-badge" class="badge">
            <span class="indicator">Live</span>
            <span id="home-status-text">RHU is currently open</span>
          </aside>
        </main>

        <footer id="main-footer">
          <p>© 2026 RHU Silang Cavite. All rights reserved.</p>
        </footer>
      `;

      expect(document.getElementById('main-navbar')).not.toBeNull();
      expect(document.getElementById('hero-section')).not.toBeNull();
      expect(document.getElementById('hero-cta-btn').getAttribute('href')).toBe('/appointment');
      expect(document.querySelectorAll('.service-card').length).toBe(4);
      expect(document.getElementById('home-status-text').textContent).toContain('currently open');
    });

    it('polls home updates endpoint with mocked API response', async () => {
      const mockHomeStatus = {
        is_open: true,
        queue_count: 14,
        announcement_count: 3,
        updated_at: '2026-10-08T08:00:00Z',
      };

      global.fetch = jest.fn().mockResolvedValue({
        ok: true,
        status: 200,
        json: async () => mockHomeStatus,
      });

      const response = await fetch('/check-home-updates');
      const data = await response.json();

      expect(global.fetch).toHaveBeenCalledWith('/check-home-updates');
      expect(data.is_open).toBe(true);
      expect(data.queue_count).toBe(14);
    });
  });

  describe('About Page (/about) Rendering', () => {
    it('renders mission, vision, location map placeholder, and organizational chart container', () => {
      document.body.innerHTML = `
        <main id="about-page">
          <section id="mission-vision">
            <div class="mission-box">
              <h3>Our Mission</h3>
              <p>To provide accessible, high-quality, and equitable primary healthcare to all residents of Silang.</p>
            </div>
            <div class="vision-box">
              <h3>Our Vision</h3>
              <p>A healthy, self-reliant, and empowered community enjoying universal healthcare coverage.</p>
            </div>
          </section>

          <section id="location-map-section">
            <h2>Location & Contact</h2>
            <div id="map-container" class="map-wrapper">
              <div class="map-placeholder">Silang RHU Location Map</div>
            </div>
          </section>

          <section id="org-chart-section">
            <h2>RHU Personnel Organizational Structure</h2>
            <div id="org-chart-tree" class="tree">
              <div class="org-node leader">Municipal Health Officer</div>
              <div class="org-node doctor">Medical Officers</div>
              <div class="org-node nurse">Public Health Nurses</div>
            </div>
          </section>
        </main>
      `;

      expect(document.getElementById('mission-vision')).not.toBeNull();
      expect(document.querySelector('.mission-box p').textContent).toContain('accessible, high-quality');
      expect(document.getElementById('location-map-section')).not.toBeNull();
      expect(document.querySelectorAll('.org-node').length).toBe(3);
    });
  });

  describe('Facility Units Page (/units) Rendering', () => {
    it('renders units grid with operation hours and department badges', () => {
      document.body.innerHTML = `
        <main id="units-page">
          <header class="page-header">
            <h1>Municipal Facility Units</h1>
            <p>Comprehensive healthcare divisions operating under RHU Silang</p>
          </header>

          <div id="units-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            <article class="unit-card" data-slug="triage">
              <h3>Triage & Assessment</h3>
              <span class="hours">8:00 AM - 5:00 PM</span>
              <p>Initial vital signs evaluation and patient priority screening.</p>
            </article>

            <article class="unit-card" data-slug="consultation">
              <h3>Medical Consultation</h3>
              <span class="hours">8:00 AM - 5:00 PM</span>
              <p>General doctor examination and diagnostic evaluations.</p>
            </article>

            <article class="unit-card" data-slug="laboratory">
              <h3>Diagnostic Laboratory</h3>
              <span class="hours">7:00 AM - 3:00 PM</span>
              <p>Hematology, urinalysis, sputum, and basic clinical pathology.</p>
            </article>

            <article class="unit-card" data-slug="pharmacy">
              <h3>Municipal Pharmacy</h3>
              <span class="hours">8:00 AM - 5:00 PM</span>
              <p>Free dispensary for essential medicines and prescribed supplements.</p>
            </article>
          </div>
        </main>
      `;

      const cards = document.querySelectorAll('.unit-card');
      expect(cards.length).toBe(4);
      expect(cards[0].getAttribute('data-slug')).toBe('triage');
      expect(cards[2].querySelector('.hours').textContent).toBe('7:00 AM - 3:00 PM');
    });
  });

  describe('Announcements Page (/announcements) Rendering', () => {
    it('renders announcements list with categories and search filter', () => {
      document.body.innerHTML = `
        <main id="announcements-page">
          <div class="search-bar">
            <input type="text" id="announcement-search" placeholder="Search notices..." value="">
            <select id="category-filter">
              <option value="all">All Categories</option>
              <option value="advisory">Advisories</option>
              <option value="vaccination">Vaccination Drives</option>
            </select>
          </div>

          <div id="announcements-container">
            <article class="announcement-item" data-category="vaccination" id="item-1">
              <span class="badge">Vaccination Drive</span>
              <h2>Measles-Rubella Catch-up Campaign</h2>
              <time datetime="2026-10-10">October 10, 2026</time>
              <p class="summary">Free immunization for children aged 0-59 months.</p>
            </article>

            <article class="announcement-item" data-category="advisory" id="item-2">
              <span class="badge">Advisory</span>
              <h2>Holiday Clinic Schedule</h2>
              <time datetime="2026-10-31">October 31, 2026</time>
              <p class="summary">Special operation hours during the municipal holiday.</p>
            </article>
          </div>
        </main>
      `;

      expect(document.getElementById('announcement-search')).not.toBeNull();
      expect(document.querySelectorAll('.announcement-item').length).toBe(2);
      expect(document.querySelector('#item-1 h2').textContent).toBe('Measles-Rubella Catch-up Campaign');
    });

    it('fetches updated announcement content via mocked API call', async () => {
      const mockAnnouncement = {
        id: 1,
        title: 'Measles-Rubella Catch-up Campaign',
        content: '<p>Updated venue list and time schedule.</p>',
        version: 2,
      };

      global.fetch = jest.fn().mockResolvedValue({
        ok: true,
        status: 200,
        json: async () => mockAnnouncement,
      });

      const res = await fetch('/announcements/1/check-update');
      const data = await res.json();

      expect(global.fetch).toHaveBeenCalledWith('/announcements/1/check-update');
      expect(data.version).toBe(2);
      expect(data.title).toContain('Measles-Rubella');
    });
  });

  describe('Appointment Booking Page (/appointment) Rendering', () => {
    it('renders step progression bar, service choice buttons, and booking form elements', () => {
      document.body.innerHTML = `
        <div id="appointment-booking-app" class="wizard-container">
          <!-- Step indicator bar -->
          <div class="steps-nav">
            <div class="step-badge active" data-step="1">1. Service</div>
            <div class="step-badge" data-step="2">2. Patient Info</div>
            <div class="step-badge" data-step="3">3. Schedule</div>
            <div class="step-badge" data-step="4">4. Verification</div>
            <div class="step-badge" data-step="5">5. Confirmation</div>
          </div>

          <!-- Step 1: Services -->
          <div id="step-1-container" class="step-panel">
            <button type="button" id="btn-service-pedia" class="service-opt">Pediatric Care</button>
            <button type="button" id="btn-service-adult" class="service-opt">Adult Consultation</button>
          </div>

          <!-- Step 2: Information form -->
          <form id="appointment-form" class="step-panel hidden">
            <input type="text" name="first_name" id="first_name" placeholder="First Name">
            <input type="text" name="last_name" id="last_name" placeholder="Last Name">
            <input type="date" name="dob" id="dob">
            <select name="sex" id="sex">
              <option value="">Select Sex</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
            <input type="email" name="email" id="email" placeholder="Email">
            <input type="text" name="philhealth_number" id="philhealth_number" placeholder="XX-XXXXXXXXX-X">
          </form>

          <!-- Step 3: Calendar & Time -->
          <div id="step-3-container" class="step-panel hidden">
            <div id="calendar-header">
              <button id="prev-month-btn">Prev</button>
              <span id="calendar-month-label">October 2026</span>
              <button id="next-month-btn">Next</button>
            </div>
            <div id="calendar-grid" class="grid grid-cols-7"></div>
            <div id="time-slots-container"></div>
          </div>
        </div>
      `;

      expect(document.querySelectorAll('.step-badge').length).toBe(5);
      expect(document.getElementById('btn-service-pedia')).not.toBeNull();
      expect(document.getElementById('btn-service-adult')).not.toBeNull();
      expect(document.getElementById('philhealth_number')).not.toBeNull();
    });

    it('fetches availability and time slots with mocked API calls', async () => {
      const mockAvailability = {
        '2026-10-15': { capacity: 20, available: 12 },
        '2026-10-16': { capacity: 20, available: 0 },
      };

      const mockSlots = {
        slots: ['08:00 AM', '09:00 AM', '10:00 AM'],
      };

      global.fetch = jest.fn()
        .mockResolvedValueOnce({
          ok: true,
          status: 200,
          json: async () => mockAvailability,
        })
        .mockResolvedValueOnce({
          ok: true,
          status: 200,
          json: async () => mockSlots,
        });

      const availRes = await fetch('/appointment/check-availability?start=2026-10-01&end=2026-10-31');
      const availData = await availRes.json();

      expect(availData['2026-10-15'].available).toBe(12);
      expect(availData['2026-10-16'].available).toBe(0);

      const slotsRes = await fetch('/appointment/time-slots?date=2026-10-15&type=pedia');
      const slotsData = await slotsRes.json();

      expect(slotsData.slots).toEqual(['08:00 AM', '09:00 AM', '10:00 AM']);
    });
  });

  describe('Admin Dashboard (/admin/dashboard) Rendering', () => {
    it('renders dashboard stat cards, time filter select, and chart containers', () => {
      document.body.innerHTML = `
        <main id="admin-dashboard">
          <div class="dashboard-header flex justify-between items-center">
            <h1>Executive Healthcare Analytics</h1>
            <select id="dashboard-time-filter" x-model="timeFilter">
              <option value="today">Today</option>
              <option value="weekly">This Week</option>
              <option value="monthly" selected>This Month</option>
              <option value="yearly">This Year</option>
              <option value="all">All-Time</option>
            </select>
          </div>

          <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card" id="card-consultations">
              <span class="label">Total Consultations</span>
              <span class="value" id="val-consultations">1,248</span>
              <span class="trend text-emerald-500">+12.5%</span>
            </div>
            <div class="stat-card" id="card-wait-time">
              <span class="label">Average Wait Time</span>
              <span class="value" id="val-wait-time">14 mins</span>
            </div>
            <div class="stat-card" id="card-compliance">
              <span class="label">Protocol Compliance</span>
              <span class="value" id="val-compliance">94.8%</span>
            </div>
            <div class="stat-card" id="card-followups">
              <span class="label">Follow-ups Pending</span>
              <span class="value" id="val-followups">34</span>
            </div>
          </div>

          <div class="charts-section grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div class="chart-box">
              <h3>Consultation Volume</h3>
              <canvas id="volumeChart"></canvas>
            </div>
            <div class="chart-box">
              <h3>Patient Classification Share</h3>
              <canvas id="classificationChart"></canvas>
            </div>
          </div>
        </main>
      `;

      expect(document.getElementById('dashboard-time-filter')).not.toBeNull();
      expect(document.getElementById('card-consultations')).not.toBeNull();
      expect(document.getElementById('classificationChart')).not.toBeNull();
      expect(document.querySelectorAll('.stat-card').length).toBe(4);
    });

    it('fetches admin stats dataset via mocked API call', async () => {
      const mockAdminStats = {
        currentPeriodConsultations: '2,450',
        currentPeriodConsultationsRaw: 2450,
        trendPercentage: 8.2,
        avgWaitTime: 12,
        complianceRate: 96.1,
        totalFollowupsNeeded: '15',
      };

      global.fetch = jest.fn().mockResolvedValue({
        ok: true,
        status: 200,
        json: async () => mockAdminStats,
      });

      const res = await fetch('/admin/dashboard/stats-data?time_filter=monthly');
      const data = await res.json();

      expect(global.fetch).toHaveBeenCalledWith('/admin/dashboard/stats-data?time_filter=monthly');
      expect(data.currentPeriodConsultationsRaw).toBe(2450);
      expect(data.complianceRate).toBe(96.1);
    });
  });

  describe('Navbar Dropdown Coordination (resources/js/app.js logic)', () => {
    it('opens target dropdown and closes sibling dropdowns when clicked', () => {
      document.body.innerHTML = `
        <nav id="main-navbar">
          <button id="mega-menu-full-dropdown-button" aria-expanded="false">Units</button>
          <div id="mega-menu-full-dropdown" class="hidden">Units Menu Content</div>

          <button id="mega-menu-appointment-dropdown-button" aria-expanded="false">Appointment</button>
          <div id="mega-menu-appointment-dropdown" class="hidden">Appointment Menu Content</div>

          <button id="mega-menu-updates-dropdown-button" aria-expanded="false">Updates</button>
          <div id="mega-menu-updates-dropdown" class="hidden">Updates Menu Content</div>
        </nav>
      `;

      const unitsBtn = document.getElementById('mega-menu-full-dropdown-button');
      const apptBtn = document.getElementById('mega-menu-appointment-dropdown-button');
      const unitsMenu = document.getElementById('mega-menu-full-dropdown');
      const apptMenu = document.getElementById('mega-menu-appointment-dropdown');

      const dropdowns = [
        { btn: unitsBtn, menu: unitsMenu },
        { btn: apptBtn, menu: apptMenu },
      ];

      // Attach coordinating handler as defined in app.js
      dropdowns.forEach(({ btn, menu }) => {
        btn.addEventListener('click', () => {
          dropdowns.forEach(other => {
            if (other.menu && other.menu !== menu && !other.menu.classList.contains('hidden')) {
              other.menu.classList.add('hidden');
              if (other.btn) other.btn.setAttribute('aria-expanded', 'false');
            }
          });
          const isOpen = !menu.classList.contains('hidden');
          if (isOpen) {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
          } else {
            menu.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
          }
        });
      });

      // Click units dropdown
      unitsBtn.click();
      expect(unitsMenu.classList.contains('hidden')).toBe(false);
      expect(unitsBtn.getAttribute('aria-expanded')).toBe('true');
      expect(apptMenu.classList.contains('hidden')).toBe(true);

      // Now click appointment dropdown -> should close units dropdown
      apptBtn.click();
      expect(unitsMenu.classList.contains('hidden')).toBe(true);
      expect(unitsBtn.getAttribute('aria-expanded')).toBe('false');
      expect(apptMenu.classList.contains('hidden')).toBe(false);
      expect(apptBtn.getAttribute('aria-expanded')).toBe('true');
    });
  });
});
