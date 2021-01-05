@extends('layouts.panelBase')
@section('content-panel')
    @if (empty($user->name) || empty($user->cpf))
        <div class="alert alert-info">
            <p><strong><mark class="info-mark">Atenção no preenchimento!</mark></strong></p>
            <p>Você precisa inserir seu nome completo e seu CPF para gerar o certificado de participação nos cursos.</p>
            <p>Se você preencher esses dados de modo errado os seus certificados ficarão inválidos.</p>
            <p>Nesse caso, apenas o administrador do sistema poderá corrigir os dados.</p>
        </div>
    @else
        <p><i class="fa fa-question-circle"></i>Somente o administrador do sistema pode atualizar seus dados para gerar os certificados.</p>
        <p><a href="{{ route('contact') }}"> Entre em contato</a> se precisar alterar seus dados.</p>
    @endif
    @if(Auth::user()->isAdmin())
        <p><a href="{{ route('userEditPassword', ['user' => $user->id]) }}">Atualizar senha</a></p>
    @endif
    <form action="{{ route('userUpdate', ['user' => $user->id]) }}" method="post">
        @csrf
        @method('PUT')
        <div class="u-full-width text-left">
            <label for="name" class="u-pull-left">Nome:</label>
            <input type="text" class="u-full-width @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') ? old('name') : $user->name }}" {{ empty($user->name) ? '' : $disabled }}>
            @error('name')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>
        </div>
        <div class="u-full-width text-left">
            <label for="cpf">CPF:</label>
            <input type="text" oninput="cpfMask(this)" class="u-full-width @error('cpf') is-invalid @enderror" name="cpf" id="cpf" value="{{ empty(old('cpf')) ? $user->cpfShow() : old('cpf')}}" placeholder="000.000.000-00"  {{ empty($user->cpf) ? '' : $disabled }}>
            @error('cpf')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>
        </div>
        <div class="u-full-width text-left">
            <label for="cpf_confirmation">Confirmar CPF:</label>
            <input type="text" oninput="cpfMask(this)" class="u-full-width @error('cpf') is-invalid @enderror" name="cpf_confirmation" id="cpf_confirmation" value="{{ empty(old('cpf_confirmation')) ? $user->cpfShow() : old('cpf_confirmation')}}" placeholder="000.000.000-00"  {{ empty($user->cpf) ? '' : $disabled }}>
            <br>
        </div>
        <div class="u-full-width text-left">
            <label for="email">E-mail:</label>
            <input type="email" class="u-full-width @error('email') is-invalid @enderror" name="email" id="email" value="{{ empty(old('email')) ? $user->email : old('email')}}"  {{ $disabled }}>
            @error('email')
            <span class="alert is-invalid">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <br>
        </div>
        @if (Auth::user()->isAdmin())
            <div class="u-full-width text-left">
                <label for="role" class="u-pull-left">Papel</label>
                <select name="role" class="u-full-width" id="role">
                <option value="admin" {{ (old('role') === 'admin' || (empty(old('role')) && $user->roleName() == 'admin')) ? 'selected' : '' }}>Administrador</option>
                <option value="teacher" {{ (old('role') === 'teacher' || (empty(old('role')) && $user->roleName() == 'teacher')) ? 'selected' : '' }}>Professor</option>
                <option value="student" {{ (old('role') === 'student' || (empty(old('role')) && $user->roleName() == 'student')) ? 'selected' : '' }}>Estudante</option>
                </select>
                <br>
            </div>
        @else
            <input type="hidden" name="email" value="{{ $user->email }}">
            <input type="hidden" name="role" value="{{ $user->roleName() }}">
        @endif
        <input type="submit" class="button bt-black" value="Salvar" {{ empty($user->name) || empty($user->cpf) ? '' : $disabled }}>
    </form>
    
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