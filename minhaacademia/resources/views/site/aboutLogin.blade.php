@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width text-left">
        <h3 class="text-center">Saiba mais sobre o Login com o YouTube.</h3>
        <p class="text-center">
            <strong>AVISO:</strong> fique tranquilo(a), pois nosso site <strong>não fará</strong> ações com sua conta,
            ou seja, comentar, compartilhar, curtir ou inscrever-se em canais.
        </p>
        <p>
            Para efetuar login no site você deve usar diretamente sua conta do YouTube.
            O login apenas será realizado se você estiver inscrito no canal 
            <a href="{{ $youtubeChannelUrl }}">{{ $youtubeChannelTitle }}</a>. 
            Siga os passos abaixo.
        </p>
        <ol>
            <li>
                Escolha sua conta Google (a mesma usada no YouTube).
            </li>
            <li>
                Se aparecer um aviso que o app ainda não foi verificado, clique em "Avançado". 
                Esse aviso aparece pois todo aplicativo que usa o sistema do Google 
                deve passar por um processo de verificação que pode demorar um pouco.
            </li>
            <li>
                Clique em "Acessar {{ $appUrl }} (não seguro)".
            </li>
            <li>
                Clique em "Permitir".
            </li>
            <li>
                Confirme sua escolha e clique novamente em "Permitir".
            </li>
        </ol>
    </div>
</div>
@endsection