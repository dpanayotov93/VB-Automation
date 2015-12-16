var cart = [];
var productsController = {
    get: function(id) {
        // window.history.pushState('products', 'Products', '/#/products');

        $('.dropdown-toggle').parent().addClass('active');
        $('ul.nav a[href=""]').parent().removeClass('active');

        console.log(id);
        var tempId = 7;

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"selectCat", id:tempId },
            success: function(data) {
                console.log(data.selectCat);
                templates.load('products')
                    .then(function(templateHtml) {
                        $('#page-content-wrapper').html(templateHtml(data.selectCat));
                        // $('#filters-table').dataTable({
                        //     responsive: true,
                        //     "bPaginate": false,
                        //     "bFilter": false,
                        //     "bSort": false,
                        //     "bInfo": false
                        // });
                        $('#products-table').dataTable({
                            responsive: true
                        });
                    });

            },
            error: function() {
                templates.load('products')
                    .then(function(templateHtml) {
                        $('#page-content-wrapper').html(templateHtml(testData));
                        $('#filters-table').dataTable({
                            responsive: true,
                            "bPaginate": false,
                            "bFilter": false,
                            "bSort": false,
                            "bInfo": false
                        });
                        $('#products-table').dataTable({
                            responsive: true
                        });
                    });
            },
            beforeSend:function(){
                 console.log('Getting Products Categories...');
            }
        });
    },
    filter: function(filter) {
        // TODO: IMPLEMENT
        console.log('Filtering ' + filter + ' ...');
        productsController.get;
    },
    addToCart: function(name) {
        cart.push(name);
        templates.load('cart')
            .then(function(templateHtml) {
                $('#sidebar-wrapper').html(templateHtml(cart));

            });
    }
}
