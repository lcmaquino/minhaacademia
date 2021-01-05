@extends('layouts.panelBase')
@php
    $certifies = request('certifies', null);
    $maxResults = request('maxResults', '');
    $filter = request('filter', '');
    $pagNavMenuItems = request('pagNavMenuItems', []);
@endphp
@section('content-panel')
    <div class="certifies-table text-center">
        <div class="certifies-table filter">
            <form action="{{ route('certifies') }}" method="GET">
                <input type="text" name="filter" id="filter" value="{{ $filter }}" placeholder="Filtrar">
                <input type="hidden" name="maxResults" value="{{ $maxResults }}">
            </form>
        </div>
        <div class="certifies-table data">
            <table>
                <thead>
                <tr>
                    <th>Curso</th>
                    @if (Auth::user()->isAdmin())
                        <th>Nome</th>
                        <th>Data</th>
                        <th>Atualização</th>
                        <th>Remover</th>
                    @else
                        <th>Data</th>
                        <th>Atualização</th>
                    @endif
                </tr>
                </thead>
                @if ($certifies->count() > 0)
                    <tbody>
                    @foreach ($certifies as $certify)
                        <tr>
                            <td>
                                <a href="{{ route('certifyShow', ['certify' => $certify]) }}">{{ $certify->title }}</a>
                            </td>
                            @if (Auth::user()->isAdmin())
                                <td>
                                    {{ $certify->name }}
                                </td>
                                <td>
                                    {{ $certify->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    {{ $certify->updated_at->greaterThan($certify->created_at) ? $certify->updated_at->format('d/m/Y') : '' }}
                                </td>
                                <td>
                                    <form action="{{ route('certifyDestroy', ['certify' => $certify->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <a href="#" onclick="this.closest('form').submit();return false;" title="Remover"><i class="fa fa-remove"></i></a>
                                    </form>
                                </td>
                            @else
                                <td>
                                    {{ $certify->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    {{ $certify->updated_at->greaterThan($certify->created_at) ? $certify->updated_at->format('d/m/Y') : '' }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody></table>
                @else
                    </table>
                    <div class="alert-info">
                        Nenhum certificado encontrado.
                    </div>
                @endif
        </div>

        <div class="certifies-table nav">
            <form action="{{ route('certifies') }}" method="GET">
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