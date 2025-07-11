@extends('layouts.app')

@section('content')
    <h2>Введіть код двофакторної автентифікації</h2>

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf

        <div>
            <label for="code">Код</label>
            {{-- Ввід коду з додатку --}}
            <input id="code" type="text" name="code" autofocus autocomplete="one-time-code">
        </div>

        <p>Або використайте код відновлення:</p>

        <div>
            <label for="recovery_code">Код відновлення</label>
            {{-- Ввід коду відновлення --}}
            <input id="recovery_code" type="text" name="recovery_code" autocomplete="one-time-code">
        </div>

        <button type="submit">
            Увійти
        </button>
    </form>
@endsection
