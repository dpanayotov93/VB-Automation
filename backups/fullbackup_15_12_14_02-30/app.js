$(document).ready(function() {
    var containerId = '#container';
    var sammyApp = Sammy(containerId, function() {
        this.get('#/', function() {
            console.log('Home');
            this.redirect('#/Home');
        });

        this.get('#/Home', homeController.init);
        this.get('#/Products', productsController.init);
        this.get('#/Partners', partnersController.init);
        this.get('#/Contacts', contactsController.init);

    });

    sammyApp.run('#/');
})
