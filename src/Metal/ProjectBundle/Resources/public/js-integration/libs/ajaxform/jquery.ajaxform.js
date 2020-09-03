(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            'jquery.ui.widget'
        ], factory);
    } else {
        // Browser globals:
        factory(window.jQuery);
    }
}(function ($) {
    'use strict';
    $.widget('brouzie.ajaxform', {
        options: {},

        _create: function () {
            if (this.element.find(':file').length > 0 && typeof this.element.fileupload === 'undefined') {
                throw new Error('Install blueimp fileupload first for using forms with files.');
            }
        },

        submit: function() {
            var $fileInputs = this.element.find(':file');
            var hasFiles = false;
            var promise;
            var self = this;

            if (this.element.data('blueimpFileupload')) {
                this.element.data('blueimpFileupload')._getFileInputFiles($fileInputs)
                    .always(function (files) {
                        if (files.length > 0) {
                            hasFiles = true;
                        }
                    });
            }

            self._trigger('before');

            if (hasFiles) {
                promise = this.element.fileupload('send', {fileInput: $fileInputs});
            } else {
                promise = $.ajax({
                    url: this.element.attr('action'),
                    type: this.element.attr('method') || 'POST',
                    dataType: 'json',
                    data: this.element.serializeArray()
                });
            }

            //TODO: before
            promise
                .done(function(data, textStatus, jqXHR) { // success
                    var jsonData = data;

                    if (typeof data === 'string') {
                        try {
                            jsonData = $.parseJSON(data);
                        } catch (e) {
                            jsonData = data;
                        }
                    }

                    self._trigger('done', null, {data: jsonData, textStatus: textStatus, jqXHR: jqXHR});
                })
                .fail(function(jqXHR, textStatus, errorThrown) { // error
                    self._trigger('fail', null, {jqXHR: jqXHR, textStatus: textStatus, errorThrown: errorThrown});
                })
                .always(function(dataOrJqXHR, textStatus, jqXHROrErrorThrown) { // complete
                    self._trigger('always', null, {dataOrJqXHR: dataOrJqXHR, textStatus: textStatus, jqXHROrErrorThrown: jqXHROrErrorThrown});
                })
            ;

            return promise;
        }
    });
}));
