@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Editar <a href="{{ route('lessonShow', ['lesson' => $lesson]) }}" title="{{ $lesson->title }}">{{ $lesson->shortTitle() }}</a> - <a href="{{ route('courseEdit', ['course' => $lesson->module()->course]) }}" title="{{ $lesson->module()->course()->title }}">{{ $lesson->module()->course()->shortTitle() }}</a></h2>

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

        <form action="{{ route('lessonUpdate', ['lesson' => $lesson->id]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="u-full-width text-left">
                <label for="title" class="u-pull-left">Título:</label>
                <input type="text" class="u-full-width" name="title" id="title" value="{{ old('title') ? old('title') : $lesson->title }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="video" class="u-pull-left">Vídeo ID:</label>
                <input type="text" class="u-full-width" name="video" id="video" value="{{ old('video') ? old('video') : $lesson->video }}">
                <p class="information"><small>Exemplo: youtube.com/watch?v=<strong>abcd</strong>, Vídeo ID: <strong>abcd</strong>.</small></p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="description" class="u-pull-left">Descrição:</label>
                <textarea class="u-full-width" name="description" id="description">{{ old('description') ? old('description') : $lesson->description }}</textarea>
                <p class="information">
                    <small>Sabia mais sobre <a href="{{ route('format') }}">formatação do texto</a>.</small>
                </p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="order">Ordem:</label>
                <select name="order" id="order">
                    @for($ord=0; $ord < $lesson->module()->lessonsCount(); $ord++)
                        <option value="{{ $ord }}" {{ $lesson->order == $ord ? 'selected' : ''}}>{{ $ord + 1 }}</option>
                    @endfor
                </select>
                <br>
            </div>
            <input type="submit" class="button bt-black bt-preview" name="action" value="Pré-visualizar">
            <input type="submit" class="button bt-black" name="action" value="Salvar">
        </form>
        <hr>
        <div class="u-full-width text-left">
            <a id="images"></a>
            <ul class="none-mark">
            @foreach ($lesson->images as $image)
                <li>
                    <form action="{{ route('imageDestroy', ['image' => $image->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <img src="{{ asset($image->path) }}" class="thumbnail" alt="{{ $image->path }}"><span style="margin-left:1rem;">{{ $image->path }}</span>
                        <a href="#images" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                    </form>
                </li>
            @endforeach
            </ul>
            <form action="{{ route('imageStore') }}" method="post" enctype="multipart/form-data">
                @csrf
                <label for="image" class="u-pull-left" style="margin-right: 1rem;">Imagem:</label>
                <input type="file" name="image" id="image" accept="image/png,image/jpeg,image/jpg">
                <input type="hidden" name="model" value="Lesson">
                <input type="hidden" name="modelId" value="{{ $lesson->id }}">
                <p><input type="submit" class="button bt-black" name="action" value="Anexar"></p>
                <p class="information">
                    <small>Após anexar uma imagem, use na descrição da aula o código [img]URL[/img] para adicioná-la.</small>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection