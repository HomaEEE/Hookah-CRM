<div class="modal-container" id="modal-{{$modal}}">
                <div class="bottom-sheet-handle"></div>
                <div class="modal-menu-close" title="Close"></div>

        <div class="modal-{{$modal}} content">
            @if ($modal == 'item')
                <div class="modal-img">
                        @if(!empty($item['sup']))
                            <p class="sup" id="modal-sup">{{ $item['sup'] }}</p>
                        @endif
                     <img src=""  id="modal-image">
                    </div>

                <h4 class="modal-menu-title" id="modal-title"></h4>
                <p class="modal-menu-description" id="modal-description"></p>
                <p class="modal-menu-price" id="modal-price"></p>
                <p class="modal-menu-out" id="modal-out"></p>
                <div class="modal-buttom-close" id="close-modal" title="Close">{{__('Close')}}</div>

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