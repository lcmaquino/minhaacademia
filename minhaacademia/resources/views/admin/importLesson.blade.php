@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Importar Aula - <a href="{{ route('courseEdit', ['course' => $module->course]) }}">{{ $module->course()->title }}</a></h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('lessomImportStore') }}" method="post">
            @csrf
            <div class="u-full-width text-left">
                <label for="video" class="u-pull-left">Vídeo ID:</label>
                <input type="text" class="u-full-width" name="video" id="video" value="{{ old('video') ? old('video') : '' }}">
                <p><small>Exemplo: youtube.com/watch?v=<strong>abcd</strong>, Vídeo ID: <strong>abcd</strong>.</small></p>
                <br>
            </div>
            <input type="hidden" name="module" value="{{ $module->id }}">
            <input type="submit" class="button bt-black" value="Importar">
        </form>
        <hr>
        <form action="{{ route('lessomImportStore') }}" method="post">
            @csrf
            <div class="u-full-width text-left">
                <label for="playlist" class="u-pull-left">Lista de Reprodução ID:</label>
                <input type="text" class="u-full-width" name="playlist" id="playlist" value="{{ old('playlist') ? old('playlist') : '' }}">
                <p><small>Exemplo: youtube.com/playlist?list=<strong>abcd</strong>, Lista de Reprodução ID: <strong>abcd</strong>.</small></p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="start" class="u-pull-left">Início:</label>
                <input type="number" class="u-full-width" name="start" id="start" value="{{ old('start') ? old('start') : '' }}">
                <p><small>Deixe em branco se quiser incluir desde o primeiro vídeo da lista.</small></p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="end" class="u-pull-left">Fim:</label>
                <input type="number" class="u-full-width" name="end" id="end" value="{{ old('end') ? old('end') : '' }}">
                <p><small>Deixe em branco se quiser incluir até o último vídeo da lista.</small></p>
                <br>
            </div>
            <input type="hidden" name="module" value="{{ $module->id }}">
            <input type="submit" class="button bt-black" value="Importar">
        </form>

    </div>
</div>
@endsection