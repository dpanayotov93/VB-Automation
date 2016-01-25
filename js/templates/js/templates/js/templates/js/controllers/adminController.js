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
                        $('#3').html(templateHtml(data.addProperty));
                        adminController.showFilters();
                    });

            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Getting ADMIN filters tab...');
            }
        });
    },
    showFilters: function() {   
         $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProperty", showProps:"1" },
            success: function(data) {
                console.log(data);
                templates.load('admin-filters-list')
                    .then(function(templateHtml) {
                        $('#show-filters-wrap').html(templateHtml(data));
                    });

            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Getting ADMIN filters...');
            }
        });       
    },
    createFilter: function() {
        var name = document.getElementById('name').value,
            namesBG = document.getElementById('names[BG]').value,
            namesEn = document.getElementById('names[EN]').value,
            descBG = document.getElementById('desc[BG]').value,
            descEN = document.getElementById('desc[EN]').value,
            searchable = document.getElementById('searchable').checked,
            langDependant = document.getElementById('langDependant').checked;
            
        console.log(name + " | " + namesBG + " | " + namesEn + " | " + descBG + " | " + descEN + " | " + searchable + " | " + langDependant);
        
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProperty", name:name, 'names[BG]':namesBG, 'names[EN]':namesEn, 'desc[BG]':descBG, 'desc[EN]':descEN, searchable:searchable, langDependant:langDependant},
            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Creating new filter...');
            }
        });
    }
}