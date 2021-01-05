@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Criar Módulo - <a href="{{ route('courseEdit', ['course' => $course->id]) }}">{{ $course->title }}</a></h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('moduleStore') }}" method="post">
            @csrf
            <div class="u-full-width text-left">
                <label for="title" class="u-pull-left">Título:</label>
                <input type="text" class="u-full-width" name="title" id="title" value="{{ old('title') ? old('title') : '' }}">
                <br>
            </div>
            <input type="hidden" name="course" value="{{ $course->id }}">
            <input type="submit" class="button bt-black" value="Salvar">
        </form>

    </div>
</div>
@endsection