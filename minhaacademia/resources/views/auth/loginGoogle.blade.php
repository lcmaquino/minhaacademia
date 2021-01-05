@extends('layouts.base')

@section('main')
<div class="container ul-full-width text-center">
    <div class="container login">
        <h2 class="section-heading">Login</h2>
        @error('subscription')
        <div class="alert alert-danger">
            <p>{{ $errors->first('subscription') }}</p>
            <p><a href="{{ empty($channelUrl) ? '#' : $channelUrl . '?sub_confirmation=1' }}" class="button youtube-subscribe">Inscrever-se</a></p>
        </div>
        @enderror
        @error('googleapi')
        <div class="alert alert-danger">
            <p>{{ $errors->first('googleapi') }}</p>
        </div>
        @enderror
        <div class="login-panel">
            <p>
                <i class="fa fa-question-circle"></i>
                Você já está inscrito no canal {{ $channelTitle }}?
                Então clique no botão para entrar diretamente com sua conta do YouTube. 
            </p>
            <a href="{{ route('login.google.url', ['approval_prompt' => $approvalPrompt]) }}" class="login">
                <button class="button-youtube-login">
                    <i class="fa fa-youtube-play" style="font-size:32px;color:#c4302b;"></i> 
                    Entrar
                </button>
            </a>
            <p class="information"><a href="{{ route('aboutLogin') }}" title="Saiba mais…">(Saiba mais…)</a></p>
        </div>
    </div>
    <ul class="navmenu">
        <li><a href="{{ route('terms') }}">Termos</a></li>
        <li><a href="{{ route('privacy') }}">Privacidade</a></li>
    </ul>
</div>
@endsection