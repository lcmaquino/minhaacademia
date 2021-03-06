@extends('layouts.main')
@section('content')
<div class="section about">
    <div class="container u-full-width">
        <h1 class="hearding">Contato</h1>
        <form action="{{ route('contactSend') }}" method="POST">
            @csrf
            <div class="u-full-width">
                <label class="u-pull-left" for="name">Nome:</label>
                <input type="text" id="name" name="name" class="u-full-width @error('name') is-invalid @enderror" value="{{ old('name') }}">
                @error('name')
                <span class="alert is-invalid">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="u-full-width">
                <label class="u-pull-left" for="email">E-mail:</label>
                <input type="email" id="email" name="email" class="u-full-width @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')
                <span class="alert is-invalid">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <label class="u-pull-left" for="message">Mensagem:</label>
            <textarea id="message" name="message" class="u-full-width @error('message') is-invalid @enderror" placeholder="Olá professor…">{{ old('message') }}</textarea>
            @error('message')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <div class="u-full-width">
                <p><strong>Atenção!</strong> Não use este formulário para solicitar a resolução de lista de exercícios.</p>
            </div>
            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                @if(session()->has('contactStatus'))
                <div class="alert alert-{{ session('contactStatus')['status'] }}">
                    <p>{{ session('contactStatus')['message'] }}</p>
                </div>
                @endif
            <input class="button bt-black" type="submit" value="Enviar">
        </form>
    </div>
</div>
    @isset($googleRecaptchaSiteKey)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $googleRecaptchaSiteKey }}"></script>
        <script>
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ $googleRecaptchaSiteKey }}', { action: 'contact' }).then(function (token) {
                    var recaptchaResponse = document.getElementById('recaptchaResponse');
                    recaptchaResponse.value = token;
                });
            });
        </script>
    @endisset
@endsection