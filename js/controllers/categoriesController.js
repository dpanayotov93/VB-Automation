var categoriesController = {
    get: function() {
        $.ajax({
            type: "POST",
            url: "../VB-Automation/php/handle.php",
            dataType: 'json',
            data: { q:"categories"},
            success: function(data) {
                templates.load('categories')
                    .then(function(templateHtml) {
                        $('#categories').html(templateHtml(data.categories));
                    });

            },
            error: function() {
                templates.load('categories')
                    .then(function(templateHtml) {
                        $('#categories').html(templateHtml(testData.categories));
                    });
            },
            beforeSend:function(){
                 console.log('Getting Products Categories...');
            }
        });
    }
}
