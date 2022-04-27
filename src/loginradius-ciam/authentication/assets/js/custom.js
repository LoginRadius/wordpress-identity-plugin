/* global LRObject */
var form_name = "";
var phoneid = "";


function forgotpass_hook(redirecturl) {
    var forgotpassword_options = {};
    forgotpassword_options.container = "forgotpassword-container";
    forgotpassword_options.onSuccess = function (response) {
        // On Success
         var forgot_phone_option = setInterval(function () {
                if(typeof LRObject.options !== 'undefined')
                {
                    clearInterval(forgot_phone_option);
        if(response.IsPosted == true && typeof response.Data !== 'undefined' && response.Data!==null)
        {
            ciamfunctions.message(commonOptions.messageList.FORGOT_PASSWORD_PHONE_MSG, "#forgotpasswordmessage", "success");  
        }else if(LRObject.options.otpEmailVerification==true && typeof response.Data==='undefined')
        {
            jQuery('#loginradius-button-resendotp').blur();
            ciamfunctions.message(commonOptions.messageList.FORGOT_PHONE_OTP_VERIFICATION_MSG, "#forgotpasswordmessage", "success");  
        }
        else if(form_name == 'resetPassword')
        {
            ciamfunctions.message(commonOptions.messageList.FORGOT_PASSWORD_SUCCESS_MSG, "#forgotpasswordmessage", "success");
             window.setTimeout(function () {
                                window.location.href = redirecturl;
                            }, 3000);
        }
        else
        {
            ciamfunctions.message(commonOptions.messageList.FORGOT_PASSWORD_MSG, "#forgotpasswordmessage", "success");            
        }
        jQuery('input[type="text"]').val('');
        jQuery('input[type="password"]').val('');
        }
                
 }, 1);
        

    };
    forgotpassword_options.onError = function (errors) {
        // On Errors
        jQuery('input[type="text"]').val('');
        jQuery('input[type="password"]').val('');
        ciamfunctions.message(errors[0].Description, "#forgotpasswordmessage", "error");
        jQuery("#ciam-forgotpassword-emailid").val("");
        //hide loading gif
        jQuery("#ciam_loading_gif").hide();
    };
    forgotpassword_options.verificationUrl = window.location; //Change as per requirement
    var lrObjectInterval2 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval2);
                    LRObject.$hooks.register('startProcess', function (name) {
            if(name == 'resetPassword')
            {
                form_name = name;
            }
        jQuery("#ciam_loading_gif").show();
    });
      LRObject.init("forgotPassword", forgotpassword_options);
     }
 }, 1);
}
function optionalTwoFA() {
    var authentication_options = {};
    authentication_options.container = "authentication-container";
    authentication_options.onSuccess = function (response) {
        // On Success
        if(response.Sid)
        {
            jQuery('#authdiv_success').remove();                
            jQuery('#authdiv_error').remove();
            jQuery("#authentication-container").after("<span id='authdiv_success'></span>");
            ciamfunctions.message(commonOptions.messageList.TWO_FA_MSG, "#authdiv_success", "success");
        }
        if (response.IsDeleted == true) {
            jQuery("#authentication-container").after("<span id='authdiv_success'>"+commonOptions.messageList.TWO_FA_DISABLED_MSG+"</span>");
            
            setTimeout(function () {
             location.reload();
            }, 2000);
        } else if(typeof response.Uid != 'undefined'){
            jQuery('#authdiv_success').remove();   
            jQuery("#authentication-container").after("<span id='authdiv_success'>"+commonOptions.messageList.TWO_FA_ENABLED_MSG+"</span>");
             setTimeout(function () {
             location.reload();
             }, 2000);
        }
    };
    authentication_options.onError = function (errors) {
        // On Errors
        jQuery('#authdiv_success').remove();                
        jQuery('#authdiv_error').remove();
        jQuery("#authentication-container").after("<span id='authdiv_error'>" + errors[0].Description + "</span>");
        setTimeout(function () {
            location.reload();
        }, 2000);
    };
    var lrObjectphoneoptions = setInterval(function () {
                if(typeof LRObject.options !== 'undefined' && LRObject.options != '')
                {
                    clearInterval(lrObjectphoneoptions);
                    if(LRObject.options.twoFactorAuthentication === true || LRObject.options.optionalTwoFactorAuthentication === true)
                    { 
                        jQuery('.profiletwofactorauthentication').show();
                        LRObject.init("createTwoFactorAuthentication", authentication_options);
                     }
      }
       }, 1);       
}

function updatephoneonprofile() {
    var updatephone_options = {};
    updatephone_options.container = "updatephone-container";
    updatephone_options.onSuccess = function (response) {
// On Success
        if(typeof response.Data !== 'undefined')
        {
            jQuery('#authphonediv_success').remove();
            jQuery('#authdiv_success').remove();                
            jQuery('#authdiv_error').remove();
            jQuery("#updatephone-container").after("<span id='authphonediv_success'></span>");
             ciamfunctions.message(commonOptions.messageList.UPDATE_PHONE_MSG, "#authphonediv_success", "success");
             
        }
        else if(response.IsPosted == true)
        {
                jQuery('#authphonediv_success').remove();
            jQuery("#updatephone-container").after("<span id='authphonediv_success'>"+commonOptions.messageList.UPDATE_PHONE_SUCCESS_MSG+"</span>");
        jQuery("#loginradius-submit-update").prop('disabled', true);
        setTimeout(function () {
            location.reload();
        }, 800);
        }
      
    };
    updatephone_options.onError = function (response) {
// On Error
    jQuery('#authphonediv_success').remove();
    jQuery('#authdiv_success').remove();
    jQuery("#updatephone-container").after("<span id='authdiv_error'>" + response[0].Message + "</span>");
        jQuery("#loginradius-submit-update").prop('disabled', true);
        setTimeout(function () {
            location.reload();
        }, 800);
    };
    var lrObjectInterval4 = setInterval(function () {
                if(typeof LRObject.options !== 'undefined' && LRObject.options != '')
                {
                    clearInterval(lrObjectInterval4);
                   if(LRObject.options.phoneLogin === true)
                    {   
            jQuery('.profilephoneuupdate').show();
            jQuery('.phoneid_table').show();
          LRObject.init("updatePhone", updatephone_options);
          if(phoneid == '--')
            {
                jQuery('#updatephone-container #loginradius-submit-update').val('Add');
            }
      }
      }
       }, 1);
}

function login_hook(url) {
    var login_options = {};
    login_options.onSuccess = function (response) {
        if (response.IsPosted == true && typeof response.access_token !== 'undefined') {
             if (jQuery('#loginradius-login-username').length !== 0) {
                 ciamfunctions.message(commonOptions.messageList.LOGIN_BY_USERNAME_MSG, "#loginmessage", "success");
            } else if(jQuery('#loginradius-login-emailid').length !== 0) {
                ciamfunctions.message(commonOptions.messageList.LOGIN_BY_EMAIL_MSG, "#loginmessage", "success");
            }
            setTimeout(function () {
                ciamfunctions.redirect(response.access_token, 'token', url);
            }, 500);
        } 
        else if( typeof response.Data !== 'undefined' && typeof response.Data.Sid !== 'undefined')
        {            
            ciamfunctions.message(commonOptions.messageList.LOGIN_BY_PHONE_MSG, "#loginmessage", "success");
        } else if( typeof response.Data !== 'undefined')
        {         
            ciamfunctions.message(commonOptions.messageList.EMAIL_VERIFICATION_SUCCESS_MSG, "#loginmessage", "success");
            setTimeout(function () {
                window.location.href = url;
            }, 3000);
        }else if(response.IsPosted == true) {
            ciamfunctions.message(commonOptions.messageList.LOGIN_BY_EMAIL_MSG, "#loginmessage", "success");
        }
        else if(response.access_token) {
            sessionStorage.access_token = response.access_token;
             var lrObjectInterval5 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval5);
                    LRObject.$hooks.register('endProcess', function (name) { /* calling this process to show the loading gif */
                        jQuery("#ciam_loading_gif").show();
                    });
                }
            }, 1);
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
    var lrObjectInterval6 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval6);
         LRObject.init("login", login_options);
     }
 }, 1);
}

function profileUpdateContainer() {
    var profileeditor_options = {};
    profileeditor_options.container = "profileeditor-container";
    profileeditor_options.onSuccess = function(response) {
    // On Success
    jQuery('#authProfilediv_success').remove();               
    jQuery('#authdiv_error').remove();
    jQuery("#profileeditor-container").after("<span id='authProfilediv_success'></span>");
             ciamfunctions.message(commonOptions.messageList.UPDATE_USER_PROFILE, "#authProfilediv_success", "success");
    };
    profileeditor_options.onError = function(errors) {
    // On Error
    jQuery('#authProfilediv_success').remove();               
    jQuery('#authdiv_error').remove();
    jQuery("#profileeditor-container").after("<span id='authdiv_error'>" + errors[0].Description + "</span>");
    };
    jQuery(".userProfileUpdate").show();
    var lrObjectInterval7 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval7);
                    LRObject.init("profileEditor",profileeditor_options);
     }
 }, 1);
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
    var lrObjectInterval7 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval7);
    LRObject.init("instantLinkLogin", options);
     }
 }, 1);
}

function registration_hook(url) {    
    var registration_options = {};
    registration_options.onSuccess = function (response) {
        //On Success
        jQuery('input').val('');
        jQuery('textarea').val('');
        jQuery('select').val('');
        jQuery('#loginradius-submit-register').val('Register');
        jQuery('#loginradius-submit-verify').val('Verify');
        jQuery('#loginradius-button-resendotp').val('Resend OTP');
        jQuery('#loginradius-otp-skip').val('Skip');
        jQuery("#ciam_loading_gif").hide();
        var lrObjectInterval8 = setInterval(function () {
                if(typeof LRObject.options !== 'undefined')
                {
                    clearInterval(lrObjectInterval8);
                    if(typeof LRObject.options.optionalEmailVerification != 'undefined'){
                         var optionalemailverification = LRObject.options.optionalEmailVerification;
                    }
                    else{
                         var optionalemailverification = '';
                    }
                    if(typeof LRObject.options.disabledEmailVerification != 'undefined'){
                         var disableemailverification = LRObject.options.disabledEmailVerification;
                    }
                    else{
                        var disableemailverification = '';
                    }
                    if (response.IsPosted && typeof response.Data === 'undefined') {
                    if ((typeof (optionalemailverification) == 'undefined' || optionalemailverification !== true) && (typeof (disableemailverification) == 'undefined' || disableemailverification !== true)) {
                       ciamfunctions.message(commonOptions.messageList.REGISTRATION_VERIFICATION_MSG, "#registration_message", "success");
                    }
                    setTimeout(function () {
                       window.location.href = url;
                    }, 2000);
                    } else if (response.access_token) {
                       ciamfunctions.redirect(response.access_token, 'token', url);
                    }
                    else if(response.IsPosted && typeof response.Data !== 'undefined' && response.Data!==null && typeof response.Data.Sid !== 'undefined')
                    {
                        jQuery('#loginradius-button-resendotp').blur();
                        ciamfunctions.message(commonOptions.messageList.REGISTRATION_OTP_MSG, "#registration_message", "success");
                    }
                   else if(LRObject.options.otpEmailVerification==true && response.Data==null) {
                    jQuery('#loginradius-button-resendotp').blur();
                    ciamfunctions.message(commonOptions.messageList.REGISTRATION_OTP_VERIFICATION_MSG, "#registration_message", "success");
                   }
                   else {
                   ciamfunctions.message(commonOptions.messageList.REGISTRATION_SUCCESS_MSG, "#registration_message", "success");
                   setTimeout(function () {
                    window.location.href = url;
                   }, 2000);
               }              
                
        jQuery(window).scrollTop(0);
           }
        }, 1);
    };
    registration_options.onError = function (errors) {
        //On Errors
        jQuery('input').val('');
        jQuery('textarea').val('');
        jQuery('select').val('');
        jQuery('#loginradius-submit-register').val('Register');
        jQuery('#loginradius-submit-verify').val('Verify');
        jQuery('#loginradius-otp-skip').val('Skip');
        jQuery('#loginradius-button-resendotp').val('Resend OTP');
        jQuery(window).scrollTop(0);
        if(commonOptions.existPhoneNumber == true){
            if(errors[0].ErrorCode == '1096'){        
                jQuery("#validation-loginradius-registration-phoneid").text(errors[0].Description);
            }else{
                ciamfunctions.message(errors[0].Description, "#registration_message", "error");
            }
        }else{
            ciamfunctions.message(errors[0].Description, "#registration_message", "error");
        }
        jQuery("#ciam_loading_gif").hide();
    };
    registration_options.container = "registration-container";
   var lrObjectInterval9 = setInterval(function () {
        if(typeof LRObject !== 'undefined')
        {
            clearInterval(lrObjectInterval9);
            LRObject.init("registration", registration_options);
        }
 }, 1);
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
        ciamfunctions.message(commonOptions.messageList.EMAIL_VERIFICATION_SUCCESS_MSG, "#loginmessage", "success");
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
   var lrObjectInterval10 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval10);
       LRObject.init("verifyEmail", verifyemail_options);
     }
 }, 1);
}

function social(url) {
    var custom_interface_option = {};
    custom_interface_option.templateName = 'loginradiuscustom_tmpl';
    var lrObjectInterval11 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval11);
       LRObject.customInterface(".interfacecontainerdiv", custom_interface_option);
     }
 }, 1);
     
    var sl_options = {};
    sl_options.onSuccess = function (response) {
         if (response.IsPosted == true && typeof response.Data.AccountSid === 'undefined') {
            ciamfunctions.message(commonOptions.messageList.SOCIAL_LOGIN_MSG, "#loginmessage", "success");
            setTimeout(function () {
            location.reload();
        }, 5000);
        }
       else if(response.access_token)
       {
       ciamfunctions.redirect(response.access_token, 'token', url);
   }
        else if( typeof response.Data !== 'undefined' && typeof response.Data.Sid !== 'undefined' )
       {
          ciamfunctions.message("An OTP has been sent.", "#loginmessage", "success");
       }
    };
    sl_options.onError = function (errors) {
        //On Errors
        ciamfunctions.message(errors[0].Description, "#loginmessage", "error");
    };
    sl_options.container = "sociallogin-container";
    var lrObjectInterval12 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval12);
      LRObject.init('socialLogin', sl_options);
  }
    }, 1);
}
function changepasswordform() {
    /* add email sctipt */
    var changepassword_options = {};
    changepassword_options.container = "changepassword-container";
    changepassword_options.onSuccess = function (response) {
        jQuery(".popup-txt").html('<span id="password_msg_success">'+commonOptions.messageList.CHANGE_PASSWORD_SUCCESS_MSG+'</span>');
        jQuery("#loginradius-submit-submit").attr("disabled", "disabled");
        // On Success
        setTimeout(function () {
            location.reload();
        }, 3000);

    };
    changepassword_options.onError = function (response) {
        // On Error
        jQuery(".popup-txt").html('<span id="password_msg_error">' + response[0].Description + '</span>');     
    };
   var lrObjectInterval13 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval13);
     LRObject.init("changePassword", changepassword_options);
   }
   }, 1);
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
 function setpasswordform()
 {
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
 
    /* end */
    jQuery(document).ready(function () {
       
   
     if (jQuery("#ciam-oneclicksignin").prop("checked") == true) {
        jQuery("#hideoneclickdiv").show();
    }
     if (jQuery("#custom-oneclick-template").prop("checked")) {   /* check on DOM load*/
        jQuery("#hideoneclickdiv").show();
    }
    /* one click signin custom template */
    jQuery("#ciam-oneclicksignin").on('click', function () {
        if (jQuery(this).prop("checked") === true) {
            jQuery("#hideoneclickdiv").show();
        } else {
            jQuery("#hideoneclickdiv").hide();
        }
    });
     if (jQuery("#ciam-otpsignin").prop("checked") == true) {
        jQuery("#hideotpdiv").show();
    }
     if (jQuery("#custom-otp-template").prop("checked")) {   /* check on DOM load*/
        jQuery("#hideotpdiv").show();
    }
    /* otp signin custom template */
    jQuery("#ciam-otpsignin").on('click', function () {
        if (jQuery(this).prop("checked") === true) {
            jQuery("#hideotpdiv").show();
        } else {
            jQuery("#hideotpdiv").hide();
        }
    });
    });
    


function generatebackupcodebutton(accesstoken) {
    jQuery("#password").after('<tr id="backupcode" class="user-pass1-wrap"><th><span>Backup code list</span></th><td><span class="get-backup-msg" style="display:none;">If you lose your phone or can\'t receive codes via SMS, voice call or Google Authenticator, you can use backup codes to sign in. So please save these backup codes somewhere.</span><span class="reset-backup-msg" style="display:none;">The two factor authentication backup code is already generated, please reset your two factor authentication backup code.</span><a href="javascript:void(0);" id="backupcode" class="ciam-password-button button" onclick="generatebackupcode(\'' + accesstoken + '\')" >Reset backup Code</a></td></tr><tr id="codelist"></tr>');
  jQuery("#ciam_loading_gif").show();
    var content = '<td colspan="2"><div style="width:100%;">';
    var lrObjectInterval14 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval14);
    LRObject.api.getBackupCode(accesstoken,
            function (response) {
                jQuery.each(response.BackUpCodes, function (index, data) {
                    content += '<div class="backupcode-div"><input class="backupcode-width" id="\'' + data + '\'" onClick="copyToClipboard(\'' + data + '\');this.select();" type="text" readonly value="' + data + '" /></div>';
                });
                jQuery('.get-backup-msg').css('display','block');
                jQuery('.reset-backup-msg').css('display','none');
                content += "</div><div><span onclick='copybackupcode()' id='copybackupcode'>Copy</span> <span class='copyMessage'>Copied!</span></div></td>";
                jQuery("#codelist").html(content);
                jQuery("#ciam_loading_gif").hide();
            }, function (errors) {
                jQuery('.reset-backup-msg').css('display','block');
    });
    }
}, 1);
}

function generatebackupcode(accesstoken) {
    jQuery("#ciam_loading_gif").show();
    var content = '<td colspan="2"><div style="width:100%;">';
    var lrObjectInterval14 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval14);
    LRObject.api.resetBackupCode(accesstoken,
            function (response) {
                jQuery.each(response.BackUpCodes, function (index, data) {
                    content += '<div class="backupcode-div"><input class="backupcode-width" id="\'' + data + '\'" onClick="copyToClipboard(\'' + data + '\');this.select();" type="text" readonly value="' + data + '" /></div>';
                });
                content += "</div><div><span onclick='copybackupcode()' id='copybackupcode'>Copy</span> <span class='copyMessage'>Copied!</span></div></td>";
                jQuery("#codelist").html(content);
                jQuery("#ciam_loading_gif").hide();
            }, function (errors) {
    });
    }
}, 1);
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
    var lrObjectInterval15 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval15);
    LRObject.api.getBackupCode(accesstoken,
            function (response) {
                jQuery.each(response.BackUpCodes, function (index, data) {

                });

            }, function (errors) {
    });
    }
 }, 1);
}

function accountlinking() {
    var la_options = {};
    la_options.container = "interfacecontainerdiv";
    la_options.templateName = 'loginradiuscustom_tmpl_link';
    la_options.onSuccess = function (response) {
        // On Success
        ciamfunctions.message(commonOptions.messageList.ACCOUNT_LINKING_MSG, "#social-msg", "success");
        setTimeout(function () {
            location.reload();
        }, 1000);
    };
    la_options.onError = function (errors) {
        // On Errors    
        ciamfunctions.message(errors[0].Description, "#social-msg", "error");              
    };
    var lrObjectInterval16 = setInterval(function () {
                if(typeof LRObject.options !== 'undefined' && LRObject.options != '')
                {
                    clearInterval(lrObjectInterval16);
                   if(LRObject.options.disabledEmailVerification !== true || LRObject.options.phoneLogin === true)
                    {     
                    LRObject.init("linkAccount", la_options);
                   }
                }
}, 1);
}

function accountunlinking() {
    var unlink_options = {};
    unlink_options.onSuccess = function (response) {
        // On Success
        ciamfunctions.message(commonOptions.messageList.ACCOUNT_UNLINKING_MSG, "#social-msg", "success");
        setTimeout(function () {
            location.reload();
        }, 1000);
    };

    unlink_options.onError = function (errors) {
        // On Errors
        ciamfunctions.message(errors[0].Description, "#social-msg", "error");
    };
     var lrObjectInterval17 = setInterval(function () {
       if(typeof LRObject.options !== 'undefined' && LRObject.options != '')
         {
            clearInterval(lrObjectInterval17);
            if(LRObject.options.disabledEmailVerification !== true || LRObject.options.phoneLogin === true)
              {     
               LRObject.init("unLinkAccount", unlink_options);
              }
        }
   }, 1);
}

function resetPassword(redirecturl) {
    var resetpassword_options = {};
    resetpassword_options.container = "resetpassword-container";
    resetpassword_options.onSuccess = function (response) {
        // On Success
        ciamfunctions.message(commonOptions.messageList.RESET_PASSWORD_MSG, "#resetpassword", "success");
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
     var lrObjectInterval21 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval21);
    LRObject.init("resetPassword", resetpassword_options);
     }
 }, 1);
}

function loadingimg() {
     var lrObjectInterval20 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval20);
    LRObject.$hooks.register('startProcess', function (name) {
        jQuery("#ciam_loading_gif").show();
    });
    LRObject.$hooks.register('endProcess', function (name) {
       if(name === 'resendOTP' && jQuery('#login-container').length > 0)
       {
            ciamfunctions.message(commonOptions.messageList.LOGIN_BY_PHONE_MSG, "#loginmessage", "success");
       }
    });

    LRObject.registrationFormSchema  = registrationSchema;
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
        if(name == 'otp')
        {
            ciamfunctions.message(commonOptions.messageList.REGISTRATION_OTP_MSG, "#registration_message", "success");
        }
        if(name == 'twofaotp')
        {
            ciamfunctions.message(commonOptions.messageList.TWO_FA_MSG, "#loginmessage", "success");
        }
        if (name === "registration") {
            show_birthdate_date_block();
        }
         if (name === "login") {
            show_birthdate_date_block();
        }      
    });
     }
 }, 1);
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

    
    if(typeof(tabValue) != "undefined" && tabValue !== ''){
        $('.ciam-options-tab-btns li').removeClass('ciam-active');
        $('.ciam-tab-frame').removeClass('ciam-active');
        $('*[data-tab="'+tabValue+'"]').addClass('ciam-active');
        $("#"+tabValue).addClass('ciam-active');
    }

    //tabs
    $('.ciam-options-tab-btns li').click(function () {
        var tab_id = $(this).attr('data-tab');

        $('.ciam-options-tab-btns li').removeClass('ciam-active');
        $('.ciam-tab-frame').removeClass('ciam-active');

        $(this).addClass('ciam-active');
        $("#" + tab_id).addClass('ciam-active');
    });  

});
/* multiple email function */
function additionalemailform(useremail, lr_profile_email, lr_profile_emailverified, count, img) {
    /* condition to hide remove button if one email is exist */
    if (count == 1) {
        jQuery("#email").val(useremail);
        if(lr_profile_emailverified==true){
          var content = '<a id="open" class="open button ciam-email-button ciam-addemail-button" href="javascript:void(0);">Add Email</a><div class="popup-outer" style="display:none;"><span id="close"><img src="' + img + '" alt="close" /></span><div class="popup-inner"><span class="popup-txt"><h1><strong>Please Enter Email</strong></h1></span><div id="addemail-container"></div></div></div><div id="remove" style="display:none;"><div class="removeemail-container"></div></div><br />';
        }
        else{
            var content = '';
        }

        } else {
        var content = '<a class="remove-popup wp_email open button ciam-email-button ciam_email_0 ciam-removeemail-button" href="javascript:void(0);">Remove</a><div class="remove-popup-outer" style="display:none;"><span class="close-removepopup"><img src="' + img + '" alt="close" /></span><div class="remove-popup-inner"><span class="popup-txt"><h1><strong>Are you sure to remove the mail?</strong></h1></span><span id="email_msg"></span><div class="removeemail-container"></div></div></div>&nbsp;&nbsp;<a id="open" class="open button ciam-email-button ciam-addemail-button" href="javascript:void(0);">Add</a><div class="popup-outer" style="display:none;"><span id="close"><img src="' + img + '" alt="close" /></span><div class="popup-inner"><span class="popup-txt"><h1><strong>Please Enter Email</strong></h1></span><div id="addemail-container"></div></div></div></div><br />';
    }

    content += '';
    /* loop to list other email except mail email */
    i = 1;

    jQuery.each(lr_profile_email, function (index, email) {

        if (email.Value !== useremail) {
            content += '<div class="ciam-email-row"><input type="email" value="' + email.Value + '" readonly="readonly" id="ciam_email_' + i + '" name="ciam_email" class="ciam-email">&nbsp;<a class="remove-popup wp_email open button ciam-email-button ciam_email_' + i + '" href="javascript:void(0);">Remove</a><div class="remove-popup-outer" style="display:none;"><span class="close-removepopup"><img src="' + img + '" alt="close" /></span><div class="remove-popup-inner"><span class="popup-txt"><h1><strong>Are you sure to remove the mail?</strong></h1></span><span id="email_msg"></span><div class="removeemail-container"></div></div></div></div>';
        }
        i++;
    });

    jQuery(".user-email-wrap td").append(content);

   
    /* add email sctipt */
    var addemail_options = {};
    addemail_options.container = "addemail-container";
    addemail_options.classPrefix = "lremail-";
    addemail_options.onSuccess = function (response) {            
        if(commonOptions.otpEmailVerification==true){        
             if(response.IsPosted==true && typeof response.Data !== 'undefined'){
                location.reload();
             }else{        
              var add_html = '<div id="ciam-addemail-success-msg" style="color:green">'+commonOptions.messageList.ADD_OTP_MSG+'</div>';
                if (ciamautohidetime > 0) {
                    jQuery(add_html).appendTo(".popup-outer:visible .popup-txt").show().fadeOut(ciamautohidetime * 1000);
                }else{
                    jQuery(add_html).appendTo(".popup-outer:visible .popup-txt").show();
                }              
             } 
        }else{
            document.cookie = "addemail="+commonOptions.messageList.ADD_EMAIL_MSG;
            location.reload();
        }
    };
    addemail_options.onError = function (response) {
        //parent.jQuery.fancybox.close();
        var add_html = '<div id="ciam-addemail-msg" style="color:#FF0000">'+response[0].Description+'</div>';
         if (ciamautohidetime > 0) {
       jQuery(add_html).appendTo(".popup-outer:visible .popup-txt").show().fadeOut(ciamautohidetime * 1000);
   }
   else{
       jQuery(add_html).appendTo(".popup-outer:visible .popup-txt").show();
   }
    };
   var lrObjectInterval18 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval18);
                    LRObject.init("addEmail", addemail_options);
                }
    }, 1);
    /* remove email script */
    var removeemail_options = {};
    removeemail_options.container = "removeemail-container";
    removeemail_options.onSuccess = function (response) {
        // On Success
        document.cookie = "addemail=Email has been removed!";
        location.reload();
    };
    removeemail_options.onError = function (response) {
        var remove_html = '<div id="ciam-removeemail-msg" style="color:#FF0000">'+response[0].Description+'</div>';
         if (ciamautohidetime > 0) {
       jQuery(remove_html).appendTo(".remove-popup-outer:visible .popup-txt").show().fadeOut(ciamautohidetime * 1000);
   }
   else
   {
       jQuery(remove_html).appendTo(".remove-popup-outer:visible .popup-txt").show();
   }
    };
    var lrObjectInterval19 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval19);

        LRObject.init("removeEmail", removeemail_options);
        LRObject.$hooks.call('setButtonsName', {
            removeemail: "Remove"
        });
     }
 }, 1);
    /* end */
  
    jQuery('.remove-popup').each(function () {
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
}

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
