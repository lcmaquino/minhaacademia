@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Criar Aula - <a href="{{ route('courseEdit', ['course' => $module->course]) }}">{{ $module->course()->title }}</a></h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(old('preview'))
        <div class="u-full-width text-left preview">
            <h4>Pré-visualizar</h4>
            {!! old('preview') !!}
        </div>
        <hr>
        @endif
        
        <form action="{{ route('lessonStore') }}" method="post">
            @csrf
            <div class="u-full-width text-left">
                <label for="title" class="u-pull-left">Título:</label>
                <input type="text" class="u-full-width" name="title" id="title" value="{{ old('title') ? old('title') : '' }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="video" class="u-pull-left">Vídeo ID:</label>
                <input type="text" class="u-full-width" name="video" id="video" value="{{ old('video') ? old('video') : '' }}">
                <p class="information"><small>Exemplo: youtube.com/watch?v=<strong>abcd</strong>, Vídeo ID: <strong>abcd</strong>.</small></p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="description" class="u-pull-left">Descrição:</label>
                <textarea class="u-full-width" name="description" id="description" cols="30" rows="10">{{ old('description') ? old('description') : '' }}</textarea>
                <p class="information">
                    <small>Sabia mais sobre <a href="{{ route('format') }}">formatação do texto</a>.</small>
                </p>
                <br>
            </div>
            <input type="hidden" name="module" value="{{ $module->id }}">
            <input type="submit" class="button bt-black bt-preview" name="action" value="Pré-visualizar">
            <input type="submit" class="button bt-black" name="action" value="Salvar">
        </form>

    </div>
</div>
@endsection