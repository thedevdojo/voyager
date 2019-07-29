@extends('voyager::app')

@section('content')
    <table width="100%">
        <thead>
            <tr>
                <th>Table</th>
                <th>Actions</th>
            </tr>
        </thead>
    @foreach ($tables as $table)
        <tr>
            <td>{{ $table }}</td>
            <td></td>
            <td>
                @php $bread = Voyager::getBread($table); @endphp
                @if ($bread)
                    <a href="{{ route('voyager.bread.edit', $bread->table) }}">Edit</a>
                @else
                <a href="{{ route('voyager.bread.create', $table) }}">Add</a>
                @endif
            </td>
        </tr>
    @endforeach
    </table>
@endsection
