@extends('layouts.main')
@section('content')

<div class="section main">
    <div class="container main">
        <div class="row">
            <div class="two columns right text-left">
                {!! request('sidebarmenu', '') !!}
            </div>
            <div class="ten columns left">
            @yield('content-panel')
            </div>
        </div>
    </div>
</div>

@endsection