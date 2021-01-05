@extends('layouts.panelBase')
@section('content-panel')
    <h1>Editar senha do Usuário</h1>

    <form action="{{ route('userUpdatePassword', ['user' => $user->id]) }}" method="post">
        @csrf
        @method('PUT')
        <div class="u-full-width text-left">
            <label for="current_password">Senha atual:<br>
                <input type="password" name="current_password" class="@error('current_password') is-invalid @enderror" id="current_password">
            </label>
            @error('current_password')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>
        </div>
        <div class="u-full-width text-left">
            <label for="new_password">Nova senha:<br>
                <input type="password" name="new_password" class="@error('new_password') is-invalid @enderror" id="new_password">
            </label>
            @error('new_password')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>
        </div>
        <div class="u-full-width text-left">
            <label for="new_password_confirmation">Confirmar nova senha:<br>
                <input type="password" name="new_password_confirmation" class="@error('new_password') is-invalid @enderror" id="new_password_confirmation">
            </label>
            <br>
        </div>
        <div class="u-full-width text-left">
            <input type="submit" value="Salvar">
        </div>
    </form>
@endsection