@extends('layouts.main')
@section('content')
<div class="section main">
    <div class="container u-full-width">
        <div class="row text-center">
            <h1 class="hearding">Ajude o canal!</h1>
            <p>Faça a sua doação para {{ empty($channelTitle) ? 'o canal' : $channelTitle }}.</p>
        </div>
    </div>
</div>
@endsection