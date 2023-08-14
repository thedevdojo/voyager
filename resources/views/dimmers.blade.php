@php
$dimmerGroups = Voyager::dimmers();
@endphp

@forelse($dimmerGroups as $dimmerGroup)
    @php
    $count = $dimmerGroup->count();
    $classes = [
        'col-xs-12',
        'col-sm-1',
        'col-md-4',
        'col-lg-3',
    ];
    $class = implode(' ', $classes);
    $prefix = "<div class='{$class}'>";
    $surfix = '</div>';
    @endphp
    @if ($dimmerGroup->any())
    <div class="clearfix container-fluid row">
        {!! $prefix.$dimmerGroup->setSeparator($surfix.$prefix)->display().$surfix !!}
    </div>
    @endif

@empty

@endforelse
