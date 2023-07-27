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
  <body>
    <header>
      <div class="container">
          <nav class="navbar navbar-dark bg-dark fixed-top">
              <a class="navbar-brand" href="/">{{ config('app.name') }}</a>
              <ul class="nav small">
                  <li class="nav-item">
                      <button class="btn btn-secondary ml-auto" onclick="window.close();">この画面を閉じる</button>
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
