@extends('layouts.certifyBase')

@section('content')
    <h4>{{ $certify->name }}</h4>
    <h6>(CPF: {{ $certify->cpfShow() }})</h6>
    <p>
        concluiu o curso livre on-line <strong>{{ $certify->title }}</strong>, 
        com carga horária de {{ $certify->duration }} horas, assistindo as 
        videoaulas e respondendo com êxito as atividades.
    </p>
    <p>{{ $certifyState }}, {{ $certify->created_at->locale('pt-br')->isoFormat('LL') }}.</p>
@endsection