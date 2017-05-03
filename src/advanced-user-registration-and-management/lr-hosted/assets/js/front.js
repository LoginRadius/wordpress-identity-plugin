function getLoginRadiusCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return "";
}
function setLoginRadiusCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires+"; path=/";
}

function redirect(token) {
                        handleResponse(true, '');
                        var form = document.createElement('form');
                        var redirectUrl = window.location.href;

                        if (getLoginRadiusCookie('lrAuthRedirect') != '') {
                            if (redirectUrl.indexOf('?') == -1) {
                                redirectUrl += "?";
                            } else {
                                redirectUrl += "&";
                            }
                            redirectUrl += "redirect_to=" + getLoginRadiusCookie('lrAuthRedirect');
                        }
                        form.action = redirectUrl;
                        form.method = 'POST';
                        var hiddenToken = document.createElement('input');
                        hiddenToken.type = 'hidden';
                        hiddenToken.value = token;
                        hiddenToken.name = 'token';
                        form.appendChild(hiddenToken);
                        document.body.appendChild(form);
                        form.submit();
                    }
                    function handleResponse(isSuccess, message) {
                        jQuery('.lr_fade').show();
                        if (message != null && message != "") {
                            jQuery('.lr_fade').hide();
                            jQuery('#messageinfo').html(message);
                            jQuery('#messageinfo').show();
                            jQuery('body').animate({scrollTop: 0}, 200);
                            if (isSuccess) {
                                jQuery('form').each(function () {
                                    this.reset();
                                });
                            }
                        } else {
                            jQuery('#messageinfo').html("");
                        }
                    }