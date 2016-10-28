/*
 * bootstrap dropdown on hover
 * 
 *
 * Copyright (c) 2014 Ben Miller
 * Licensed under the MIT license.
 */

// boilerplate from http://stefangabos.ro/jquery/jquery-plugin-boilerplate-revisited/

(function($) {

    // here we go!
    $.bootstrapDropdownOnHover = function(element, options) {

        // plugin's default options
        // this is private property and is accessible only from inside the plugin
        var defaults = {
            mouseOutDelay: 500,
            responsiveThreshold: 992,
            hideBackdrop: true
        };

        // to avoid confusions, use "plugin" to reference the
        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('pluginName').settings.propertyName from outside the plugin,
        // where "element" is the element the plugin is attached to;
        plugin.settings = {};

        // reference to the jQuery version of DOM element
        var $container = $(element);
        // reference to the window object
        var $W = $(window);
        // reference to the timer that times how long the mouse is outside of the menu element
        var menuTimer = -1;

        // the "constructor" method that gets called when the object is created
        plugin.init = function() {

            // the plugin's final properties are the merged default and
            // user-provided options (if any)
            plugin.settings = $.extend({}, defaults, options);

            // find each dropdown parent item
            $container.find("[data-toggle='dropdown']").each(function(i, el) {
                // the trigger element, usually a button or an anchor
                var $trigger = $(el);
                // the menu element, the ul container of the li elements
                var $menu = $trigger.parent().find(".dropdown-menu");
                // the parent of both the trigger and the menu
                var $parent = $trigger.parent();

                $trigger.on("mouseenter.bnoh", function() {

                    // responsive check, disable this function if it is in the responsive threshold
                    if (responsive()) {
                        return;
                    }

                    // when the mouse enters the trigger element, we should immediately cancel the existing timeout
                    // if the mouse goes into the menu, then back to the trigger, we don't want the menu to hide
                    clearTimeout(menuTimer);

                    // if the dropdown is not yet open, we should open it.
                    // we check first to make sure not to double toggle the menu
                    if (!$parent.hasClass("open")) {

                        // trigger bootstrap's dropdown
                        $trigger.dropdown('toggle');

                        /* Bootstrap puts a backdrop on touch-enabled (mostly mobile) devices, but it could also appear
                        on devices such as the Surface or other large touch-screen devices that might also be used with
                        a trackpad or mouse. This overlay prevents the mouse from interacting with other elements, so we're
                        going to disable the backdrop for the use of this plugin.

                        This behavior can be disabled using the hideBackdrop setting.
                         */
                        if (plugin.settings.hideBackdrop) {
                            $parent.find(".dropdown-backdrop").remove();
                        }
                    }
                });

                $trigger.on("mouseleave.bnoh", function() {

                    // responsive check, disable this function if it is in the responsive threshold
                    if (responsive()) {
                        return;
                    }

                    // when the mouse leaves the trigger, set the menu timeout
                    menuTimer = setTimeout(function() {

                        // on timeout, check whether the menu is open
                        if ($parent.hasClass("open") && $parent.find(".dropdown-backdrop").length === 0) {

                            // if it is open, then we'll hide it
                            $trigger.dropdown('toggle');

                            // blur the trigger element to remove the focus or active styling
                            $trigger.blur();
                        }
                    }, plugin.settings.mouseOutDelay); // setting for mouse-out duration
                });

                $menu.on("mouseenter.bnoh", function() {

                    // responsive check, disable this function if it is in the responsive threshold
                    if (responsive()) {
                        return;
                    }

                    // we don't want to hide the menu (the timer set on the trigger mouseleave will hide it)
                    // unless we clear it when the menu is hovered upon
                    clearTimeout(menuTimer);
                });

                $menu.on("mouseleave.bnoh", function() {

                    // responsive check, disable this function if it is in the responsive threshold
                    if (responsive()) {
                        return;
                    }

                    // set the menu timer upon the mouse leaving the menu
                    menuTimer = setTimeout(function() {

                        // check to see that the menu is open
                        if ($parent.hasClass("open")) {

                            // if it is, then hide it
                            $trigger.dropdown('toggle');

                            // blur the trigger element to remove the focus or active styling
                            $trigger.blur();
                        }
                    }, plugin.settings.mouseOutDelay); // setting for mouse-out duration
                });
            });
        };

        // public methods
        // these methods can be called like:
        // plugin.methodName(arg1, arg2, ... argn) from inside the plugin or
        // element.data('pluginName').publicMethod(arg1, arg2, ... argn) from outside
        // the plugin, where "element" is the element the plugin is attached to;

        // destroy and unset all the plugin functions
        plugin.destroy = function() {

            // unbind mouse enter and leave events for triggers
            $container.find("[data-toggle='dropdown']").unbind(".bnoh");

            // unbind mouse enter and leave events for menus
            $container.find(".dropdown-menu").unbind(".bnoh");

            // remove plugin data
            $container.removeData("bootstrapDropdownOnHover");
        };

        // private methods
        // these methods can be called only from inside the plugin like:
        // methodName(arg1, arg2, ... argn)

        var responsive = function() {

            // returns true if the plugin is set to be responsive and the width of the window is less than
            // the plugin responsive threashhold setting
            return $W.width() <= plugin.settings.responsiveThreshold;
        };

        // fire up the plugin!
        // call the "constructor" method
        plugin.init();

    };

    // add the plugin to the jQuery.fn object
    $.fn.bootstrapDropdownOnHover = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined === $(this).data('bootstrapDropdownOnHover')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.bootstrapDropdownOnHover(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('pluginName').publicMethod(arg1, arg2, ... argn) or
                // element.data('pluginName').settings.propertyName
                $(this).data('bootstrapDropdownOnHover', plugin);

            }

        });

    };

})(jQuery);