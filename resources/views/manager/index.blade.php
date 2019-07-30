@extends('voyager::app')

@section('content')
    <table class="text-left m-4 w-full" style="border-collapse:collapse">
        <thead>
            <tr>
                <th class="py-4 px-6 bg-grey-lighter font-sans font-medium uppercase text-sm text-grey border-b border-grey-light">Table</th>
                <th class="py-4 px-6 bg-grey-lighter font-sans font-medium uppercase text-sm text-grey border-b border-grey-light text-right">Actions</th>
            </tr>
        </thead>
    @foreach ($tables as $table)
        <tr class="hover:bg-blue-lightest">
            <td class="py-4 px-6 border-b border-grey-light">{{ $table }}</td>
            <td class="py-4 px-6 border-b border-grey-light text-right">
                @php $bread = Voyager::getBread($table); @endphp
                @if ($bread)
                <a class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{ route('voyager.bread.edit', $bread->table) }}">Edit</a>
                @else
                <a class="bg-green-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" href="{{ route('voyager.bread.create', $table) }}">Add</a>
                @endif
            </td>
        </tr>
    @endforeach
    </table>
@endsection
