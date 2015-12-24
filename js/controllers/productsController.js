/* global templates */
var cart = [];
var comparables = [];
var filters = [];
var productsController = {
    get: function(id) {
        var tempId = 7;

        $('.dropdown-toggle').parent().addClass('active');
        $('#menu-partners').parent().removeClass('active');
        $('#menu-contacts').parent().removeClass('active');
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
    compare: function(name) {
        var totalItemsChecked = comparables.length,
            index = comparables.indexOf(name);
        console.log('Total: ' + totalItemsChecked);
        if (totalItemsChecked === 2) {
            comparables.splice(index, 1);
            $('input.checkbox-primary').removeAttr('disabled');
        } else if (totalItemsChecked === 1) {
            if(index > -1) {
                comparables.splice(index, 1);
                console.log('Item --' + name + '-- removed from the compare list');
                $('input.checkbox-primary').removeAttr('disabled');
            } else {
                comparables.push(name);
                $('#myModal').modal('show');
                console.log('Item --' + name + '-- added to the compare list');
                $('input.checkbox-primary:not(:checked)').attr('disabled', 'disabled');
            }
        } else if (totalItemsChecked === 0) {
            comparables.push(name);
            console.log('Item --' + name + '-- added to the compare list');
            $('input.checkbox-primary').removeAttr('disabled');
        } else {
            $('input.checkbox-primary').removeAttr('disabled');
        }
        console.log(comparables);
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
