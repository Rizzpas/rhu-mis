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
])

@php
    $id = $id ?? ($name ?? 'select_' . uniqid());
    
    // Normalize options into array of ['value' => ..., 'label' => ...]
    $normalizedOptions = [];
    foreach ($options as $key => $opt) {
        if (is_array($opt)) {
            $normalizedOptions[] = [
                'value' => (string)($opt['value'] ?? $key),
                'label' => (string)($opt['label'] ?? $opt['value'] ?? $key),
            ];
        } else {
            $normalizedOptions[] = [
                'value' => (string)$key,
                'label' => (string)$opt,
            ];
        }
    }

    $initialValue = $value ?? ($normalizedOptions[0]['value'] ?? '');
    
    // Size variants
    $sizeClasses = [
        'sm' => 'py-1.5 pl-3 pr-8 text-xs font-semibold',
        'md' => 'py-2.5 pl-3.5 pr-9 text-sm font-medium',
        'lg' => 'py-3 pl-4 pr-10 text-base font-medium',
    ][$size] ?? 'py-2.5 pl-3.5 pr-9 text-sm font-medium';

    $chevronSize = [
        'sm' => 'w-3.5 h-3.5 right-2.5',
        'md' => 'w-4 h-4 right-3',
        'lg' => 'w-4 h-4 right-3.5',
    ][$size] ?? 'w-4 h-4 right-3';
@endphp

<div class="relative custom-dropdown-container inline-block w-full {{ $containerClass }}"
     x-data="{
        open: false,
        selected: @js($initialValue),
        options: @js($normalizedOptions),
        highlightedIndex: -1,
        
        init() {
            // Check if there is an initial value
            const initial = this.options.find(o => String(o.value) === String(this.selected));
            if (!initial && this.options.length > 0 && !@js($placeholder)) {
                this.selected = this.options[0].value;
            }
        },

        get selectedLabel() {
            const found = this.options.find(o => String(o.value) === String(this.selected));
            return found ? found.label : (@js($placeholder) || 'Select');
        },

        selectOption(val) {
            if (@js($disabled)) return;
            this.selected = val;
            this.open = false;
            
            // Dispatch standard input and change events for form/alpine sync
            this.$nextTick(() => {
                if (this.$refs.hiddenInput) {
                    this.$refs.hiddenInput.value = val;
                    this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.$dispatch('input', val);
                this.$dispatch('change', val);
            });
        },

        toggle() {
            if (@js($disabled)) return;
            this.open = !this.open;
            if (this.open) {
                this.highlightedIndex = this.options.findIndex(o => String(o.value) === String(this.selected));
                this.$nextTick(() => {
                    this.scrollToSelected();
                });
            }
        },

        close() {
            this.open = false;
        },

        onKeyDown(e) {
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
                this.highlightedIndex = (this.highlightedIndex + 1) % this.options.length;
                this.scrollToHighlighted();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.highlightedIndex = (this.highlightedIndex - 1 + this.options.length) % this.options.length;
                this.scrollToHighlighted();
            } else if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.options.length) {
                    this.selectOption(this.options[this.highlightedIndex].value);
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
            const idx = this.options.findIndex(o => String(o.value) === String(this.selected));
            if (idx >= 0) {
                this.highlightedIndex = idx;
                this.scrollToHighlighted();
            }
        }
     }"
     @click.outside="close()"
     @keydown="onKeyDown($event)"
     {{ $attributes->whereStartsWith(['wire:model']) }}>

    {{-- Hidden Native Input for standard HTML form submissions and x-model proxy --}}
    <input type="hidden" 
           @if($name) name="{{ $name }}" @endif
           id="{{ $id }}" 
           x-ref="hiddenInput" 
           :value="selected" 
           @if($disabled) disabled @endif
           {{ $attributes->whereStartsWith(['x-model']) }}>

    {{-- Custom Trigger Button --}}
    <button type="button"
            @click="toggle()"
            :aria-expanded="open"
            aria-haspopup="listbox"
            @if($disabled) disabled @endif
            class="relative w-full flex items-center justify-between text-left rounded-xl border bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 shadow-2xs transition-all duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed {{ $sizeClasses }} {{ $class }}"
            :class="open 
                ? 'border-emerald-600 ring-2 ring-emerald-500/30 dark:border-emerald-500' 
                : 'border-slate-300 dark:border-slate-600 hover:border-slate-400 dark:hover:border-slate-500'">
        
        <span class="truncate block pr-2" x-text="selectedLabel"></span>

        {{-- Custom SVG Chevron --}}
        <span class="absolute inset-y-0 {{ $chevronSize }} flex items-center pointer-events-none transition-transform duration-200"
              :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-400'">
            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </span>
    </button>

    {{-- Custom Elevated Options Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         style="display: none;"
         class="absolute z-50 mt-1.5 w-full min-w-[180px] max-h-64 overflow-y-auto rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/10 dark:shadow-slate-950/40 p-1.5 backdrop-blur-md focus:outline-none scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-700"
         tabindex="-1"
         role="listbox">
        
        <ul x-ref="optionsList" class="space-y-0.5">
            <template x-for="(opt, idx) in options" :key="opt.value">
                <li @click="selectOption(opt.value)"
                    @mouseenter="highlightedIndex = idx"
                    role="option"
                    :aria-selected="String(selected) === String(opt.value)"
                    class="group flex items-center justify-between px-3 py-2 text-xs sm:text-sm rounded-lg transition-colors cursor-pointer select-none"
                    :class="{
                        'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold': String(selected) === String(opt.value),
                        'bg-slate-100 dark:bg-slate-700/60 text-slate-900 dark:text-white': highlightedIndex === idx && String(selected) !== String(opt.value),
                        'text-slate-700 dark:text-slate-300 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 hover:text-emerald-900 dark:hover:text-emerald-200': String(selected) !== String(opt.value) && highlightedIndex !== idx
                    }">
                    
                    <span class="truncate" x-text="opt.label"></span>

                    {{-- Emerald Checkmark for selected item --}}
                    <svg x-show="String(selected) === String(opt.value)"
                         class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </li>
            </template>
        </ul>
    </div>
</div>
