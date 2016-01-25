var homeController = {
    init: function() {
        templates.load('home')
            .then(function (templateHtml) {
                $('#page-content-wrapper').html(templateHtml());
            });
    }
}