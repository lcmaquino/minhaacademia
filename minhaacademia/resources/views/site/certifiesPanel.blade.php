@extends('layouts.panelBase')

@section('content-panel')
    <h1>Verificar certificado.</h1>
    @if (isset($code))
        @if(empty($certify))
            <div>
                <p>Certificado inválido. Código <strong>{{ $code }}</strong> não encontrado.</p>
            </div>
        @else
            <div>
                <p>Certificado válido.</p>
                <p>Nome: {{ $certify->name }}</p>
                <p>CPF: {{ $certify->cpfShow() }}</p>
                <p>Curso: {{ $course->title }}</p>
                <p>Carga horária: {{ $course->duration }}</p>
                <p>Código: {{ $certify->code }}</p>
            </div>
        @endif
    @endif

    <form action="" method="get">
        <label for="code">Código:<br>
            <input type="text" name="code" id="code">
        </label>
        <input type="submit" value="Verificar">
    </form>
@endsection