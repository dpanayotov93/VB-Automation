/* global Sammy */
/* global partnersController */
/* global contactController */
/* global categoriesController */

$(document).ready(function() {
    var containerId = '#page-content-wrapper';
    var sammyApp = Sammy(containerId, function() {
        this.get('#/', function() {
            this.redirect('#/Home');
        });

        this.get('#/Home', homeController.init);
        this.get('#/Products', productsController.get); 
        this.get('#/Profile', userController.showProfile);
        this.get('#/Partners', partnersController.init);
        this.get('#/Contact', contactController.init);

    });

    sammyApp.run('#/');

    categoriesController.get();
    userController.keepSession();

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
})
