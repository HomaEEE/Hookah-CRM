<section class=" mx-auto px-4 mb-24">
<div class="reserve relative fade-up"
         x-data="reservationForm">
         <div class="bg-img ">
            <img src="{{ asset('assets/imgs/about/reserve/bg.webp') }}" alt="Резерв стола Noire" class="w-full h-full object-cover" />
         </div>

        <div class="container flex flex-col justify-between">

            <div class="flex items-center gap-6 w-full">
                <h2 class="text-2xl md:text-3xl text-white font-light uppercase tracking-wide whitespace-nowrap">
                    {{ __('Reserve') }}
                </h2>
                <div class="flex-grow h-[1px] bg-white/30"></div>
            </div>

            <div x-show="success" x-transition x-cloak class="glass w-full">
                <div class="text-4xl mb-4">🥂</div>
                <p class="text-xl font-light text-white uppercase tracking-wide">Стіл успішно заброньовано!</p>
                <p class="text-sm text-gray-400 mt-2">Очікуйте дзвінка для підтвердження.</p>
            </div>

            <form x-show="!success" @submit.prevent="submitForm" class="glass">

                <div x-show="errorMessage" x-transition x-cloak class="mb-6 p-3 bg-red-500/20 border border-red-500/50 text-white text-sm rounded-lg text-center" x-text="errorMessage"></div>

                <div x-show="step === 1" x-transition.opacity.duration.300ms class="flex flex-col md:flex-row items-center justify-between gap-6 md:gap-4">

                    <div class="flex flex-col flex-1 w-full border-b border-white/10 md:border-none pb-3 md:pb-0">
                        <label class="text-[11px] text-gray-400 font-medium tracking-wider mb-1 uppercase">Гости</label>
                        <select x-model="formData.guests" class="bg-transparent text-white text-lg font-light outline-none appearance-none cursor-pointer w-full">
                            <template x-for="n in 10">
                                <option class="text-black" :value="n" x-text="n + (n === 1 ? ' персона' : (n > 1 && n < 5 ? ' персони' : ' персон'))"></option>
                            </template>
                            <option class="text-black" value="10+">10+ персон</option>
                        </select>
                    </div>

                    <div class="flex flex-col flex-1 w-full border-b border-white/10 md:border-none pb-3 md:pb-0">
                        <label class="text-[11px] text-gray-400 font-medium tracking-wider mb-1 uppercase">Дата</label>
                        <select x-show="dateMode !== 'custom'" x-model="dateMode" @change="handleDateChange" class="bg-transparent text-white text-lg font-light outline-none appearance-none cursor-pointer w-full">
                            <template x-for="item in availableDates">
                                <option class="text-black" :value="item.value" x-text="item.label"></option>
                            </template>
                            <option class="text-black" value="custom">📅 {{ __('Other date...') }}</option>
                        </select>

                        <div x-show="dateMode === 'custom'" class="flex items-center gap-2 w-full">
                            <input x-model="formData.date" type="date" :required="dateMode === 'custom'" class="bg-transparent text-white text-lg font-light outline-none cursor-pointer w-full [&::-webkit-calendar-picker-indicator]:filter [&::-webkit-calendar-picker-indicator]:invert">
                            <button type="button" @click="dateMode = availableDates[0].value; handleDateChange()" class="text-gray-400 hover:text-white text-sm">✕</button>
                        </div>
                    </div>

                    <div class="flex flex-col flex-1 w-full border-b border-white/10 md:border-none pb-4 md:pb-0">
                        <label class="text-[11px] text-gray-400 font-medium tracking-wider mb-1 uppercase">Время</label>
                        <select x-model="formData.time" class="bg-transparent text-white text-lg font-light outline-none appearance-none cursor-pointer w-full">
                            <template x-for="t in availableTimes">
                                <option class="text-black" :value="t" x-text="t"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex-shrink-0 w-full md:w-auto pt-2 md:pt-0">
                        <button type="button" @click="step = 2" class="w-full md:w-auto bg-[#f4f4f5] hover:bg-white text-black font-medium text-sm px-8 py-4 rounded-xl transition-colors duration-300">
                            {{ __('Next step reserve') }}
                        </button>
                    </div>
                </div>

                <div x-show="step === 2" x-transition.opacity.duration.300ms x-cloak class="flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8">

                    <div class="flex flex-col flex-1 w-full">
                        <label class="text-[11px] text-gray-400 font-medium tracking-wider mb-2 uppercase">{{ __('Name') }}</label>
                        <input type="text" x-model="formData.name" required placeholder="Олександр" class="bg-transparent text-white text-lg font-light border-b border-white/20 focus:border-white pb-2 outline-none w-full transition-colors placeholder-gray-500">
                    </div>

                    <div class="flex flex-col flex-1 w-full">
                        <label class="text-[11px] text-gray-400 font-medium tracking-wider mb-2 uppercase">{{ __('Phone') }}</label>
                        <input type="tel" x-model="formData.phone" required placeholder="+380 (__) ___-__-__" class="bg-transparent text-white text-lg font-light border-b border-white/20 focus:border-white pb-2 outline-none w-full transition-colors placeholder-gray-500">
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto mt-4 md:mt-0">
                        <button type="button" @click="step = 1" :disabled="loading" class="px-6 py-4 text-white/70 hover:text-white font-medium text-sm transition-colors disabled:opacity-50">
                            {{ __('Back') }}
                        </button>

                        <button type="submit" :disabled="loading" class="flex-grow md:flex-grow-0 w-full md:w-auto bg-[#f4f4f5] hover:bg-white text-black font-medium text-sm px-8 py-4 rounded-xl transition-colors duration-300 flex items-center justify-center gap-2 disabled:opacity-50">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-black" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loading ? '{{ __('Sending...') }}' : '{{ __('Reserve') }}'"></span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reservationForm', () => ({
            step: 1,
            dateMode: '',
            loading: false,
            success: false,
            errorMessage: '',

            availableDates: [],
            availableTimes: [],

            formData: {
                guests: null, // Оставляем пустым изначально
                date: '',
                time: '',
                name: '',
                phone: ''
            },

            init() {
                // 1. Сначала генерируем массивы
                this.generateDates();
                this.generateTimes();

                // 2. ЗАТЕМ задаем значения по умолчанию (важно совпадение типов!)
                this.formData.guests = 2; // Строго число 2, а не строка '2'
                this.formData.time = '19:00'; // Задаем стартовое время
            },

            generateDates() {
                const dates = [];
                const today = new Date();

                for (let i = 0; i <= 6; i++) {
                    const d = new Date(today);
                    d.setDate(today.getDate() + i);

                    let label = '';
                    if (i === 0) {
                        label = 'Сьогодні';
                    } else if (i === 1) {
                        label = 'Завтра';
                    } else {
                        label = d.toLocaleDateString('uk-UA', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    }

                    const value = d.toISOString().split('T')[0];
                    dates.push({ label, value });
                }

                this.availableDates = dates;

                // Устанавливаем сегодняшнюю дату по умолчанию
                this.dateMode = dates[0].value;
                this.formData.date = dates[0].value;
            },

            generateTimes() {
                const times = [];
                for (let h = 12; h <= 20; h++) {
                    times.push(`${h}:00`);
                    if (h !== 20) {
                        times.push(`${h}:30`);
                    }
                }
                this.availableTimes = times;
            },

            handleDateChange() {
                if (this.dateMode !== 'custom') {
                    this.formData.date = this.dateMode;
                } else {
                    this.formData.date = '';
                }
            },

            async submitForm() {
                this.loading = true;
                this.errorMessage = '';

                try {
                    const tokenElement = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = tokenElement ? tokenElement.getAttribute('content') : '';

                    const response = await fetch('/api/reservations', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (result.errors) {
                            throw new Error(Object.values(result.errors)[0][0]);
                        }
                        throw new Error(result.message || 'Помилка відправки.');
                    }

                    this.success = true;

                } catch (error) {
                    this.errorMessage = error.message;
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>