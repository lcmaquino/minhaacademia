@extends('layouts.main')

@section('content')
<div class="section main">
    <div class="container u-full-width">
        <h1>Verificar certificado.</h1>
        @if (isset($code))
            @if(empty($certify))
                <div class="alert alert-danger">
                    <h4>Certificado inválido.</h4>
                    <p>Código <em>{{ $code }}</em> não encontrado.</p>
                </div>
            @else
                <div style="padding: 4px 8px; margin: 2rem auto; max-width: 320px; border: 1px solid #e0e0e0;" class="text-left">
                    <div class="text-center"><h4>Certificado válido.</h4></div>
                    <p><strong>Nome:</strong> {{ $certify->name }}</p>
                    <p><strong>CPF:</strong> {{ $certify->cpfShow() }}</p>
                    <p><strong>Curso:</strong> {{ $certify->title }}</p>
                    <p><strong>Carga horária:</strong> {{ $certify->duration }}</p>
                    <p><strong>Data de conclusão:</strong> {{ $certify->created_at->format('d/m/Y') }}</p>
                    <p><strong>Código:</strong> {{ $certify->code }}</p>
                    @if ($certify->created_at != $certify->updated_at)
                    <div class="alert alert-info">
                        <p><strong>(*)</strong> Os dados desse certificado foram atualizados em: 
                            {{ $certify->updated_at->format('d/m/Y') }}.</p>
                    </div>
                    @endif
                </div>
            @endif
        @endif
        <form action="" method="get">
            <label for="code">Código de autenticação:<br>
                <input type="text" name="code" id="code">
            </label>
            <input type="submit" value="Verificar">
        </form>
    </div>
</div>
@endsection