var contactController = {
    init: function() {
        templates.load('contact')
                        .then(function (templateHtml) {
                            $('#page-content-wrapper').html(templateHtml());
                           
                        });
    }
}