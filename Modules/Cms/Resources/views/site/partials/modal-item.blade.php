<div class="modal-container flex flex-col md:flex-row p-4 gap-4 border-r border border-zinc-100" id="modal-item">

    <div class="modal-menu-close" title="Close"></div>

    <div class="modal-item content w-full">
            <div class="flex flex-col w-full gap-5">

                <div class="flex flex-col md:flex-row gap-4">

                      <div class="w-full md:w-42 md:h-42 shrink-0 overflow-hidden relative">
                        @if(!empty($item['new']))
                            <p class="sup">NEW</p>
                        @endif
                        <img src="" id="modal-image" class="w-full h-full object-cover">
                    </div>

                    <div class="flex flex-col flex-1">
                        <div class="" id="modal-title"></div>
                        <div class="" id="modal-description"></div>

                        <div class="mt-auto">
                            <div class="" id="modal-price"></div>
                            <div class="text-xs" id="modal-out"></div>
                        </div>

                        <button class="w-full py-3 modal-buttom-close" id="close-modal" title="Close">
                            {{ __('Close') }}
                        </button>
                    </div>

                </div>
        </div>
    </div>
</div>