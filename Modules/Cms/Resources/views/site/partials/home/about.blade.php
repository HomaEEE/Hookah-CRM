<section class="about">

    <div class="marquee" style="overflow: hidden; width: 100%;">
        <div class="marquee-track" style="display: flex; white-space: nowrap;">
            <span class="marquee-item">{{ __('About marquee item') }} </span>
            <span class="marquee-item">{{ __('About marquee item') }} </span>
            <span class="marquee-item">{{ __('About marquee item') }} </span>
            <span class="marquee-item">{{ __('About marquee item') }} </span>
            <span class="marquee-item">{{ __('About marquee item') }} </span>
            <span class="marquee-item">{{ __('About marquee item') }} </span>
            <span class="marquee-item">{{ __('About marquee item') }} </span>
            <span class="marquee-item">{{ __('About marquee item') }} </span>
        </div>
    </div>


<div class="inner py-12">
    <div class="container mx-auto px-4  grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <div class="relative w-full aspect-square mx-auto flex items-center justify-center">

            <div class="absolute inset-0 w-full h-full pointer-events-none">

                <div class="rotate-clockwise absolute inset-0 w-full h-full flex items-center justify-center origin-center will-change-transform">
                    <svg viewBox="0 0 200 200" class="w-full h-full overflow-visible">
                        <path id="outerCirclePath" d="M 100, 100 m -75, 0 a 75,75 0 1,1 150,0 a 75,75 0 1,1 -150,0" fill="transparent" />

                        <text font-size="10.3" font-weight="300" letter-spacing="1" fill="#BBBBBB">
                            <textPath href="#outerCirclePath" startOffset="0%">
                                Lounge bar NOIRE Lounge bar NOIRE Lounge bar NOIRE Lounge bar NOIRE
                            </textPath>
                        </text>
                    </svg>
                </div>

                <div class="rotate-counterclockwise absolute inset-0 w-full h-full flex items-center justify-center origin-center will-change-transform">
                    <svg viewBox="0 0 200 200" class="w-full h-full overflow-visible">
                        <path id="innerCirclePath" d="M 100, 100 m -65, 0 a 65,65 0 1,1 130,0 a 65,65 0 1,1 -130,0" fill="transparent" />

                        <text font-size="8" font-weight="200" letter-spacing="1" fill="#727272">
                            <textPath href="#innerCirclePath" startOffset="0%">
                                Smoke Different Smoke Different Smoke Different Smoke Different Smoke Different
                            </textPath>
                        </text>
                    </svg>
                </div>

            </div>

            <div class="relative w-full h-full z-10 flex items-center justify-center">
                <img src="{{ asset('assets/imgs/about/coal-1.webp') }}"
                    alt="Уголь"
                    id="coal-1"
                    width="465"
                    height="471"
                    loading="lazy"
                    decoding="async"
                    class="absolute w-[27vw] md:w-[12vw] top-[20%] left-[30%] drop-shadow-[0_0_15px_rgba(255,85,0,0.4)]">

                <img src="{{ asset('assets/imgs/about/coal-2.webp') }}"
                    alt="Уголь"
                    id="coal-2"
                    width="383"
                    height="338"
                    loading="lazy"
                    decoding="async"
                    class="absolute w-[30vw] md:w-[30%] bottom-[25%] left-[20%] drop-shadow-[0_0_20px_rgba(255,60,0,0.5)]">

                <img src="{{ asset('assets/imgs/about/coal-3.webp') }}"
                    alt="Уголь"
                    id="coal-3"
                    width="405"
                    height="347"
                    loading="lazy"
                    decoding="async"
                    class="absolute w-[25vw] md:w-[30%] top-[40%] right-[20%] drop-shadow-[0_0_10px_rgba(255,100,0,0.3)]">
            </div>

        </div>

        <div class="flex flex-col z-20 ">
            <h3 class="gradient-title">{{ __('Atmosphere') }}</h3>
            <p>{{ __('Atmosphere description') }}</p>
            <span>{{ __('Atmosphere quote') }}</span>
        </div>

    </div>
</div>
</section>

