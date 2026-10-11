@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => 'Select a date...',
    'highlightDates' => [],
    'class' => '',
    'align' => 'left', // 'left', 'right', 'auto'
    'size' => 'md',
])

@php
    $id = $id ?? ($name ?? 'datepicker_' . uniqid());
    $initialValue = $value !== null ? (string)$value : '';
@endphp

<div class="relative custom-datepicker-container inline-block w-full {{ $class }}"
     x-data="{
        open: false,
        selected: @js($initialValue),
        viewYear: new Date().getFullYear(),
        viewMonth: new Date().getMonth(),
        highlightDates: @js($highlightDates),
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        shortMonthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

        init() {
            @if($attributes->has('x-model'))
            const modelName = @js($attributes->get('x-model'));
            if (this[modelName] !== undefined && this[modelName] !== null) {
                this.selected = String(this[modelName]);
            }
            this.$watch(modelName, (newVal) => {
                const val = (newVal !== undefined && newVal !== null) ? String(newVal) : '';
                if (val !== this.selected) {
                    this.selected = val;
                    this.syncCalendarView();
                }
            });
            @endif

            this.syncCalendarView();
        },

        syncCalendarView() {
            if (this.selected && this.selected.includes('-')) {
                const parts = this.selected.split('-');
                if (parts.length === 3) {
                    const y = parseInt(parts[0], 10);
                    const m = parseInt(parts[1], 10) - 1;
                    if (!isNaN(y) && !isNaN(m)) {
                        this.viewYear = y;
                        this.viewMonth = m;
                    }
                }
            } else {
                const now = new Date();
                this.viewYear = now.getFullYear();
                this.viewMonth = now.getMonth();
            }
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.syncCalendarView();
            }
        },

        close() {
            this.open = false;
        },

        get formattedLabel() {
            if (!this.selected || !this.selected.includes('-')) return '';
            const parts = this.selected.split('-');
            if (parts.length !== 3) return this.selected;
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1;
            const d = parseInt(parts[2], 10);
            if (isNaN(y) || isNaN(m) || isNaN(d)) return this.selected;
            return `${this.shortMonthNames[m]} ${String(d).padStart(2, '0')}, ${y}`;
        },

        get monthYearLabel() {
            return `${this.monthNames[this.viewMonth]} ${this.viewYear}`;
        },

        prevMonth() {
            if (this.viewMonth === 0) {
                this.viewMonth = 11;
                this.viewYear--;
            } else {
                this.viewMonth--;
            }
        },

        nextMonth() {
            if (this.viewMonth === 11) {
                this.viewMonth = 0;
                this.viewYear++;
            } else {
                this.viewMonth++;
            }
        },

        selectDate(dateKey) {
            this.selected = dateKey;
            this.updateModel();
            this.close();
        },

        clearDate() {
            this.selected = '';
            this.updateModel();
            this.close();
        },

        goToToday() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const d = String(now.getDate()).padStart(2, '0');
            const todayKey = `${y}-${m}-${d}`;
            this.viewYear = y;
            this.viewMonth = now.getMonth();
            this.selectDate(todayKey);
        },

        updateModel() {
            @if($attributes->has('x-model'))
            const modelName = @js($attributes->get('x-model'));
            try {
                this[modelName] = this.selected;
            } catch(e) {}
            @endif

            this.$nextTick(() => {
                if (this.$refs.hiddenInput) {
                    this.$refs.hiddenInput.value = this.selected;
                    this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.$dispatch('input', this.selected);
                this.$dispatch('change', this.selected);
            });
        },

        get calendarCells() {
            const cells = [];
            const year = this.viewYear;
            const month = this.viewMonth;

            const firstDayOfWeek = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();

            const today = new Date();
            const todayKey = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

            // Previous month trailing days
            for (let i = firstDayOfWeek - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                const prevM = month === 0 ? 12 : month;
                const prevY = month === 0 ? year - 1 : year;
                const dateKey = `${prevY}-${String(prevM).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                cells.push({
                    day,
                    dateKey,
                    isCurrentMonth: false,
                    isToday: dateKey === todayKey,
                    isSelected: false,
                    hasIndicator: false
                });
            }

            // Current month days
            for (let day = 1; day <= daysInMonth; day++) {
                const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                cells.push({
                    day,
                    dateKey,
                    isCurrentMonth: true,
                    isToday: dateKey === todayKey,
                    isSelected: this.selected === dateKey,
                    hasIndicator: Array.isArray(this.highlightDates) && this.highlightDates.includes(dateKey)
                });
            }

            // Next month leading days (complete 42 cells grid)
            const remaining = 42 - cells.length;
            for (let day = 1; day <= remaining; day++) {
                const nextM = month === 11 ? 1 : month + 2;
                const nextY = month === 11 ? year + 1 : year;
                const dateKey = `${nextY}-${String(nextM).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                cells.push({
                    day,
                    dateKey,
                    isCurrentMonth: false,
                    isToday: dateKey === todayKey,
                    isSelected: false,
                    hasIndicator: false
                });
            }

            return cells;
        }
     }"
     @click.outside="close()"
     @keydown.escape.window="close()">

    {{-- Hidden Input for form / DOM identification --}}
    <input type="hidden"
           x-ref="hiddenInput"
           id="{{ $id }}"
           @if($name) name="{{ $name }}" @endif
           :value="selected">

    {{-- Styled Interactive Trigger Button --}}
    <button type="button"
            @click="toggle()"
            class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 hover:border-emerald-500/80 dark:hover:border-emerald-500/80 rounded-xl text-xs text-left transition shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 group cursor-pointer"
            :class="open ? 'ring-2 ring-emerald-500/30 border-emerald-500 dark:border-emerald-500' : ''">
        
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <div class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 transition-colors group-hover:bg-teal-600 group-hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <span class="block truncate font-medium"
                      :class="selected ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-400 dark:text-gray-500'"
                      x-text="formattedLabel || @js($placeholder)">
                </span>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0 ml-1">
            {{-- Clear Button --}}
            <template x-if="selected">
                <span @click.stop="clearDate()"
                      class="p-1 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                      title="Clear date">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </span>
            </template>

            {{-- Chevron / Indicator --}}
            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 transition-transform duration-200"
                 :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    {{-- Custom Elevated Calendar Popover --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
         style="display: none;"
         class="absolute z-50 mt-2 w-full min-w-[280px] bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl shadow-slate-900/15 dark:shadow-slate-950/60 p-3.5 backdrop-blur-md focus:outline-none"
         :class="@js($align === 'right') ? 'right-0 left-auto' : 'left-0'">

        {{-- Month Navigation Header --}}
        <div class="flex items-center justify-between mb-3 px-1">
            <button type="button"
                    @click="prevMonth()"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    title="Previous Month">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <span class="text-xs font-bold text-slate-900 dark:text-white tracking-wide" x-text="monthYearLabel"></span>

            <button type="button"
                    @click="nextMonth()"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    title="Next Month">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Weekday Names (Su Mo Tu We Th Fr Sa) --}}
        <div class="grid grid-cols-7 gap-1 mb-1 text-center">
            <template x-for="dayName in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']" :key="dayName">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider py-0.5" x-text="dayName"></span>
            </template>
        </div>

        {{-- Days Grid (42 cells) --}}
        <div class="grid grid-cols-7 gap-1 text-center">
            <template x-for="cell in calendarCells" :key="cell.dateKey">
                <button type="button"
                        @click="selectDate(cell.dateKey)"
                        :disabled="!cell.isCurrentMonth"
                        class="h-8 rounded-xl text-xs font-medium flex flex-col items-center justify-center relative transition cursor-pointer select-none"
                        :class="{
                            'opacity-25 cursor-default pointer-events-none': !cell.isCurrentMonth,
                            'bg-emerald-600 text-white font-bold shadow-xs': cell.isSelected,
                            'hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 text-slate-700 dark:text-slate-200': cell.isCurrentMonth && !cell.isSelected,
                            'border border-emerald-500/50 dark:border-emerald-400/50 font-semibold': cell.isToday && !cell.isSelected
                        }">
                    <span x-text="cell.day"></span>
                    {{-- Dot indicator for days with announcements --}}
                    <template x-if="cell.hasIndicator && !cell.isSelected">
                        <span class="w-1 h-1 rounded-full bg-teal-500 dark:bg-teal-400 absolute bottom-1"></span>
                    </template>
                </button>
            </template>
        </div>

        {{-- Quick Actions Footer: Today / Clear --}}
        <div class="flex items-center justify-between pt-3 mt-2.5 border-t border-slate-100 dark:border-slate-800 text-[11px]">
            <button type="button"
                    @click="goToToday()"
                    class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold flex items-center gap-1 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Today</span>
            </button>
            <button type="button"
                    @click="clearDate()"
                    class="text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition font-medium cursor-pointer">
                Clear
            </button>
        </div>
    </div>
</div>
