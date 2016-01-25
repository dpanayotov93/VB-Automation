var partnersController = {
    init: function() {
        templates.load('partners')
                .then(function (templateHtml) {
                    $('#page-content-wrapper').html(templateHtml());
                    
                    $('#menu-partners').parent().addClass('active');
                    $('#menu-contacts').parent().removeClass('active');
                    $('#menu-profile').parent().removeClass('active');
                    $('.dropdown-toggle').parent().removeClass('active');
                    $('ul.nav a[href=""]').parent().removeClass('active');
                });
    }
}