var userController = {
    signUp: function() {
        var btnSignUp = document.getElementById('btn-reg'),
            email, password;

        btnSignUp.addEventListener('click', function() {
            var email = document.getElementById("email").value;
            var password = document.getElementById('password').value;
            console.log('Registering...');
            var data;

            $.ajax({
              type: "POST",
              url: "../VB-Automation/php/handle.php",
              data: { q:'signup', email:email, pass:password },
              dataType: "json",
              success: function(data) {
                  console.log(data.status.message);
                  document.body.style.overflowY = 'auto';
              }
            });

        })
    },
    signIn: function() {
            var data,
                email = document.getElementById("email1").value;
                password = document.getElementById('exampleInputPassword1').value;

            $.ajax({
              type: "POST",
              url: "../VB-Automation/php/handle.php",
              data: { q:'signin', email:email, pass:password },
              dataType: "json",
              success: function(data) {
                  var statusMessage = data.status.type;
                  console.log(data);
                  if (statusMessage === 'success') {
                      console.log(); (data);
                      cookiesController.set(email, password);
                      $('#addListItem').modal('hide');
                      $('#menu-profile')[0].style.display = 'inline-block';
                      $('#menu-login').hide();
                      $('#menu-admin')[0].style.display = 'none';
                  } else {
                      $('#menu-profile').show();
                      $('#menu-login').hide();
                      $('#menu-admin').show();
                  }
              }
            });
    },
    signOut: function() {
        cart.style.display = 'none';
        cookiesController.del();
        // location.reload();
    },
    addToCart: function(name) {
        testData.cart.push(name);
    },
    keepSession: function() {
        // var email, password, errorMessage;
        // var containerWrap = document.getElementById("sign-pane-wrap");
        // var btnSignUp = document.getElementById('user-btn-signUp');
        // var btnSignIn = document.getElementById('user-btn-signIn');
        // var btnNavSignIn = document.getElementById('btn-sign-in');
        // var btnNavSignUp = document.getElementById('btn-sign-up');
        // var btnNavSignOut = document.getElementById('btn-sign-out');
        // var cart = document.getElementById('cart');
        // var accessLevel = 1;
        // var value = document.cookie || " ";
        // var parts = value.split("; ");
        // var email = parts[0].split("=");
        // var password = parts[1].split("=");
        // console.log(email[1] + ' ' + password[1]);
        // $.ajax({
        //   type: "POST",
        //   url: "../VB-Automation/php/handle.php",
        //   data: { q:'signin', email:email[1], pass:password[1] },
        //   dataType: "json",
        //   success: function(data) {
        //       console.log(data);
        //       var statusMessage = data.status.type;
        //       if (statusMessage === 'success') {
        //           console.log(data);
        //           containerWrap.style.visibility = 'hidden';
        //           containerWrap.style.opacity = '0';
        //           document.body.style.overflowY = 'auto';
        //
        //           btnNavSignIn.style.display = 'none';
        //           btnNavSignUp.style.display = 'none';
        //           btnNavSignOut.style.display = 'inline';
        //           cart.style.display = 'inline';
        //
        //           if (data.signin[0].access > 1) {
        //               document.getElementById('admin-tools').style.display = 'inline';
        //           }
        //       } else {
        //           errorMessage = document.getElementById('signIn-error-massage');
        //           errorMessage.innerHTML = 'WRONG USERNAME AND PASSWORD COMBINATION!';
        //       }
        //   },
        //   error: function(data) {
        //       console.log(data);
        //   }
        // });
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

        console.log('fuck you');

        $.ajax({
            type: "POST",
            url: "../VB-Automation/php/handle.php",
            dataType: 'json',
            data: { q: "getUserInfo", user: email },
            success: function (data) {
                console.log(data);
                templates.load('profile')
                    .then(function (templateHtml) {
                        $('#page-content-wrapper').html(templateHtml(data.getUserInfo[0]));

                        $('#menu-profile').parent().addClass('active');
                        $('.dropdown-toggle').parent().removeClass('active');
                        $('ul.nav a[href=""]').parent().removeClass('active');
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
