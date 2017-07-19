@extends('voyager::master')

@section('css')

    @include('voyager::compass.includes.styles')
    
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-compass"></i>
        <p> {{ __('voyager.generic.compass') }}</p>
        <span class="page-description">Welcome to the Voyager Compass. Every good app needs a compass to point them in the right direction. In this section you will find many resources and administrative tasks to help guide you as you build out your application.</span>
    </h1>
@stop

@section('content')

    <div id="gradient_bg"></div>

    <div class="container-fluid">
        @include('voyager::alerts') 
    </div>

    <div class="page-content compass container-fluid">
        <ul class="nav nav-tabs">
          <li @if(empty($active_tab) || (isset($active_tab) && $active_tab == 'resources')){!! 'class="active"' !!}@endif><a data-toggle="tab" href="#resources"><i class="voyager-book"></i> Resources</a></li>
          <li @if($active_tab == 'commands'){!! 'class="active"' !!}@endif><a data-toggle="tab" href="#commands"><i class="voyager-terminal"></i> Commands</a></li>
          <li @if($active_tab == 'logs'){!! 'class="active"' !!}@endif><a data-toggle="tab" href="#logs"><i class="voyager-logbook"></i> Logs</a></li>
        </ul>

        <div class="tab-content">
            <div id="resources" class="tab-pane fade in @if(empty($active_tab) || (isset($active_tab) && $active_tab == 'resources')){!! 'active' !!}@endif">
                <h3><i class="voyager-book"></i> Resources <small>Voyager resources to help you find things quicker.</small></h3>

                <div class="collapsible">
                    <div class="collapse-head" data-toggle="collapse" data-target="#links" aria-expanded="true" aria-controls="links">
                        <h4>Links</h4>
                        <i class="voyager-angle-down"></i>
                        <i class="voyager-angle-up"></i>
                    </div>
                    <div class="collapse-content collapse in" id="links">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="https://laravelvoyager.com/docs" target="_blank" class="voyager-link" style="background-image:url('/vendor/tcg/voyager/assets/images/compass/documentation.jpg')">
                                    <span class="resource_label"><i class="voyager-documentation"></i> <span class="copy">Documentation</span></span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="https://laravelvoyager.com" target="_blank" class="voyager-link" style="background-image:url('/vendor/tcg/voyager/assets/images/compass/voyager-home.jpg')">
                                    <span class="resource_label"><i class="voyager-browser"></i> <span class="copy">Voyager Homepage</span></span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="https://larapack.io" target="_blank" class="voyager-link" style="background-image:url('/vendor/tcg/voyager/assets/images/compass/hooks.jpg')">
                                    <span class="resource_label"><i class="voyager-hook"></i> <span class="copy">Voyager Hooks</span></span>
                                </a>
                            </div>
                        </div>
                    </div>
              </div>

              <div class="collapsible">

                <div class="collapse-head" data-toggle="collapse" data-target="#fonts" aria-expanded="true" aria-controls="fonts">
                    <h4>Fonts</h4>
                    <i class="voyager-angle-down"></i>
                    <i class="voyager-angle-up"></i>
                </div>

                <div class="collapse-content collapse in" id="fonts">

                    @include('voyager::compass.includes.fonts')

                </div>

              </div>
            </div>

          <div id="commands" class="tab-pane fade in @if($active_tab == 'commands'){!! 'active' !!}@endif">
            <h3><i class="voyager-terminal"></i> Commands <small>Run Artisan Commands from Voyager.</small></h3>
            <div id="command_lists">
                @include('voyager::compass.includes.commands')
            </div>

          </div>
          <div id="logs" class="tab-pane fade in @if($active_tab == 'logs'){!! 'active' !!}@endif">
            <div class="row">

                @include('voyager::compass.includes.logs')

            </div>
          </div>
        </div>

    </div>

@stop

@section('javascript')
    <script>
        $('document').ready(function(){
            $('.collapse-head').click(function(){
                var collapseContainer = $(this).parent();
                if(collapseContainer.find('.collapse-content').hasClass('in')){
                    collapseContainer.find('.voyager-angle-up').fadeOut('fast');
                    collapseContainer.find('.voyager-angle-down').fadeIn('slow');
                } else {
                    collapseContainer.find('.voyager-angle-down').fadeOut('fast');
                    collapseContainer.find('.voyager-angle-up').fadeIn('slow');
                }
            });
        });
    </script>
    <!-- JS for commands -->
    <script>
        
        $(document).ready(function(){
            $('.command').click(function(){
                $(this).find('.cmd_form').slideDown(); 
                $(this).addClass('more_args');
                $(this).find('input[type="text"]').focus();
            });

            $('.close-output').click(function(){
                $('#commands pre').slideUp();
            });
        });

    </script>

    <!-- JS for logs -->
    <script>
      $(document).ready(function () {
        $('.table-container tr').on('click', function () {
          $('#' + $(this).data('display')).toggle();
        });
        $('#table-log').DataTable({
          "order": [1, 'desc'],
          "stateSave": true,
          "stateSaveCallback": function (settings, data) {
            window.localStorage.setItem("datatable", JSON.stringify(data));
          },
          "stateLoadCallback": function (settings) {
            var data = JSON.parse(window.localStorage.getItem("datatable"));
            if (data) data.start = 0;
            return data;
          }
        });
        $('#delete-log, #delete-all-log').click(function () {
          return confirm('Are you sure?');
        });
      });
    </script>
@stop
