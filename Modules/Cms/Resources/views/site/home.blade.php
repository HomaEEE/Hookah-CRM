@extends('cms::layouts.site')

@section('page', __('Home page'))
@section('description', 'Кальянна у центрі Одеси (вул. Ніни Строкатої, 27). Преміальні кальяни, авторські коктейлі, кухня та атмосфера ідеального відпочинку. Бронюйте столик у Noire!')
@section('keywords', 'кальянна, Одеса, центр, Ніни Строкатої, 27, преміальні кальяни, авторські коктейлі, кухня, відпочинок, Noire, бронювання столика')

@push('schema')

<script type="application/ld+json">
@verbatim
{
  "@context": "https://schema.org",
  "@type": "BarOrPub",
  "name": "Noire Lounge Bar",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Nina Strokata Street 27",
    "addressLocality": "Odesa",
    "addressRegion": "OD",
    "postalCode": "65000",
    "addressCountry": "UA"
  },
  "image": "https://noire.ee/assets/imgs/logo.svg",
  "url": "https://noire.ee",
  "telephone": "+380684442454",
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
      ],
      "opens": "12:00",
      "closes": "22:30"
    }
  ],
  "priceRange": "$$$",
  "servesCuisine": [
    "Hookah",
    "Cocktails",
    "Food"
  ],
  "hasMenu": "https://noire.ee/menu",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.6",
    "reviewCount": "1000"
  }
}
@endverbatim
</script>
@endpush

@section('content')

    @include('cms::site.partials.home.hero')
    @include('cms::site.partials.home.about')
    @include('cms::site.partials.home.reserve', [
        'place_id' => $place->id,
    ])
    @include('cms::site.partials.home.menu')
    @include('cms::site.partials.home.feedbacks')
    @include('cms::site.partials.home.footer')

@endsection