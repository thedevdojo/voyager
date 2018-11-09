
        @include('voyager::multilingual.input-hidden-bread-browse', ['data' => $dataTypeContent])
        <div class="readmore">{{ mb_strlen( $dataTypeContent->{$row->field} ) > 200 ? mb_substr($dataTypeContent->{$row->field}, 0, 200) . ' ...' : $dataTypeContent->{$row->field} }}</div>
