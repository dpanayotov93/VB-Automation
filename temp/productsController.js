var productsController = {
    init: function() {
        $('#container2').show();
        $('#cat-filter-wrap').show();
        userController.keepSession();
        categoriesController.onLoaded();
    },
    filter: function(name, value, id) {
        console.log(name + " | " + value + " | " + id);
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
                templates.load('products2')
                    .then(function(templateHtml) {
                        $('#container').html(templateHtml(data.selectCat));
                        var newData = data.selectCat.products;
                        newData.forEach(function(entry) {
                            console.log(entry.imgurl);
                            entry.imgurl = '<img src="' + entry.imgurl + '" alt="" />';
                        });
                        $(document).ready(function(){
                            $('#products-table').dynatable({
                              dataset: {
                                records: newData
                              }
                            });
                        });

                        templates.load('filters')
                            .then(function(templateHtml) {
                                console.log(data.selectCat.filters);
                                $('#filters-list').html(templateHtml(data.selectCat.filters));
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
    addProduct: function() {
        var containerWrap = document.getElementById("sign-pane-wrap");
        var container = document.getElementById("sign-pane");

        containerWrap.style.visibility = 'visible';
        container.style.visibility = 'visible';
        container.style.opacity = '1';
        containerWrap.style.opacity = '1';
        document.body.style.overflowY = 'hidden';

        templates.load('admin')
            .then(function(templateHtml) {
                $('#sign-pane').html(templateHtml);
            });
    },
    addProductAjax: function() {
        $.ajax({
          type: "POST",
          url: "../emag/php/handle.php",
          data: {
                q:'tmp',
                id:1,
                System:document.getElementById('add-system').value,
                Design:document.getElementById('add-design').value,
                Resolution:document.getElementById('add-resolution').value,
                Shaft:document.getElementById('add-shaft').value,
                Output:document.getElementById('add-output').value,
                Features:document.getElementById('add-features').value,
                Bit:document.getElementById('add-bit').value,
                Type:document.getElementById('add-type').value,
                Connection:document.getElementById('add-connection').value,
                Approval:document.getElementById('add-approval').value,
            },
          success: function(data) {
              console.log(data);
              containerWrap.style.visibility = 'hidden';
              containerWrap.style.opacity = '0';
              document.body.style.overflowY = 'auto';
          }
        });
    },
    compare: function() {
        var comparePanel = document.getElementById('compare-panel');
        // comparePanel.style.display = 'block';
        var nodes = document.getElementsByClassName('product');
        console.log(nodes[1]);
        for(var i=0; i<nodes.length; i++) {
            var nodesChildren = nodes[i].getElementsByTagName("span");

        }
    }
}
