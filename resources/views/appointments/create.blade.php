@extends('layouts.app')

@section('content')
    <div class="bg-transparent min-h-screen py-12" x-data="appointmentForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-start">
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-green-700 dark:text-green-400 transition bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                    <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
            <!-- Progress Indicator -->
            <!-- Progress Indicator -->
            <div class="mb-8 w-full max-w-3xl mx-auto" x-data="{ stepLabels: ['Service', 'Info', 'Date', 'Verify', 'Confirm'] }">
                <div class="flex items-start justify-between relative">
                    <!-- Progress Bar Background -->
                    <div class="absolute left-0 top-4 md:top-5 transform -translate-y-1/2 w-full h-1 bg-gray-200 z-0"></div>
                    <!-- Progress Bar Fill -->
                    <div class="absolute left-0 top-4 md:top-5 transform -translate-y-1/2 h-1 bg-teal-500 z-0 transition-all duration-300"
                        :style="'width: ' + progress + '%'"></div>

                    <!-- Steps 1-5 Bubbles -->
                    <template x-for="i in 5">
                        <div class="relative z-10 flex flex-col items-center w-1/5">
                            <button type="button" @click="goToStep(i)"
                                class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold transition-colors duration-300 text-xs md:text-base focus:outline-none hover:ring-2 hover:ring-offset-1 hover:ring-teal-400 shadow-sm"
                                :class="step >= i ? 'bg-teal-500 text-white' : 'bg-gray-200 text-gray-500'" x-text="i"
                                :disabled="i > step && i > maxStepReached + 1"></button>
                            <span class="text-[9px] md:text-xs font-bold mt-2 text-gray-500 dark:text-gray-400 text-center uppercase tracking-wider" x-text="stepLabels[i-1]"></span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden p-8 relative">

                <!-- Loading Overlay -->
                <div x-show="isLoading"
                    class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-50 z-60 flex items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Book an Appointment</h2>

                <form id="appointment-form" action="{{ route('appointment.store') }}" method="POST"
                    @submit.prevent="submitForm"
                    @submit-appointment-form.window="console.log('Submitting form...'); $el.submit(); isLoading = true">
                    @csrf
                    <div x-show="step === 1" x-transition>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Step 1: Select Service</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pediatrics Tile -->
                            <div @click="setService('pedia')"
                                class="cursor-pointer border-2 rounded-xl p-6 flex flex-col items-center justify-center transition hover:border-teal-400 hover:bg-teal-50 dark:bg-gray-700 dark:hover:bg-gray-600"
                                :class="formData.type === 'pedia' ? 'border-teal-500 bg-teal-50 ring-2 ring-teal-200' : 'border-gray-200 dark:border-gray-700'">
                                <span class="text-4xl mb-2">👶</span>
                                <span class="font-bold text-gray-800 dark:text-white">{{ __('Pediatrics') }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">General consultation for children</span>
                            </div>

                            <!-- Adult Follow-up Tile -->
                            <div @click="setService('adult')"
                                class="cursor-pointer border-2 rounded-xl p-6 flex flex-col items-center justify-center transition hover:border-teal-400 hover:bg-teal-50 dark:bg-gray-700 dark:hover:bg-gray-600"
                                :class="formData.type === 'adult' ? 'border-teal-500 bg-teal-50 ring-2 ring-teal-200' : 'border-gray-200 dark:border-gray-700'">
                                <span class="text-4xl mb-2">👨‍⚕️</span>
                                <span class="font-bold text-gray-800 dark:text-white">{{ __('Adult Follow-up') }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">Check-ups for regular adult & senior patients</span>
                            </div>
                        </div>
                        <input type="hidden" name="type" x-model="formData.type">

                        <div class="mt-6" x-show="formData.type === 'pedia'" x-transition>
                            <div class="p-4 border rounded-lg transition"
                                :class="formData.is_follow_up ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900'">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" x-model="formData.is_follow_up"
                                        class="w-5 h-5 text-teal-600 rounded focus:ring-teal-500 border-gray-300 dark:border-gray-600">
                                    <input type="hidden" name="is_follow_up" :value="formData.is_follow_up ? 1 : 0">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-800 dark:text-white">Is this a
                                            follow-up visit?</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-200 mt-1">Check this if the
                                            patient was previously advised by the doctor to return.</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="type" x-model="formData.type">
                    </div>

                    <!-- Step 2: Patient Info -->
                    <div x-show="step === 2" x-transition>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Step 2: Patient Information</h3>

                        {{-- Section 1: Personal Details --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 mb-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-4">Personal Details</h4>
                            
                            {{-- Name fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('First Name') }} <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" x-model="formData.first_name" required
                                        @input="formData.first_name = $event.target.value.toUpperCase()"
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 uppercase dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 transition duration-150"
                                        :class="errors.first_name ? 'border-red-500 ring-red-500' : 'border-gray-300'">
                                    <p x-show="errors.first_name" class="text-red-500 text-xs mt-1" x-text="errors.first_name"></p>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Middle Name') }}</label>
                                    <input type="text" name="middle_name" x-model="formData.middle_name"
                                        @input="formData.middle_name = $event.target.value.toUpperCase()"
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 uppercase dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 border-gray-300 transition duration-150">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Last Name') }} <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" x-model="formData.last_name" required
                                        @input="formData.last_name = $event.target.value.toUpperCase()"
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 uppercase dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 transition duration-150"
                                        :class="errors.last_name ? 'border-red-500 ring-red-500' : 'border-gray-300'">
                                    <p x-show="errors.last_name" class="text-red-500 text-xs mt-1" x-text="errors.last_name"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Suffix') }}</label>
                                    <input type="text" name="suffix" x-model="formData.suffix" placeholder="JR"
                                        @input="formData.suffix = $event.target.value.toUpperCase()"
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 uppercase dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 border-gray-300 transition duration-150">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Sex --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sex') }} <span class="text-red-500">*</span></label>
                                    <select name="sex" x-model="formData.sex" required
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 transition duration-150"
                                        :class="errors.sex ? 'border-red-500 ring-red-500' : 'border-gray-300'">
                                        <option value="">Select Sex</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <p x-show="errors.sex" class="text-red-500 text-xs mt-1" x-text="errors.sex"></p>
                                </div>

                                {{-- Date of Birth --}}
                                <div x-data="{
                                    showDatepicker: false,
                                    currentDate: new Date(),
                                    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                    get daysInMonth() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate(); },
                                    get startDay() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay(); },
                                    setMonth(monthIndex) { this.currentDate = new Date(this.currentDate.getFullYear(), monthIndex, 1); },
                                    setYear(year) { this.currentDate = new Date(year, this.currentDate.getMonth(), 1); },
                                    isFutureDate(day) {
                                        let dateToCheck = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                        let today = new Date(); today.setHours(0,0,0,0);
                                        return dateToCheck > today;
                                    },
                                    selectDate(day) {
                                        if (this.isFutureDate(day)) return;
                                        let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                        let offset = date.getTimezoneOffset();
                                        date = new Date(date.getTime() - (offset*60*1000));
                                        formData.dob = date.toISOString().split('T')[0];
                                        this.showDatepicker = false;
                                    },
                                    isSelected(day) {
                                        if(!formData.dob) return false;
                                        let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                        let offset = date.getTimezoneOffset();
                                        date = new Date(date.getTime() - (offset*60*1000));
                                        return formData.dob === date.toISOString().split('T')[0];
                                    },
                                    init() { if (formData.dob) { this.currentDate = new Date(formData.dob); } }
                                }" class="relative">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Date of Birth') }} <span class="text-red-500">*</span></label>
                                    <input type="hidden" name="dob" x-model="formData.dob">
                                    <div @click="showDatepicker = !showDatepicker"
                                        class="mt-1 w-full rounded-md shadow-sm border p-2 cursor-pointer flex justify-between items-center bg-white dark:bg-gray-700 dark:text-white dark:border-gray-600 focus-within:border-teal-500 focus-within:ring-1 focus-within:ring-teal-500 transition duration-150"
                                        :class="errors.dob ? 'border-red-500 ring-red-500' : 'border-gray-300'">
                                        <span x-text="formData.dob ? formData.dob : 'Select Date'" :class="formData.dob ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-300'"></span>
                                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p x-show="errors.dob" class="text-red-500 text-xs mt-1" x-text="errors.dob"></p>

                                    <!-- Datepicker Popup -->
                                    <div x-show="showDatepicker" @click.away="showDatepicker = false" style="display: none;"
                                        class="absolute z-50 mt-1 w-[300px] p-4 bg-white dark:bg-gray-800 dark:border-gray-600 border border-gray-200 rounded-lg shadow-xl outline-none">
                                        <div class="flex justify-between items-center mb-4 gap-2">
                                            <select @change="setMonth($event.target.value)" class="w-1/2 flex-1 rounded-md border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 dark:bg-gray-700 focus:border-teal-500 p-1">
                                                <template x-for="(month, index) in monthNames" :key="index">
                                                    <option :value="index" x-text="month" :selected="index === currentDate.getMonth()"></option>
                                                </template>
                                            </select>
                                            <select @change="setYear($event.target.value)" class="w-1/2 flex-1 rounded-md border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 dark:bg-gray-700 focus:border-teal-500 p-1">
                                                <template x-for="year in Array.from({length: 120}, (_, i) => new Date().getFullYear() - i)" :key="year">
                                                    <option :value="year" x-text="year" :selected="year === currentDate.getFullYear()"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-7 gap-1 mb-2">
                                            <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                                                <div class="text-center text-xs font-bold text-gray-400 dark:text-gray-500" x-text="day"></div>
                                            </template>
                                        </div>
                                        <div class="grid grid-cols-7 gap-1">
                                            <template x-for="blank in startDay"><div class="p-1"></div></template>
                                            <template x-for="day in daysInMonth" :key="day">
                                                <div @click="selectDate(day)"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full text-sm cursor-pointer transition-colors"
                                                    :class="{
                                                         'bg-teal-600 text-white font-bold shadow-md': isSelected(day),
                                                         'hover:bg-teal-100 dark:hover:bg-teal-900/50 text-gray-700 dark:text-gray-300': !isSelected(day) && !isFutureDate(day),
                                                         'text-gray-300 dark:text-gray-600 cursor-not-allowed': isFutureDate(day),
                                                         'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-medium': !isSelected(day) && new Date().getDate() === day && new Date().getMonth() === currentDate.getMonth() && new Date().getFullYear() === currentDate.getFullYear()
                                                     }" x-text="day">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Demographics (Hidden for Follow-ups) --}}
                        <div x-show="!isFollowUp" x-cloak class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 mb-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-4">Demographics</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Civil Status') }}</label>
                                    <select name="civil_status" x-model="formData.civil_status"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2">
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                                <div x-data="{
                                    selectedReligion: '',
                                    customReligion: '',
                                    get finalReligion() { return this.selectedReligion === 'Others' ? this.customReligion : this.selectedReligion; }
                                }" x-effect="formData.religion = finalReligion">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Religion') }}</label>
                                    <select x-model="selectedReligion" :required="!isFollowUp && formData.type !== 'pedia'"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2"
                                        :class="errors.religion ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                                        <option value="">Select Religion...</option>
                                        <option value="N/A">Not Applicable (N/A)</option>
                                        <option value="Roman Catholic">Roman Catholic</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                                        <option value="Born Again">Born Again</option>
                                        <option value="Adventist">Adventist</option>
                                        <option value="Aglipayan">Aglipayan</option>
                                        <option value="Jehovah's Witnesses">Jehovah's Witnesses</option>
                                        <option value="Others">Others</option>
                                    </select>
                                    <p x-show="errors.religion" class="text-red-500 text-xs mt-1" x-text="errors.religion"></p>
                                    <input type="text" x-show="selectedReligion === 'Others'" x-model="customReligion"
                                        placeholder="Specify religion..." style="display:none;"
                                        class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm">
                                    <input type="hidden" name="religion" :value="finalReligion">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Education') }}</label>
                                    <select name="education" x-model="formData.education"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2">
                                        <option value="">Select Highest Attainment</option>
                                        <option value="N/A">Not Applicable (N/A)</option>
                                        <option value="No Formal Education">No Formal Education</option>
                                        <option value="Primary Education (Elementary)">Primary Education (Elementary)</option>
                                        <template x-if="formData.type !== 'pedia'">
                                            <optgroup label="Adult Education">
                                                <option value="Secondary Education (High School)">Secondary Education (High School)</option>
                                                <option value="Vocational">Vocational / Trade Course</option>
                                                <option value="College Undergraduate">College Undergraduate</option>
                                                <option value="College Graduate">College Graduate</option>
                                                <option value="Post-Graduate">Post-Graduate (Master's/Doctorate)</option>
                                            </optgroup>
                                        </template>
                                    </select>
                                </div>
                                <!-- Occupation Dropdown -->
                                <div class="md:col-span-1" x-data="{
                                    selectedOccupation: '',
                                    customOccupation: '',
                                    get finalOccupation() { return this.selectedOccupation === 'Others' ? this.customOccupation : this.selectedOccupation; }
                                }" x-effect="formData.occupation = finalOccupation">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Occupation') }}</label>
                                    <select x-model="selectedOccupation" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2">
                                        <option value="">Select Occupation...</option>
                                        <option value="N/A">Not Applicable (N/A)</option>
                                        <option value="Student">Student</option>
                                        <template x-if="formData.type !== 'pedia'">
                                            <optgroup label="Adult Occupation">
                                                <option value="Employed">Employed</option>
                                                <option value="Self-Employed">Self-Employed</option>
                                                <option value="Housewife/Househusband">Housewife / Househusband</option>
                                                <option value="Retired">Retired</option>
                                                <option value="Others">Others (Please specify)</option>
                                            </optgroup>
                                        </template>
                                    </select>
                                    <input type="text" x-show="selectedOccupation === 'Others'" x-model="customOccupation"
                                        placeholder="Specify occupation..." style="display:none;"
                                        class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm">
                                    <input type="hidden" name="occupation" :value="finalOccupation">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Blood Type') }} <span class="text-red-500">*</span></label>
                                    <select name="blood_type" x-model="formData.blood_type" :required="!isFollowUp"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2"
                                        :class="errors.blood_type ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="Unknown">Unknown / Not Sure</option>
                                    </select>
                                    <p x-show="errors.blood_type" class="text-red-500 text-xs mt-1" x-text="errors.blood_type"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Family Information --}}
                        <div x-show="!isFollowUp" class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 mb-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-4">Family Information</h4>
                            
                            {{-- Mother's Maiden Name --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __("Mother's Maiden Name") }} <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('First Name') }} <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="formData.mother_first_name" :required="!isFollowUp" placeholder="First Name"
                                            @input="formData.mother_first_name = $event.target.value.toUpperCase()"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm uppercase">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Middle Name') }}</label>
                                        <input type="text" x-model="formData.mother_middle_name" placeholder="Middle Name"
                                            @input="formData.mother_middle_name = $event.target.value.toUpperCase()"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm uppercase">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Last Name') }} <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="formData.mother_last_name" :required="!isFollowUp" placeholder="Last Name"
                                            @input="formData.mother_last_name = $event.target.value.toUpperCase()"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm uppercase">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Suffix') }}</label>
                                        <input type="text" x-model="formData.mother_suffix" placeholder="Suffix"
                                            @input="formData.mother_suffix = $event.target.value.toUpperCase()"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm uppercase">
                                    </div>
                                </div>
                                <input type="hidden" name="mothers_maiden_name" :value="`${formData.mother_first_name} ${formData.mother_middle_name ? formData.mother_middle_name + ' ' : ''}${formData.mother_last_name}${formData.mother_suffix ? ' ' + formData.mother_suffix : ''}`.trim().replace(/\s+/g, ' ').toUpperCase()">
                                <p x-show="errors.mothers_maiden_name" class="text-red-500 text-xs mt-1" x-text="errors.mothers_maiden_name"></p>
                            </div>
                            
                            {{-- PhilHealth Number --}}
                            <div x-data="{ showPhilhealth: false }" class="grid grid-cols-1 md:w-1/2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span x-text="formData.type === 'pedia' ? `Guardian's PhilHealth No.` : `PhilHealth No.`"></span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-1">
                                    <input :type="showPhilhealth ? 'text' : 'password'" name="philhealth_number" :required="!isFollowUp"
                                        x-model="formData.philhealth_number" placeholder="12-123456789-0"
                                        @input="formData.philhealth_number = formatPhilHealth($event.target.value)"
                                        class="block w-full rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 pr-10"
                                        :class="errors.philhealth ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'">
                                    <button type="button" @click="showPhilhealth = !showPhilhealth" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                                        <svg x-show="!showPhilhealth" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="showPhilhealth" style="display:none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                                <p x-show="errors.philhealth" class="text-red-500 text-xs mt-1" x-text="errors.philhealth"></p>
                            </div>
                        </div>

                        {{-- Hidden classification auto-set by service type --}}
                        <input type="hidden" name="classification" :value="formData.type === 'pedia' ? 'Pediatric' : 'Regular Adult'">

                        {{-- Section 4: Contact & Address --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 mb-6 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-4">Contact & Address</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <span x-text="formData.type === 'pedia' ? 'Guardian Email Address' : 'Email Address'"></span> <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" x-model="formData.email" :required="true" placeholder="example@email.com"
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500"
                                        :class="errors.email ? 'border-red-500 ring-red-500' : 'border-gray-300'">
                                    <p x-show="errors.email" class="text-red-500 text-xs mt-1" x-text="errors.email"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">We will send a verification code to this email.</p>
                                </div>
                                <div x-show="formData.type !== 'pedia' && !isFollowUp">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Contact Number') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="contact_number" x-model="formData.contact_number" x-bind:required="!isFollowUp && formData.type !== 'pedia'" placeholder="09xxxxxxxxx" maxlength="11"
                                        x-on:input="formData.contact_number = $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500"
                                        :class="errors.contact_number ? 'border-red-500 ring-red-500' : 'border-gray-300'">
                                    <p x-show="errors.contact_number" class="text-red-500 text-xs mt-1" x-text="errors.contact_number"></p>
                                </div>
                            </div>

                            <div x-show="!isFollowUp" x-data="{
                                house_no: '',
                                street: '',
                                building: '',
                                barangay: '',
                                city_province: 'Silang, Cavite',
                                barangays: [],
                                loadingBrgy: true,
                                get fullAddress() {
                                    let parts = [];
                                    if (this.house_no.trim()) parts.push(this.house_no.trim());
                                    if (this.street.trim()) parts.push(this.street.trim());
                                    if (this.building.trim()) parts.push(this.building.trim());
                                    if (this.barangay) parts.push(this.barangay);
                                    parts.push(this.city_province);
                                    return parts.join(', ').replace(/^, | ,/g, '').trim();
                                },
                                async fetchBarangays() {
                                    this.loadingBrgy = true;
                                    try {
                                        const res = await fetch('https://psgc.gitlab.io/api/cities-municipalities/042118000/barangays/');
                                        const data = await res.json();
                                        this.barangays = data.sort((a,b) => a.name.localeCompare(b.name));
                                    } catch(e) { console.error(e); } 
                                    finally { this.loadingBrgy = false; }
                                }
                            }" x-init="fetchBarangays()" x-effect="formData.address = fullAddress; formData.barangay = barangay; formData.house_no = house_no; formData.street = street; formData.building = building; formData.city_province = city_province">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Address') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">For patients within Silang, Cavite only.</p>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                    <div class="md:col-span-3">
                                        <input type="text" x-model="house_no" :required="!isFollowUp" placeholder="House No."
                                            class="w-full rounded-md border p-2 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 shadow-sm border-gray-300 uppercase">
                                    </div>
                                    <div class="md:col-span-4">
                                        <input type="text" x-model="street" placeholder="Street Name (Opt)"
                                            class="w-full rounded-md border p-2 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 shadow-sm border-gray-300 uppercase">
                                    </div>
                                    <div class="md:col-span-5">
                                        <input type="text" x-model="building" placeholder="Bldg/Subd (Opt)"
                                            class="w-full rounded-md border p-2 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 shadow-sm border-gray-300 uppercase">
                                    </div>
                                    <div class="md:col-span-6">
                                        <select x-model="barangay" :disabled="loadingBrgy" :required="!isFollowUp"
                                            class="w-full rounded-md border p-2 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500 shadow-sm uppercase"
                                            :class="errors.address && !barangay ? 'border-red-500' : 'border-gray-300'">
                                            <option value="" x-text="loadingBrgy ? 'Loading barangays...' : 'Select Barangay'"></option>
                                            <template x-for="bg in barangays" :key="bg.code">
                                                <option :value="bg.name" x-text="bg.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="md:col-span-6">
                                        <input type="text" x-model="city_province" readonly
                                            class="w-full rounded-md border-0 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 shadow-inner p-2 text-sm cursor-not-allowed font-medium uppercase">
                                    </div>
                                </div>
                                <input type="hidden" name="address" :value="fullAddress">
                                <input type="hidden" name="house_no" :value="house_no">
                                <input type="hidden" name="street" :value="street">
                                <input type="hidden" name="building" :value="building">
                                <input type="hidden" name="city_province" :value="city_province">
                                <p x-show="errors.address" class="text-red-500 text-xs mt-1" x-text="errors.address"></p>
                            </div>
                        </div>

                        {{-- Section 5: Guardian Info (Pedia only) --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 mb-6 border border-gray-100 dark:border-gray-700" x-show="formData.type === 'pedia' && !isFollowUp" x-cloak>
                            <h4 class="text-sm font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-4">Guardian Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Guardian Name') }} <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                        <div class="md:col-span-4">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('First Name') }} <span class="text-red-500">*</span></label>
                                            <input type="text" name="guardian_first_name" x-model="formData.guardian_first_name" :required="formData.type === 'pedia' && !isFollowUp" placeholder="First Name"
                                                @input="formData.guardian_first_name = $event.target.value.toUpperCase()"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm uppercase">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Middle Name') }}</label>
                                            <input type="text" name="guardian_middle_name" x-model="formData.guardian_middle_name" placeholder="Middle Name"
                                                @input="formData.guardian_middle_name = $event.target.value.toUpperCase()"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm uppercase">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Last Name') }} <span class="text-red-500">*</span></label>
                                            <input type="text" name="guardian_last_name" x-model="formData.guardian_last_name" :required="formData.type === 'pedia' && !isFollowUp" placeholder="Last Name"
                                                @input="formData.guardian_last_name = $event.target.value.toUpperCase()"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm uppercase">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Suffix') }}</label>
                                            <input type="text" name="guardian_suffix" x-model="formData.guardian_suffix" placeholder="Suffix"
                                                @input="formData.guardian_suffix = $event.target.value.toUpperCase()"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm uppercase">
                                        </div>
                                    </div>
                                    <p x-show="errors.guardian_first_name || errors.guardian_last_name" class="text-red-500 text-xs mt-1" x-text="errors.guardian_first_name || errors.guardian_last_name"></p>
                                </div>
                                <div class="md:col-span-1" x-data="{
                                    selectedGuardianRelation: '',
                                    customGuardianRelation: '',
                                    get finalGuardianRelation() { return this.selectedGuardianRelation === 'Others (Please Specify)' ? this.customGuardianRelation : this.selectedGuardianRelation; }
                                }" x-effect="formData.guardian_relation = finalGuardianRelation">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Relationship to Patient') }} <span class="text-red-500">*</span></label>
                                    <select x-model="selectedGuardianRelation" :required="formData.type === 'pedia' && !isFollowUp"
                                        class="block w-full rounded-md shadow-sm border p-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-teal-500">
                                        <option value="">Select Relationship</option>
                                        <option value="Mother">Mother</option>
                                        <option value="Father">Father</option>
                                        <option value="Grandparent">Grandparent</option>
                                        <option value="Sibling">Sibling</option>
                                        <option value="Others (Please Specify)">Others (Please Specify)</option>
                                    </select>
                                    <input type="text" x-show="selectedGuardianRelation === 'Others (Please Specify)'" x-model="customGuardianRelation"
                                        placeholder="Specify relationship..." style="display:none;"
                                        class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm uppercase" @input="customGuardianRelation = $event.target.value.toUpperCase()">
                                    <input type="hidden" name="guardian_relation" :value="finalGuardianRelation">
                                    <p x-show="errors.guardian_relation" class="text-red-500 text-xs mt-1" x-text="errors.guardian_relation"></p>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Guardian Contact Number') }} <span class="text-red-500">*</span></label>
                                    <input type="text" name="guardian_contact" x-model="formData.guardian_contact" placeholder="09xxxxxxxxx" maxlength="11"
                                        x-on:input="formData.guardian_contact = formData.guardian_contact.replace(/[^0-9]/g, '').slice(0, 11)"
                                        class="mt-1 block w-full rounded-md shadow-sm border p-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:border-teal-500 focus:ring-teal-500"
                                        :class="errors.guardian_contact ? 'border-red-500 ring-red-500' : 'border-gray-300'">
                                    <p x-show="errors.guardian_contact" class="text-red-500 text-xs mt-1" x-text="errors.guardian_contact"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 6: Chief Complaint --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-4">Reason for Visit</h4>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Chief Complaint') }} <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                 <template x-for="symptom in ['Cough', 'Fever', 'Headache', 'Abdominal Pain', 'Sore Throat', 'Difficulty Breathing', 'Dizziness', 'Skin Rash']">
                                    <label class="flex items-center space-x-2 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition cursor-pointer">
                                        <input type="checkbox" :value="symptom" x-model="formData.symptoms" class="rounded text-teal-600 focus:ring-teal-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                        <span class="text-sm text-gray-700 dark:text-gray-300" x-text="symptom"></span>
                                    </label>
                                 </template>
                            </div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">{{ __('Others Specify') }}</label>
                            <textarea x-model="formData.other_symptom" rows="2" placeholder="Other symptoms or reason..."
                                class="mt-1 block w-full rounded-md shadow-sm border p-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-teal-500"></textarea>
                            
                            <input type="hidden" name="complaint" :value="combinedComplaint">
                            <p x-show="errors.complaint" class="text-red-500 text-xs mt-1" x-text="errors.complaint"></p>
                        </div>
                    </div>

<!-- Step 3: Date & Time Selection -->
                    <div x-show="step === 3" x-transition x-init="initCalendar(); fetchAvailability();">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Step 3: Select Date & Arrival Time</h3>
                        
                        <div x-show="isFollowUp && doctorName" x-cloak class="mb-4 bg-teal-50 border border-teal-200 rounded-lg p-4 dark:bg-teal-900/20 dark:border-teal-800">
                            <h4 class="font-bold text-teal-800 dark:text-teal-300 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Follow-up Appointment
                            </h4>
                            <p class="text-sm text-teal-700 dark:text-teal-400 mt-1">
                                Your assigned doctor is <strong x-text="doctorName"></strong>. 
                                Please select an available date matching their schedule: <strong x-text="doctorSchedule"></strong>.
                            </p>
                        </div>

                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <button type="button" @click="prevMonth()" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed" :disabled="isPrevMonthDisabled()"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                                <h4 class="font-bold text-lg" x-text="monthName + ' ' + currentYear"></h4>
                                <button type="button" @click="nextMonth()" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed" :disabled="isNextMonthDisabled()"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                            </div>
                            <div class="flex gap-2 text-xs mb-4 justify-center">
                                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Available</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-400"></span> Limited</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500"></span> Full</span>
                            </div>
                            <div class="grid grid-cols-7 gap-2 text-center text-sm mb-2">
                                <span class="text-gray-400">Su</span><span class="text-gray-400">Mo</span><span class="text-gray-400">Tu</span>
                                <span class="text-gray-400">We</span><span class="text-gray-400">Th</span><span class="text-gray-400">Fr</span><span class="text-gray-400">Sa</span>
                            </div>
                            <div class="grid grid-cols-7 gap-2">
                                <template x-for="blank in startDay" :key="'blank-'+blank">
                                    <div></div>
                                </template>
                                
                                <template x-for="day in daysInMonth" :key="day">
                                    <div @click="!isPastDate(day) && !isDayFull(day) && selectDate(getDateString(day))"
                                        class="h-16 md:h-20 rounded-lg flex flex-col items-center justify-center font-medium transition text-xs md:text-sm relative group border"
                                        :class="{
                                                'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 cursor-not-allowed border-transparent': isPastDate(day),
                                                'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 cursor-not-allowed border-red-200 dark:border-red-800': !isPastDate(day) && isDayFull(day),
                                                'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-800/50 cursor-pointer border-yellow-200 dark:border-yellow-800': !isPastDate(day) && isDayLimited(day) && !isDayFull(day),
                                                'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800/50 cursor-pointer border-green-200 dark:border-green-800': !isPastDate(day) && !isDayLimited(day) && !isDayFull(day),
                                                'ring-2 ring-teal-500 ring-offset-2': selectedDate === getDateString(day)
                                             }">
                                        <span class="text-lg font-bold" x-text="day"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="preferred_date" x-model="selectedDate" required>
                        <input type="hidden" name="preferred_time" x-model="selectedTime" required>
                        <div x-show="selectedDate" class="text-center mt-2">
                            <p class="text-sm text-teal-600 font-medium">Selected: <span x-text="formatDate(selectedDate)"></span></p>
                            <p x-show="formData.type === 'pedia' && !isFollowUp" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Slots Usage: <span class="font-bold" x-text="getSlotsUsage(selectedDate)"></span>
                            </p>
                        </div>

                        <!-- Time Slot Picker -->
                        <div x-show="selectedDate" x-cloak class="mt-6">
                            <h4 class="text-md font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Select Preferred Arrival Time
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                Choose a 30-minute window based on the doctor's schedule. 
                                <strong class="text-amber-600">Please arrive 30 minutes before your selected time</strong> for initial vital signs triage.
                            </p>

                            <div x-show="loadingSlots" class="flex items-center justify-center py-8">
                                <svg class="animate-spin h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="ml-2 text-sm text-gray-500">Loading available time slots...</span>
                            </div>

                            <div x-show="!loadingSlots && timeSlots.length === 0" class="text-center py-6 bg-red-50 rounded-lg border border-red-200">
                                <p class="text-sm text-red-600 font-medium">No time slots available for this date. Please select a different day.</p>
                            </div>

                            <div x-show="!loadingSlots && timeSlots.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                <template x-for="slot in timeSlots" :key="slot.value">
                                    <button type="button" @click="selectedTime = slot.value"
                                        class="p-3 rounded-lg border-2 text-sm font-semibold text-center transition-all duration-200"
                                        :class="selectedTime === slot.value 
                                            ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 ring-2 ring-teal-500 ring-offset-1 shadow-md' 
                                            : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:border-teal-300 hover:bg-teal-50/50 dark:hover:bg-teal-900/10'"
                                    >
                                        <svg class="w-4 h-4 mx-auto mb-1" :class="selectedTime === slot.value ? 'text-teal-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span x-text="slot.label"></span>
                                    </button>
                                </template>
                            </div>

                            <div x-show="selectedTime" x-cloak class="mt-4 bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 rounded-lg p-3 text-center">
                                <p class="text-sm font-bold text-teal-800 dark:text-teal-300">
                                    🕐 Your Arrival Window: <span x-text="selectedTime"></span>
                                </p>
                                <p class="text-xs text-teal-600 dark:text-teal-400 mt-1">⚠️ Please arrive 30 minutes early for Triage processing.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: OTP Verification -->
                    <div x-show="step === 4" x-transition>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Step 4: Email Verification
                        </h3>
                        <div class="text-center">
                            <div class="mb-4">
                                <span class="text-5xl">📧</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                We have sent a verification code to <span class="font-bold text-teal-600"
                                    x-text="formData.email"></span>.
                                <br>Please enter the code below to proceed.
                            </p>

                            <div class="max-w-xs mx-auto mb-6">
                                <input type="text" x-model="otpCode" placeholder="Enter 6-digit Code" maxlength="6"
                                    class="text-center text-2xl tracking-widest block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-3">
                                <p x-show="errors.otp" class="text-red-500 text-sm mt-2" x-text="errors.otp"></p>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Didn't receive code? <button type="button" @click="sendOtp()"
                                    class="text-teal-600 hover:underline font-medium">Resend Code</button>
                            </p>
                        </div>
                    </div>

                    <!-- Step 5: Confirm -->
                    <div x-show="step === 5" x-transition>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Step 5: Review & Confirm</h3>

                        <dl
                            class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4 text-sm">
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Service Type</dt>
                                <dd class="font-medium text-gray-900 dark:text-white capitalize"
                                    x-text="formData.type === 'pedia' ? 'Pediatrics' : 'Follow-up'"></dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Date</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedDate)">
                                </dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Arrival Time</dt>
                                <dd class="font-medium text-teal-700 dark:text-teal-300" x-text="selectedTime || 'Not selected'">
                                </dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Patient Name</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    <span
                                        x-text="`${formData.first_name} ${formData.middle_name ? formData.middle_name + ' ' : ''}${formData.last_name}${formData.suffix ? ' ' + formData.suffix : ''}`"></span>
                                </dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Date of Birth</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.dob"></dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Sex</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.sex"></dd>
                            </div>
                            <div class="col-span-2" x-show="!isFollowUp">
                                <dt class="text-gray-500 dark:text-gray-400">Address</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.address"></dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400">Visit Type</dt>
                                <dd class="font-medium text-gray-900 dark:text-white capitalize"
                                    x-text="formData.is_follow_up ? 'Follow-up Consultation' : 'General Consultation'"></dd>
                            </div>
                            <div class="col-span-2" x-show="formData.type === 'pedia' && !isFollowUp">
                                <dt class="text-gray-500 dark:text-gray-400 mt-4 border-t pt-4 font-bold">Guardian Details
                                </dt>
                                <div class="grid grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400 text-xs">Name</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white"
                                            x-text="`${formData.guardian_first_name} ${formData.guardian_middle_name ? formData.guardian_middle_name + ' ' : ''}${formData.guardian_last_name}${formData.guardian_suffix ? ' ' + formData.guardian_suffix : ''}`.trim()">
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400 text-xs">Relation</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white"
                                            x-text="formData.guardian_relation"></dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-gray-500 dark:text-gray-400 text-xs">Contact</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white"
                                            x-text="formData.guardian_contact"></dd>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 mt-4 border-t pt-4">
                                <dt class="text-gray-500 dark:text-gray-400">Email (For OTP & Confirmation)</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.email"></dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400">Chief Complaint</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="combinedComplaint || 'N/A'">
                                </dd>
                            </div>
                        </dl>

                        <div class="mb-4">
                            <label class="flex items-start space-x-3">
                                <input type="checkbox" name="data_privacy_agreed" x-model="formData.data_privacy_agreed"
                                    required
                                    class="mt-1 rounded border-gray-300 dark:border-gray-600 text-teal-600 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    I hereby agree to the Data Privacy Act. I understand that my information will be used
                                    for medical appointment purposes only and will be handled with strict confidentiality.
                                    <button type="button" @click="showPrivacy = true"
                                        class="text-teal-600 hover:underline">{{ __('Read Data Privacy Policy') }}</button>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="mt-8 flex justify-between">
                        <button type="button" @click="prevStep()" x-show="step > 1"
                            class="text-gray-600 font-medium hover:text-gray-900 dark:hover:text-white dark:text-white px-4 py-2">
                            {{ __('Back') }}
                        </button>
                        <div x-show="step === 1"></div> <!-- Spacer -->

                        <!-- Step 1-3 Next Buttons -->
                        <button type="button" @click="nextStep()" x-show="step < 4"
                            class="bg-teal-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="(step === 3 && (!selectedDate || !selectedTime))">
                            <span
                                x-text="step === 3 ? '{{ __('Proceed to Verification') }}' : '{{ __('Next Step') }}'"></span>
                        </button>

                        <!-- Step 4 Verify Button -->
                        <button type="button" @click="nextStep()" x-show="step === 4"
                            class="bg-teal-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ __('Verify & Continue') }}
                        </button>

                        <!-- Step 5 Confirm Button -->
                        <button type="submit" x-show="step === 5"
                            class="bg-teal-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-teal-700 shadow-lg transform hover:-translate-y-0.5 transition"
                            :class="!formData.data_privacy_agreed ? 'opacity-50 cursor-not-allowed' : ''">
                            {{ __('Confirm Booking') }}
                        </button>
                    </div>

                </form>
                <!-- Data Privacy Modal -->
                <div x-show="showPrivacy" style="display: none;"
                    class="fixed inset-0 z-70 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPrivacy = false">
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full max-h-[80vh] flex flex-col z-80">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Data Privacy Policy</h3>
                        </div>
                        <div class="p-6 overflow-y-auto">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <strong>Compliance with Republic Act No. 10173 (Data Privacy Act of 2012)</strong>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                The Rural Health Unit (RHU) is committed to protecting your personal information. By
                                providing your data, you agree to its collection and use for the following purposes:
                            </p>
                            <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 mb-4 space-y-1">
                                <li>Appointment scheduling and management.</li>
                                <li>Medical record keeping and tracking of patient history.</li>
                                <li>Communication regarding your health concerns and appointment status.</li>
                                <li>Public health reporting requirements (anonymized where applicable).</li>
                            </ul>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Your data will be stored securely and will not be shared with unauthorized third parties
                                without your consent, except as required by law.
                            </p>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex justify-end">
                            <button type="button" @click="showPrivacy = false"
                                class="bg-gray-200 text-gray-800 dark:text-white px-4 py-2 rounded hover:bg-gray-300 mr-2">{{ __('Close') }}</button>
                            <button type="button" @click="formData.data_privacy_agreed = true; showPrivacy = false"
                                class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">{{ __('I Agree') }}</button>
                        </div>
                    </div>
                </div>
                <!-- Duplicate Booking Modal -->
                <div x-show="showDuplicateModal" style="display: none;"
                    class="fixed inset-0 z-70 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        @click="showDuplicateModal = false"></div>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all max-w-md w-full z-80 p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-2">Duplicate Booking
                            Detected</h3>
                        <div class="mt-2 text-left bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                            <p class="text-sm text-yellow-800 mb-2">
                                It looks like you already have an active appointment request under this name and email.
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Please check your inbox for your <strong>Appointment Confirmation</strong> or status
                                updates. If you need to cancel or reschedule, please check the 'Manage Appointment' page.
                            </p>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button type="button" @click="showDuplicateModal = false"
                                class="inline-flex justify-center w-full rounded-full border border-transparent shadow-md px-4 py-2 bg-teal-600 text-base font-bold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition sm:text-sm">
                                Okay, I Understand
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function appointmentForm() {
            return {
                get isFollowUp() {
                    return this.formData.type === 'adult' || (this.formData.type === 'pedia' && this.formData.is_follow_up);
                },
                step: 1,
                maxStepReached: 1,
                progress: 0,
                isLoading: false,
                selectedDate: '{{ old('preferred_date', '') }}',
                selectedTime: '{{ old('preferred_time', '') }}',
                timeSlots: [],
                loadingSlots: false,
                followUpDoctorId: null,
                otpCode: '',
                availability: {},
                doctorSchedule: '',
                doctorName: '',
                showPrivacy: false,
                showDuplicateModal: {{ session('duplicate_booking') ? 'true' : 'false' }},

                // Calendar Variables
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                daysInMonth: 0,
                startDay: 0,
                monthName: '',
                months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                
                initCalendar() {
                    this.updateCalendar();
                },
                updateCalendar() {
                    let d = new Date(this.currentYear, this.currentMonth, 1);
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
                    let today = new Date();
                    return this.currentYear === today.getFullYear() && this.currentMonth === today.getMonth();
                },
                isNextMonthDisabled() {
                    let today = new Date();
                    let maxMonth = today.getMonth() + 1;
                    let maxYear = today.getFullYear();
                    if (maxMonth > 11) {
                        maxMonth = 0;
                        maxYear++;
                    }
                    return this.currentYear > maxYear || (this.currentYear === maxYear && this.currentMonth >= maxMonth);
                },
                getDateString(day) {
                    let d = new Date(this.currentYear, this.currentMonth, day);
                    let offset = d.getTimezoneOffset();
                    d = new Date(d.getTime() - (offset*60*1000));
                    return d.toISOString().split('T')[0];
                },
                isPastDate(day) {
                    let dateToCheck = new Date(this.currentYear, this.currentMonth, day);
                    let today = new Date();
                    today.setHours(0,0,0,0);
                    return dateToCheck < today;
                },
                isDayFull(day) {
                    let ds = this.getDateString(day);
                    
                    if (this.isFollowUp) {
                        // For follow-up, if the day is not in doctor's schedule, it's considered unavailable (full/disabled)
                        if (!this.validDays) return false;
                        
                        let d = new Date(this.currentYear, this.currentMonth, day);
                        let dayShort = d.toLocaleString('en-US', { weekday: 'short' }); // e.g. "Mon"
                        return !this.validDays.includes(dayShort);
                    } else {
                        // Normal Pedia
                        let slotData = this.availability[ds];
                        if (!slotData) return false; // Default to false while loading
                        if (slotData.capacity <= 0) return true; // Doctor is absent or no capacity
                        return slotData.available <= 0;
                    }
                },
                isDayLimited(day) {
                    if (this.isFollowUp) return false; // Follow-ups don't show "Limited" warning

                    let ds = this.getDateString(day);
                    let slotData = this.availability[ds];
                    if (!slotData || slotData.capacity <= 0) return false;
                    
                    let limitThreshold = slotData.capacity * 0.25; // e.g., if capacity 20, < 5 available is limited
                    return slotData.available > 0 && slotData.available <= limitThreshold;
                },

                // Form Data
                formData: {
                    first_name: '{{ old('first_name', '') }}',
                    middle_name: '{{ old('middle_name', '') }}',
                    last_name: '{{ old('last_name', '') }}',
                    suffix: '{{ old('suffix', '') }}',
                    sex: '{{ old('sex', '') }}',
                    dob: '{{ old('dob', '') }}',
                    civil_status: '{{ old('civil_status', '') }}',
                    blood_type: '{{ old('blood_type', '') }}',
                    address: '{{ old('address', '') }}',
                    barangay: '',
                    philhealth_number: '{{ old('philhealth_number', '') }}',
                    education: '{{ old('education', '') }}',
                    religion: '{{ old('religion', '') }}',
                    occupation: '{{ old('occupation', '') }}',
                    mother_first_name: '',
                    mother_middle_name: '',
                    mother_last_name: '',
                    mothers_maiden_name: '{{ old('mothers_maiden_name', '') }}',
                    classification: '{{ old('classification', '') }}',
                    email: '{{ old('email', '') }}',
                    type: 'pedia',
                    is_follow_up: false,
                    guardian_first_name: '',
                    guardian_middle_name: '',
                    guardian_last_name: '{{ old('guardian_last_name', '') }}',
                    guardian_suffix: '{{ old('guardian_suffix', '') }}',
                    guardian_relation: '{{ old('guardian_relation', '') }}',
                    guardian_contact: '{{ old('guardian_contact', '') }}',
                    guardian_email: '{{ old('guardian_email', '') }}',
                    symptoms: [],
                    other_symptom: '',
                    data_privacy_agreed: {{ old('data_privacy_agreed') ? 'true' : 'false' }}
                },

                formatPhilHealth(value) {
                    if (!value) return '';
                    let raw = value.replace(/\D/g, '').substring(0, 12);
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
                    let parts = [...this.formData.symptoms];
                    if (this.formData.other_symptom && this.formData.other_symptom.trim() !== '') {
                        parts.push('Others: ' + this.formData.other_symptom.trim());
                    }
                    return parts.join(', ');
                },

                init() {
                    @if($errors->any())
                        // Alert user of errors
                        alert("Submission Failed: {{ implode(' ', $errors->all()) }}");

                        // Smart Step Restoration
                        if (this.selectedDate) {
                            this.step = 3; // Go to calendar to confirm date
                            this.maxStepReached = 3;
                            // If we have OTP verified in session? Hard to know. 
                            // But user can click next if date is selected.
                        } else if (this.formData.first_name) {
                            this.step = 2; // Go to info
                            this.maxStepReached = 2;
                        }
                        this.updateProgress();
                    @endif
                },

                errors: {},

                setService(type) {
                    this.formData.type = type;
                    // Adult is always follow-up in this flow. Pedia is optional (checkbox).
                    this.formData.is_follow_up = (type === 'adult');
                    
                    // If changing service type, clarify availability again
                    this.fetchAvailability();
                },

                async nextStep() {
                    this.errors = {};
                    let canProceed = false;

                    if (this.step === 1) {
                        canProceed = true;
                    } else if (this.step === 2) {
                        if (this.validateInfo()) {
                            // Check for duplicate booking before proceeding
                            try {
                                this.isLoading = true;
                                
                                if (this.isFollowUp) {
                                    const followUpRes = await fetch('{{ route("appointment.verify-follow-up") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({
                                            first_name: this.formData.first_name,
                                            last_name: this.formData.last_name,
                                            dob: this.formData.dob
                                        })
                                    });
                                    if (followUpRes.status === 419) {
                                        alert("Your session has expired. Please refresh the page and try again.");
                                        window.location.reload();
                                        return;
                                    }
                                    const followUpData = await followUpRes.json();
                                    if (!followUpData.valid) {
                                        window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: followUpData.message } }));
                                        this.isLoading = false;
                                        return;
                                    }
                                    
                                    // Save the doctor's schedule to restrict calendar
                                    this.doctorSchedule = followUpData.doctor_schedule;
                                    this.validDays = followUpData.valid_days;
                                    this.doctorName = followUpData.doctor_name;
                                    this.followUpDoctorId = followUpData.doctor_id || null;
                                }

                                const response = await fetch('{{ route("appointment.check-duplicate") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        first_name: this.formData.first_name,
                                        last_name: this.formData.last_name,
                                        email: this.formData.email
                                    })
                                });
                                if (response.status === 419) {
                                    alert("Your session has expired. Please refresh the page and try again.");
                                    window.location.reload();
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
                                // If check fails, allow proceed or handle error? Let's allow proceed to fallback to server check
                                canProceed = true;
                            } finally {
                                this.isLoading = false;
                            }
                        } else {
                            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please complete or correct the required fields.' } }));
                            this.$nextTick(() => {
                                const errorInput = document.querySelector('.border-red-500, .ring-red-500');
                                if (errorInput) {
                                    errorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                } else {
                                    const errorTexts = Array.from(document.querySelectorAll('p.text-red-500')).filter(el => el.style.display !== 'none' && el.innerText.trim() !== '');
                                    if(errorTexts.length) errorTexts[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                        }
                    } else if (this.step === 3) {
                        if (!this.selectedDate) {
                            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please select a preferred date.' } }));
                        } else if (!this.selectedTime) {
                            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please select a preferred arrival time.' } }));
                        } else {
                            await this.sendOtp();
                            canProceed = false; // logic handled in sendOtp success
                        }
                    } else if (this.step === 4) {
                        await this.verifyOtp();
                        canProceed = false; // logic handled in verifyOtp success
                    }

                    if (canProceed) {
                        this.step++;
                        if (this.step > this.maxStepReached) this.maxStepReached = this.step;
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
                    // Allow going back to any previous step, or forward if reached before
                    if (targetStep < this.step || targetStep <= this.maxStepReached) {
                        this.step = targetStep;
                        this.updateProgress();
                    }
                },

                updateProgress() {
                    this.progress = (this.step - 1) * 25;
                    this.$nextTick(() => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                },

                selectDate(date) {
                    this.selectedDate = date;
                    this.selectedTime = ''; // Reset time when date changes
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
                        const response = await fetch(`{{ route('appointment.time-slots') }}?${params}`);
                        const data = await response.json();
                        this.timeSlots = data.slots || [];
                    } catch (e) {
                        console.error('Failed to fetch time slots', e);
                    } finally {
                        this.loadingSlots = false;
                    }
                },

                validateInfo() {
                    let isValid = true;
                    if (!this.formData.first_name) { this.errors.first_name = 'Required'; isValid = false; }
                    if (!this.formData.last_name) { this.errors.last_name = 'Required'; isValid = false; }
                    if (!this.formData.sex) { this.errors.sex = 'Required'; isValid = false; }
                    if (!this.formData.dob) { this.errors.dob = 'Required'; isValid = false; }
                    
                    if (this.isFollowUp) {
                        // Follow-ups only need name + DOB (already validated above)
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
                        let dobDate = new Date(this.formData.dob);
                        let age = new Date().getFullYear() - dobDate.getFullYear();
                        let month = new Date().getMonth() - dobDate.getMonth();
                        if (month < 0 || (month === 0 && new Date().getDate() < dobDate.getDate())) {
                            age--;
                        }

                        if (this.formData.type === 'pedia') {
                            if (age > 12) {
                                this.errors.dob = 'Pediatric patients must be 12 years old or below';
                                isValid = false;
                            }
                            if (!this.isFollowUp) {
                                if (!this.formData.guardian_first_name || !this.formData.guardian_last_name) { this.errors.guardian_first_name = 'First and Last Name are required'; isValid = false; }
                                if (!this.formData.guardian_relation) { this.errors.guardian_relation = 'Required'; isValid = false; }
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

                async fetchAvailability() {
                    let d1 = new Date(this.currentYear, this.currentMonth, 1);
                    let d2 = new Date(this.currentYear, this.currentMonth + 1, 0);
                    let offset1 = d1.getTimezoneOffset();
                    let offset2 = d2.getTimezoneOffset();
                    d1 = new Date(d1.getTime() - (offset1*60*1000));
                    d2 = new Date(d2.getTime() - (offset2*60*1000));
                    
                    const start = d1.toISOString().split('T')[0];
                    const end = d2.toISOString().split('T')[0];

                    try {
                        const params = new URLSearchParams({ start, end });
                        const response = await fetch(`{{ route("appointment.check-availability") }}?${params}`);
                        const data = await response.json();
                        this.availability = { ...this.availability, ...data };
                    } catch (e) {
                        console.error("Failed to fetch availability", e);
                    }
                },

                async sendOtp() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route("appointment.send-otp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ email: this.formData.email })
                        });

                        if (response.status === 419) {
                            alert("Your session has expired. Please refresh the page and try again.");
                            window.location.reload();
                            return;
                        }

                        const data = await response.json();

                        if (data.success) {
                            this.step = 4;
                            if (this.step > this.maxStepReached) this.maxStepReached = this.step;
                            this.updateProgress();
                        } else {
                            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: data.message || 'Failed to send OTP' } }));
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'An error occurred. Please try again.' } }));
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
                        const response = await fetch('{{ route("appointment.verify-otp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                email: this.formData.email,
                                otp: this.otpCode
                            })
                        });

                        if (response.status === 419) {
                            alert("Your session has expired. Please refresh the page and try again.");
                            window.location.reload();
                            return;
                        }

                        const data = await response.json();

                        if (data.success) {
                            this.step = 5;
                            if (this.step > this.maxStepReached) this.maxStepReached = this.step;
                            this.updateProgress();
                        } else {
                            this.errors.otp = data.message || 'Invalid OTP';
                        }
                    } catch (e) {
                        this.errors.otp = 'Verification failed. Try again.';
                    } finally {
                        this.isLoading = false;
                    }
                },

                submitForm(e) {
                    if (!this.formData.data_privacy_agreed) {
                        window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please agree to the Data Privacy Act to proceed.' } }));
                        return;
                    }

                    // Prevent default submission initially
                    // Trigger the modal
                    window.dispatchEvent(new CustomEvent('open-confirmation', {
                        detail: {
                            title: 'Confirm Appointment',
                            message: 'Are you sure all the details are correct? Click "Yes, Proceed" to submit your appointment request.',
                            confirmText: 'Yes, Proceed',
                            callback: 'submit-appointment-form'
                        }
                    }));
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                },

                getSlotsUsage(date) {
                    const avail = this.availability[date];
                    if (!avail) return '0/20';
                    if (typeof avail === 'object') {
                        const cap = avail.capacity || 20;
                        const booked = avail.booked !== undefined ? avail.booked : 0;
                        return booked + '/' + cap;
                    }
                    return avail + '/20';
                }
            }
        }
    </script>
    <script>
        // Filter fields to strictly alphanumeric uppercase without symbols/numbers
        document.addEventListener('input', function (e) {
            const nameFields = ['first_name', 'middle_name', 'last_name', 'suffix', 'guardian_first_name', 'guardian_last_name', 'guardian_middle_name', 'guardian_suffix', 'mothers_maiden_name'];
            if (e.target.tagName === 'INPUT' && nameFields.includes(e.target.name)) {
                let val = e.target.value;
                let newVal = val.replace(/[^a-zA-ZÑñ\s\.\-\d]/g, '').toUpperCase();
                if (val !== newVal) {
                    let start = e.target.selectionStart;
                    let end = e.target.selectionEnd;
                    e.target.value = newVal;
                    if (e.target.hasAttribute('x-model') || e.target.hasAttribute('wire:model')) {
                        e.target.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    try { e.target.setSelectionRange(start, end); } catch (ex) { }
                }
            }
        });
    </script>
@endsection