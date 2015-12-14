var partnersController = {
    init: function() {
        $('#filters-list').hide();
        userController.keepSession();

        templates.load('partners')
            .then(function(templateHtml) {
                $('#container').html(templateHtml);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            });
    }
}
