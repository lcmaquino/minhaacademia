@extends('layouts.mainCourse')
@section('content-left')
  @isset($lesson->video)
  <div class="embedded video">
    <iframe allowfullscreen title="Player de Vídeo youtube" src="https://www.youtube.com/embed/{{ $lesson->video }}?feature=oembed&amp;start&amp;end&amp;wmode=opaque&amp;loop=0&amp;controls=1&amp;mute=0&amp;rel=0&amp;modestbranding=0"></iframe>
  </div>
  @endisset
  <div class="description">
    <h5>
    @if (Auth::user()->isAdmin() || Auth::id() == $lesson->module()->course()->teacher)
      <form action="{{ route('lessonDestroy', ['lesson' => $lesson->id]) }}" method="POST">
        @csrf
        @method('DELETE')
        {{ $lesson->title }}
        <a href="{{ route('lessonEdit', ['lesson' => $lesson->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>
        <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
      </form>
    @else
      {{ $lesson->title }}
    @endif
    </h5>
    {!! $lesson->render() !!}
  </div>
@endsection