/* global templates */
/* global testData */
/* global cookiesController */
var userData = {};
var userController = {
    showModal: function () {
        templates.load('signPage')
                    .then(function (templateHtml) {
                        $('.modal-body').html(templateHtml);
                    });
    },
    signUp: function (email, password) {
        var q = 'addUserInfo',
            id = cookiesController.get('userId'),
            fname = document.getElementById('fname').value,
            lname = document.getElementById('lname').value,
            firm = document.getElementById('firm').value,
            country = document.getElementById('country').value,
            city = document.getElementById('city').value,
            address = document.getElementById('address').value,
            tel = document.getElementById('tel').value;

            console.log('Registering: \n' + id + '\n' + email + "\n" + password + "\n" + fname + "\n" + lname + "\n" + firm + "\n" + country + "\n" + city + "\n" + address + "\n" + tel);

            $.ajax({
                type: "POST",
                url: "../emag/php/handle.php",
                data: { q: q, id: id, email: email, fname: fname, lname: lname, firm: firm, country: country, city: city, address: address, phone: tel },
                dataType: "json",
                success: function (data) {
                    console.log(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
    },
    personalInfo: function (email, password) {
        console.log(email + password);
        userData.email = email;
        userData.password = password;

        cookiesController.set(email, password);

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            data: { q: 'signup', email: email, pass: password },
            dataType: "json",
            success: function (data) {
                userController.keepSession();
                templates.load('personal-info')
                    .then(function (templateHtml) {
                        userData.id = cookiesController.get('userId')
                        console.log(userData);
                        $('.modal-body').html(templateHtml(userData));
                    });
            }
        });
    },
    signIn: function() {
            var email = document.getElementById("email1").value,
            password = document.getElementById('exampleInputPassword1').value;

            $.ajax({
              type: "POST",
              url: "../emag/php/handle.php",
              data: { q:'signin', email:email, pass:password },
              dataType: "json",
              success: function(data) {
                  var statusMessage = data.status.type;
                  console.log(data);
                  if (statusMessage === 'success') {
                      console.log(); 
                      cookiesController.set(email, password);

                      userData.id = data.signin.id;
                      console.log('id ' + userData.id);
                      $('#addListItem').modal('hide');
                      $('#menu-profile')[0].style.display = 'inline-block';
                      $('#menu-login').hide();
                      $('#menu-admin')[0].style.display = 'none';
                      
                      if(data.signin[0].access === '3') {
                          console.log('Welcome Admin!');
                          $('#menu-admin')[0].style.display = 'inline-block';
                      }
                  }
              }
            });
    },
    signOut: function() {
        console.log('Signing out...');
        $('#menu-profile')[0].style.display = 'none';
        $('#menu-login').show();
        $('#menu-admin')[0].style.display = 'none';
        cookiesController.del('email');
        cookiesController.del('password');
        cookiesController.del('userId');
        window.location.href = "http://78.90.40.202/emag/";
    },
    addToCart: function(name) {
        testData.cart.push(name);
    },
    keepSession: function() {
        var email = cookiesController.get('email'),
        password = cookiesController.get('password');

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            data: { q: 'signin', email: email, pass: password },
            dataType: "json",
            success: function (data) {
                var statusMessage = data.status.type,
                    date = new Date(),
                    cookieUserId;
                    
                console.log(data);
                if (statusMessage === 'success') {
                    console.log(statusMessage);
                    
                    userData.id = data.signin[0].id;
                    
                    date.setTime(date.getTime()+(7*24*60*60*1000));
                    cookieUserId = "userId=" + data.signin[0].id + '; expires=' + date;
                    document.cookie = cookieUserId;
                    
                    
                    $('#addListItem').modal('hide');
                    $('#menu-profile')[0].style.display = 'inline-block';
                    $('#menu-login').hide();
                    $('#menu-admin')[0].style.display = 'none';
                    if(data.signin[0].access === '3') {
                        console.log('Welcome Admin!');
                        $('#menu-admin')[0].style.display = 'inline-block';
                    }
                }
            }
        });
    },
    showPanel: function() {
        $('.form').find('input, textarea').on('keyup blur focus', function (e) {

        var $this = $(this),
          label = $this.prev('label');

          if (e.type === 'keyup') {
        		if ($this.val() === '') {
              label.removeClass('active highlight');
            } else {
              label.addClass('active highlight');
            }
        } else if (e.type === 'blur') {
        	if( $this.val() === '' ) {
        		label.removeClass('active highlight');
        		} else {
        	    label.removeClass('highlight');
        		}
        } else if (e.type === 'focus') {

          if( $this.val() === '' ) {
        		label.removeClass('highlight');
        		}
          else if( $this.val() !== '' ) {
        	    label.addClass('highlight');
        		}
        }

        });

        $('.tab a').on('click', function (e) {

        e.preventDefault();

        $(this).parent().addClass('active');
        $(this).parent().siblings().removeClass('active');

        target = $(this).attr('href');

        $('.tab-content > div').not(target).hide();

        $(target).fadeIn(600);

        });

        userController.signUp();
    },
    showProfile: function() {
        var email = cookiesController.get('email');

        $.ajax({
            type: "POST",
            url: "../emag/php/handle.php",
            dataType: 'json',
            data: { q: "getUserInfo", user: email },
            success: function (data) {
                console.log(data);
                templates.load('profile')
                    .then(function (templateHtml) {
                        $('#page-content-wrapper').html(templateHtml(data.getUserInfo[0]));

                        $('#menu-partners').parent().removeClass('active');
                        $('#menu-contacts').parent().removeClass('active');
                        $('#menu-profile').parent().addClass('active');
                        $('.dropdown-toggle').parent().removeClass('active');
                        $('ul.nav a[href=""]').parent().removeClass('active');

                        data.getUserInfo[0].username = cookiesController.get('email');  
                        data.getUserInfo[0].password = cookiesController.get('password');

                        templates.load('profile-info')
                            .then(function (templateHtml) {
                                $('#profile-content').html(templateHtml(data.getUserInfo[0]));
                            });
                    });

            },
            error: function () {
                templates.load('profile')
                    .then(function (templateHtml) {
                        $('#page-content-wrapper').html(templateHtml(testData.user));

                        $('#menu-profile').parent().addClass('active');
                        $('.dropdown-toggle').parent().removeClass('active');
                        $('ul.nav a[href=""]').parent().removeClass('active');
                    });
            },
            beforeSend: function () {
                console.log('Getting Profile Information...');
            }
        });
    }
}
