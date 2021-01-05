@extends('layouts.panelBase')
@php
    $users = request('users', null);
    $maxResults = request('maxResults', '');
    $filter = request('filter', '');
    $pagNavMenuItems = request('pagNavMenuItems', []);
@endphp
@section('content-panel')
    <div class="users-table text-center">
        <div class="users-table user-create">
            <p><a class="is-active" href="{{ route('userCreate') }}">Novo usuário</a></p>
        </div>        
        <div class="users-table filter">
            <form action="{{ route('users') }}" method="GET">
                <input type="text" name="filter" id="filter" value="{{ $filter }}" placeholder="Filtrar">
                <input type="hidden" name="maxResults" value="{{ $maxResults }}">
            </form>
        </div>
        <div class="users-table data">
            <table>
                <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Remover</th>
                </tr>
                </thead>
                @if ($users->count() > 0)
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="user">
                                <a href="{{ route('userEdit', ['user' => $user]) }}">{{ (explode('@', $user->email))[0] }}</a>
                            </td>
                            <td class="user-remove">
                                <form action="{{ route('userDestroy', ['user' => $user]) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody></table>
                @else
                    </table>
                    <div class="alert-info">
                        Nenhum usuário encontrado.
                    </div>
                @endif
        </div>

        <div class="users-table nav">
            <form action="{{ route('users') }}" method="GET">
                <label for="maxResults"><span class="pag-info">Linhas por página:</span> 
                    <select name="maxResults" id="maxResults" onchange="this.form.submit();return false;">
                        <option value="5" {{ $maxResults == 5 ? 'selected' : ''}}>5</option>
                        <option value="30" {{ $maxResults == 30 ? 'selected' : ''}}>30</option>
                        <option value="50" {{ $maxResults == 50 ? 'selected' : ''}}>50</option>
                    </select>
                </label>
                <input type="hidden" name="filter" value="{{ $filter }}">
            </form>
            <ul class="navmenu">
                @foreach ($pagNavMenuItems as $item)
                    <li>{!! $item !!}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection