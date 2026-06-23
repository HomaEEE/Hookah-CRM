<section class=" mx-auto p-4 md:p-8 mb-24" id="reserve">
<div class="reserve relative fade-up"
         x-data="reservationForm">
         <div class="bg-img ">
            <img src="{{ asset('assets/imgs/about/reserve/bg.webp') }}" alt="Резерв стола Noire" class="w-full h-full object-cover" />
         </div>

        <div class="container flex flex-col justify-between">

            <div class="flex items-center gap-6 w-full">
                <h3 class="whitespace-nowrap">
                    {{ __('Reserve table') }}
                </h3>
                <div class="flex-grow h-[1px] bg-white/30"></div>
            </div>

            <div x-show="success" x-transition x-cloak class="glass w-full">
                <div class="flex flex-col md:flex-row gap-10 items-left">
                    <div class="text-4xl mb-4">🥂</div>
                    <p class="text-xl font-light text-white uppercase tracking-wide">Стіл успішно заброньовано!</p>
                    <p class="text-sm text-gray-400 mt-2">Очікуйте дзвінка для підтвердження.</p>
                </div>
            </div>

            <form x-show="!success" @submit.prevent="submitForm" class="reservation-form glass">

    <div x-show="errorMessage" x-transition x-cloak class="reservation-form__error" x-text="errorMessage"></div>

    <div x-show="step === 1" x-transition.opacity.duration.300ms class="reservation-form__step">

        <div class="reservation-form__field">
            <label>{{ __('Persons') }}</label>
            <select x-model="formData.guests">
                <template x-for="n in 10">
                    <option :value="n" x-text="n + (n === 1 ? ' персона' : (n > 1 && n < 5 ? ' персони' : ' персон'))"></option>
                </template>
                <option value="10+">10+ персон</option>
            </select>
        </div>

        <div class="reservation-form__field">
            <label>{{ __('Date') }}</label>
            <select x-show="dateMode !== 'custom'" x-model="dateMode" @change="handleDateChange">
                <template x-for="item in availableDates">
                    <option :value="item.value" x-text="item.label"></option>
                </template>
                <option value="custom">📅 {{ __('Other date...') }}</option>
            </select>

            <div x-show="dateMode === 'custom'" class="reservation-form__custom-date">
                <input x-model="formData.date" type="date" :required="dateMode === 'custom'">
                <button type="button" @click="dateMode = availableDates[0].value; handleDateChange()">✕</button>
            </div>
        </div>

        <div class="reservation-form__field">
            <label>{{ __('Time') }}</label>
            <select x-model="formData.time">
                <template x-for="t in availableTimes">
                    <option :value="t" x-text="t"></option>
                </template>
            </select>
        </div>

        <button type="button" @click="step = 2" class="reservation-form__btn reservation-form__btn--primary">
            {{ __('Next step reserve') }}
        </button>
    </div>

    <div x-show="step === 2" x-transition.opacity.duration.300ms x-cloak class="reservation-form__step">

        <div class="reservation-form__field">
            <label>{{ __('Name') }}</label>
            <input type="text" x-model="formData.name" required placeholder="Олександр">
        </div>

        <div class="reservation-form__field">
            <label>{{ __('Phone') }}</label>
            <input type="tel" x-model="formData.phone" x-mask="+380 (99) 999-99-99" required placeholder="+380 (__) ___-__-__">
        </div>

        <div class="reservation-form__actions flex-col md:flex-row gap-10">
            <button type="button" @click="step = 1" :disabled="loading" class="reservation-form__btn">
                {{ __('Back') }}
            </button>
            <button type="submit" :disabled="loading" class="reservation-form__btn reservation-form__btn--primary">
                <span x-show="loading" class="spinner"></span>
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
                guests: null,
                place_id: '{{ $place_id }}',
                date: '',
                time: '',
                name: '',
                phone: ''
            },

            init() {
                // Генерация массивов работает идеально
                this.generateDates();
                this.generateTimes();

                // Стартовые значения
                this.formData.guests = 2;
                this.formData.time = '12:00';
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

        // Берем год, месяц и день строго в локальном часовом поясе пользователя
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const value = `${year}-${month}-${day}`;

        dates.push({ label, value });
    }

    this.availableDates = dates;
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

                    // 1. СКЛЕИВАЕМ ДАТУ И ВРЕМЯ ИЗ ФОРМЫ
                    // Берем сгенерированную дату (напр. 2026-10-11) и время (19:00)
                    const fullDateTime = `${this.formData.date} ${this.formData.time}:00`;

                    // 2. ФОРМИРУЕМ ПРАВИЛЬНЫЙ ОБЪЕКТ ДЛЯ LARAVEL
                    const payload = {
                        place_id: parseInt(this.formData.place_id, 10),
                        name: this.formData.name,
                        phone: this.formData.phone,
                        person: parseInt(this.formData.guests, 10),
                        time: fullDateTime
                    };

                    const response = await fetch('/api/v1/reservations', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload) // Отправляем подготовленный payload!
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