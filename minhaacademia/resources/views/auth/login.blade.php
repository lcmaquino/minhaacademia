@extends('layouts.base')

@section('main')
<div class="container ul-full-width">
    <div class="container login">
        <h2 class="section-heading">Login</h2>
        <div class="login-panel">
            <form action="{{ route('login-panel.post') }}" method="POST">
                @csrf
                <div class="u-full-width text-left">
                    <label for="email">E-mail:</label>
                    <input type="email" class="u-full-width @error('email') is-invalid @enderror" name="email" id="email" value="{{ empty(old('email')) ? '' : old('email')}}">
                    @error('email')
                    <span class="alert is-invalid">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <br>

                    <label for="password">Senha:</label>
                    <input type="password" class="u-full-width @error('password') is-invalid @enderror" name="password" id="password" value="">
                    @error('password')
                    <span class="alert is-invalid">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <br>

                    <input type="submit" value="Entrar">
                </div>
            </form>
            <ul class="navmenu">
                <li><a href="{{ route('terms') }}">Termos</a></li>
                <li><a href="{{ route('privacy') }}">Privacidade</a></li>
            </ul>
        </div>
</div>
@endsection