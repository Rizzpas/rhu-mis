@props([
    'name' => null,
    'id' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'size' => 'md', // 'sm', 'md', 'lg'
    'class' => '',
    'containerClass' => '',
    'disabled' => false,
    'form' => null,
    'dropUp' => false,
    'searchable' => false,
    'searchPlaceholder' => 'Search options...',
])

@php
    $id = $id ?? ($name ?? 'select_' . uniqid());
    $alpineDisabled = $attributes->get(':disabled') ?? $attributes->get('x-bind:disabled');
    
    // Normalize options into array of ['value' => ..., 'label' => ..., 'sublabel' => ..., 'badge' => ..., 'badgeClass' => ..., 'icon' => ..., 'iconColor' => ...]
    $normalizedOptions = [];
    foreach ($options as $key => $opt) {
        if (is_array($opt)) {
            $normalizedOptions[] = [
                'value' => (string)($opt['value'] ?? $key),
                'label' => (string)($opt['label'] ?? $opt['value'] ?? $key),
                'sublabel' => (string)($opt['sublabel'] ?? ''),
                'badge' => (string)($opt['badge'] ?? ''),
                'badgeClass' => (string)($opt['badgeClass'] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'),
                'icon' => (string)($opt['icon'] ?? ''),
                'iconColor' => (string)($opt['iconColor'] ?? 'text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-400'),
            ];
        } else {
            $normalizedOptions[] = [
                'value' => (string)$key,
                'label' => (string)$opt,
                'sublabel' => '',
                'badge' => '',
                'badgeClass' => '',
                'icon' => '',
                'iconColor' => '',
            ];
        }
    }

    $initialValue = $value !== null ? (string)$value : '';
    if ($initialValue === '' && !empty($normalizedOptions) && !$placeholder) {
        $initialValue = $normalizedOptions[0]['value'];
    }
    
    // Size variants
    $sizeClasses = [
        'sm' => 'py-1.5 pl-3 pr-8 text-xs font-normal',
        'md' => 'py-2.5 pl-3.5 pr-9 text-xs sm:text-xs font-normal',
        'lg' => 'py-3 pl-4 pr-10 text-sm font-normal',
    ][$size] ?? 'py-2.5 pl-3.5 pr-9 text-xs sm:text-xs font-normal';

    $chevronSize = [
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-4 h-4',
    ][$size] ?? 'w-4 h-4';

    $chevronRight = [
        'sm' => 'right-2.5',
        'md' => 'right-3',
        'lg' => 'right-3.5',
    ][$size] ?? 'right-3';
@endphp

<div class="relative custom-dropdown-container inline-block w-full {{ $containerClass }}"
     :class="{ 'z-50': open }"
     x-data="{
        open: false,
        selected: @js($initialValue),
        options: @js($normalizedOptions),
        highlightedIndex: -1,
        search: '',
        searchable: @js((bool)$searchable),
        dropUp: @js((bool)$dropUp),
        
        isDisabled() {
            @if($alpineDisabled)
                try { return !!({{ $alpineDisabled }}); } catch(e) { return false; }
            @else
                return @js((bool)$disabled);
            @endif
        },

        init() {
            let initial = this.options.find(o => String(o.value) === String(this.selected) && String(o.value) !== '');
            if (!initial && this.selected) {
                initial = this.options.find(o => String(o.value).toLowerCase() === String(this.selected).toLowerCase() && String(o.value) !== '');
                if (initial) {
                    this.selected = initial.value;
                }
            }
            if (!initial && this.options.length > 0 && !@js($placeholder)) {
                this.selected = this.options[0].value;
            }

            @if($attributes->has('x-model'))
            const modelName = @js($attributes->get('x-model'));
            if (this[modelName] !== undefined && this[modelName] !== null) {
                const parentVal = String(this[modelName]);
                const match = this.options.find(o => String(o.value).toLowerCase() === parentVal.toLowerCase() && String(o.value) !== '');
                this.selected = match ? match.value : parentVal;
            }
            this.$watch(modelName, (newVal) => {
                if (newVal !== undefined) {
                    const match = this.options.find(o => String(o.value).toLowerCase() === String(newVal).toLowerCase() && String(o.value) !== '');
                    const matchedVal = match ? match.value : String(newVal);
                    if (matchedVal !== String(this.selected)) {
                        this.selected = matchedVal;
                    }
                }
            });
            @endif
        },

        get filteredOptions() {
            if (!this.searchable || !this.search || !this.search.trim()) {
                return this.options;
            }
            const q = this.search.toLowerCase().trim();
            return this.options.filter(o => 
                (o.label && o.label.toLowerCase().includes(q)) || 
                (o.sublabel && o.sublabel.toLowerCase().includes(q)) || 
                (o.value && o.value.toLowerCase().includes(q))
            );
        },

        get selectedOption() {
            if (this.selected === null || this.selected === undefined || String(this.selected).trim() === '') {
                return null;
            }
            return this.options.find(o => String(o.value) === String(this.selected) && String(o.value) !== '')
                || this.options.find(o => String(o.value).toLowerCase() === String(this.selected).toLowerCase() && String(o.value) !== '');
        },

        get selectedLabel() {
            const found = this.selectedOption;
            return found ? found.label : (@js($placeholder) || 'Select');
        },

        setSelected(val) {
            this.selected = String(val);
            if (this.$refs.hiddenInput) {
                this.$refs.hiddenInput.value = this.selected;
            }
        },

        selectOption(val) {
            if (this.isDisabled()) return;
            this.selected = String(val);
            this.open = false;
            this.search = '';
            
            @if($attributes->has('x-model'))
            const modelName = @js($attributes->get('x-model'));
            try {
                this[modelName] = this.selected;
            } catch(e) {}
            @endif

            // Dispatch standard input and change events for form/alpine sync
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

        toggle() {
            if (this.isDisabled()) return;
            this.open = !this.open;
            if (this.open) {
                this.search = '';
                this.highlightedIndex = this.filteredOptions.findIndex(o => String(o.value) === String(this.selected));
                
                @if(!$dropUp)
                const rect = this.$el.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;
                const dropdownHeight = 270;
                if (spaceBelow < dropdownHeight && rect.top > spaceBelow) {
                    this.dropUp = true;
                } else {
                    this.dropUp = false;
                }
                @endif

                this.$nextTick(() => {
                    if (this.searchable && this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                    this.scrollToSelected();
                });
            }
        },

        close() {
            this.open = false;
            this.search = '';
        },

        onKeyDown(e) {
            if (this.isDisabled()) return;
            if (!this.open) {
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.open = true;
                }
                return;
            }

            if (e.key === 'Escape') {
                e.preventDefault();
                this.open = false;
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (this.filteredOptions.length > 0) {
                    this.highlightedIndex = (this.highlightedIndex + 1) % this.filteredOptions.length;
                    this.scrollToHighlighted();
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (this.filteredOptions.length > 0) {
                    this.highlightedIndex = (this.highlightedIndex - 1 + this.filteredOptions.length) % this.filteredOptions.length;
                    this.scrollToHighlighted();
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredOptions.length) {
                    this.selectOption(this.filteredOptions[this.highlightedIndex].value);
                }
            }
        },

        scrollToHighlighted() {
            this.$nextTick(() => {
                const el = this.$refs.optionsList?.children[this.highlightedIndex];
                if (el) el.scrollIntoView({ block: 'nearest' });
            });
        },

        scrollToSelected() {
            const idx = this.filteredOptions.findIndex(o => String(o.value) === String(this.selected));
            if (idx >= 0) {
                this.highlightedIndex = idx;
                this.scrollToHighlighted();
            }
        }
     }"
     @click.outside="close()"
     @keydown="onKeyDown($event)"
     @select-reset.window="if (!$event.detail || $event.detail.name === @js($name)) { selected = @js($initialValue); if ($refs.hiddenInput) $refs.hiddenInput.value = selected; }"
     {{ $attributes->whereDoesntStartWith(['x-model', ':disabled', 'x-bind:disabled'])->except(['class', 'containerClass', 'container-class', 'size', 'options', 'name', 'id', 'value', 'placeholder', 'disabled', 'form']) }}>

    {{-- Hidden Native Input for standard HTML form submissions and x-model proxy --}}
    <input type="hidden" 
           @if($name) name="{{ $name }}" @endif
           @if($form) form="{{ $form }}" @endif
           id="{{ $id }}" 
           x-ref="hiddenInput" 
           :value="selected" 
           :disabled="isDisabled()">

    {{-- Custom Trigger Button --}}
    <button type="button"
            @click="toggle()"
            :aria-expanded="open"
            aria-haspopup="listbox"
            :disabled="isDisabled()"
            class="relative w-full flex items-center justify-between text-left rounded-xl border shadow-2xs transition-all duration-200 cursor-pointer focus:outline-none {{ $sizeClasses }} {{ $class }}"
            :class="isDisabled() 
                ? 'border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-400 dark:text-slate-500 cursor-not-allowed shadow-none' 
                : (open 
                    ? 'border-emerald-500 dark:border-emerald-500 ring-2 ring-emerald-500/20 bg-white dark:bg-slate-800 text-slate-900 dark:text-white' 
                    : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 text-slate-900 dark:text-white')">
        
        <div class="flex items-center gap-2 min-w-0 flex-1 pr-2">
            {{-- Selected Option Icon --}}
            <template x-if="selectedOption && selectedOption.icon">
                <span class="w-5 h-5 rounded-lg flex items-center justify-center shrink-0 border border-slate-200/60 dark:border-slate-700/60" :class="selectedOption.iconColor || 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'">
                    {{-- Mars (Male) --}}
                    <template x-if="selectedOption.icon === 'mars'">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="10" cy="14" r="5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5l-5.5 5.5M19 5h-4M19 5v4"/></svg>
                    </template>
                    {{-- Venus (Female) --}}
                    <template x-if="selectedOption.icon === 'venus'">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="9" r="5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7M9 18h6"/></svg>
                    </template>
                    {{-- Blood Drop --}}
                    <template x-if="selectedOption.icon === 'drop'">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 01.78.375l5.5 7a1 1 0 01-.78 1.625H4.5a1 1 0 01-.78-1.625l5.5-7A1 1 0 0110 2z" clip-rule="evenodd"/></svg>
                    </template>
                    {{-- Priority Star --}}
                    <template x-if="selectedOption.icon === 'star'">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </template>
                    {{-- Accessibility --}}
                    <template x-if="selectedOption.icon === 'accessibility'">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="4" r="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13v-2a2 2 0 00-2-2H7a2 2 0 00-2 2v2m4 0v7m6-7v7"/></svg>
                    </template>
                    {{-- User --}}
                    <template x-if="selectedOption.icon === 'user'">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </template>
                    {{-- Child --}}
                    <template x-if="selectedOption.icon === 'child'">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005 0"/></svg>
                    </template>
                </span>
            </template>

            <span class="truncate block font-normal" 
                  :class="(!selectedOption || !selected) ? 'text-slate-400 dark:text-slate-500' : 'text-slate-900 dark:text-white'" 
                  x-text="selectedLabel"></span>

            {{-- Selected Option Badge --}}
            <template x-if="selectedOption && selectedOption.badge">
                <span class="ml-auto mr-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase shrink-0 border" 
                      :class="selectedOption.badgeClass || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'" 
                      x-text="selectedOption.badge"></span>
            </template>
        </div>

        {{-- Custom SVG Chevron --}}
        <span class="absolute inset-y-0 {{ $chevronRight }} flex items-center pointer-events-none transition-transform duration-200"
              :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-400'">
            <svg class="{{ $chevronSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </span>
    </button>

    {{-- Custom Elevated Options Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         :x-transition:enter-start="dropUp ? 'opacity-0 translate-y-1 scale-95' : 'opacity-0 -translate-y-1 scale-95'"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         :x-transition:leave-end="dropUp ? 'opacity-0 translate-y-1 scale-95' : 'opacity-0 -translate-y-1 scale-95'"
         style="display: none;"
         class="absolute z-50 w-full min-w-[220px] max-h-64 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-slate-950/60 p-1.5 backdrop-blur-md focus:outline-none custom-scrollbar"
         :class="dropUp ? 'bottom-full mb-1.5' : 'mt-1.5'"
         tabindex="-1"
         role="listbox">
        
        <template x-if="searchable">
            <div class="p-1 pb-1.5 mb-1 border-b border-slate-100 dark:border-slate-800 sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xs z-10">
                <div class="relative">
                    <input type="text" 
                           x-ref="searchInput" 
                           x-model="search" 
                           @click.stop 
                           placeholder="{{ $searchPlaceholder }}" 
                           class="w-full text-xs px-2.5 py-1.5 pl-7 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-emerald-500 font-normal">
                    <svg class="w-3.5 h-3.5 absolute left-2 top-2 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </template>

        <ul x-ref="optionsList" class="space-y-1">
            <template x-for="(opt, idx) in filteredOptions" :key="opt.value">
                <li @click="selectOption(opt.value)"
                    @mouseenter="highlightedIndex = idx"
                    role="option"
                    :aria-selected="String(selected) === String(opt.value)"
                    class="group flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs transition-all cursor-pointer select-none border"
                    :class="{
                        'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 font-semibold border-emerald-300 dark:border-emerald-800 shadow-2xs': String(selected) === String(opt.value),
                        'bg-slate-100 dark:bg-slate-800/80 text-slate-900 dark:text-white border-transparent': highlightedIndex === idx && String(selected) !== String(opt.value),
                        'text-slate-700 dark:text-slate-300 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50': String(selected) !== String(opt.value) && highlightedIndex !== idx
                    }">
                    
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        {{-- Option Icon --}}
                        <template x-if="opt.icon">
                            <span class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 border border-slate-200/60 dark:border-slate-700/60" :class="opt.iconColor || 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'">
                                <template x-if="opt.icon === 'mars'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="10" cy="14" r="5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5l-5.5 5.5M19 5h-4M19 5v4"/></svg>
                                </template>
                                <template x-if="opt.icon === 'venus'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="9" r="5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7M9 18h6"/></svg>
                                </template>
                                <template x-if="opt.icon === 'drop'">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 01.78.375l5.5 7a1 1 0 01-.78 1.625H4.5a1 1 0 01-.78-1.625l5.5-7A1 1 0 0110 2z" clip-rule="evenodd"/></svg>
                                </template>
                                <template x-if="opt.icon === 'star'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                </template>
                                <template x-if="opt.icon === 'accessibility'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="4" r="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13v-2a2 2 0 00-2-2H7a2 2 0 00-2 2v2m4 0v7m6-7v7"/></svg>
                                </template>
                                <template x-if="opt.icon === 'user'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </template>
                                <template x-if="opt.icon === 'child'">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005 0"/></svg>
                                </template>
                            </span>
                        </template>

                        <div class="min-w-0 flex-1">
                            <span class="truncate block font-medium" x-text="opt.label"></span>
                            <template x-if="opt.sublabel">
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-normal normal-case leading-tight mt-0.5" x-text="opt.sublabel"></span>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        {{-- Option Badge --}}
                        <template x-if="opt.badge">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase shrink-0 border" 
                                  :class="opt.badgeClass || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'" 
                                  x-text="opt.badge"></span>
                        </template>

                        {{-- Emerald Checkmark for selected item --}}
                        <svg x-show="String(selected) === String(opt.value)"
                             class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" 
                             fill="none" 
                             stroke="currentColor" 
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </li>
            </template>

            <template x-if="filteredOptions.length === 0">
                <li class="px-3 py-3 text-center text-xs text-slate-400 dark:text-slate-500 font-normal">
                    No options found
                </li>
            </template>
        </ul>
    </div>
</div>
