@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Editar Módulo - <a href="{{ route('courseEdit', ['course' => $module->course]) }}" title="{{ $module->course()->title }}">{{ $module->course()->shortTitle() }}</a></h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('moduleUpdate', ['module' => $module->id]) }}" method="post">
            @csrf
            @method('PUT')
            <div class="u-full-width text-left">
                <label for="title" class="u-pull-left">Título:</label>
                <input type="text" class="u-full-width" name="title" id="title" value="{{ old('title') ? old('title') : $module->title }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="order">Ordem:</label>
                <select name="order" id="order">
                    @for($ord=0; $ord < $module->course()->modulesCount(); $ord++)
                        <option value="{{ $ord }}" {{ $module->order == $ord ? 'selected' : ''}}>{{ $ord + 1 }}</option>
                    @endfor
                </select>
                <br>
            </div>
            <input type="submit" class="button bt-black" value="Salvar">
        </form>

    </div>
</div>
@endsection