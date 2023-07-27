<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')_{{ \Login::get_view_kindergarten_name() }}</title>
    <!-- <title>{{ config('app.name') }}</title> -->
    @yield('styles')
    @yield('scripts')
  </head>
  <body id="print" class="{{ \Login::get_view_type() }}">
    <header>
      <div class="right _container mb-2">
        {{ \Login::get_view_kindergarten_name() }}
      </div>
    </header>
    <main>
      <div class="_container">
        @yield('content')
      </div>
    </main>
    <!-- <footer>
      <hr/>
      <p class="text-center">
        <small>{{ config('app.name_en') }}</small>
      </p>
    </footer> -->
  </body>
</html>
