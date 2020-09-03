(function (w, $) {
    function initializeTinyMCE() {
        if ($('textarea[data="editable"], textarea[data="editable-with-bbcode"]').length < 1) {
            return;
        }

        tinymce.init({
            selector: "textarea[data='editable']",
            language_url: TINYMCE_LANGUAGE_URL,
            menubar: false,
            height: 300,
            toolbar: 'insertfile undo redo | bold italic | link image media',
            plugins: [
                "autolink link spellchecker image media table paste"
            ],
            file_picker_callback: function (callback, value, meta) {
                // http://stackoverflow.com/questions/24900018/tinymce4-file-picker-callback-return-additional-params

                if (meta.filetype == 'file') {
//                    callback('mypage.html', {text: 'My text'});
                }

                // Provide image and alt text for the image dialog
                if (meta.filetype == 'image') {
                    tinymce.activeEditor.windowManager.open({
                        title: 'Select image',
                        url: TINYMCE_FILE_OPENER_POPUP_URL,
                        width: 800,
                        height: 600
                    }, {
                        insertCallback: function (url, data) {
                            callback(url, data);
                        }
                    });
                    // callback('myimage.jpg', {alt: 'My alt text'});
                }

                // Provide alternative source and posted for the media dialog
                if (meta.filetype == 'media') {
//                    callback('movie.mp4', {source2: 'alt.ogg', poster: 'image.jpg'});
                }
            },
            relative_urls: false,
            remove_script_host: true,
            document_base_url: TINYMCE_DOCUMENT_BASE_URL,
            toolbar_items_size: 'small'
        });

        tinymce.init({
            selector: "textarea[data='editable-with-bbcode']",
            language_url: TINYMCE_LANGUAGE_URL,
            menubar: false,
            height: 300,
            toolbar: 'insertfile undo redo | bold italic | link image media',
            plugins: [
                "autolink bbcode spellchecker link image media paste"
            ],
            toolbar_items_size: 'small'
        });
    }

    function removeTinyMCE() {
        $('textarea[data="editable-with-bbcode"]').each(function (i, el) {
            tinymce.execCommand('mceRemoveEditor', false, $(el).attr('id'));
        });

        $('textarea[data="editable"]').each(function (i, el) {
            tinymce.execCommand('mceRemoveEditor', false, $(el).attr('id'));
        });
    }

    w.Brouzie = w.Brouzie || {};
    Brouzie.TinyMCE = {
        initialize: initializeTinyMCE,
        remove: removeTinyMCE
    };

    $(document).ready(function () {
        //TODO: не вызывать это для каждого попапа
        $('body')
            .bind('popup.open', function (e) {
                Brouzie.TinyMCE.initialize();
            })
            .bind('popup.close', function (e) {
                Brouzie.TinyMCE.remove();
            });
    });

})(window, jQuery);


