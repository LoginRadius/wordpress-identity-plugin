/* global LRObject */
function forgotpass_hook(redirecturl) {
    var forgotpassword_options = {};
    forgotpassword_options.container = "forgotpassword-container";
    forgotpassword_options.onSuccess = function (response) {
        // On Success
        if (commonOptions.phoneLogin == true) {
            if (response.IsPosted == true && typeof (response.Data) == "undefined") {
                ciamfunctions.message("Password has been set successfully.", "#forgotpasswordmessage", "success");
                window.location.href = redirecturl;
            } else {
                ciamfunctions.message("OTP has been sent to your Phone No.", "#forgotpasswordmessage", "success");
                jQuery('input').val('');
                jQuery('#loginradius-submit-send').val('Send');
            }
        } else {
            ciamfunctions.message("Password change link sent to your email id", "#forgotpasswordmessage", "success");
            jQuery('input').val('');
            jQuery('#loginradius-submit-send').val('Send');
            window.location.href = redirecturl;
        }

    };
    forgotpassword_options.onError = function (errors) {
        // On Errors
        jQuery('input').val('');
        jQuery('#loginradius-submit-send').val('Send');
        ciamfunctions.message(errors[0].Description, "#forgotpasswordmessage", "error");
        jQuery("#ciam-forgotpassword-emailid").val("");
        //hide loading gif
        jQuery("#ciam_loading_gif").hide();
    };
    forgotpassword_options.verificationUrl = window.location; //Change as per requirement
    LRObject.init("forgotPassword", forgotpassword_options);
}
function optionalTwoFA() {
    var authentication_options = {};
    authentication_options.container = "authentication-container";
    authentication_options.onSuccess = function (response) {
        // On Success
        if (response.IsDeleted == true) {
            jQuery("#authentication-container").after("<span id='authdiv_success'>Two Factor Authenticaion is disabled</span>");
        } else {
            jQuery("#authentication-container").after("<span id='authdiv_success'>Two Factor Authenticaion is enabled</span>");
        }
        setTimeout(function () {
            location.reload();
        }, 2000);
    };
    authentication_options.onError = function (errors) {
        // On Errors
        jQuery("#authentication-container").after("<span id='authdiv_error'>" + errors[0].Message + "</span>");
        setTimeout(function () {
            location.reload();
        }, 2000);
    };
    LRObject.init("createTwoFactorAuthentication", authentication_options);
}
function updatephoneonprofile() {
    var updatephone_options = {};
    updatephone_options.container = "updatephone-container";
    updatephone_options.onSuccess = function (response) {
// On Success
        jQuery("#updatephone-container").after("<span id='authdiv_success'>Phone number updated successfully</span>");
        jQuery("#loginradius-submit-update").prop('disabled', true);
        setTimeout(function () {
            location.reload();
        }, 800);
    };
    updatephone_options.onError = function (response) {
// On Error
        jQuery("#updatephone-container").after("<span id='authdiv_error'>" + response[0].Message + "</span>");
        jQuery("#loginradius-submit-update").prop('disabled', true);
        setTimeout(function () {
            location.reload();
        }, 800);
    };
    LRObject.init("updatePhone", updatephone_options);
}
function login_hook(url) {
    var login_options = {};
    login_options.onSuccess = function (response) {
        if (response.IsPosted == true) {
            ciamfunctions.message("Verification Link has been sent", "#loginmessage", "success");
            setTimeout(function () {
                ciamfunctions.redirect(response.access_token, 'token', url);
            }, 500);
        } else {
            sessionStorage.access_token = response.access_token;
            LRObject.$hooks.register('endProcess', function (name) { /* calling this process to show the loading gif */
                jQuery("#ciam_loading_gif").show();
            });
            ciamfunctions.redirect(response.access_token, 'token', url);
        }
        //On Success
    };
    login_options.onError = function (errors) {
        //On Errors
        jQuery(window).scrollTop(0);
        ciamfunctions.message(errors[0].Description, "#loginmessage", "error");
    };
    login_options.container = "login-container";
    LRObject.init("login", login_options);
}
function oneclicksignin() {
    var options = {};
    options.onSuccess = function (response) {
//On Success
        if (response.access_token) {
            ciamfunctions.redirect(response.access_token, 'token', '');
            return;
        }
    };
    options.onError = function (errors) {
//On Error
        ciamfunctions.message(errors[0].Description, "#loginmessage", "error");
    };
    LRObject.init("instantLinkLogin", options);
}
function registration_hook(url, authenticationtype, phonelogin) {
    var registration_options = {};
    registration_options.onSuccess = function (response) {
        //On Success
        jQuery('input').val('');
        jQuery('textarea').val('');
        jQuery('select').val('');
        jQuery('#loginradius-submit-register').val('Register');
        jQuery('#loginradius-submit-verify').val('Verify');
        jQuery('#loginradius-button-resendotp').val('Resend OTP');
        jQuery("#ciam_loading_gif").hide();
        if (typeof (phonelogin) === "undefined" || phonelogin === "" || phonelogin == "email") {
            if (authenticationtype === "optional") {
                ciamfunctions.message("Link has been sent to your email address, it is optional to verify it you can directly get login with your credentials", "#registration_message", "success");
            } else if (authenticationtype === "disable") {
                ciamfunctions.message("Thanks for getting register", "#registration_message", "success");
            } else if (authenticationtype === "required") {
                ciamfunctions.message("Verification Link has been sent to your email address", "#registration_message", "success");
            }
        } else if (phonelogin === "phone") {
            ciamfunctions.message("Register Successfully.Use Phone Number to login", "#registration_message", "success");
        }
        jQuery(window).scrollTop(0);
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
        jQuery('#loginradius-submit-verify').val('Verify');
        jQuery('#loginradius-button-resendotp').val('Resend OTP');
        jQuery(window).scrollTop(0);
        console.log('errors[0].Description : ' + errors[0].Description);
        ciamfunctions.message(errors[0].Description, "#registration_message", "error");
        jQuery("#ciam_loading_gif").hide();

    };
    registration_options.container = "registration-container";
    LRObject.init("registration", registration_options);
}

function emailverification(url) {
    var verifyemail_options = {};
    verifyemail_options.onSuccess = function (response) {
        /* login upon email is active */
        if (response.access_token) {
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
    LRObject.init("verifyEmail", verifyemail_options);
}
function social(url) {
    var custom_interface_option = {};
    custom_interface_option.templateName = 'loginradiuscustom_tmpl';
    LRObject.customInterface(".interfacecontainerdiv", custom_interface_option);
    var sl_options = {};
    sl_options.onSuccess = function (response) {
        if (response.IsPosted == true) {
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
    /* add email sctipt */
    var changepassword_options = {};
    changepassword_options.container = "changepassword-container";
    changepassword_options.onSuccess = function (response) {
        jQuery(".popup-txt").after('<span id="password_msg_success">Password updated successfully</span>');
        jQuery("#loginradius-submit-submit").attr("disabled", "disabled");
        // On Success
        setTimeout(function () {
            location.reload();
        }, 5000);

    };
    changepassword_options.onError = function (response) {
        // On Error
        jQuery(".popup-txt").after('<span id="password_msg_error">' + response[0].Description + '</span>');
        jQuery("#loginradius-submit-submit").attr("disabled", "disabled");
        setTimeout(function () {
            location.reload();
        }, 5000);
    };
    LRObject.init("changePassword", changepassword_options);
    /* end */
    jQuery(document).ready(function () {
        jQuery("#open_password_popup").on('click', function () {
            jQuery('.popup-outer-password').fadeIn('slow');
        });

        jQuery("#close_password_popup").on('click', function () {
            jQuery('.popup-outer-password').fadeOut('slow');
        });
        /* closing the popup on send button click */
        jQuery("#loginradius-submit-send").on('click', function () {
            jQuery('.popup-outer-password').fadeOut('slow');
        });
    });
}

function generatebackupcodebutton(accesstoken) {
    jQuery("#password").after('<tr id="backupcode" class="user-pass1-wrap"><th><span>Generate Backup code</span></th><td><a href="javascript:void(0);" id="backupcode" class="ciam-password-button" onclick="generatebackupcode(\'' + accesstoken + '\')" >Generate</a>&nbsp;<span class="ciam-tooltip tip-top" data-title="Save these code for future use.These code will help you in login on lost of your mobile device.Every time you refresh the page you will generate the new code so please save change code on page reload for security reasons."><span class="dashicons dashicons-editor-help"></span></span></td></tr><tr id="codelist"></tr>');
}
function generatebackupcode(accesstoken) {
    jQuery("#ciam_loading_gif").show();
    var content = '<td colspan="2"><div style="width:100%;">';
    LRObject.api.resetBackupCode(accesstoken,
            function (response) {
                jQuery.each(response.BackUpCodes, function (index, data) {
                    content += '<div class="backupcode-div"><input class="backupcode-width" id="\'' + data + '\'" onClick="copyToClipboard(\'' + data + '\');this.select();" type="text" readonly value="' + data + '" /></div>';
                });
                content += "</div><div><span onclick='copybackupcode()' id='copybackupcode'>Copy</span> <span class='copyMessage'>Copied!</span></div></td>";
                jQuery("#codelist").html(content);
                jQuery("#ciam_loading_gif").hide();
            }, function (errors) {
        getbackupcode(accesstoken);
    });
}

function copyToClipboard(value) {
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
}

function getbackupcode(accesstoken) {
    LRObject.api.getBackupCode(accesstoken,
            function (response) {
                jQuery.each(response.BackUpCodes, function (index, data) {

                });

            }, function (errors) {

    });
}

function accountlinking() {
    var la_options = {};
    la_options.container = "interfacecontainerdiv";
    la_options.templateName = 'loginradiuscustom_tmpl_link';
    la_options.onSuccess = function (response) {
        // On Success
        ciamfunctions.message("Account linked successfully", "#social-msg", "success");
        setTimeout(function () {
            location.reload();
        }, 1000);
    };
    la_options.onError = function (errors) {
        // On Errors
        if (errors[0].Description === "The LoginRadius access token has expired, please request a new token from LoginRadius API.") {
            ciamfunctions.message('Your LoginRadius access token has expired.Please login again to enjoy account linking functionality.', "#social-msg", "error");
        }
        if (errors[0].Description !== "This Uid have only traditional unverified account" && errors[0].Description !== "The LoginRadius access token has expired, please request a new token from LoginRadius API.") {
            ciamfunctions.message(errors[0].Description, "#social-msg", "error");
        }
    };
    LRObject.init("linkAccount", la_options);
}

function accountunlinking() {
    var unlink_options = {};
    unlink_options.onSuccess = function (response) {
        // On                         Success
        ciamfunctions.message("Account unlinked successfully", "#social-msg", "success");
        setTimeout(function () {
            location.reload();
        }, 1000);
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
        ciamfunctions.message("Password change successfully.", "#resetpassword", "success");
        //hide loading gif
        jQuery("#ciam_loading_gif").hide();
        window.location.href = redirecturl;
    };
    resetpassword_options.onError = function (errors) {
        // On Errors inner html the error message......
        ciamfunctions.message(errors[0].Description, "#resetpassword", "error");
        jQuery("#ciam-resetpassword-password,#ciam-resetpassword-confirmpassword").val('');
        jQuery("#ciam_loading_gif").hide();
    };
    LRObject.init("resetPassword", resetpassword_options);
}

function loadingimg() {
    LRObject.$hooks.register('startProcess', function (name) {
        jQuery("#ciam_loading_gif").show();
    });
    LRObject.$hooks.register('afterFormRender', function (name) {
        if (name === "socialRegistration") {
            show_birthdate_date_block();
            jQuery('#registration-container,#interfacecontainerdiv,#login-container').hide();
            jQuery("#ciam_loading_gif").hide();
        }
        if (name == 'twofaotp' || name == 'otp' || name == "loginRequiredFieldsUpdate" || name == "showQRcode") {
            jQuery('#interfacecontainerdiv').hide();
            jQuery("#ciam_loading_gif").hide();
        }
        if (name === "registration") {
            show_birthdate_date_block();
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
        if (ciamautohidetime > 0) {
            if (type == "error") {
                jQuery(id).text(text).css("color", "#FF0000").show().fadeOut(ciamautohidetime * 1000);
            } else {
                jQuery(id).text(text).css('color', '#008000').show().fadeOut(ciamautohidetime * 1000);
            }
        } else {
            if (type == "error") {
                jQuery(id).text(text).css("color", "#FF0000").show();
            } else {
                jQuery(id).text(text).css('color', '#008000').show();
            }
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


function showAndHideCustomDiv(option) {
    if ('samepage' === option || 'homepage' === option || 'dashboard' === option || 'prevpage' === option) {
        jQuery('#customRedirectUrlField').hide();
    } else {
        jQuery('#customRedirectUrlField').show();
    }
}

jQuery(document).ready(function ($) {
    showAndHideCustomDiv(jQuery('input:radio[name="ciam_authentication_settings[after_login_redirect]"]:checked').val());
    jQuery('input:radio[name="ciam_authentication_settings[after_login_redirect]"]').change(function () {
        showAndHideCustomDiv(jQuery(this).val());
    });
    //tabs
    $('.ciam-options-tab-btns li').click(function () {
        var tab_id = $(this).attr('data-tab');

        $('.ciam-options-tab-btns li').removeClass('ciam-active');
        $('.ciam-tab-frame').removeClass('ciam-active');

        $(this).addClass('ciam-active');
        $("#" + tab_id).addClass('ciam-active');
    });
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
/* multiple email function */
function additionalemailform(useremail, lr_profile_email, count, img) {
    /* condition to hide remove button if one email is exist */
    if (count == 1) {
        jQuery("#email").val(useremail);
        var content = '<a id="open" class="open ciam-email-button" href="javascript:void(0);">Add Email</a><div class="popup-outer" style="display:none;"><span id="close"><img src="' + img + '" alt="close" /></span><div class="popup-inner"><span class="popup-txt"><h1><strong>Please Enter Email</strong></h1></span><div id="addemail-container"></div></div></div><div id="remove" style="display:none;"><div class="removeemail-container"></div></div><br />';
    } else {
        var content = '<a class="remove-popup wp_email open ciam-email-button ciam_email_0" href="javascript:void(0);">Remove</a><div class="remove-popup-outer" style="display:none;"><span class="close-removepopup"><img src="' + img + '" alt="close" /></span><div class="remove-popup-inner"><span class="popup-txt"><h1><strong>Are you sure to remove the mail?</strong></h1></span><span id="email_msg"></span><div class="removeemail-container"></div></div></div>&nbsp;&nbsp;<a id="open" class="open ciam-email-button" href="javascript:void(0);">Add</a><div class="popup-outer" style="display:none;"><span id="close"><img src="' + img + '" alt="close" /></span><div class="popup-inner"><span class="popup-txt"><h1><strong>Please Enter Email</strong></h1></span><div id="addemail-container"></div></div></div></div><br />';
    }

    content += '';
    /* loop to list other email except mail email */
    i = 1;

    jQuery.each(lr_profile_email, function (index, email) {

        if (email.Value !== useremail) {
            content += '<div><input type="email" value="' + email.Value + '" readonly="readonly" id="ciam_email_' + i + '" name="ciam_emai" class="ciam-email"><a class="remove-popup wp_email open ciam-email-button ciam_email_' + i + '" href="javascript:void(0);">Remove</a><div class="remove-popup-outer" style="display:none;"><span class="close-removepopup"><img src="' + img + '" alt="close" /></span><div class="remove-popup-inner"><span class="popup-txt"><h1><strong>Are you sure to remove the mail?</strong></h1></span><span id="email_msg"></span><div class="removeemail-container"></div></div></div></div>';
        }
        i++;
    });
    content += '<div id="ciam-email-msg"></div>'

    jQuery(".user-email-wrap td").append(content);
    /* add email sctipt */
    var addemail_options = {};
    addemail_options.container = "addemail-container";
    addemail_options.onSuccess = function (response) {
        document.cookie = "addemail=Please verify your email";
        // parent.jQuery.fancybox.close();
        location.reload();
    };
    addemail_options.onError = function (response) {
        //parent.jQuery.fancybox.close();
        ciamfunctions.message(response[0].Description, "#ciam-email-msg", "error");
    };
    LRObject.init("addEmail", addemail_options);
    /* remove email script */
    var removeemail_options = {};
    removeemail_options.container = "removeemail-container";
    removeemail_options.onSuccess = function (response) {
        // On Success
        document.cookie = "addemail=Email has been removed!";
        location.reload();
    };
    removeemail_options.onError = function (response) {
        ciamfunctions.message(response[0].Description, "#ciam-email-msg", "error");
    };
    LRObject.util.ready(function () {
        LRObject.init("removeEmail", removeemail_options);
    });
    /* end */
    jQuery(".removeemail").each(function () {
        jQuery(this).click(function () {
            jQuery("#loginradius-removeemail-emailid").val(jQuery(this).parent('div').children('input').val());
        });
    });
    jQuery(document).ready(function () {
        jQuery("#open").on('click', function () {
            jQuery('.popup-outer').fadeIn('slow');
        });
        jQuery("#close").on('click', function () {
            jQuery('.popup-outer').fadeOut('slow');
        });
        /* remove email popup code */
        jQuery(".remove-popup").on("click", function () {
            jQuery('.remove-popup-outer').fadeIn('slow');
        });
        jQuery(".close-removepopup").on("click", function () {
            jQuery('.remove-popup-outer').fadeOut('slow');
        });
        /* closing the popup on send button click */
        jQuery("#loginradius-submit-send").on('click', function () {
            jQuery('.popup-outer').fadeOut('slow');
        });
    });
    jQuery(".wp_email").on("click", function () {
        var emailid = jQuery(this).attr('class').split(' ')[4];
        if (emailid !== 'ciam_email_0') {
            jQuery("#loginradius-removeemail-emailid").val(jQuery("#" + emailid).val());
        } else {
            jQuery("#loginradius-removeemail-emailid").val(jQuery("#email").val());
        }
    });
}
jQuery(document).ready(function () {
    document.cookie = "addemail=";
    /* checking if captcha is enable or not on dom load */
    if (jQuery("#captcha").prop("checked")) {  /* check on DOM load*/
        jQuery("#showcaptcha").show();
    }
    jQuery("#captcha").on('click', function () {
        if (jQuery(this).prop("checked") === true) {
            jQuery("#showcaptcha").show();
        } else {
            jQuery("#showcaptcha").hide();
        }
    });
    jQuery('input[name^="ciam_authentication_settings[captcha]"]').each(function () {/* check on DOM load*/
        if (jQuery(this).prop("checked") == false) {
            jQuery("#showcaptcha").hide();
        } else {
            jQuery("#showcaptcha").show();
        }
    });
    /* toggling the google recaptha key on select box value */
    jQuery("#captchatype").on('change', function () {
        if (jQuery(this).val() === "") {
            jQuery("#recaptchasitekey").hide();
        } else {
            jQuery("#recaptchasitekey").show();
        }
    });

    /* 2 factor authentication */
    jQuery("#2fa").on('click', function () {
        if (jQuery(this).prop("checked") === true) {
            jQuery("#showauthdiv").show();
        } else {
            jQuery("#showauthdiv").hide();
        }
    });

    /* one click signin custom template */
    jQuery("#ciam-oneclicksignin").on('click', function () {
        if (jQuery(this).prop("checked") === true) {
            jQuery("#hideoneclickdiv").show();
        } else {
            jQuery("#hideoneclickdiv").hide();
        }
    });

    jQuery('input[name^="ciam_authentication_settings[2fa]"]').each(function () {/* check on DOM load*/
        if (jQuery(this).prop("checked") == false) {
            jQuery("#showauthdiv").hide();
        } else {
            jQuery("#showauthdiv").show();
        }
    });
    if (jQuery("#ciam-oneclicksignin").prop("checked") == true) {
        jQuery("#hideoneclickdiv").show();
    }
    jQuery('input[name^="ciam_authentication_settings[phonelogin]"]').each(function () {/* check on DOM load*/
        if (jQuery('input[name^="ciam_authentication_settings[phonelogin]"]:checked').val() == "email") {
            jQuery("#emailflowdiv").show();
            jQuery("#phonetemplatediv").hide();
        } else if(jQuery('input[name^="ciam_authentication_settings[phonelogin]"]:checked').val() == "phone") {
            jQuery("#emailflowdiv").hide();
            jQuery("#phonetemplatediv").show();
        }
    });
    jQuery('input[name^="ciam_authentication_settings[phonelogin]"]').on('click', function () {
        if (jQuery('input[name^="ciam_authentication_settings[phonelogin]"]:checked').val() == "email") {
            jQuery("#emailflowdiv").show();
            jQuery("#phonetemplatediv").hide();
        } else if(jQuery('input[name^="ciam_authentication_settings[phonelogin]"]:checked').val() == "phone") {
            jQuery("#emailflowdiv").hide();
            jQuery("#phonetemplatediv").show();
        }
    });

    jQuery("#password-setting").on('click', function () {
        if (jQuery(this).prop("checked") === true) {
            jQuery("#password-limit").show();
        } else {
            jQuery("#password-limit").hide();
        }
    });

    if (jQuery("#custom-oneclick-template").prop("checked")) {   /* check on DOM load*/
        jQuery("#hideoneclickdiv").show();
    }
    /* authentication type section code in admin */

    jQuery('input[name^="ciam_authentication_settings[authentication_flow_type]"]').each(function () { /* checking on DOM load */
        if (jQuery(this).prop('checked')) {
            if (jQuery(this).val() === "required") {
                jQuery("#requireflow").show();
                jQuery("#optionalflow").hide();
                jQuery("#customemailtemplates").show();
            } else if (jQuery(this).val() === "optional") {
                jQuery("#requireflow").hide();
                jQuery("#optionalflow").show();
                jQuery("#customemailtemplates").show();
            } else if (jQuery(this).val() === "disable") {
                jQuery("#requireflow,#optionalflow,#customemailtemplates").hide();
            }
        }
    });

    if (jQuery(".authentication_flow_type").on('click', function () {
        if (jQuery(this).val() === "required") {
            jQuery("#requireflow").show();
            jQuery("#optionalflow").hide();
            jQuery("#customemailtemplates").show();
            jQuery('#optionalflow').find('input[type=checkbox]:checked').removeAttr('checked');/* removing the selected checkbox */
        } else if (jQuery(this).val() === "optional") {
            jQuery("#requireflow").hide();
            jQuery("#customemailtemplates").show();
            jQuery('#requireflow').find('input[type=checkbox]:checked').removeAttr('checked');/* removing the selected checkbox */
            jQuery("#optionalflow").show();
        } else if (jQuery(this).val() === "disable") {
            jQuery("#requireflow,#optionalflow, #customemailtemplates").hide();
            jQuery('#requireflow').find('input[type=checkbox]:checked').removeAttr('checked');/* removing the selected checkbox */
            jQuery('#optionalflow').find('input[type=checkbox]:checked').removeAttr('checked');/* removing the selected checkbox */
        }
    }));

});

function copybackupcode() {
    var input = '';
    var code = '';

    jQuery('.backupcode-div').each(function () {
        input += jQuery(this).html() + "\n";

    });
    jQuery(input).each(function () {
        code += jQuery(this).val() + "\n";

    });

    var tempInput = document.createElement("textarea");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = code;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    jQuery('.copyMessage').css('color', '#008000').show();
    setTimeout(removeCodeCss, 5000);
}

function removeCodeCss() {
    jQuery('.code-list').find('span').removeAttr('style');
    jQuery('.copyMessage').hide();
}

jQuery(document).ready(function () {
    jQuery('#savebtn > #submit').on('click', function () {
        if (jQuery("#captcha").prop("checked") === true) {
            if (jQuery('#captchatype').val() === '' || jQuery('#recaptchasitekey').val() === '') {
                jQuery('#captchameassage').text('All Captcha fields are required!').css('color', '#FF0000');
                return false;
            }
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