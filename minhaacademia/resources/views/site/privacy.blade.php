@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width text-left">
        <h3 class="text-center">Política de Privacidade e Segurança de Dados - {{ $appName }}.</h3>
        <p>
            {{ $appName }} - {{ $appContactMail }}
        </p>
        <p>
            Escreva aqui sua Política de Privacidade e Segurança de Dados.
        </p>
    </div>
</div>
@endsection