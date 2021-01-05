@extends('emails.base')

@section('content')
<p>Nome:<br>{{ $contactForm->name }}</p>
<p></p>
<p>Mensagem:<br>{{ $contactForm->message }}</p>
@endsection
