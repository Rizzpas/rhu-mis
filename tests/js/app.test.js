jest.mock('../../resources/js/bootstrap', () => ({}), { virtual: false });
jest.mock('flowbite', () => ({}), { virtual: true });

describe('resources/js/app.js Interactive Features', () => {
  beforeAll(() => {
    require('../../resources/js/app.js');
  });

  beforeEach(() => {
    document.body.innerHTML = `
      <nav id="main-navbar">
        <button id="mega-menu-full-dropdown-button">Units</button>
        <div id="mega-menu-full-dropdown" class="hidden"></div>
        <button id="mega-menu-appointment-dropdown-button">Appointment</button>
        <div id="mega-menu-appointment-dropdown" class="hidden"></div>
        <button id="mega-menu-updates-dropdown-button">Updates</button>
        <div id="mega-menu-updates-dropdown" class="hidden"></div>
      </nav>
      <div id="announcements-container">
        <div data-reveal class="item">Announcement 1</div>
      </div>
      <div id="schedule-container">
        <div data-reveal class="item">Schedule 1</div>
      </div>
      <select id="sample-select" name="sample_field">
        <option value="">Select an option</option>
        <option value="opt1">Option 1</option>
        <option value="opt2">Option 2</option>
        <option value="opt3">Option 3</option>
      </select>
    `;
    document.dispatchEvent(new Event('DOMContentLoaded'));
  });

  it('initializes Alpine, navbar listeners, and custom selects on DOMContentLoaded', () => {
    expect(window.Alpine).toBeDefined();

    // Verify custom select enhancement
    const customSelect = document.querySelector('.custom-select-container');
    expect(customSelect).not.toBeNull();

    const triggerBtn = customSelect.querySelector('button');
    expect(triggerBtn).not.toBeNull();
    expect(triggerBtn.textContent).toContain('Select an option');
  });

  it('interacts with custom select: opens dropdown, selects an option, and updates native select', () => {
    const customSelect = document.querySelector('.custom-select-container');
    const triggerBtn = customSelect.querySelector('button');
    const nativeSelect = document.getElementById('sample-select');
    const menu = customSelect.querySelector('.max-h-60');

    // Click trigger button to open
    triggerBtn.click();
    expect(menu.classList.contains('hidden')).toBe(false);

    // Find option elements
    const optionEls = customSelect.querySelectorAll('.custom-select-option');
    expect(optionEls.length).toBe(4);

    // Click Option 2
    optionEls[2].click();

    expect(nativeSelect.value).toBe('opt2');
    expect(triggerBtn.textContent).toContain('Option 2');
    expect(menu.classList.contains('hidden')).toBe(true);
  });

  it('closes custom select on Escape key press', () => {
    const customSelect = document.querySelector('.custom-select-container');
    const triggerBtn = customSelect.querySelector('button');
    const menu = customSelect.querySelector('.max-h-60');

    // Open
    triggerBtn.click();
    expect(menu.classList.contains('hidden')).toBe(false);

    // Press Escape
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
    expect(menu.classList.contains('hidden')).toBe(true);
  });

  it('listens to Echo QueueUpdated events and dispatches refresh and toast events', () => {
    const mockChannel = {
      listen: jest.fn(),
    };
    window.Echo = {
      channel: jest.fn().mockReturnValue(mockChannel),
    };

    const toastListener = jest.fn();
    const refreshListener = jest.fn();
    window.addEventListener('add-toast', toastListener);
    window.addEventListener('refresh-dynamic-blocks', refreshListener);

    // Re-trigger DOMContentLoaded with Echo present
    document.dispatchEvent(new Event('DOMContentLoaded'));

    expect(window.Echo.channel).toHaveBeenCalledWith('queues');
    expect(mockChannel.listen).toHaveBeenCalledWith('QueueUpdated', expect.any(Function));

    // Simulate event callback
    const queueCallback = mockChannel.listen.mock.calls[0][1];
    queueCallback({ message: 'Patient called to Triage', patient_id: 101 });

    expect(refreshListener).toHaveBeenCalled();
    expect(toastListener).toHaveBeenCalled();
  });
});
