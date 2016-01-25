var cookiesController = {
    set: function(email, password) {
        var date = new Date(),
            cookieEmail, cookiePassword;

        date.setTime(date.getTime()+(7*24*60*60*1000));
        cookieEmail = "email=" + email + '; expires=' + date;
        cookiePassword = "password=" + password + '; expires=' + date;

        document.cookie = cookieEmail;
        document.cookie = cookiePassword;
    },
    del: function() {
        document.cookie = 'email' + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        document.cookie = 'password' + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
}
