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
  <body id="kindergarten" class="{{ \Login::get_view_type() }}">
    <header>
      <div class="container">
        @yield('header')
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
