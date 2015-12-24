var cart = [];
var filters = [];
var productsController = {
    get: function(id) {
        var tempId = 7;

        $('.dropdown-toggle').parent().addClass('active');
        $('#menu-profile').parent().removeClass('active');
        $('ul.nav a[href=""]').parent().removeClass('active');

        console.log(id);

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
    compare: function() {
        console.log('chechbox init');
            var totalItemsChecked = $('.checkbox-primary:checked').size();
            if (totalItemsChecked === 2) {
                console.log('Compare '+ totalItemsChecked + '!');
                $('input.checkbox-primary:not(:checked)').attr('disabled', 'disabled');
                $('#myModal').modal('show');
            } else if (totalItemsChecked > 2) {
                console.log('Compare ' + totalItemsChecked + '!');
                $('input.checkbox-primary:not(:checked)').attr('disabled', 'disabled');
            } else {
                $('input.checkbox-primary').removeAttr('disabled');
            }
    },
    filter: function (filter) {
        var tempId = 7;
        console.log('Filtering ' + filter + ' ...');

        filters.push(filter);

        console.log(filters);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q: "selectCat", id: tempId, filters: filters },
            success: function (data) {
                console.log(data.selectCat);
                templates.load('products')
                    .then(function (templateHtml) {
                        $('#page-content-wrapper').html(templateHtml(data.selectCat));
                        $('#products-table').dataTable({
                            responsive: true
                        });
                    });

            },
            error: function (data) {
                console.log(data);
            },
            beforeSend: function () {
                console.log('Getting Filtered Products Categories...');
            }
        });
    },
    addToCart: function(name) {
        cart.push(name);
        console.log(name);
        templates.load('cart')
            .then(function(templateHtml) {
                $('#sidebar-wrapper').html(templateHtml(cart));

            });
    }
}
