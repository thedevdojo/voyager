@php
$dimmers = \Arrilot\Widgets\Facade::group('voyager::dimmers');

// Remove all the inaccessible widgets from the `voyager::dimmers` group.
foreach (config('voyager.dashboard.widgets', []) as $widgetClass) {
    if (!app($widgetClass)->isAccessible()) {
        $dimmers->removeByName($widgetClass);
    }
}

$count = $dimmers->count();
$classes = [
    'col-xs-12',
    'col-sm-'.($count >= 2 ? '6' : '12'),
    'col-md-'.($count >= 3 ? '4' : ($count >= 2 ? '6' : '12')),
];
$class = implode(' ', $classes);
$prefix = "<div class='{$class}'>";
$surfix = '</div>';
@endphp
@if ($dimmers->any())
<div class="clearfix container-fluid row">
    {!! $prefix.$dimmers->setSeparator($surfix.$prefix)->display().$surfix !!}
</div>
@endif
