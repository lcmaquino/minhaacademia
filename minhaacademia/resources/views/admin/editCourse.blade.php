@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2><a href="{{ route('courseShow', ['course' => $course->id]) }}" title="{{ $course->title }}">{{ $course->shortTitle() }}</a></h2>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="text-center">
            <form action="{{ route('courseExport', ['course' => $course]) }}" method="POST">
                @csrf
                <input type="submit" value="Exportar">
            </form>
        </div>

        <hr>

        @if(old('preview'))
        <div class="u-full-width text-left preview">
            <h4>Pré-visualizar</h4>
            {!! old('preview') !!}
        </div>
        <hr>
        @endif

        <form action="{{ route('courseUpdate', ['course' => $course->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="u-full-width text-center">
                @isset($course->icon)
                    <p>
                        <img src="{{ asset($course->icon['path']) }}" class="course icon" alt="Ícone do curso">
                    </p>
                @else
                    <div class="default-icon text-center">
                        <img src="{{ asset('img/default-icon.png') }}" alt="ícone padrão" title="ícone padrão" style="width:100%;height:100%;">
                    </div>
                @endisset
                <label for="icon" class="u-pull">Ícone:</label>
                <input type="file" name="icon" id="icon" accept="image/png,image/jpeg,image/jpg">
                <p class="information">
                    <small>
                        Dimensão recomendada do ícone: 128px &times; 128px.
                    </small>
                </p>
            </div>
            <div class="u-full-width text-left">
                <label for="title" class="u-pull-left">Título:</label>
                <input type="text" class="u-full-width" name="title" id="title" value="{{ old('title') ? old('title') : $course->title }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="video" class="u-pull-left">Vídeo ID:</label>
                <input type="text" class="u-full-width" name="video" id="video" value="{{ old('video') ? old('video') : $course->video }}">
                <p class="information"><small>Exemplo: youtube.com/watch?v=<strong>abcd</strong>, Vídeo ID: <strong>abcd</strong>.</small></p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="description" class="u-pull-left">Descrição:</label>
                <textarea class="u-full-width" name="description" id="description" cols="30" rows="10">{{ old('description') ? old('description') : $course->description }}</textarea>
                <p class="information">
                    <small>Sabia mais sobre <a href="{{ route('format') }}">formatação do texto</a>.</small>
                </p>
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="duration" class="u-pull-left">Carga horária:</label>
                <input type="number" class="u-full-width" name="duration" id="duration" value="{{ old('duration') ? old('duration') : $course->duration }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <p class="label">Visibilidade:</p>
                <ul class="none-mark">
                    <li>
                        <input type="radio" id="visibility-0" name="visibility" value="0" {{ (old('visibility') !== null && old('visibility') == '0') || $course->visibility == '0' ? 'checked' : ''}}>
                        <label style="display:inline; margin-left:4px;" for="visibility-0">
                            Privado
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="visibility-1" name="visibility" value="1" {{ (old('visibility') !== null && old('visibility') == '1') || $course->visibility == '1' ? 'checked' : ''}}>
                        <label style="display:inline; margin-left:4px;" for="visibility-1">
                            Público
                        </label>
                    </li>
                </ul>
                <br>
            </div>
            <input type="submit" class="button bt-black" name="action" value="Pré-visualizar">
            <input type="submit" class="button bt-black" name="action" value="Salvar">
        </form>
        <hr>

        <div class="u-full-width text-left">
            <a id="images"></a>
            <ul class="none-mark">
            @foreach ($course->images as $image)
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
                <input type="hidden" name="model" value="Course">
                <input type="hidden" name="modelId" value="{{ $course->id }}">
                <p><input type="submit" class="button bt-black" name="action" value="Anexar"></p>
                <p class="information">
                    <small>Após anexar uma imagem, use na descrição do curso o código [img]URL[/img] para adicioná-la.</small>
                </p>
            </form>
        </div>

        @foreach($course->modules as $module)
        <hr>
        <div class="text-left">
            <a id="module-{{ $module->id }}"></a>
            <form action="{{ route('moduleDestroy', ['module' => $module->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <p><span class="information">{{ $module->order + 1 }}. </span>{{ $module->title }}  
                    <a href="{{ route('moduleEdit', ['module' => $module->id]) }}" title="Editar"><i class="fa fa-edit"></i></a>  
                    <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                </p>
            </form>
            <a id="module-{{ $module->id }}-lessons"></a>
            <p><span class="lesson-info">Aula(s)</span>
                <a href="{{ route('lesson', ['course' => $course->id, 'module' => $module->id]) }}" title="Adicionar aula"><i class="fa fa-plus-square-o"></i></a>
                <a href="{{ route('lessonImport', ['module' => $module->id]) }}" title="Importar videoaula"><i class="fa fa-youtube-play"></i></a>
            </p>
            @if ($module->lessons()->count())
            @foreach ($module->lessons as $lesson)
            <a id="lesson-{{ $lesson->id }}"></a>
            <form action="{{ route('lessonDestroy', ['lesson' => $lesson->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <p><span class="information">{{ $lesson->order + 1 }}.</span>
                    <a href="{{ route('lessonShow', ['lesson' => $lesson->id]) }}">{{ $lesson->title }}</a>
                    <a href="{{ route('lessonEdit', ['lesson' => $lesson]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                    <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                </p>
            </form>
            @endforeach
            @else
                <p>Nenhuma aula</p>
            @endif

            <a id="module-{{ $module->id }}-activities"></a>
            <p><span class="activity-info">Atividades(s)</span>
                <a href="{{ route('activity', ['module' => $module->id]) }}" title="Adicionar atividade"><i class="fa fa-plus-square-o"></i></a>
            </p>
            @if ($module->activities()->count())
            @foreach ($module->activities as $activity)
            <a id="activity-{{ $activity->id }}"></a>
            <form action="{{ route('activityDestroy', ['activity' => $activity]) }}" method="POST">
                @csrf
                @method('DELETE')
                <p><span class="information">{{ $activity->order + 1 }}.</span>
                    <a href="{{ route('activityShow', ['activity' => $activity]) }}">{{ $activity->title }}</a>
                    <a href="{{ route('activityEdit', ['activity' => $activity]) }}" title="Editar"><i class="fa fa-edit"></i></a>
                    <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                </p>
            </form>
            @endforeach
            @else
                <p>Nenhuma atividade</p>
            @endif
        @endforeach
        <p class="text-center"><a href="{{ route('module', ['course' => $course->id]) }}" class="button bt-black">Adicionar módulo</a></p>
    </div>
</div>
@endsection