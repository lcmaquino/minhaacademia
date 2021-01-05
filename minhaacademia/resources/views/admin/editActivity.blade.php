@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Editar <a href={{ route('activityShow', ['activity' => $activity]) }} title="{{ $activity->title }}">{{ $activity->shortTitle() }}</a> - <a href="{{ route('courseEdit', ['course' => $activity->module()->course]) }}" title="{{ $activity->module()->course()->shortTitle() }}">{{ $activity->module()->course()->shortTitle() }}</a></h2>

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

        <form action="{{ route('activityUpdate', ['activity' => $activity->id]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="u-full-width text-left">
                <label for="title" class="u-pull-left">Título:</label>
                <input type="text" class="u-full-width" name="title" id="title" value="{{ old('title') ? old('title') : $activity->title }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="description" class="u-pull-left">Descrição:</label>
                <textarea class="u-full-width" name="description" id="description" cols="30" rows="10">{{ old('description') ? old('description') : $activity->description }}</textarea>
                <p class="information">
                    <small>Sabia mais sobre <a href="{{ route('format') }}">formatação do texto</a>.</small>
                </p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="order">Ordem:</label>
                <select name="order" id="order">
                    @for($ord=0; $ord < $activity->module()->activitiesCount(); $ord++)
                        <option value="{{ $ord }}" {{ $activity->order == $ord ? 'selected' : ''}}>{{ $ord + 1 }}</option>
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
            @foreach ($activity->images as $image)
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
                <input type="hidden" name="model" value="Activity">
                <input type="hidden" name="modelId" value="{{ $activity->id }}">
                <p><input type="submit" class="button bt-black" name="action" value="Anexar"></p>
                <p class="information">
                    <small>Após anexar uma imagem, use na descrição da atividade o código [img]URL[/img] para adicioná-la.</small>
                </p>
            </form>
        </div>

        @foreach($activity->questions as $question)
        <hr>
            <a id="question-{{ $question->id }}"></a>
            <div class="activity question text-left">
                <div class="question-content">
                    @if ($activity->questionsCount() > 1)
                        <form action="{{ route('questionDestroy', ['question' => $question->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <p>
                                <span class="question-info">Questão {{$question->order + 1}}) </span>
                                <a href="{{ route('questionEdit', ['question' => $question->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                                <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                            </p>
                        </form>
                    @else
                        <p>
                            <span class="question-info">Questão {{$question->order + 1}}) </span>
                            <a href="{{ route('questionEdit', ['question' => $question->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                        </p>
                    @endif
                    {!! $question->render() !!}
                </div>
                <p><span class="question-info">Resposta:</span> {{ $question->answerStr() }}</p>
                <p><span class="question-info">Itens</span><a href="{{ route('item', ['question' => $question->id]) }}" title="Adicionar item"><i class="fa fa-plus-square-o"></i></a></p>
                <div class="question-items">
                    <ul class="none-mark">
                    @foreach ($question->items as $item)
                        <li>
                            <form action="{{ route('itemDestroy', ['item' => $item->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                {{ $item->orderStr() }}) {!! $item->render() !!}
                                <a href="{{ route('itemEdit', ['item' => $item->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                                @if ($item->order != $question->answer)
                                    <a href="#question-{{ $question->id }}" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                                @endif
                            </form>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    <p><a href="{{ route('question', ['activity' => $activity->id]) }}" class="button bt-black">Adicionar questão</a></p>
    </div>
</div>
@endsection