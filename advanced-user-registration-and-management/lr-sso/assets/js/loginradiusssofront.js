
jQuery(document).ready(function ($) {
    
    LoginRadiusSSO.init(lrSsoOptions.sitename);
    if(lrSsoOptions.raasenable){
        if (!LoginRadiusRaaS.loginradiushtml5passToken) {
            LoginRadiusRaaS.loginradiushtml5passToken = function (token) {
                if (token) {
                    window.location = lrSsoOptions.loginurl;
                }
            }
        }
    }
    
    if(lrSsoOptions.islogin && lrSsoOptions.logouturl != false){
        LoginRadiusSSO.isNotLoginThenLogout(function () {
            window.location.href = lrSsoOptions.logouturl;
        });
    }
    
    if(!lrSsoOptions.istoken && !lrSsoOptions.islogin){
        LoginRadiusSSO.login(window.location.href);
    }
    
    var href = $('#wp-admin-bar-logout a').attr('href');
    $('#wp-admin-bar-logout a').css({"cursor": "pointer"});
    $('#wp-admin-bar-logout a').removeAttr('href');

    $('#wp-admin-bar-logout').click(function (e) {
        e.preventDefault();
        ssoLogout(href);
    });

    if ($('a[href*="logout"]').length > 0) {
        href = $('a[href*="logout"]').attr('href');
        $('a[href*="logout"]').attr('data-action', 'lr-sso-logout');
        $('a[href*="logout"]').css({"cursor": "pointer"});
        $('a[href*="logout"]').removeAttr('href');
        $('a[data-action="lr-sso-logout"]').click(function () {
            ssoLogout(href);
        });
    }
    return false;
});

function ssoLogout(href) {
    LoginRadiusSSO.ssoLogout(href);
}