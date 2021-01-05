@extends('layouts.mainCourse')
@section('content-left')
  <h5>
    @if (Auth::user()->isAdmin() || Auth::id() == $activity->module()->course()->teacher)
      <form action="{{ route('activityDestroy', ['activity' => $activity->id]) }}" method="POST">
        @csrf
        @method('DELETE')
        {{ $activity->title }}
        <a href="{{ route('activityEdit', ['activity' => $activity->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>
        <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
      </form>        
    @else
      {{ $activity->title }}
    @endif
  </h5>
  @error('activity')
  <div class="alert alert-danger text-center">
    <p>{{ $errors->first('activity') }}</p>
  </div>
  @enderror

  @if ($activity->isCompleted(Auth::user()))
    <div class="alert alert-info text-center">
        <p>Você completou esta atividade.</p>
    </div>
  @endif
  {!! $activity->render() !!}
  <div class="activity">
    <form action="{{ route('activityAnswer', ['activity' => $activity]) }}" method="POST">
      @csrf
      @foreach ($activity->questions as $question)
      <div class="activity question">
        <p>
          <span class="question-info">Questão {{$question->order + 1}}) </span>
        </p>
        <div class="question-content">
          {!! $question->render() !!}
        </div>
        <div class="question-items">
          <ul class="none-mark">
            @foreach ($question->items as $item)
              <li><input type="radio" id="question-{{ $question->id }}-item-{{ $item->id }}" name="question-{{ $question->id }}" value="{{ $item->order }}" {{ (old('question-' . $question->id) !== null) && old('question-' . $question->id) == $item->order ? 'checked' : ''}}>
              <label style="display:inline; margin-left:4px;" for="question-{{ $question->id }}-item-{{ $item->id }}">{{ chr(65 + $item->order) }}) </label>{!! $item->render() !!}</li>
            @endforeach
          </ul>
        </div>
      </div>
      @endforeach
      <div class="text-center">
        <input type="submit" value="Responder" {{ $activity->isCompleted(Auth::user()) ? 'disabled' : ''}}>
      </div>
    </form>
  </div>
@endsection