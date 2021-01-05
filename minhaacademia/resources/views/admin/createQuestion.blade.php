@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Criar Questão - <a href="{{ route('activityEdit', ['activity' => $activity->id]) }}">{{ $activity->title }}</a></h2>
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
        
        <form action="{{ route('questionStore') }}" method="post">
            @csrf
            <div class="u-full-width text-left">
                <label for="content" class="u-pull-left">Enunciado:</label>
                <textarea class="u-full-width" name="content" id="content">{{ old('content') ? old('content') : '' }}</textarea>
                <p class="information">
                    <small>Sabia mais sobre <a href="{{ route('format') }}">formatação do texto</a>.</small>
                </p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="answer">Resposta:<br>
                    <select name="answer" id="answer">
                        <option value="0" {{ old('answer') == 0 ? 'selected' : '' }}>A</option>
                        <option value="1" {{ old('answer') == 1 ? 'selected' : '' }}>B</option>
                        <option value="2" {{ old('answer') == 2 ? 'selected' : '' }}>C</option>
                        <option value="3" {{ old('answer') == 3 ? 'selected' : '' }}>D</option>
                        <option value="4" {{ old('answer') == 4 ? 'selected' : '' }}>E</option>
                      </select>
                </label>
            </div>
            <input type="hidden" name="activity" value="{{ $activity->id }}">
            <input type="submit" class="button bt-black bt-preview" name="action" value="Pré-visualizar">
            <input type="submit" class="button bt-black" name="action" value="Salvar">
        </form>

    </div>
</div>
@endsection