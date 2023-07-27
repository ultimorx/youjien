<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    @yield('styles')
    @yield('scripts')
  </head>
  <body id="city">
    <header>
      <div class="container">
          <nav class="navbar navbar-dark bg-purple fixed-top">
              <span>
                  <a class="navbar-brand" href="/city">本巣市{{ config('app.name') }}</a>
                  <a class="navbar-brand small" href="/login">ログアウト</a>
            </span>
              <ul class="nav small">
                  <li class="nav-item">
                      <a class="nav-link text-white" href="{{ url('city') }}">トップ</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link text-white" href="{{ url('city/information') }}">連絡</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link text-white" href="{{ url('city/absence') }}">病欠理由</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link text-white" href="{{ url('city/event') }}">園外行事</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link text-white" href="{{ url('city/user') }}">ユーザー</a>
                  </li>
              </ul>
          </nav>
      </div>
    </header>
    <main>
      <div class="container">
        @yield('content')
      </div>
    </main>
    <footer>
      <hr/>
      <p class="text-center">
        <small>{{ config('app.name_en') }}</small>
      </p>
    </footer>
  </body>
</html>
