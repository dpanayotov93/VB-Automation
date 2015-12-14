var categoriesController = {
    onLoaded: function() {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"categories"},
            success: function(data) {
                console.log(data);
                // testData.categories = data.categories;
                templates.load('categories')
                    .then(function(templateHtml) {
                        $('#categories-list').html(templateHtml(data.categories));
                        // console.log(templateHtml(data.categories));
                        categoriesController.onMouseClick(7);

                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    });

            },
            error: function() {
                templates.load('categories')
                    .then(function(templateHtml) {
                        $('#categories-list').html(templateHtml(testData.categories));
                        // console.log(templateHtml(testData.categories));
                        categoriesController.onMouseClick(7);

                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    });
            },
            beforeSend:function(){
                 console.log('sending');
            }
        });

    },
    onMouseClick:function(id) {
        var tempId = 7;
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"selectCat", id:tempId },
            success: function(data) {
                templates.load('products2')
                    .then(function(templateHtml) {
                        $('#container').html(templateHtml(data.selectCat));
                        var newData = data.selectCat.products;
                        newData.forEach(function(entry) {
                            // console.log(entry.imgurl);
                            entry.imgurl = '<img src="' + entry.imgurl + '" alt="" />';
                        });
                        $(document).ready(function(){
                            $('#products-table').dynatable({
                              dataset: {
                                records: newData
                              }
                            });
                            $('#products-table').cardtable();
                        });

                        templates.load('filters')
                            .then(function(templateHtml) {
                                $('#filters-list').html(templateHtml(data.selectCat.filters));
                                $('#filters-table').stacktable();
                            });
                    });


            },
            error: function(data) {
                templates.load('products2')
                    .then(function(templateHtml) {
                        $('#container').html(templateHtml(testData));
                        var newData = testData.products;
                        newData.forEach(function(entry) {
                            // console.log(entry.imgurl);
                            entry.imgurl = '<img id="product-img" src="' + entry.imgurl + '" alt="" />';
                        });
                        $(document).ready(function(){
                            $('#products-table').dynatable({
                              dataset: {
                                records: newData
                              }
                            });
                            $('#products-table').cardtable();
                        });

                        templates.load('filters')
                            .then(function(templateHtml) {
                                // console.log(data.selectCat.filters);
                                $('#filters-list').html(templateHtml(testData.filters));
                                $('#filters-table').stacktable();
                            });
                    });
            },
            beforeSend:function(){
                 console.log('fetching');
            }
        });
    },
    moveFiltersPane: function() {
        alert();
    }
}
