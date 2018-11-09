
        @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
        <span>{{ $dataTypeContent->{$row->field} }}</span>
