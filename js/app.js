$(document).ready(function() {
    var containerId = '#page-content-wrapper';
    var sammyApp = Sammy(containerId, function() {
        this.get('#/', function() {
            console.log('Home');
            // this.redirect('#/Home');
        });

        // this.get('#/Home', homeController.init);
        this.get('#/Products', productsController.get);
        // this.get('#/Partners', partnersController.init);
        // this.get('#/Contacts', contactsController.init);

    });

    sammyApp.run('#/');

    categoriesController.get();

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
})
