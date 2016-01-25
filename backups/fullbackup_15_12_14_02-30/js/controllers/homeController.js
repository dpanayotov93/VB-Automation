var homeController = {
    init: function() {
        var container = document.getElementById("sign-pane");
        $('#filters-list').hide();
        // userController.keepSession();
        templates.load('home')
            .then(function(templateHtml) {
                $('#container').html(templateHtml(testData.products));
                $("html, body").animate({ scrollTop: 0 }, "slow");
            });
    }
}
