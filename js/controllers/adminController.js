/* global templates */
var adminController = {
    init: function() {
        templates.load('admin')
            .then(function (templateHtml) {
                $('#page-content-wrapper').html(templateHtml());

                $('#menu-admin').parent().addClass('active');

                $('#menu-partners').parent().removeClass('active');
                $('#menu-contacts').parent().removeClass('active');
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
    tabProducts: function() {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addCategory", showCats:"1" },
            success: function(data) {
                console.log(data.addCategory);
                templates.load('admin-products')
                    .then(function(templateHtml) {
                        $('#4').html(templateHtml(data.addCategory));
                    });

            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Getting ADMIN products tab...');
            }
        });
    },
    tabStatistics: function() {
        templates.load('admin-statistics')
            .then(function(templateHtml) {
                $('#0').html(templateHtml);
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
    showProducts: function() {
         var selectCat = document.getElementById("selectedCategory");
         var catid = selectCat.options[selectCat.selectedIndex].value;
         console.log(catid);
         $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProduct", catid:catid, show:1},
            success: function(data) {
                console.log(data);
                templates.load('admin-products-list') //TODO
                    .then(function(templateHtml) {
                        $('#show-products-wrap').html(templateHtml(data.addProduct)); //TODO
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
    editCategory: function(id) {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addCategory" },
            success: function(data) {
                console.log(data);
                data.addCategory.id = id;
                templates.load('admin-category-edit')
                    .then(function(templateHtml) {
                       console.log("Data: " + data.addCategory);
                       $('#edit-category-modal-body').html(templateHtml(data.addCategory));
                    });
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                console.log('editing ADMIN category...');
            }
        });
    },
    sendEditedFilter: function() {
        var searchable = document.getElementById('searchable').checked;
        var langDependant = document.getElementById('langDependant').checked;
        var id = document.getElementById('filterId').innerHTML;

        if (langDependant) {
            var data = {
                q: "addProperty",
                id: id,
                searchable: searchable,
            }
        }
        else {
            var data = {
                q: "addProperty",
                id: id,
                searchable: searchable,
                langDependant: langDependant
            }
        }

        $("textarea").each(function(){
            data[this.id] = this.value;
        });

        console.log(data);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: data,
            success: function(data) {
                console.log(data);
                document.getElementById('admin-tab-filters').click();
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Editing category...');
            }
        });
    },
    sendEditedCategories: function() {
        var id =  document.getElementById('categoryId').innerHTML;
        var data = {
            q: "addCategory",
            id: id
        }

        $("textarea").each(function(){
            data[this.id] = this.value;
        });

        console.log(data);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: data,
            success: function(data) {
                console.log(data);
                document.getElementById('admin-tab-categories').click();
                //document.getElementById('admin-show-all-categories').click();
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                 console.log('Editing category...');
            }
        });
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
    deleteCategory: function(id) {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addCategory", id:id, delete:1 },
            success: function(data) {
                console.log(data);
                adminController.showCategories();
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                console.log('deleting ADMIN category...');
            }
        });
    },
    deleteProduct: function(id) {
        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q:"addProduct", id:id, debug:1, delete:1 },
            success: function(data) {
                console.log(data);
                adminController.showProducts();
            },
            error: function(data) {
                console.log(data);
            },
            beforeSend:function(){
                console.log('deleting ADMIN product...');
            }
        });
    },
    createFilter: function() {
        var searchableValue = document.getElementById('searchable').checked,
            langDependantValue = document.getElementById('langDependant').checked

        if (!langDependantValue) {
            var data = {
                q: "addProperty",
                searchable: searchableValue
            }
        }
        else {
            var data = {
                q: "addProperty",
                searchable: searchableValue,
                langDependant: langDependantValue
            }
        }

        $("textarea").each(function(){
            data[this.id] = this.value;
        });

        console.log(data);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: data,
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
            namesBG = document.getElementById('names[BG]').value,
            namesEn = document.getElementById('names[EN]').value,
            descBG = document.getElementById('desc[BG]').value,
            descEN = document.getElementById('desc[EN]').value,
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
            data: { q:"addCategory", parentid:parentid, 'names[BG]':namesBG, 'names[EN]':namesEn, 'desc[BG]':descBG, 'desc[EN]':descEN, imgUrl:imgUrl, fid:fid},
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
    },
    createProduct: function() {
        var selectCat = document.getElementById("selectedCategory");
        var catid = selectCat.options[selectCat.selectedIndex].value;
        var pdata = {
            q: "addProduct",
            catid: catid
        }

        $("textarea").each(function(){
            pdata[this.id] = this.value;
        });

        console.log(pdata);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: pdata,
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
    },
    addProduct: function() {
        var selectCat = document.getElementById("selectedCategory");
        var catid = selectCat.options[selectCat.selectedIndex].value;
        console.log(catid);
        $.ajax({
           type: "POST",
           url: "../emag/php/handle.php",
           dataType: 'json',
           data: { q:"addProduct", catid:catid },
           success: function(data) {
               console.log(data);
               templates.load('admin-products-add') //TODO
                   .then(function(templateHtml) {
                       $('#create-product-wrap').html(templateHtml(data.addProduct)); //TODO
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
