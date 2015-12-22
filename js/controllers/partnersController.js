var partnersController = {
    init: function() {
        templates.load('partners')
                .then(function (templateHtml) {
                    $('#page-content-wrapper').html(templateHtml());

                });
    }
}