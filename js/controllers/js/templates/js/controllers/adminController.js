var adminController = {
    init: function() {
        templates.load('admin')
            .then(function (templateHtml) {
                $('#page-content-wrapper').html(templateHtml());
                
                $('#menu-partners').parent().removeClass('active');
                $('#menu-contacts').parent().addClass('active');
                $('#menu-profile').parent().removeClass('active');
                $('.dropdown-toggle').parent().removeClass('active');
                $('ul.nav a[href=""]').parent().removeClass('active');
            });
    },
    tabFilters: function() {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProperty" },
            success: function(data) {
                console.log(data.addProperty);
                templates.load('admin-filters')
                    .then(function(templateHtml) {
                        $('#admin-filters').html(templateHtml(data.addProperty));
                            // $('#admin-filters').dataTable({
                            //     responsive: true
                            // });
                    });

            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Getting ADMIN filters...');
            }
        });
    }
}