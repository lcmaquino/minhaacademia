@extends('layouts.main')
@section('content')
  <div class="section main">
    <div class="container main">
      <div class="row">
        <div class="eight columns left text-left">
        @yield('content-left')
        </div>
        <div class="four columns right text-left">
          <div class="content">
            <div class="content header">
              <h5>
              @guest
                <a class="{{ Route::currentRouteName() == 'courseShow' ? 'is-active' : '' }}" href="{{ route('courseShow', ['course' => $course->id]) }}" title="{{ $course->title }}">{{ $course->shortTitle() }}</a>
              @else
                @if (Auth::user()->isAdmin() || Auth::id() == $course->teacher)
                  <form action="{{ route('courseDestroy', ['course' => $course->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <a class="{{ Route::currentRouteName() == 'courseShow' ? 'is-active' : '' }}" href="{{ route('courseShow', ['course' => $course->id]) }}" title="{{ $course->title }}">{{ $course->shortTitle() }}</a>
                    {!! request('certifyLink', '') !!}
                    <a href="{{ route('courseEdit', ['course' => $course->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                    <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                  </form>
                @else
                  <a class="{{ Route::currentRouteName() == 'courseShow' ? 'is-active' : '' }}" href="{{ route('courseShow', ['course' => $course->id]) }}" title="{{ $course->title }}">{{ $course->shortTitle() }}</a>
                  {!! request('certifyLink', '') !!}
                @endif
              @endguest
              </h5>
            </div>
            <div class="content nav">
              {!! request('sidebarmenu', '') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection