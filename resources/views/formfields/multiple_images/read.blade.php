
        @if(json_decode($dataTypeContent->{$row->field}))
            @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                <img class="img-responsive"
                     src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Voyager::image($file) }}">
            @endforeach
        @else
            <img class="img-responsive"
                 src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
        @endif
