@extends('layouts.app')

@include('components.nav')

@section('content')
    <div class="w-full lg:max-w-8xl bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                    <a href="#" class="inline-block p-4 @if(Route::has('settings')) text-blue-600 @endif border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500" aria-current="page">Settings</a>
                </li>
            </ul>
        </div>
        <div class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
{{--            <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">{{ $token }}</span>--}}

            <h2 class="text-gray-900 dark:text-white">Двофакторна автентифікація</h2>

            @if(session('status') == 'two-factor-authentication-enabled')
                <p class="text-gray-900 dark:text-white">Ви успішно увімкнули двофакторну автентифікацію!</p>
            @endif

            @if(auth()->user()->two_factor_secret)
                {{-- Користувач увімкнув 2FA --}}
                <p class="text-gray-900 dark:text-white">Статус: <span style="color: green;">Увімкнено</span></p>

                {{-- Показуємо QR-код та коди відновлення --}}
                <div>
                    <p class="text-gray-900 dark:text-white">Збережіть ці коди відновлення у надійному місці. Вони знадобляться для доступу до вашого акаунту, якщо ви втратите свій пристрій.</p>
                    <ul>
                        @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                            <li>{{ $code }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Форма для вимкнення 2FA --}}
                <form method="POST" action="/user/two-factor-authentication">
                    @csrf
                    @method('DELETE')
                    <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="submit">Вимкнути 2FA</button>
                </form>

            @else
                {{-- Користувач ще не увімкнув 2FA --}}
                <p class="text-gray-900 dark:text-white">Статус: <span style="color: red;">Вимкнено</span></p>

                {{-- Форма для увімкнення 2FA --}}
                <form method="POST" action="/user/two-factor-authentication">
                    @csrf
                    <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="submit">Увімкнути 2FA</button>
                </form>
            @endif

            {{-- Якщо 2FA ще не підтверджено, показуємо QR-код --}}
            @if (session('status') == 'two-factor-authentication-enabled' || session('status') == 'two-factor-authentication-confirmed')
                <div>
                    <p>Відскануйте цей QR-код за допомогою вашого додатку для автентифікації (наприклад, Google Authenticator).</p>
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}

                    <p>Або введіть цей ключ вручну: {{ decrypt(auth()->user()->two_factor_secret) }}</p>

                    {{-- Форма для підтвердження --}}
                    <form method="POST" action="/user/confirmed-two-factor-authentication">
                        @csrf
                        <label for="code">Код підтвердження:</label>
                        <input type="text" name="code" id="code" required>
                        <button type="submit">Підтвердити та активувати</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @include('components.footer')
@endsection
