// get trim() working in IE
if ( typeof String.prototype.trim !== 'function' ) {
    String.prototype.trim = function() {
        return this.replace( /^\s+|\s+$/g, '' );
    };
}


function loginRadiusCheckElement( arr, obj ) {
    for ( var i = 0; i < arr.length; i++ ) {
        if ( arr[i] == obj ) {
            return true;
        }
    }
    return false;
}

window.onload = function() {
    loginRadiusAdminUI();
};

function loginRadiusAdminUI() {

    // user activate/deactivate toggle
    var loginRadiusStatusOption = jQuery('[name="LoginRadius_settings[LoginRadius_enableUserActivation]"]');
    for ( var i = 0; i < loginRadiusStatusOption.length; i++ ) {
        if ( loginRadiusStatusOption[i].checked && loginRadiusStatusOption[i].value == '1' ) {
            jQuery('#loginRadiusDefaultStatus').css({
                "display": "table-row"
            });
        } else if ( loginRadiusStatusOption[i].checked && loginRadiusStatusOption[i].value == '0' ) {
            jQuery('#loginRadiusDefaultStatus').hide();
        }
    }
    
    // email required
    var loginRadiusEmailRequired = jQuery('[name="LoginRadius_settings[LoginRadius_dummyemail]"]');
    for ( var i = 0; i < loginRadiusEmailRequired.length; i++ ) {
        if ( loginRadiusEmailRequired[i].checked && loginRadiusEmailRequired[i].value == 'notdummyemail' ) {
            jQuery('#loginRadiusPopupMessage').show();
            jQuery('#loginRadiusPopupErrorMessage').show();
        } else if ( loginRadiusEmailRequired[i].checked && loginRadiusEmailRequired[i].value == 'dummyemail' ) {
            jQuery('#loginRadiusPopupMessage').hide();
            jQuery('#loginRadiusPopupErrorMessage').hide();
        }
    }

    // Hiding social Login position for registration page, if not enabled
    var registrationFormOption = jQuery('#showonregistrationpageyes');
    if ( registrationFormOption ) {
        if ( registrationFormOption.checked ) {
            jQuery('#registration_interface').show();
        } else {
            jQuery('#registration_interface').hide();
        }
    }


}

jQuery(document).ready(function($) {

    function hideAndShowCustomUrlBox(element, inputBoxName) {
        if (element.is(':checked') && element.val() == "custom") {
            jQuery('#' + inputBoxName).show();
        } else {
            jQuery('#' + inputBoxName).hide();
        }
    }

    function hideAndShowElement(element, inputBoxName) {
        if (element.is(':checked')) {
            jQuery(inputBoxName).show();
        } else {
            jQuery(inputBoxName).hide();
        }
    }

    function display_element(elem, elementToShow) {
        if (elem.is(":checked")) {
            jQuery('#' + elementToShow).show();
        }
    };

    function hide_element(elem, elementToHide) {
        if (elem.is(":checked")) {
            jQuery('#' + elementToHide).hide();
        }
    }

    // Color Picker
    $('.color_picker').wpColorPicker();

    //tabs
    $('.lr-options-tab-btns li').click(function(){
        var tab_id = $(this).attr('data-tab');

        $('.lr-options-tab-btns li').removeClass('lr-active');
        $('.lr-tab-frame').removeClass('lr-active');

        $(this).addClass('lr-active');
        $("#"+tab_id).addClass('lr-active');
    });

    jQuery('#showonregistrationpageyes').click(function() {
        display_element(jQuery(this), 'registration_interface');

    });
    jQuery('#showonregistrationpageno').click(function() {
        hide_element(jQuery(this), 'registration_interface');

    });
    jQuery('#controlActivationYes').click(function() {
        display_element(jQuery(this), 'loginRadiusDefaultStatus');

    });
    jQuery('#controlActivationNo').click(function() {
        hide_element(jQuery(this), 'loginRadiusDefaultStatus');

    });
    jQuery('#dummyMailYes').click(function() {
        jQuery('#loginRadiusPopupMessage').show();
        jQuery('#loginRadiusPopupErrorMessage').show();

    });
    jQuery('#dummyMailNo').click(function() {
        jQuery('#loginRadiusPopupMessage').hide();
        jQuery('#loginRadiusPopupErrorMessage').hide();

    });

    //Redirection Radio Buttons
    jQuery('.loginRedirectionRadio').change(function() {
        hideAndShowCustomUrlBox(jQuery(this), 'loginRadiusCustomLoginUrl');
    });

    jQuery('.registerRedirectionRadio').change(function() {
        hideAndShowCustomUrlBox(jQuery(this), 'loginRadiusCustomRegistrationUrl');
    });

    jQuery('.logoutRedirectionRadio').change(function() {
        hideAndShowCustomUrlBox(jQuery(this), 'loginRadiusCustomLogoutUrl');
    });

    jQuery('#lr-clicker-login-form').change(function() {
        hideAndShowElement(jQuery(this), '.lr-login-form-options');
    });

    jQuery('#lr-clicker-reg-form').change(function() {
        hideAndShowElement(jQuery(this), '.lr-reg-form-options');
    });

    jQuery('#lr-clicker-commenting').change(function() {
        hideAndShowElement(jQuery(this), '.lr-commenting-options');
    });

    jQuery('#lr-clicker-get-email').change(function() {
        hideAndShowElement(jQuery(this), '.lr-get-email-messages');
    });

    hideAndShowElement(jQuery('#lr-clicker-login-form'), '.lr-login-form-options');
    hideAndShowElement(jQuery('#lr-clicker-reg-form'), '.lr-reg-form-options');

    hideAndShowElement(jQuery('#lr-clicker-commenting'), '.lr-commenting-options');

    hideAndShowElement(jQuery('#lr-clicker-get-email'), '.lr-get-email-messages');

    //Redirection Radio Buttons Init
    hideAndShowCustomUrlBox(jQuery('.loginRedirectionRadio.custom'), 'loginRadiusCustomLoginUrl');
    hideAndShowCustomUrlBox(jQuery('.registerRedirectionRadio.custom'), 'loginRadiusCustomRegistrationUrl');
    hideAndShowCustomUrlBox(jQuery('.logoutRedirectionRadio.custom'), 'loginRadiusCustomLogoutUrl');

});