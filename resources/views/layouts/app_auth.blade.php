<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="hold-transition {{ Route::currentRouteName() == 'login' ? 'login' : 'register' }}-page ">
    <div class="{{ Route::currentRouteName() == 'login' ? 'login' : 'register' }}-box" id="app">

        <div class="{{ Route::currentRouteName() == 'login' ? 'login' : 'register' }}-logo">
            <a href="/dashboard"><b>Sherstkov</b>Service</a>
        </div>

        <router-view></router-view>
        
        <vue-progress-bar></vue-progress-bar>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>

</html>
