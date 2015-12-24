var contactController = {
    init: function() {
        templates.load('contact')
                        .then(function (templateHtml) {
                            $('#page-content-wrapper').html(templateHtml());
                            
                            $('#menu-partners').parent().removeClass('active');
                            $('#menu-contacts').parent().addClass('active');
                            $('#menu-profile').parent().removeClass('active');
                            $('.dropdown-toggle').parent().removeClass('active');
                            $('ul.nav a[href=""]').parent().removeClass('active');
                        });
    }
}