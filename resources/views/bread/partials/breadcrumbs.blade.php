<ol class="breadcrumb hidden-xs">
    <li class="active">
        <a href="{{ route('voyager.dashboard')}}"><i class="voyager-boat"></i> {{ __('voyager::generic.dashboard') }}
        </a>
    </li>
    <li>
        <a href="{{route('voyager.'.$dataType->slug.'.index')}}">{{ $dataType->getTranslatedAttribute('display_name_singular') }}</a>
    </li>

    <li>{{ $last_segment }}</li>
</ol>
