<div class="modal-container flex flex-col md:flex-row p-4 gap-4 border-r border border-zinc-100"
     id="modal-feedback"

     x-data="{
         formData: { name: '', contact: '', text: '', place_id: '{{ $place->id }}' },
         loading: false,
         success: false,
         errorMessage: ''
     }">

    <div class="modal-menu-close" title="Close"></div>

    <div class="modal-feedback content w-full">
        <div x-show="success" x-cloak class="p-4 text-green-500 border border-green-500/50 rounded-lg">
            {{ __('Thank you for your feedback!') }}
        </div>

        <form x-show="!success"
              @submit.prevent="
                  loading = true;
                  errorMessage = '';
                  fetch('/api/feedback', {
                      method: 'POST',
                      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                      body: JSON.stringify(formData)
                  })
                  .then(response => response.ok ? (success = true) : Promise.reject())
                  .catch(() => errorMessage = '{{ __('Something went wrong') }}')
                  .finally(() => loading = false)
              "
              class="form">

            <div x-show="errorMessage" x-text="errorMessage" class="text-red-500 mb-4"></div>

            <div class="input-group">
                <input type="text" x-model="formData.name" placeholder=" " required />
                <label>{{ __('Name') }}</label>
            </div>

            <div class="input-group">
                <input type="tel" x-model="formData.contact" placeholder=" " required />
                <label>{{ __('Phone or Telegram') }}</label>
            </div>

            <div class="input-group">
                <textarea x-model="formData.text" rows="8" required></textarea>
                <label>{{ __('Comment') }}</label>
            </div>

            <button type="submit" :disabled="loading" class="button-success">
                <span x-show="!loading">{{ __('Send') }}</span>
                <span x-show="loading">{{ __('Sending...') }}</span>
            </button>
        </form>
    </div>
</div>