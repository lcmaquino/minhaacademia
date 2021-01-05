@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Editar Questão - <a href="{{ route('activityEdit', ['activity' => $question->activity]) }}">{{ $question->activity()->title }}</a></h2>

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

        <form action="{{ route('questionUpdate', ['question' => $question->id]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="u-full-width text-left">
                <label for="content" class="u-pull-left">Enunciado:</label>
                <textarea class="u-full-width" name="content" id="content">{{ old('content') ? old('content') : $question->content }}</textarea>
                <p class="information">
                    <small>Sabia mais sobre <a href="{{ route('format') }}">formatação do texto</a>.</small>
                </p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="answer">Resposta:<br>
                    <select name="answer" id="answer">
                        @for($i = 0; $i < $question->items->count(); $i++)
                        <option value="{{ $i }}" {{ $question->answer == $i ? 'selected' : '' }}>{{ chr(65 + $i) }}</option>
                        @endfor
                      </select>
                </label>
            </div>
            <div class="u-full-width text-left">
                <label for="order">Ordem:</label>
                <select name="order" id="order">
                    @for($ord=0; $ord < $question->activity()->questionsCount(); $ord++)
                        <option value="{{ $ord }}" {{ $question->order == $ord ? 'selected' : ''}}>{{ $ord + 1 }}</option>
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
            @foreach ($question->images as $image)
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
                <input type="hidden" name="model" value="Question">
                <input type="hidden" name="modelId" value="{{ $question->id }}">
                <p><input type="submit" class="button bt-black" name="action" value="Anexar"></p>
                <p class="information">
                    <small>Após anexar uma imagem, use no enunciado de sua questão o código [img]URL[/img] para adicioná-la.</small>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection