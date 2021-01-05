@extends('layouts.main')
@section('content')
<div class="section about">
    <div class="container u-full-width">
        <div class="row">
            <div class="six columns contact">
                <img src="{{ asset('img/perfil-site.png') }}" alt="Prof. Aquino" class="u-full-width">
            </div>
            <div class="six columns contact text-left">
                <h1 class="hearding">Sobre</h1>
                <p>
                    Descreva aqui você e seu canal!
                </p>
            </div>
        </div>
        @isset($channelStatistics)
            <h1>Alguns números</h1>
            <div class="row text-center">
                <div class="four columns">
                    <h2>{{ $channelStatistics['subscriberCount'] }}</h2>
                    <p>Pessoas inscritas no canal.</p>
                </div>
                <div class="four columns">
                    <h2>{{ $channelStatistics['videoCount'] }}</h2>
                    <p>Vídeos publicados.</p>
                </div>
                <div class="four columns">
                    <h2>{{ $channelStatistics['viewCount'] }}</h2>
                    <p>Número de visualizações.</p>
                </div>
            </div>
        @endisset
        @isset($courseStatistics)
            <div class="row text-center">
                <div class="four columns">
                    <h2>{{ $courseStatistics['certifiesCount'] }}</h2>
                    <p>Cursos concluídos.</p>
                </div>
                <div class="four columns">
                    <h2>{{ $courseStatistics['lessonProgressCount'] }}</h2>
                    <p>Lições concluídas.</p>
                </div>
                <div class="four columns">
                    <h2>{{ $courseStatistics['activityProgressCount'] }}</h2>
                    <p>Atividades concluídas.</p>
                </div>
            </div>
        @endisset
    </div>
</div>
@endsection