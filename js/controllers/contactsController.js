var contactsController = {
    init: function() {
        $('#filters-list').hide();
        userController.keepSession();
        templates.load('contacts')
            .then(function(templateHtml) {
                $('#container').html(templateHtml);
                $("html, body").animate({ scrollTop: 0 }, "slow");
            });
    }
}
