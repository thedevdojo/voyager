window.jQuery = window.$ = $ = require('jquery');
window.Vue = require('vue');
window.perfectScrollbar = require('perfect-scrollbar/jquery')($);
window.Cropper = require('cropperjs');
window.toastr = require('./toastr');
window.DataTable = require('./bootstrap-datatables');
window.SimpleMDE = require('simplemde');
window.tooltip = require('./bootstrap-tooltip');
window.MediaManager = require('./media');
require('dropzone');
require('./readmore');
require('./jquery-match-height');
require('./bootstrap-toggle');
require('./jquery-cookie');
require('./jquery-nestable');
require('bootstrap');
require('bootstrap-switch');
require('select2');
require('bootstrap-datetimepicker/src/js/bootstrap-datetimepicker');
var brace = require('brace');
require('brace/mode/json');
require('brace/theme/github');
require('./slugify');
window.TinyMCE = window.tinymce = require('./tinymce');
require('./multilingual');
require('./voyager_tinymce');
require('./voyager_ace_editor');
window.helpers = require('./helpers.js');

$(document).ready(function () {

    var appContainer = $(".app-container"),
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

    $(".hamburger, .navbar-expand-toggle").on('click', function () {
        appContainer.toggleClass("expanded");
        $(this).toggleClass('is-active');
        if ($(this).hasClass('is-active')) {
            window.localStorage.setItem('voyager.stickySidebar', true);
        } else {
            window.localStorage.setItem('voyager.stickySidebar', false);
        }
    });

    $('select.select2').select2({width: '100%'});
    $('select.select2-taggable').select2({
        width: '100%',
        tags: true,
        createTag: function(params) {
            var term = $.trim(params.term);

            if (term === '') {
                return null;
            }
        
            return {
                id: term,
                text: term,
                newTag: true
            }
        }
    }).on('select2:selecting', function(e) {
        var $el = $(this);
        var route = $el.data('route');
        var label = $el.data('label');
        var errorMessage = $el.data('error-message');
        var newTag = e.params.args.data.newTag;
        
        if (!newTag) return;

        $el.select2('close');

        $.post(route, {
            [label]: e.params.args.data.text,
        }).done(function(data) {
            var newOption = new Option(e.params.args.data.text, data.data.id, false, true);
            $el.append(newOption).trigger('change');
        }).fail(function(error) {
            toastr.error(errorMessage);
        });

        return false;
    });

    $('.match-height').matchHeight();

    $('.datatable').DataTable({
        "dom": '<"top"fl<"clear">>rt<"bottom"ip<"clear">>'
    });

    $(".side-menu .nav .dropdown").on('show.bs.collapse', function () {
        return $(".side-menu .nav .dropdown .collapse").collapse('hide');
    });

    $(document).on('click', '.panel-heading a.panel-action[data-toggle="panel-collapse"]', function (e) {
        e.preventDefault();
        var $this = $(this);

        // Toggle Collapse
        if (!$this.hasClass('panel-collapsed')) {
            $this.parents('.panel').find('.panel-body').slideUp();
            $this.addClass('panel-collapsed');
            $this.removeClass('voyager-angle-up').addClass('voyager-angle-down');
        } else {
            $this.parents('.panel').find('.panel-body').slideDown();
            $this.removeClass('panel-collapsed');
            $this.removeClass('voyager-angle-down').addClass('voyager-angle-up');
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

    // Save shortcut
    $(document).keydown(function (e) {
        if ((e.metaKey || e.ctrlKey) && e.keyCode == 83) { /*ctrl+s or command+s*/
            $(".btn.save").click();
            e.preventDefault();
            return false;
        }
    });

    /********** MARKDOWN EDITOR **********/

    $('textarea.simplemde').each(function () {
        var simplemde = new SimpleMDE({
            element: this,
        });
        simplemde.render();
    });

    /********** END MARKDOWN EDITOR **********/

});


$(document).ready(function () {
    $(".form-edit-add").submit(function (e) {
        e.preventDefault();

        var url = $(this).attr('action');
        var form = $(this);
        var data = new FormData();

        // Safari 11.1 Bug
        // Filter out empty file just before the Ajax request
        // https://stackoverflow.com/questions/49672992/ajax-request-fails-when-sending-formdata-including-empty-file-input-in-safari
        for (i = 0; i < this.elements.length; i++) {
            if (this.elements[i].type == 'file') {
                if (this.elements[i].value == '') {
                    continue;
                }
            }
            data.append(this.elements[i].name, this.elements[i].value)
        }

        data.set('_validate', '1');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            processData: false,
            contentType: false,

            beforeSend: function () {
                $("body").css("cursor", "progress");
                $(".has-error").removeClass("has-error");
                $(".help-block").remove();
            },

            success: function (d) {
                $("body").css("cursor", "auto");
                $.each(d.errors, function (inputName, errorMessage) {

                    // This will work also for fields with brackets in the name, ie. name="image[]
                    var $inputElement = $("[name='" + inputName + "']"),
                        inputElementPosition = $inputElement.first().parent().offset().top,
                        navbarHeight = $('nav.navbar').height();

                    // Scroll to first error
                    if (Object.keys(d.errors).indexOf(inputName) === 0) {
                        $('html, body').animate({
                            scrollTop: inputElementPosition - navbarHeight + 'px'
                        }, 'fast');
                    }

                    // Hightlight and show the error message
                    $inputElement.parent()
                        .addClass("has-error")
                        .append("<span class='help-block' style='color:#f96868'>" + errorMessage + "</span>")

                });
            },

            error: function () {
                $(form).unbind("submit").submit();
            }
        });
    });
});
