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
});
