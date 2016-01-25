var userController = {
    signUp: function() {
        var containerWrap = document.getElementById("sign-pane"),
            btnSignUp = document.getElementById('user-btn-get'),
            email, password;

        btnSignUp.addEventListener('click', function() {
            var email = document.getElementById("user-email").value;
            var password = document.getElementById('user-password').value;
            console.log('Registering...');
            var data;

            $.ajax({
              type: "POST",
              url: "../emag/php/handle.php",
              data: { q:'signup', email:email, pass:password },
              dataType: "json",
              success: function(data) {
                  console.log(data.status.message);
                  containerWrap.hide();
                  document.body.style.overflowY = 'auto';
              }
            });

        })
    },
    signIn: function() {
        var email, password,
            submitBtn  = document.getElementById("submit-btn"),
            joinLi  = document.getElementById("join-li"),
            containerWrap = document.getElementById("sign-pane"),
            cart = document.getElementById('cart'),
            accessLevel = 1;

        submitBtn.addEventListener('click', function() {
            email = document.getElementById("username-input").value;
            password = document.getElementById('password-input').value;

            console.log('logging...');

            var data;

            $.ajax({
              type: "POST",
              url: "../emag/php/handle.php",
              data: { q:'signin', email:email, pass:password },
              dataType: "json",
              success: function(data) {
                  var statusMessage = data.status.type;
                  alert(statusMessage);
                  if (statusMessage === 'success') {
                      alert(data);
                      containerWrap.style.visibility = 'hidden';
                      containerWrap.style.opacity = '0';
                      document.body.style.overflowY = 'auto';

                      $('#join-li')[0].style.display = 'none';

                    //   cookiesController.set(email, password);
                      //
                    //   if (data.signin[0].access > 1) {
                    //       document.getElementById('admin-tools').style.display = 'inline';
                    //   }
                  } else {
                    //   errorMessage = document.getElementById('signIn-error-massage');
                    //   errorMessage.innerHTML = 'WRONG USERNAME AND PASSWORD COMBINATION!';
                  }
              }
            });

        })
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
        //   url: "../emag/php/handle.php",
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
    }
}
