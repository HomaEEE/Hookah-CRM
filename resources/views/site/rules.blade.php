@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Rules page'))

@section('content')
<div class="container page">
    <div class="row">
        <div class="col-xs">
            <h2>{{__('Rules')}}</h2>

            <h4>КУРИМО. КАЛЬЯН ПРАВИЛЬНО!</h4>

            Друзі, ми в Noire прагнемо, щоб кожен ваш візит був незабутнім. Аби досягти цього, просимо вас дотримуватися кількох простих правил.

            <ul>
                <li>Один кальян – для двох: Так, саме так! Це дозволить кожному насолодитися смаком без перегріву та втрати аромату.</li>
                <li>Не на голодний шлунок: Поєднайте куріння з нашою смачною їжої та крафтовими лимонадами.</li>
                <li>Продувайте кальян: Це допоможе зберегти насичений смак.</li>
                <li>Не залишайте без нагляду: Наші майстри завжди готові допомогти.</li>
                <li>Близько 60 хвилин – оптимальний час для насолоди кальяном.</li>
            </ul>

            <h4> ВАЖЛИВО!</h4>
            <ul>
                <li>18+: Пам’ятайте про документ, що посвідчує особу.</li>
                <li>Тільки кальяни: <b>цигарки, Iqos, Glo, вейпи та інші електроні аналоги</b> залиште вдома.</li>
                <li>Атмосфера поваги: Будьте ввічливими до персоналу та інших відвідувачів.</li>
            </ul>
        </div>
    </div>

</div>
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>

<script src="/assets/js/odessa.js"></script>
@endpush
