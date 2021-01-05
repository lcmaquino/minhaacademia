@extends('layouts.base')

@section('main')
<div class="section">
    <div class="container u-full-width">
        <div class="error" style="margin: 20% auto;">
            <h2 style="margin-bottom: 4rem;">Ops! Página não encontrada. :(</h2>
            <p><a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="Página inicial" style="vertical-align: middle; margin-right: 4rem;">Ir para página inicial</a></p>
        </div>
    </div>
</div>
@endsection