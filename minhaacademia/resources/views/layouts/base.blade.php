<!DOCTYPE html>
<!--
  "Educai as crianças para que não seja necessário punir os adultos."

  Pitágoras.
-->
<html lang="pt-br">
<head>
  <!-- Basic Page Needs -->
  <meta charset="utf-8">
  <title>{{ request('pagetitle', '') }}</title>
  <meta name="description" content="{{ request('appDescription', '') }}">
  <meta name="author" content="{{ request('appAuthor', '') }}">

  <!-- Mobile Specific Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT -->
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,400;0,600;0,900;1,400;1,600;1,900&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ url(mix('css/normalize.css')) }}">
  <link rel="stylesheet" href="{{ url(mix('css/skeleton.css')) }}">
  <link rel="stylesheet" href="{{ url(mix('css/custom.css')) }}">

  <!-- Scripts -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  {!! request('mathjax', '') !!}

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

</head>
<body>
    @if(request('googleapi', null) !== null)
    <div class="alert alert-info">
      <p>{{ request('googleapi', null) }}</p>
    </div>
    @endif

<!-- Primary Page Layout -->
    @yield('main')
<!-- End Document -->
</body>
</html>