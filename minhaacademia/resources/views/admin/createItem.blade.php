@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Criar Item - Questão {{ $question->order + 1 }} - <a href="{{ route('activityEdit', ['activity' => $question->activity()->id]) }}">{{ $question->activity()->title }}</a></h2>
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
        
        <form action="{{ route('itemStore') }}" method="post">
            @csrf
            <div class="u-full-width text-left">
                <label for="content" class="u-pull-left">Conteúdo:</label>
                <textarea class="u-full-width textarea-small" name="content" id="content">{{ old('content') ? old('content') : '' }}</textarea>
                <p class="information">
                    <small>Sabia mais sobre <a href="{{ route('format') }}">formatação do texto</a>.</small>
                </p>
                <br>
            </div>
            <input type="hidden" name="question" value="{{ $question->id }}">
            <input type="submit" class="button bt-black bt-preview" name="action" value="Pré-visualizar">
            <input type="submit" class="button bt-black" name="action" value="Salvar">
        </form>

    </div>
</div>
@endsection