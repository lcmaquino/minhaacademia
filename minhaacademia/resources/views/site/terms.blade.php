@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width text-left">
        <h3 class="text-center">Termos de serviço - {{ $appName }}.</h3>
        <p>
            {{ $appName }} - {{ $appContactMail }}
        </p>
        <p>
            Escreva aqui seus Termos de serviço.
        </p>
    </div>
</div>
@endsection