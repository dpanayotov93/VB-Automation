var btnNavSignOut = document.getElementById('btn-sign-out');
// var containerWrap = document.getElementById("sign-pane")
var container = document.getElementById("sign-pane");
var cart = document.getElementById("cart");
var btnSignIn = document.getElementById("join-li");
var btnSignUp = document.getElementById("btn-sign-up");
var scrollToTopButton = document.getElementById("show-close-filters");
var btnSignUp = document.getElementById("btn-sign-up");
var btnNavSignOut = document.getElementById('btn-sign-out');
var containerWrap = document.getElementById("sign-pane-wrap")
var container = document.getElementById("sign-pane");
var cart = document.getElementById("cart");


// window.addEventListener('scroll', function(e){
//     var distanceY = window.pageYOffset || document.documentElement.scrollTop,
//         shrinkOn = 30,
//         shrinkButonOn = 150,
//         distanceTop = $("#products-list").offset().top;
//         header = document.querySelector("header");
//     var logo = document.querySelector(".cd-logo");
//     if (distanceY > shrinkOn) {
//         classie.add(header,"smaller");
//         $(document).ready(function() {
//             var searchInput = $('.dynatable-search')[0];
//             classie.add(logo,"smaller");
//             classie.add(searchInput,"smaller");
//         });
//     } else {
//         if (classie.has(header,"smaller")) {
//             var searchInput = $('.dynatable-search')[0];
//             classie.remove(header,"smaller");
//             $(document).ready(function() {
//                 classie.remove(logo,"smaller");
//                 classie.remove(searchInput,"smaller");
//             });
//         }
//     }
// });


// scrollToTopButton.addEventListener('click', function() {
//     $("html, body").animate({ scrollTop: 0 }, "slow");
// });

btnSignIn.addEventListener('click', function() {
    $('#sign-pane').show();
    templates.load('signIn')
        .then(function(templateHtml) {
            $('#sign-pane').html(templateHtml);

            var paneClose = document.getElementById('close-sign-pane');
            paneClose.addEventListener('click', function() {
                // containerWrap.style.opacity = '0';
                container.style.visibility = 'hidden';
                $('#sign-pane').hide();
            });

            // event.preventDefault();
            if($('header').hasClass('nav-is-visible')) $('.moves-out').removeClass('moves-out');
            $('header').toggleClass('nav-is-visible');
            $('.cd-main-nav').toggleClass('nav-is-visible');
            // $('.cd-main-content').toggleClass('nav-is-visible');
            $('#sign-pane')[0].style.visibility = "visible";

            userController.showPanel();
            userController.signIn();
        });
})

function searchShowHide() {
    var searchInput = $('.dynatable-search')[0];
    if ($('.dynatable-search').hasClass('expanded')) {
        classie.remove(searchInput, "expanded");
    } else {
        classie.add(searchInput,"expanded");
    }
}


// btnSignUp.addEventListener('click', function() {
//     containerWrap.style.visibility = 'visible';
//     containerWrap.style.opacity = '0.98';
//     container.style.opacity = '0.98';
//     // document.body.style.overflowY = "hidden";
//
//     templates.load('signUp')
//         .then(function(templateHtml) {
//             $('#sign-pane').html(templateHtml);
//             var paneClose = document.getElementById('close-sign-pane');
//             paneClose.addEventListener('click', function() {
//                 containerWrap.style.visibility = 'hidden';
//                 containerWrap.style.opacity = '0';
//                 container.style.opacity = '0';
//             });
//             userController.signUp();
//         });
// })

// btnNavSignOut.addEventListener('click', function() {
//     var btnNavSignIn = document.getElementById('btn-sign-in');
//     var btnNavSignUp = document.getElementById('btn-sign-up');
//     var btnNavSignOut = document.getElementById('btn-sign-out');
//
//     btnNavSignIn.style.display = 'inline';
//     btnNavSignUp.style.display = 'inline';
//     btnNavSignOut.style.display = 'none';
//
//     userController.signOut();
// })

// cart.addEventListener('click', function() {
//     containerWrap.style.visibility = 'visible';
//     containerWrap.style.opacity = '1';
//     container.style.opacity = '1';
//     document.body.style.overflowY = "hidden";
//
//     templates.load('cart')
//         .then(function(templateHtml) {
//             $('#sign-pane').html(templateHtml(testData.cart));
//
//         });
// })
//
// $(document).mouseup(function (e) {
//     var container = $("#sign-pane");
//
//     if (!container.is(e.target) // if the target of the click isn't the container...
//     && container.has(e.target).length === 0) // ... nor a descendant of the container
//     {
//         container.style.visibility = 'hidden';
//         container.hide();
//     }
// });
