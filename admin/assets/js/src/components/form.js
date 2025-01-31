Formwork.Form = function (form) {
    var $window = $(window);
    var $form = $(form);

    $form.data('originalData', $form.serialize());

    $window.on('beforeunload', function () {
        if (hasChanged()) {
            return true;
        }
    });

    $form.on('submit', function () {
        $window.off('beforeunload');
    });

    $('input:file[data-auto-upload]', $form).on('change', function () {
        if (!hasChanged(false)) {
            $form.trigger('submit');
        }
    });

    $('[data-command=continue]', '#changesModal').on('click', function () {
        $window.off('beforeunload');
        window.location.href = $(this).attr('data-href');
    });

    $('a[href]:not([href^="#"]):not([target="_blank"])').on('click', function (event) {
        if (hasChanged()) {
            var link = this;
            event.preventDefault();
            Formwork.Modals.show('changesModal', null, function ($modal) {
                $('[data-command=continue]', $modal).attr('data-href', link.href);
            });
        }
    });

    function hasChanged(checkFileInputs) {
        if (typeof checkFileInputs === 'undefined') {
            checkFileInputs = true;
        }
        var $fileInputs = $(':file', $form);
        if (checkFileInputs === true && $fileInputs.length > 0) {
            for (var i = 0; i < $fileInputs.length; i++) {
                if ($fileInputs[i].files.length > 0) {
                    return true;
                }
            }
        }
        return $form.serialize() !== $form.data('originalData');
    }
};
