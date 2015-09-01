/*
 *  jquery-boilerplate - v3.4.0
 *  A jump-start for jQuery plugins development.
 *  http://jqueryboilerplate.com
 *
 *  Made by Zeno Rocha
 *  Under MIT License
 */
// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {

    "use strict";

    // undefined is used here as the undefined global variable in ECMAScript 3 is
    // mutable (ie. it can be changed by someone else). undefined isn't really being
    // passed in so we can ensure the value of it is truly undefined. In ES5, undefined
    // can no longer be modified.

    // window and document are passed through as local variable rather than global
    // as this (slightly) quickens the resolution process and can be more efficiently
    // minified (especially when both are regularly referenced in your plugin).

    // Create the defaults once
    var pluginName = "uniqueEmail",
        defaults = {
            events: ["keyup", "blur"]
        };

    // The actual plugin constructor
    function Plugin ( element, options ) {
        this.element = element;
        // jQuery has an extend method which merges the contents of two or
        // more objects, storing the result in the first object. The first object
        // is generally empty as we don't want to alter the default options for
        // future instances of the plugin
        this.settings = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend(Plugin.prototype, {
        init: function () {

            this.$element = $(this.element);
            this.$form = this.$element.parents('form:first');
            this.$validationEl = this.$element.siblings('.email-validation');

            this.bindEvents();
        },
        yourOtherFunction: function () {
            // some logic
        },

        /**
         * Validate email address
         * @param emailAddress
         * @returns {boolean}
         */
        isValidEmailAddress: function(emailAddress) {
            var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
            return pattern.test(emailAddress);
        },

        /**
         * Sends an AJAX request
         */
        sendRequest: function() {
            var plugin = this;

            $.ajax({
                url: '/freeform_unique_email/',
                data: {
                    email: $(this.element).val()
                },
                success: function(response) {
                    response = JSON.parse(response);
                    plugin.setStatus(response);
                    plugin.setStyles(response.success)
                    plugin.setValidationElement(response);
                    plugin.disableFormSubmit(response);
                }
            });
        },

        /**
         * Sets a status property for this instance
         * Needed to decide what to do on submit event
         * @param response
         * @returns {boolean}
         */
        setStatus: function(response) {
            var plugin = this;
            plugin.email_valid = response.success;
            return response.success;
        },

        /**
         * Set styles of input element
         * @param response
         */
        setStyles: function(success) {

            if(success) {
                this.$element.css({
                    'borderColor' : 'green'
                });
            }
            if(!success) {
                this.$element.css({
                    'borderColor' : 'red'
                });
            }
            return;
        },

        unsetStyles: function() {
            this.$element.css('borderColor', 'transparent');
        },

        /**
         * Set validation element
         * Creates a HTML element on the fly and sets properties
         * according to success value
         * @param response
         */
        setValidationElement: function(response) {
            var $p, $validationEl, plugin;

            $p = this.$element.closest('p');
            if(this.$validationEl.length < 1) {
                console.log('validation element not available, create it!');
                this.$validationEl = $('<small/>', {
                    class: 'email-validation'
                });
                $p.append(this.$validationEl);
            }

            this.$validationEl
                .css('color', response.success === true ? 'green' : 'red')
                .attr('class', 'email-validation')
                .html(response.message);

            return;
        },

        disableFormSubmit: function(response) {

        },

        // Bind events that trigger methods
        bindEvents: function() {
            var plugin = this, events = '';

            /**
             * Bind 'keydown' events (except cursor)
             * Requests email validation information via ajax
             */
            events = plugin.settings.events.join('.' + plugin._name + ' ') + '.' + plugin._name + ' ';
            plugin.$element.on(events, function(e) {
                var code = (e.keyCode || e.which);

                // do nothing if it's an arrow key
                if(code == 37 || code == 38 || code == 39 || code == 40) {
                    return;
                }

                if(!plugin.isValidEmailAddress(plugin.$element.val())) {
                    if(plugin.$validationEl.length >= 1) {
                        plugin.$validationEl.remove();
                        plugin.setStyles(false);
                        plugin.$validationEl = [];
                    }
                    return;
                }
                plugin.sendRequest();
            });

            /**
             * Bind 'submit' event
             * Do not submit if email is not valid
             */
            plugin.$form.on('submit'+'.'+plugin._name, function(e) {
                if(!plugin.email_valid)
                    alert('Please validate your email address first!');
                return plugin.email_valid;
            });

            /*
             Bind event(s) to handlers that trigger other functions, ie:
             "plugin.$element.on('click', function() {});". Note the use of
             the cached variable we created in the buildCache method.

             All events are namespaced, ie:
             ".on('click'+'.'+this._name', function() {});".
             This allows us to unbind plugin-specific events using the
             unbindEvents method below.
             */
            //plugin.$element.on('click'+'.'+plugin._name, function() {
            //    /*
            //     Use the "call" method so that inside of the method being
            //     called, ie: "someOtherFunction", the "this" keyword refers
            //     to the plugin instance, not the event handler.
            //
            //     More: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/call
            //     */
            //    plugin.someOtherFunction.call(plugin);
            //});
        },
    });

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[ pluginName ] = function ( options ) {
        return this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            }
        });
    };

})( jQuery, window, document );

$('#freeform_email').uniqueEmail();