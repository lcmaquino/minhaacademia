@extends('layouts.mainCourse')
@section('content-left')
  @isset($course->video)
    <div class="embedded video">
      <iframe allowfullscreen title="Player de Vídeo youtube" src="https://www.youtube.com/embed/{{ $course->video }}?feature=oembed&amp;start&amp;end&amp;wmode=opaque&amp;loop=0&amp;controls=1&amp;mute=0&amp;rel=0&amp;modestbranding=0"></iframe>
    </div>
  @endisset
  <div class="description">
    <p class="information"><i class="fa fa-clock-o gray"></i>Carga horária: {{ $course->duration }}h</p>
    {!! $course->render() !!}
  </div>
@endsection