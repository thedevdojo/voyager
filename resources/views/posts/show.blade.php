<h1>{{$title}}</h1>

<div class="col-sm-3">
    <img src="/storage/{{$image}}" width="100%">
    <p>{{$excerpt}}</p>
</div>

{!! $body !!}

@if($author)
    <br clear="all">
    <hr>
    <div class="row">
        @if($author['avatar'])
            <div class="col-sm-1">
                <img src="/storage/{{$author['avatar']}}" width="100%">
            </div>
        @endif

        <div class="col-sm-11">
            <?php $i = 0; ?>
            @foreach($author as $author_i => $author_data)
                @if($author_i != 'avatar')
                    <p>
                        @if($i == 0) <strong> @endif
                            {{$author_data}}
                        @if($i == 0) </strong> @endif
                    </p>
                @endif
            <?php $i = 1; ?>
            @endforeach
        </div>
    </div>
@endif