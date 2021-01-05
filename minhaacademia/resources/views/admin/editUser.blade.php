@extends('admin.layout')
@section('content')
    <h1>Editar Usuário</h1>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <p><a href="{{ route('userEditPassword', ['user' => $user->id]) }}">Atualizar senha</a></p>
    <form action="{{ route('userUpdatePassword', ['user' => $user->id]) }}" method="post">
        @csrf
        @method('PUT')
        <p>
            <label for="name">Nome:<br>
                <input type="text" name="name" id="name" value="{{ $user->name }}">
            </label>
        </p>
        <p>
            <label for="email">E-mail:<br>
                <input type="email" name="email" id="email" value="{{ $user->email }}">
            </label>
        </p>
        <p>
            <label for="cpf">CPF:<br>
                <input type="text" name="cpf" id="cpf" value="{{ $user->cpf }}">
            </label>
        </p>
        
        @if (auth()->user()->id !== $user->id)
        <p>
            <label for="role">Papel</label>
            <select name="role" id="role">
                <option value="admin" {{ $user->isAdmin() ? 'selected' : ''}}>Administrador</option>
                <option value="teacher" {{ $user->isTeacher() ? 'selected' : ''}}>Professor</option>
                <option value="student" {{ $user->isStudent() ? 'selected' : ''}}>Estudante</option>
            </select>
        </p>
        @else
            <p>Papel: Administrador</p>
            <input type="hidden" name="role" value="admin">
        @endif
        <input type="submit" value="Salvar">
    </form>
@endsection