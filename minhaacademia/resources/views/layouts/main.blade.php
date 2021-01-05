@extends('layouts.base')
@section('main')
  <div class="section menu">
    <div class="container first bg-white u-full-width">
      <div class="row">
        <div class="four columns text-left">
            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="Professor Aquino - Matemática"></a>
        </div>
        <div class="three columns text-center">
          <a href="{{ route('donation') }}"><i class="fa fa-dollar dark-gray size-m"></i>Fazer doação</a>
        </div>
        <div class="five columns text-right">
            {!! request('navmenu', '') !!}
        </div>
      </div>
    </div>
  </div>

  @yield('content')

  <div class="section footer">
    <div class="container last bg-white">
        <div class="row">
            {!! request('socialmenu', '') !!}
        </div>
        <div class="row">
          <p>{{ request('appName', ' ') }} &bullet; {{ now()->year }}</p>
        </div>
        <div class="row">
          <ul class="navmenu">
            <li><a href="{{ route('about') }}">Sobre</a></li>
            <li><a href="{{ route('contact') }}">Contato</a></li>
            <li><a href="{{ route('terms') }}">Termos</a></li>
            <li><a href="{{ route('privacy') }}">Privacidade</a></li>
          </ul>
        </div>
        <div class="row">
          <p><small><a href="https://www.github.com/lcmaquino/minhaacademia">Desenvolvido</a> em <a href="https://laravel.com/">Laravel</a>.</small></p>
        </div>
    </div>
  </div>
  @endsection
