@extends('layouts.panelBase')
@section('content-panel')
    <div class="alert alert-info">
        <p><strong><mark class="info-mark">Atenção!</mark></strong></p>
        <p>Todos os seus dados serão removidos, exceto o código de verificação
            dos certificados que você gerou.
        </p>
    </div>

    @error('destroy')
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>        
    @enderror

    <div class="u-full-width">
        <form action="{{ route('userDestroy', ['user' => $user]) }}" method="post">
            @csrf
            @method('DELETE')
            <input type="submit" class="button bt-black" value="Remover conta">
        </form>
    </div>
@endsection