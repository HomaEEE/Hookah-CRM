<div class="modal-container flex p-4 gap-4 border-r border border-zinc-100" id="modal-{{$modal}}">
                <!--div class="bottom-sheet-handle"></div-->
                <div class="modal-menu-close" title="Close"></div>

        <div class="modal-{{$modal}} content">
            @if ($modal == 'item')
                <div class="flex flex-col xs:flex-row w-full gap-5">
                    <div class="flex gap-4">
                        <!-- Фото слева -->
                        <div class="w-32 h-32 xs:w-full shrink-0 overflow-hidden relative">
                            @if(!empty($item['new']))
                                <p class="sup">NEW</p>
                            @endif
                            <img src="" id="modal-image" class="w-full h-full object-cover">
                        </div>

                        <!-- Название, текст, цена справа -->
                        <div class="flex flex-row flex-1 min-w-0 py-0.5">
                            <div class=" text-lg sm:text-xl leading-tight mb-1.5 truncate modal-menu-title" id="modal-title"></div>
                            <p class="text-sm line-clamp-3 mb-3 modal-menu-description" id="modal-description"></p>

                            <div class="mt-auto">
                                <div class="modal-menu-price" id="modal-price"></div>
                                <div class="text-xs" id="modal-out"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка закрыть внизу -->
                    <button class="w-full py-3 modal-buttom-close" id="close-modal" title="Close">
                        {{__('Close')}}
                    </button>
                </div>
            @endif
            @if ($modal == 'feedback')

            <div class="modal-{{$modal}} content" >
                <h4>{{ __('Feedback') }}</h4>
            <p>{{ __('Feedback comment') }}</p>

            <form data-action="/sendFeedback" method="POST" id="feedback" class="form">
                @csrf
                <input type="hidden" name="place_id" value="{{ $place->id }}" id="place_id">

                <div class="input-group">
                    <input type="text" name="name" id="name" placeholder=" " required />
                    <label for="name">{{ __('Name') }}</label>
                </div>
                <div class="input-group">
                    <input type="tel" name="contact" id="name" placeholder=" " required />
                    <label for="contact">{{ __('Phone or Telegram') }}</label>
                </div>

                <div class="input-group">
                    <textarea type="text" name="text" id="text" rows="8" required></textarea>
                    <label for="text">{{ __('Comment') }}</label>
                </div>
                <button type="submit" class="button-success">{{ __('Send') }}</button>
            </form>
            </div>

            @endif

        </div>
    </div>
</div>