@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <div style="row text-center">
            <img src="{{ asset('img/youtube_channel_banner.png') }}" alt="{{ request('pagetitle', 'YouTube Channel Banner') }}" style="width:100%;">
        </div>
        <div class="row">
            <h1>Catálogo de Cursos</h1>
            @if ($courses)
                @foreach ($courses as $course)
                <div class="course card">
                    <div class="card header">
                        <div class="card thumbnail">
                            @isset($course->icon)
                                <img src="{{ asset($course->icon['path']) }}" class="course icon" alt="ícone">
                            @else
                                <img src="{{ asset('img/default-icon.png') }}" class="course icon" alt="ícone">
                            @endisset
                        </div>
                        <div class="card title">
                            <p><a href="{{ route('courseShow', ['course' => $course->id]) }}" title="{{ $course->title }}">{{ $course->shortTitle() }}</a></p>
                        </div>
                    </div>
                    <div class="card description">
                        <hr>
                        <p>{!! $course->shortDescription() !!}</p>
                    </div>
                    @guest
                        <div class="card container">
                            <div class="card information">
                                <i class="fa fa-file-text-o gray"></i>
                                <small>
                                    <span>
                                        {{ $course->lessonsCount() }} {{ $course->lessonsCount() < 2 ? 'aula' : 'aulas'}}
                                    </span>
                                </small>
                                <small>
                                    <span class="separator">&bullet;</span>
                                </small>
                                <i class="fa fa-list-ul gray"></i>
                                <small>
                                    <span>
                                            {{ $course->activitiesCount() }} {{ $course->activitiesCount() < 2 ? 'atividade' : 'atividades'}}
                                    </span>
                                </small>
                                <small>
                                    <span class="separator">&bullet;</span>
                                </small>
                                    <i class="fa fa-clock-o gray"></i>
                                <small>
                                    <span>{{ $course->duration }}h</span>
                                </small>
                            </div>
                            <div class="card progress">
                            </div>
                        </div>
                    @else
                    <div class="card container">
                        <div class="card information">
                            <i class="fa fa-file-text-o gray"></i>
                            <small>
                                <span>
                                    ({{ $course->lessonsCompletedCount(Auth::user()) }}/{{ $course->lessonsCount() }}) {{ $course->lessonsCount() < 2 ? 'aulas' : 'aulas'}}
                                </span>
                            </small>
                            <small>
                                <span class="separator">&bullet;</span>
                            </small>
                                <i class="fa fa-list-ul gray"></i>
                            <small>
                                <span>
                                    ({{ $course->activitiesCompletedCount(Auth::user()) }}/{{ $course->activitiesCount() }}) {{ $course->activitiesCount() < 2 ? 'atividade' : 'atividades'}}
                                </span>
                            </small>
                            <small>
                                <span class="separator">&bullet;</span>
                            </small>
                                <i class="fa fa-clock-o gray"></i>
                            <small>
                                <span>{{ $course->duration }}h</span>
                            </small>
                        </div>
                        <div class="card progress">
                            <div class="finish" style="width:{{ $course->calculateProgress(Auth::user()) }}%;"></div>
                        </div>
                    </div>
                    @endguest
                </div>
                @endforeach
            @else
                <p>Nenhum curso cadastrado.</p>
            @endif
        </div>
    </div>
</div>
@endsection