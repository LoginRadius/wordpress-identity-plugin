/*
 Author: LoginRadius Team
 Author URI: http://www.LoginRadius.com
 */

if (phpvar.providers != "Providers Not Configured") {
    $LRIC.util.ready(function () {
        var options = {};
        options.apikey = phpvar.apiKey;
        options.appname = phpvar.siteName;
        options.templatename = "commenting_login_interface";
        options.providers = phpvar.providers;
        $LRIC.renderInterface("login_interface", options);
    });
    $LRIC.util.ready(function () {
        var options = {};
        options.apikey = phpvar.apiKey;
        options.appname = phpvar.siteName;
        options.templatename = "commenting_required_interface";
        options.providers = phpvar.providers;
        $LRIC.renderInterface("required_interface", options);
    });
    $LRIC.util.ready(function () {
        var options = {};
        options.apikey = phpvar.apiKey;
        options.appname = phpvar.siteName;
        options.templatename = "commenting_sharing_interface";
        options.providers = ['Facebook', 'Twitter', 'LinkedIn'];
        $LRIC.renderInterface("lr-share-container", options);
    });
} else {
    // Handle provider array error
    console.log("Providers Not Configured");
}