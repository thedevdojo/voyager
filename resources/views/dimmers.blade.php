@php
$dimmers = \Arrilot\Widgets\Facade::group('voyager::dimmers');

// Loop over the defined dashboard widgets. Get the related model and remove the
// widget from the group if the current user hasn't the permission to read it.
foreach (config('voyager.dashboard.widgets') as $widgetClass) {
    $relatedModel = app($widgetClass)->getRelatedModel();

    if (! Auth::user()->can('read', $relatedModel)) {
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
