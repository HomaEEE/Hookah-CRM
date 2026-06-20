<section class="feedbacks mb-16">
<div class="container mx-auto p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">
                    <div>
                        <h2>{{ __('Feedbacks') }}</h2>
                    </div>
                  <div class="content-start md:content-end" x-data>
    <button @click="$dispatch('open-feedback-modal')" class="cursor-pointer">
        <p class="place-content-start md:place-self-end">{{ __('Feedback button') }}</p>
    </button>
</div>

<div x-data="{ show: false }"
     @open-feedback-modal.window="show = true"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/80">

    <div @click.away="show = false" class="modal-container ...">
        <div class="modal-menu-close" @click="show = false" title="Close"></div>

        @include('cms::site.partials.modal-feedback')
    </div>
</div>
                </div>
                </div>
                <div class="swiper-feedbacks">
                <div class="swiper-wrapper">
                  @foreach ($feedbacks as $feedback)
                      <div class="swiper-slide feedback-item">

                        <div class="author">
                          <p>{{ $feedback->name }}</p>
                          <span>{{ $feedback->created_at->format('d.m.Y') }}</span>
                        </div>
                        <p>{{ $feedback->text }}</p>

                      </div>
                  @endforeach
                </div>
              </div>
</section>

