window.jQuery = window.$ = require('jquery');
window.perfectScrollbar = require('./perfect-scrollbar');
window.toastr = require('toastr');
window.DataTable = require('./bootstrap-datatables');
window.SimpleMDE = require('simplemde');
require('./readmore');
require('./jquery-match-height');
require('./bootstrap-toggle');
require('./jquery-cookie');
require('./jquery-nestable');
require('bootstrap');
require('bootstrap-switch');
require('jquery-match-height');
require('select2');
require('bootstrap-datetimepicker/src/js/bootstrap-datetimepicker');
require('ace-builds/src-min-noconflict/ace');
require('./slugify');
require('tinymce');
require('./multilingual');
require('./voyager_tinymce');
require('./voyager_ace_editor');



$(document).ready(function(){
    var appContainer = $(".app-container"),
        sidebarAnchor = $('#sidebar-anchor'),
        fadedOverlay = $('.fadetoblack'),
        hamburger = $('.hamburger');

  $('.side-menu').perfectScrollbar();

  $('#voyager-loader').fadeOut();
  $('.readmore').readmore({
    collapsedHeight: 60,
    embedCSS: true,
    lessLink: '<a href="#" class="readm-link">Read Less</a>',
    moreLink: '<a href="#" class="readm-link">Read More</a>',
  });

  $(".hamburger, .navbar-expand-toggle, .side-menu .navbar-nav li:not(.dropdown)").on('click', function() {
      if ($(this).is('button')) {
        appContainer.toggleClass("expanded");
        $(this).toggleClass('is-active');
      } else {
        if (!sidebarAnchor.hasClass('active')) {
          appContainer.removeClass("expanded");
          hamburger.toggleClass('is-active');
        }
      }
  });

  fadedOverlay.on('click', function(){
    appContainer.removeClass('expanded');
    hamburger.removeClass('is-active');
  });

  sidebarAnchor.on('click', function(){
    if (appContainer.hasClass('expanded')) {
      if ($(this).hasClass('active')) {
        appContainer.removeClass("expanded");
        $(this).removeClass('active');
        window.localStorage.removeItem('voyager.stickySidebar');
        toastr.success("Sidebar isn't sticky anymore.");

        sidebarAnchor[0].title = sidebarAnchor.data('sticky');
      }
      else {
        $(this).addClass('active');
        window.localStorage.setItem('voyager.stickySidebar', true);
        toastr.success("Sidebar is now sticky");

        sidebarAnchor.data('sticky', sidebarAnchor[0].title);
        sidebarAnchor[0].title = sidebarAnchor.data('unstick');
      }
    }
    else {
      appContainer.addClass("expanded");
      $(this).removeClass('active');
      window.localStorage.removeItem('voyager.stickySidebar');
      toastr.success("Sidebar isn't sticky anymore.");

      sidebarAnchor[0].title = sidebarAnchor.data('sticky');
    }
  });

  $('select.select2').select2({ width: '100%' });

  $('.match-height').matchHeight();

  $('.datatable').DataTable({
    "dom": '<"top"fl<"clear">>rt<"bottom"ip<"clear">>'
  });

  $(".side-menu .nav .dropdown").on('show.bs.collapse', function() {
    return $(".side-menu .nav .dropdown .collapse").collapse('hide');
  });

  $(document).on('click', '.panel-heading a.panel-action[data-toggle="panel-collapse"]', function(e){
    e.preventDefault();
    var $this = $(this);

    // Toggle Collapse
    if(!$this.hasClass('panel-collapsed')) {
      $this.parents('.panel').find('.panel-body').slideUp();
      $this.addClass('panel-collapsed');
      $this.removeClass('voyager-angle-down').addClass('voyager-angle-up');
    } else {
      $this.parents('.panel').find('.panel-body').slideDown();
      $this.removeClass('panel-collapsed');
      $this.removeClass('voyager-angle-up').addClass('voyager-angle-down');
    }
  });

  //Toggle fullscreen
  $(document).on('click', '.panel-heading a.panel-action[data-toggle="panel-fullscreen"]', function (e) {
    e.preventDefault();
    var $this = $(this);
    if (!$this.hasClass('voyager-resize-full')) {
      $this.removeClass('voyager-resize-small').addClass('voyager-resize-full');
    } else {
      $this.removeClass('voyager-resize-full').addClass('voyager-resize-small');
    }
    $this.closest('.panel').toggleClass('is-fullscreen');
  });

  $('.datepicker').datetimepicker();

  // Right navbar toggle
  $('.navbar-right-expand-toggle').on('click', function(){
    $('ul.navbar-right').toggleClass('expanded');
  });

  // Save shortcut
  $(document).keydown(function (e){
    if ((e.metaKey || e.ctrlKey) && e.keyCode == 83) { /*ctrl+s or command+s*/
      $(".btn.save").click();
      e.preventDefault();
      return false;
    }
  });

  /********** MARKDOWN EDITOR **********/

  $('textarea.simplemde').each(function() {
      var simplemde = new SimpleMDE({
          element: this,
      });
      simplemde.render(); 
  });

  console.log('hit');

  /********** END MARKDOWN EDITOR **********/

});


$(document).ready(function(){
  $(".form-edit-add").submit(function(e){
    e.preventDefault();

    var url = $(this).attr('action');
    var form = $(this);
    var data = new FormData(this);

    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      data: data,
      processData: false,
      contentType: false,
      beforeSend: function(){
        $("body").css("cursor", "progress");
        $("div").removeClass("has-error");
        $(".help-block").remove();
      },
      success: function(d){
        $("body").css("cursor", "auto");

        $.each(d.errors, function(key, row){
                                        //Scroll to first error
                                        if (Object.keys(d.errors).indexOf(key) === 0) {
                                            $('html, body').animate({
                                                scrollTop: $("[name='"+key+"']").parent().offset().top
                                                        - $('nav.navbar').height() + 'px'
                                            }, 'fast');
                                        }
                                        
          $("[name='"+key+"']").parent().addClass("has-error");
          $("[name='"+key+"']").parent().append("<span class='help-block' style='color:#f96868'>"+row+"</span>")
        });
      },
      error: function(){
        $(form).unbind("submit").submit();
      }
    });
  });
});


/*--------------------
|
| HELPERS
|
--------------------*/

function displayAlert(alert, alerter) {
    let alertMethod = alerter[alert.type];

    if (alertMethod) {
        return alertMethod(alert.message);
    }

    alerter.error("No alert method found for alert type: " + alert.type);
}

function displayAlerts(alerts, alerter, type) {
    if (type) {
        // Only display alerts of this type...
        alerts = alerts.filter(function(alert) {
            return type == alert.type;
        });
    }

    for (a in alerts) {
        displayAlert(alerts[a], alerter);
    }
}

function bootstrapAlerter(customOptions) {
    // Default options
    let options = {
        alertsContainer: '#alertsContainer',
        dismissible: false,
        dismissButton: '<button class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
    };

    if (customOptions) {
        options = $.extend({}, options, customOptions);
    }

    let dismissibleClass = '';
    let dismissButton = '';

    if (options.dismissible) {
        dismissButton = options.dismissButton;
        dismissibleClass = ' alert-dismissible';
    }

    function notify(type, message) {
        let alert = '<div class="alert alert-'  + type +  dismissibleClass + '" role="alert">'
                        + dismissButton + message +
                    '</div>';

        $(options.alertsContainer).append(alert);
    }

    return {
        success(message) {
            notify('success', message);
        },
        info(message) {
            notify('info', message);
        },
        warning(message) {
            notify('warning', message);
        },
        error(message) {
            notify('danger', message);
        }
    };
}

