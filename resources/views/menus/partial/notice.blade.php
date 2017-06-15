@if(config('voyager.show_dev_tips'))
    <div class="container-fluid">
        <div class="alert alert-info">
            <strong>{{ trans('voyager.generic_how_to_use') }}:</strong>
            <p>{{ trans_choice('voyager.menu_builder_usage_hint', !empty($menu) ? 0 : 1) }} <code>menu('{{ !empty($menu) ? $menu->name : 'name' }}')</code></p>
        </div>
    </div>
@endif
