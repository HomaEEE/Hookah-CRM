<section class="feedbacks">
<div class="container mx-auto p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12">
                    <div>
                        <h2>{{ __('Feedbacks') }}</h2>
                    </div>
                    <div class="content-start md:content-end">
                        <a href="#comment"><p class="place-content-start md:place-self-end">{{ __('Send feedback') }}</p></a>
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