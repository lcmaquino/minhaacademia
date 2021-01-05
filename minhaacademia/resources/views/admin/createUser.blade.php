@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h2>Cadastrar usuário</h2>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('imageStore') }}" method="post">
            @csrf
            <div class="u-full-width text-left">
                <label for="name" class="u-pull-left">Nome:</label>
                <input type="text" class="u-full-width" name="name" id="name" value="{{ old('name') }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="name"class="u-pull-left">E-mail:</label>
                <input type="email" class="u-full-width" name="email" id="email" value="{{ old('email') }}">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="password"class="u-pull-left">Senha:</label>
                <input type="password" class="u-full-width" name="password" id="password">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="cpf"class="u-pull-left">CPF:</label>
                <input type="text" class="u-full-width" oninput="cpfMask(this)" name="cpf" id="cpf" value="{{ old('cpf') ? old('cpf') : '' }}" placeholder="000.000.000-00">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="cpf_confirmation"class="u-pull-left">Confirmar CPF:</label>
                <input type="text" class="u-full-width" oninput="cpfMask(this)" name="cpf_confirmation" id="cpf_confirmation" value="{{ old('cpf_confirmation') ? old('cpf_confirmation') : '' }}" placeholder="000.000.000-00">
                <br>
            </div>
            <div class="u-full-width text-left">
                <label for="role"class="u-pull-left">Papel</label>
                <select name="role" class="u-full-width" id="role">
                    <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Estudante</option>
                    <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Professor</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
                <br>
            </div>
            <input type="submit" value="Salvar">
        </form>
    </div>
</div>

<script>
    function cpfMask(i){
        var v = i.value;
        if(isNaN(v[v.length-1])){
            i.value = v.substring(0, v.length-1);
            return;
        }
        i.setAttribute("maxlength", "14");
        if (v.length == 3 || v.length == 7) i.value += ".";
        if (v.length == 11) i.value += "-";
    }
</script>
@endsection