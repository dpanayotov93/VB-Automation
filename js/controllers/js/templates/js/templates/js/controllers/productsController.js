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
                            responsive: true,
                            "aoColumnDefs": [ 
                                { "bSortable": false, "aTargets": [ 0, 1, 2 ] }
                            ]
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
    filter: function(value, name) {
        console.log(name + " | " + value);
        testData.selectedFilers.name.push(name)
        testData.selectedFilers.value.push(value)

        var newData = {
            q:"selectCat",
            id:7
        }
        console.log(testData.name);
        for (var i = 0; i < testData.selectedFilers.name.length; i += 1) {
            newData["filters[" + testData.selectedFilers.name[i] + "]"] = testData.selectedFilers.value[i];
        }
        console.log(newData);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: newData,
            success: function(data) {
                console.log(data);
                templates.load('products')
                    .then(function(templateHtml) {
                        $('#page-content-wrapper').html(templateHtml(data.selectCat));
                        $('#products-table').dataTable({
                            responsive: true,
                            "aoColumnDefs": [ 
                                { "bSortable": false, "aTargets": [ 0, 1, 2 ] }
                            ]
                        });
                    });
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('fetching');
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
