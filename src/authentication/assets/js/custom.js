/* global LRObject */

function forgotpass_hook(redirecturl) {

    var forgotpassword_options = {};
    forgotpassword_options.container = "forgotpassword-container";
    forgotpassword_options.onSuccess = function (response) {
        // On Success
          jQuery('input').val('');
        ciamfunctions.message("Password change link sent to your email id", "#forgotpasswordmessage", "success");


        setTimeout(function () {
            LRObject.$hooks.register('endProcess', function (name) {

                jQuery("#ciam_loading_gif").hide();

            }, 10000);
        });
        window.location.href = redirecturl;


    };
    forgotpassword_options.onError = function (errors) {
        // On Errors
        jQuery('input').val('');
        ciamfunctions.message(errors[0].Description, "#forgotpasswordmessage", "error");

        jQuery("#ciam-forgotpassword-emailid").val("");
        //hide loading gif
        LRObject.$hooks.register('endProcess', function (name) {

            jQuery("#ciam_loading_gif").hide();

        });

    };
    forgotpassword_options.verificationUrl = window.location; //Change as per requirement

    LRObject.init("forgotPassword", forgotpassword_options);

}

function login_hook(url) {

    var login_options = {};
    login_options.onSuccess = function (response) {
        
        sessionStorage.access_token = response.access_token;
       
        ciamfunctions.redirect(response.access_token, 'token', url);

        //On Success

    };
    login_options.onError = function (errors) {
        //On Errors

        ciamfunctions.message(errors[0].Description, "#loginmessage", "error");
    };
    login_options.container = "login-container";
    LRObject.init("login", login_options);


}

function registration_hook(url) {

    var registration_options = {};
    registration_options.onSuccess = function (response) {
        //On Success
        jQuery('input').val('');
        jQuery('textarea').val('');
        jQuery('select').val('');
        jQuery('#loginradius-submit-register').val('Register');
        LRObject.$hooks.register('endProcess', function (name) { //hide loading gif
            jQuery("#ciam_loading_gif").hide();
        });


        ciamfunctions.message("Verification Link has been sent to your email address", "#registration_message", "success");


        setTimeout(function () {
            window.location.href = url;
        }, 2000);

    };
    registration_options.onError = function (errors) {
        //On Errors
        jQuery('input').val('');
        jQuery('textarea').val('');
        jQuery('select').val('');
        jQuery('#loginradius-submit-register').val('Register'); 
         
        ciamfunctions.message(errors[0].Description, "#registration_message", "error");

        LRObject.$hooks.register('endProcess', function (name) { //hide loading gif
            jQuery("#ciam_loading_gif").hide();
        });

    };
    registration_options.container = "registration-container";
    LRObject.init("registration", registration_options);

}

function emailverification(url) { 

    var verifyemail_options = {};
    verifyemail_options.onSuccess = function (response) {
        console.log(JSON.stringify(response.access_token));
       
        /* login upon email is active */
        if(response.access_token){
            
            ciamfunctions.redirect(response.access_token, 'token', url);
            return;
        }
        jQuery("#ciam_loading_gif").hide();
        ciamfunctions.message("Your Email has been verified", "#loginmessage", "success");
        setTimeout(function () {
            window.location.href = url;
        }, 3000);
    };
    verifyemail_options.onError = function (errors) {
        // On Errors

        ciamfunctions.message(errors[0].Description, "#loginmessage", "error");

            jQuery("#ciam_loading_gif").hide();
            setTimeout(function () {
            window.location.href = url;
        }, 3000);

    };

    var vtype = LRObject.util.getQueryParameterByName("vtype");
    var vtype = LRObject.util.getQueryParameterByName("vtoken");
    LRObject.init("verifyEmail", verifyemail_options);


}

function social(url) {

    var custom_interface_option = {};
    custom_interface_option.templateName = 'loginradiuscustom_tmpl';
    LRObject.customInterface(".interfacecontainerdiv", custom_interface_option);
    var sl_options = {};
    sl_options.onSuccess = function (response) {
        if(response.IsPosted == true){
           ciamfunctions.message("Please verify you email", "#loginmessage", "success"); 
           
         }
         
         ciamfunctions.redirect(response.access_token, 'token', url);
    };
    sl_options.onError = function (errors) {
        //On Errors
        ciamfunctions.message(errors[0].Description, "#loginmessage", "error");
    };
    sl_options.container = "sociallogin-container";

    LRObject.init('socialLogin', sl_options);
}

function changepasswordform() {
    LRObject.$hooks.register('afterFormRender', function (name) {
        if (name === "changepassword") {
            var formHTML = jQuery('form[name="loginradius-changepassword"]').html();
            jQuery('#changepassword-container').html('');
            jQuery('#changepassword-container').html(formHTML);
            jQuery('#changepassword-container').append('<span class="show-password"></span>')
            jQuery('#loginradius-submit-submit').hide();
        }
    });

    jQuery("#password th").html('');
    jQuery("#password td").html('');
    jQuery("#password th").html('<input id="passform" type="checkbox" name="passform" value="1"  checked/>Change Password');
    // checking checkbox is checked or not
    jQuery("#password td").html('<span class="password-input-wrapper show-password"><input style="display:hidden;" type="password" name="pass1" id="pass1" class="regular-text strong" value="" autocomplete="off" data-pw="Z4G%PbRnMl)krYm)vrCiNV!C" aria-describedby="pass-strength-result"></span><div id="changepassword-container"></div><span id="msg"></span>');
    var changepassword_options = {};

    changepassword_options.container = "changepassword-container";
    changepassword_options.onSuccess = function (response) {
        // On Success

    };
    changepassword_options.onError = function (response) {
        // On Error

        ciamfunctions.message("Something went wrong!", "#msg", "error");
    };



    LRObject.init("changePassword", changepassword_options);


    jQuery("#passform").click(function () {
        if (jQuery("#passform").prop('checked') == false) {

            jQuery("#password td").hide();
        }

        if (jQuery("#passform").prop('checked') == true) {

            jQuery("#password td").show();
        }
    });

    jQuery("#submit").on("click", function () {


        if (jQuery("#passform").prop("checked") == true) {

            if (jQuery("#loginradius-changepassword-oldpassword").val() != "" && jQuery("#ciam-changepassword-newpassword").val() != "" && jQuery("#loginradius-changepassword-newpassword").val() != "") {
                return true;
            } else {
                ciamfunctions.message("Password fields can not be left blank", "#msg", "error");

                return false;
            }

        } else if (jQuery("#passform").prop("checked") == false) {
            return true;
        }


    });
}

function accountlinking() {

    var la_options = {};

    la_options.container = "interfacecontainerdiv";

    la_options.templateName = 'loginradiuscustom_tmpl_link';
    la_options.onSuccess = function (response) {
        // On Success
        
        ciamfunctions.message("Account linked successfully", "#social-msg", "success");
        setTimeout(function(){
            location.reload();
        },1000);
        


    };
    la_options.onError = function (errors) {
        // On Errors

        ciamfunctions.message(errors[0].Description, "#social-msg", "error");

    };


    LRObject.init("linkAccount", la_options);
}

function accountunlinking() {

    var unlink_options = {};


    unlink_options.onSuccess = function (response) {
        // On                         Success
         ciamfunctions.message("Account unlinked successfully", "#social-msg", "success");
            setTimeout(function(){
                location.reload();
            },1000);
        


    };
    unlink_options.onError = function (errors) {
        // On                        Errors

        ciamfunctions.message(errors[0].Description, "#social-msg", "error");


    };



    LRObject.init("unLinkAccount", unlink_options);

}

function changepassword(redirecturl) {
    var resetpassword_options = {};
    resetpassword_options.container = "resetpassword-container";
    resetpassword_options.onSuccess = function (response) {
        // On Success
        ciamfunctions.message("Password change successfuly", "#resetpassword", "success");
        //hide loading gif
        setTimeout(function () {
            LRObject.$hooks.register('endProcess', function (name) {
                jQuery("#ciam_loading_gif").hide();
            }, 3000);
        });


        window.location.href = redirecturl;

    };
    resetpassword_options.onError = function (errors) {
        // On Errors inner html the error message......


        ciamfunctions.message(errors[0].Description, "#resetpassword", "error");

        jQuery("#ciam-resetpassword-password").val('');
        jQuery("#ciam-resetpassword-confirmpassword").val('');

        //hide loading gif
        LRObject.$hooks.register('endProcess', function (name) {
            jQuery("#ciam_loading_gif").hide();
        });

    };
    LRObject.init("resetPassword", resetpassword_options);
}

function loadingimg() { 
    LRObject.$hooks.register('startProcess', function (name) { 

        jQuery("#ciam_loading_gif").show();
    });

    LRObject.$hooks.register('afterFormRender', function (name) {

        if (name === "socialRegistration") {
            jQuery('#interfacecontainerdiv,#login-container').hide();
            jQuery("#ciam_loading_gif").hide();
        }



    });
}
jQuery(document).ready(function () {
    jQuery("#submit").on('click', function () {
        var newpassword = jQuery("#loginradius-changepassword-newpassword").val();
        var confirmpassword = jQuery("#loginradius-changepassword-confirmnewpassword").val();

        if (newpassword !== confirmpassword) {
            ciamfunctions.message("New Password must match with Confirm Password!", "#msg", "error");

            return false;
        }
    });
});


/* Anonymous Function for Message in Front section */

var ciamfunctions = {

    message: function (text, id, type) {
        if (type == "error") {
            jQuery(id).text(text).css("color", "#FF0000").show().fadeOut(5000);
        } else {
            jQuery(id).text(text).css('color', '#008000').show().fadeOut(5000);
        }
    },
    redirect: function (token, name, url) {
        var token_name = name ? name : 'token';
        var form = document.createElement('form');
        form.action = window.location.href;
        form.method = 'POST';
        var hiddenToken = document.createElement('input');
        hiddenToken.type = 'hidden';
        hiddenToken.value = token;
        hiddenToken.name = token_name;
        form.appendChild(hiddenToken);
        document.body.appendChild(form);
        form.submit();

    }

};

/* Admin setting js start */

jQuery(document).ready(function ($) {
    showAndHideCustomDiv(jQuery('input:radio[name="ciam_authentication_settings[after_login_redirect]"]:checked').val());
    jQuery('input:radio[name="ciam_authentication_settings[after_login_redirect]"]').change(function () {
        showAndHideCustomDiv(jQuery(this).val());
    });
  
});

function showAndHideCustomDiv(option) {
    if ('samepage' === option || 'homepage' === option || 'dashboard' === option || 'prevpage' === option) {
        jQuery('#customRedirectUrlField').hide();
    } else {
        jQuery('#customRedirectUrlField').show();
    }
}

jQuery(document).ready(function ($) {

    //tabs
    $('.ciam-options-tab-btns li').click(function () {
        var tab_id = $(this).attr('data-tab');

        $('.ciam-options-tab-btns li').removeClass('ciam-active');
        $('.ciam-tab-frame').removeClass('ciam-active');

        $(this).addClass('ciam-active');
        $("#" + tab_id).addClass('ciam-active');
    });

});
jQuery(function ($) {
    function hideAndShowElement(element, inputBoxName) {
        if (element.is(':checked')) {
            $(inputBoxName).hide();
        } else {
            $(inputBoxName).show();
        }
    }
    // Hide/Show Options if enabled/disabled on change
    $('#ciam-autopage').change(function () {
        hideAndShowElement($(this), '.ciam-custom-page-settings');
    });
    hideAndShowElement($('#ciam-autopage'), '.ciam-custom-page-settings');
});
jQuery(document).ready(function(){
    jQuery("#email").attr('readonly', 'readonly');
});



/* multiple email function */


function additionalemailform(useremail,lr_profile_email,count) {
    
     /* condition to hide remove if one email is exist */
   if (count == 1) {
       jQuery("#email").val(useremail);
        var content = '<a class="various ciam-email-button" href="#addemail">Add Email</a><div id="addemail" style="display:none;"><div id="addemail-container"></div></div><div id="remove" style="display:none;"><div class="removeemail-container"></div></div><br />';
    }else{
        var content = '<a href="#remove" class="wp_email various ciam-email-button">Remove</a>&nbsp;&nbsp;<a class="various ciam-email-button" href="#addemail">Add Email</a><div id="addemail" style="display:none;"><div id="addemail-container"></div></div><div id="remove" style="display:none;"><div class="removeemail-container"></div></div><br />';
    } 
    
    content += '';
     jQuery.each(lr_profile_email, function (index, email) {
        
        i = 0;
       
        if (email.Value !== useremail) {
            content += '<div><input type="email" value="' + email.Value + '" readonly="readonly" id="ciam_email_' + i + '" name="ciam_emai" class="ciam-email"><a href="#remove" class="removeemail various ciam-email-button">Remove</a></div>';
        }
        i++;
    });
   content +='<div id="ciam-email-msg"></div>'
    
    jQuery(".user-email-wrap td").append(content);
     /* add email sctipt */
    var addemail_options= {};
    addemail_options.container = "addemail-container";
    addemail_options.onSuccess = function(response) {
     document.cookie = "addemail=Please verify your email";
     parent.jQuery.fancybox.close();
     location.reload();
    
    };
    addemail_options.onError = function(response) {
   
    parent.jQuery.fancybox.close();
    ciamfunctions.message(response[0].Description, "#ciam-email-msg", "error");
    
    };



    LRObject.init("addEmail",addemail_options);

   /* remove email script */
   
   var removeemail_options= {};
    removeemail_options.container = "removeemail-container";
    removeemail_options.onSuccess = function(response) {
    // On Success
    document.cookie = "addemail=Email has been removed!";
     parent.jQuery.fancybox.close();
     location.reload();
    };
    removeemail_options.onError = function(response) {
    parent.jQuery.fancybox.close();
    ciamfunctions.message(response[0].Description, "#ciam-email-msg", "error");
    };

    LRObject.util.ready(function() {

    LRObject.init("removeEmail",removeemail_options);

    });

    /* end */
        jQuery(".various").fancybox({
                maxWidth	: 800,
                maxHeight	: 600,
                fitToView	: false,
                width		: '28%',
                height		: '8%',
                autoSize	: false,
                closeClick	: false,
                openEffect	: 'none',
                closeEffect	: 'none',
                helpers   : { 
                overlay : {closeClick: false,opacity: 0.8,css: {'background-color': ''}} 
               }
        });
      
      jQuery(".removeemail").each(function () {
            jQuery(this).click(function () {
               jQuery("#loginradius-removeemail-emailid").val(jQuery(this).parent('div').children('input').val());
               
            });
       });
       
       jQuery(".wp_email").on("click",function(){
          
            jQuery("#loginradius-removeemail-emailid").val(jQuery("#email").val());
       });
      
      
}

jQuery(document).ready(function(){
    document.cookie = "addemail=";
    document.cookie = "addemail=";
   
});



jQuery(document).ready(function(){
LRObject.$hooks.register('afterFormRender', function (actionName) {
        if (actionName == "registration") {
            show_birthdate_date_block();
        }
});
});

function show_birthdate_date_block() {
    var maxYear = new Date().getFullYear();
    var minYear = maxYear - 100;
    jQuery('body').on('focus', ".loginradius-birthdate", function () {
        jQuery('.loginradius-birthdate').datepicker({
            dateFormat: 'mm-dd-yy',
            maxDate: new Date(),
            minDate: "-100y",
            changeMonth: true,
            changeYear: true,
            yearRange: (minYear + ":" + maxYear)
        });
    });
}

 jQuery("#loginradius-submit-register").on('click',function(){ alert('hello');
        jQuery('form[name="loginradius-registration"]').reset();
    });