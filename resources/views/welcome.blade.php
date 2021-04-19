<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
        <div class="top-right links">
            @auth
            @if(auth()->user()->is_admin)
            <a href="{{ url('/dashboard') }}">Главная</a>
            @else
            <a href="{{ url('/products') }}">Продукты</a>
            @endif
            @else
            <a href="{{ route('login') }}">Войти</a>

            @if (Route::has('register'))
            <a href="{{ route('register') }}">Регистрация</a>
            @endif
            @endauth
        </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                SherstkovService
            </div>
            <h3>Демо-аккаунты</h3>
            <table class="table mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Логин</th>
                        <th scope="col">Пароль</th>
                        <th scope="col">Описание</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>admin@site.local</th>
                        <td>admin</td>
                        <td>Аккаунт администратора</td>
                    </tr>
                    <tr>
                        <th>user@site.local</th>
                        <td>user</td>
                        <td>Аккаунт пользователя</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
