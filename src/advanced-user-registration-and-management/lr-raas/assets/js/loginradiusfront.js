
//Initialize raas options
var raasoption = {};

raasoption.apikey = RaasDetails.api_key;
raasoption.appName = RaasDetails.sitename;
raasoption.emailVerificationUrl = RaasDetails.emailVerificationUrl;
raasoption.forgotPasswordUrl = RaasDetails.forgotPasswordUrl;
raasoption.inFormvalidationMessage = true;



// Disable Email Verification
if ('1' == RaasDetails.disable_email_verify) {
    raasoption.DisabledEmailVerification = true;
}

// Optional Email Verification
if ('1' == RaasDetails.optional_email_verify) {
    raasoption.OptionalEmailVerification = true;
}

// Enable V2recaptcha V2 by default
raasoption.V2RecaptchaSiteKey = RaasDetails.v2RecaptchaSiteKey;
if ('' == raasoption.V2RecaptchaSiteKey || undefined == raasoption.V2RecaptchaSiteKey) {
    console.log('The V2recaptcha Site Key is required. Please obtain this key from https://www.google.com/recaptcha and enter the site key in the User Registration settings. Please also update your LoginRadius account with your V2recaptcha account info.')
}

// recaptcha V1 has been deprecated
// To enable V1 comment out below line
// recaptcha V1 not compatible with raas popup
raasoption.V2Recaptcha = true;

//// Enable login on Email Verification
if (RaasDetails.enable_email_verify_login == '1') {

    raasoption.enableLoginOnEmailVerification = true;
}

// Ask for email for unverified social login
if ('1' == RaasDetails.enable_ask_email_for_unverified) {
    raasoption.askEmailAlwaysForUnverified = true;
}

// Ask for password for social logins
if ('1' == RaasDetails.enable_ask_for_password) {
    raasoption.promptPasswordOnSocialLogin = true;
}

// Enable UserName feature
if ('1' == RaasDetails.enable_username) {
    raasoption.enableUserName = true;
}

// Email Verification Template
if ('' != RaasDetails.emailVerificationTemplate) {
    raasoption.emailVerificationTemplate = RaasDetails.emailVerificationTemplate;
}

// Forgot Password Template
if ('' != RaasDetails.forgotPasswordTemplate) {
    raasoption.emailVerificationTemplate = RaasDetails.forgotPasswordTemplate;
}

raasoption.templatename = "loginradiuscustom_raas_tmpl";
raasoption.hashTemplate = true;

if ("" != RaasDetails.storageVariable) {
    sessionStorage.setItem('lr-user-uid', RaasDetails.storageVariable);
}

LoginRadiusRaaS.CustomInterface(".interfacecontainerdiv", raasoption);




jQuery(document).ready(function () {
    initializeRaasForms();
    jQuery(".lrraas_signup").click(function () {
        ShowformbyId("reg_from");
    });
    jQuery(".lrraas_forgetpassowrd").click(function () {
        ShowformbyId("forgot_from");
    });
    jQuery(".lrraas_signin").click(function () {
        ShowformbyId("login-container");
    });
    jQuery(document).on('click', '.lr_fade', function () {
        jQuery('.lr_fade').hide();
    });

    jQuery('body').on('click', ".lrsociallogin", function () {
        handleResponse(true, "");
    });
    jQuery('body').on('focus', ".loginradius-raas-birthdate", function () {
        var maxYear = new Date().getFullYear();
        var minYear = maxYear - 100;
        jQuery('.loginradius-raas-birthdate').datepicker({
            dateFormat: 'mm-dd-yy',
            maxDate: new Date(),
            minDate: "-100y",
            changeMonth: true,
            changeYear: true,
            yearRange: (minYear + ":" + maxYear)

        });
        jQuery('.loginradius-raas-birthdate').datepicker(jQuery.datepicker.regional[ "es" ]);
    });
});

function ShowformbyId(currentform) {
    jQuery("#" + currentform).show();
}

function HideAllForms() {
    jQuery('#registration-container,#resetpassword-container,#login-container,#reontainer,#forgotpassword-container,#changepasswordbox,#setpasswordbox').hide();
}

function redirect(token) {
    if (0 == jQuery('.lr_fade').length) {
        jQuery('body').append(RaasDetails.spinner);
    }
    jQuery('.lr_fade').show();

    handleResponse(true, '');
    var form = document.createElement('form');
    form.action = window.location.href;
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

    if (message != null && message != "") {
        jQuery('.lr_fade').hide();

        var msg_color = 'success';
        if (false == isSuccess) {
            msg_color = 'error';
        }

        jQuery('.messageinfo').html('<div class="' + msg_color + '">' + message + '</div>');
        jQuery('.messageinfo').show();
        jQuery('body').animate({scrollTop: 0}, 200);
        if (isSuccess) {
            jQuery('form').each(function () {
                this.reset();
            });
        }
    } else {
        jQuery('.messageinfo').html("");
    }
}

//Function allows setting custom labels on all
//User Registration form labels
// Example Format
// "form-name": "New Label"
LoginRadiusRaaS.$hooks.setFormCustomLabel({
    // "firstname" : "First Name TEST",
    // "lastname" : "Last Name TEST",
    // "emailid" : "Email Address TEST",
    // "password" : "Password TEST",
    // "confirmpassword" : "Password Confirmation TEST",
    // "gender" : "Gender TEST",
    // //Custom Field
    // "cf_example" : "Custom Field Example TEST"
});

LoginRadiusRaaS.$hooks.socialLogin.onFormRender = function () {

    if (0 < jQuery('.lr-popup-container.lr-show').length) {
        //jQuery('#popup-resetpassword-container,#popup-login-container,#popup-registration-container,#popup-forgotpassword-container,#popup-changepasswordbox,#popup-setpasswordbox');
        jQuery('.lr-popup-container.lr-show .lr-column').addClass('full-size');
        jQuery('.lr-popup-container.lr-show .lr-column + .lr-column').hide();
    } else {
        HideAllForms();
        jQuery('.hr-or-rule,.lr-link').hide();
        
    }

    ShowformbyId("social_registration_from");
};
function passwordChange(){
    LoginRadiusRaaS.passwordHandleForms("setpasswordbox", "changepasswordbox", function (israas) {
        if (israas) {
            jQuery("#changepasswordbox").show();
        } else {
            if(raasoption.DisabledEmailVerification != true ){
               
                jQuery("#setpasswordbox").show();
            }
           // console.log(raasoption);
            
        }
    }, function () {
        document.forms["setpassword"].action = "";
        document.forms["setpassword"].submit();
    }, function () {
    }, function () {
        document.forms["changepassword"].action = "";
        document.forms["changepassword"].submit();
    }, function () {
    }, raasoption)
}

//initialize registration form
function initializeRaasForms() {

    //initialize reset password form and handle email verifaction
    var vtype = $SL.util.getQueryParameterByName("vtype");

    
    if (vtype != null && vtype != "") {
        LoginRadiusRaaS.init(raasoption, 'resetpassword', function (response) {
            handleResponse(true, 'Password reset successfully');
            HideAllForms();
            jQuery('#social-registration-container,.hr-or-rule').show();
            ShowformbyId("login-container");
        }, function (response) {
            jQuery('.lr_fade').hide();
            handleResponse(false, response[0].description ? response[0].description : '');
        }, "resetpassword-container");

        if (vtype == "reset") {

            LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {

                handleResponse(true, "");
                HideAllForms();
                jQuery('#social-registration-container').hide();
                jQuery('.hr-or-rule').hide();
                ShowformbyId("resetpassword-container");
            }, function (response) {
                
                HideAllForms();
                ShowformbyId("login-container");
                // on failure this function will call ‘errors’ is an array of error with message.
                handleResponse(false, response[0].description ? response[0].description : '');
            });
        } else {
            LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
                
                // On Success this callback will call
                if (response.access_token != null && response.access_token != "") {
                    
                    handleResponse(true, 'Your email has been verified successfully');
                    setTimeout(function ()
                                {
                    redirect(response.access_token);
                    }, 4000);
                } else {
                    handleResponse(true, 'Your email has been verified successfully, Please Login');
                    
                    setTimeout(function ()
                                {
                                   window.location.href = RaasDetails.current_page;

                               }, 4000)
                    HideAllForms();
                    ShowformbyId("login-container");
                }

            }, function (response) {
                
                // On failure this function will call ‘errors’ is an array of error with message.
                
                HideAllForms();
                ShowformbyId("login-container");
                
                handleResponse(false, response[0].description ? response[0].description : '');
            });
        }
    }
}