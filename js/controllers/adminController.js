/* global templates */
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
    tabCategories: function() {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addCategory" },
            success: function(data) {
                console.log(data.addCategory);
                templates.load('admin-categories')
                    .then(function(templateHtml) {
                        $('#2').html(templateHtml(data.addCategory));
                        adminController.showCategories();
                    });

            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Getting ADMIN categories tab...');
            }
        });
    },
    getAllFilterIds: function() {
        var listOfFilterIds = [];
         $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProperty", showProps:"1" },
            success: function(data) {
                console.log(data.addProperty);
                for (var i = 0; i < data.addProperty.length; i++) {
                    listOfFilterIds.push(data.addProperty[i].id);
                }
                adminController.getAllFilterNames(listOfFilterIds);
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Getting ADMIN filters...');
            }
        });
    },
    getAllFilterNames: function(listOfFilterIds) {
        var filtersData = {};
        var listOfFilterNames = [];
         $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProperty", showProps:"1" },
            success: function(data) {
                console.log(data.addProperty);
                for (var i = 0; i < data.addProperty.length; i++) {
                    listOfFilterNames.push(data.addProperty[i].nameEN);
                }

                for (var i = 0; i < listOfFilterIds.length; i++) {
                    filtersData[listOfFilterIds[i]] = listOfFilterNames[i];
                }
                console.log(filtersData);
                templates.load('admin-categories-add-filters')
                    .then(function(templateHtml) {
                        $('#addCategoryFilters').html(templateHtml(filtersData));
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
                        $('#show-filters-wrap').html(templateHtml(data.addProperty));
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
    showCategories: function() {
         $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addCategory", showCats:"1" },
            success: function(data) {
                console.log(data);
                templates.load('admin-categories-list') //TODO
                    .then(function(templateHtml) {
                        $('#show-categories-wrap').html(templateHtml(data.addCategory)); //TODO
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
    editFilter: function(id) {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProperty" },
            success: function(data) {
                console.log(data);
                data.addProperty.id = id;
                templates.load('admin-filter-edit')
                    .then(function(templateHtml) {
                       $('#edit-filter-modal-body').html(templateHtml(data.addProperty));
                    });
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                    console.log('editing ADMIN filter...');
            }
        });
    },
    sendEditedFilter: function() {
        var name = document.getElementById('name').value,
            namesBG = document.getElementById('names[BG]').value,
            namesEn = document.getElementById('names[EN]').value,
            descBG = document.getElementById('desc[BG]').value,
            descEN = document.getElementById('desc[EN]').value,
            searchable = document.getElementById('searchable').checked,
            langDependant = document.getElementById('langDependant').checked,
            id = document.getElementById('filterId').innerHTML;

        console.log(id + " | " + name + " | " + namesBG + " | " + namesEn + " | " + descBG + " | " + descEN + " | " + searchable + " | " + langDependant);

    },
    deleteFilter: function(id) {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProperty", id:id, delete:1 },
            success: function(data) {
                console.log(data);
                adminController.showFilters();
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                    console.log('deleting ADMIN filter...');
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
                document.getElementById('admin-tab-filters').click();
                //document.getElementById('admin-show-all-filters').click();
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Creating new filter...');
            }
        });
    },
    createCategory: function() {
        var parentid = document.getElementById('parentid').value,
            namesBG = document.getElementById('namesBG').value,
            namesEn = document.getElementById('namesEN').value,
            descBG = document.getElementById('descBG').value,
            descEN = document.getElementById('descEN').value,
            imgUrl = document.getElementById('imgurl').value;
            checkboxes = document.getElementsByName('checkbox');
            fid = [];

            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    fid.push(checkboxes[i].value);
                }
            }

        console.log(parentid + " | " + namesBG + " | " + namesEn + " | " + descBG + " | " + descEN + " | " + imgUrl + " | " + fid);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addCategory", parentid:parentid, 'namesBG':namesBG, 'namesEN':namesEn, 'descBG':descBG, 'descEN':descEN, imgUrl:imgUrl, fid:fid},
            success: function(data) {
                console.log(data);
                document.getElementById('admin-tab-categories').click();
                //document.getElementById('admin-show-all-categories').click();
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Creating new category...');
            }
        });
    }
}
