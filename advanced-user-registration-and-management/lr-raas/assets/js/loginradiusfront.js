
//Initialize raas options
var raasoption = {};
raasoption.apikey = RaasDetails.api_key;
raasoption.appName = RaasDetails.sitename;
raasoption.emailVerificationUrl = RaasDetails.emailVerificationUrl;
raasoption.forgotPasswordUrl = RaasDetails.forgotPasswordUrl;
raasoption.inFormvalidationMessage = true;

if( '1' == RaasDetails.disable_email_verify ) {
    raasoption.DisabledEmailVerification = true;
}

if( '1' == RaasDetails.v2captcha ) {
    raasoption.V2RecaptchaSiteKey = RaasDetails.v2RecaptchaSiteKey;
    raasoption.V2Recaptcha = true;
}

raasoption.templatename = "loginradiuscustom_raas_tmpl";
raasoption.hashTemplate = true;

if ( "" != RaasDetails.storageVariable ) {
    sessionStorage.setItem( 'lr-user-uid', RaasDetails.storageVariable );
}

LoginRadiusRaaS.CustomInterface( ".interfacecontainerdiv", raasoption );


initializeRaasForms(raasoption);

jQuery(document).ready(function () {
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
    jQuery('#resetpassword-container,#login-container,#reontainer,#forgotpassword-container,#changepasswordbox,#setpasswordbox').hide();
}

function redirect(token) {
    if( 0 == jQuery('.lr_fade').length ) {
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

function handleResponse( isSuccess, message, container ) {

    if (message != null && message != "") {
        jQuery('.lr_fade').hide();

        var msg_color = 'success';
        if( false == isSuccess ) {
            msg_color = 'error';
        }

        jQuery(container + ' .messageinfo').html('<div class="' + msg_color + '">' + message + '</div>');
        jQuery(container + ' .messageinfo').show();
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
    
    if( 0 < jQuery('.lr-popup-container.lr-show').length ) {
        //jQuery('#popup-resetpassword-container,#popup-login-container,#popup-registration-container,#popup-forgotpassword-container,#popup-changepasswordbox,#popup-setpasswordbox');
        jQuery('.lr-popup-container.lr-show .lr-column').addClass('full-size');
        jQuery('.lr-popup-container.lr-show .lr-column + .lr-column').hide();
    } else {
        HideAllForms();
        jQuery('.hr-or-rule,.lr-link').hide();
    }
    
    ShowformbyId("social_registration_from");
};

//initialize registration form
function initializeRaasForms( raasoption ) {

    //initialize reset password form and handle email verifaction
    var vtype = $SL.util.getQueryParameterByName("vtype");

    if ( vtype != null && vtype != "" ) {
        LoginRadiusRaaS.init(raasoption, 'resetpassword', function (response) {
            handleResponse( true, 'Password reset successfully', '.lr-user-reg-container' );
            HideAllForms();
            ShowformbyId("login-container");
        }, function (response) {
            jQuery('.lr_fade').hide();
            handleResponse( false, response[0].description ? response[0].description : '', '.lr-user-reg-container' );
        }, "resetpassword-container");

        if ( vtype == "reset" ) {
            LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
                handleResponse( true, "");
                HideAllForms();
                ShowformbyId("resetpassword-container");
            }, function (response) {
                HideAllForms();
                ShowformbyId("login-container");
                // on failure this function will call ‘errors’ is an array of error with message.
                handleResponse( false, response[0].description ? response[0].description : '', '.lr-user-reg-container' );
            });
        } else {
            LoginRadiusRaaS.init(raasoption, 'emailverification', function (response) {
                // On Success this callback will call
                handleResponse( true, 'Your email has been verified successfully, Please Login', '.lr-user-reg-container' );
                HideAllForms();
                ShowformbyId("login-container");
            }, function (response) {
                // On failure this function will call ‘errors’ is an array of error with message.
                HideAllForms();
                ShowformbyId("login-container");
                handleResponse( false, response[0].description ? response[0].description : '', '.lr-user-reg-container' );
            });
        }
    }
}