var partnersController = {
    init: function() {
        templates.load('products')
                .then(function (templateHtml) {
                    $('#page-content-wrapper').html(templateHtml());

                });
    }
}