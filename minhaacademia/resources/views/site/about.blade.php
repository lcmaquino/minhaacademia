@extends('layouts.main')
@section('content')
<div class="section about">

    <div class="container u-full-width">
        <div class="row">
            <div class="seven columns">
                <h1 class="hearding">Conheça o canal</h1>
                @isset($youtubeChannelDefaultVideo)
                    <div class="embedded video">
                        <iframe allowfullscreen title="Player de Vídeo youtube" src="https://www.youtube.com/embed/{{ $youtubeChannelDefaultVideo }}?feature=oembed&amp;start&amp;end&amp;wmode=opaque&amp;loop=0&amp;controls=1&amp;mute=0&amp;rel=0&amp;modestbranding=0"></iframe>
                    </div>
                @else
                    <h3>Acesse o canal no YouTube! :)</h3>
                @endisset
            </div>

            <div class="five columns text-left">
                <h1 class="hearding">Sobre</h1>
                {!! $youtubeChannelAbout !!}
            </div>
        </div>

        @if($channelStatistics || $courseStatistics)
            <h1>Alguns números</h1>            
        @endif
        @isset($channelStatistics)
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