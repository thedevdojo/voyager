<div class="panel widget center bgimage" style="margin-bottom:0;overflow:hidden;background-image:url('{{ $image }}');">
    <div class="dimmer"></div>
    <div class="panel-content">
        @if (isset($icon))<i class='{{ $icon }}'></i>@endif
        <h4>{!! $title !!}</h4>
        <p>{!! $text !!}</p>
        <a href="{{ $button['link'] }}" class="btn btn-primary">{!! $button['text'] !!}</a>
    </div>
</div>
