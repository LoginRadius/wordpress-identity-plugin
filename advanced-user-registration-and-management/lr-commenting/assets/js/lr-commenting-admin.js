/*
 Author: LoginRadius Team
 Author URI: http://www.LoginRadius.com
 */

jQuery(function($) {

    function hideAndShowElement(element, inputBoxName) {
        if (element.is(':checked')) {
            jQuery(inputBoxName).show();
        } else {
            jQuery(inputBoxName).hide();
        }
    }

    jQuery('#wp-enable-moderation-msg').change(function () {
        hideAndShowElement(jQuery(this), '.lr-moderation-msg');
    });

    hideAndShowElement(jQuery('#wp-enable-moderation-msg'), '.lr-moderation-msg');

    if ($('#lr-comment-enable').is(':checked')) {
        $(".lr-option-disabled-hr.lr-commenting").hide();
    } else {
        $(".lr-option-disabled-hr.lr-commenting").show();
    }

    $('#lr-comment-enable').change(function () {
        if ($(this).is(':checked')) {
            $(".lr-option-disabled-hr.lr-commenting").hide();
        } else {
            $(".lr-option-disabled-hr.lr-commenting").show();
        }
    });

    $('#lr-comment-formatting').change(function() {
        hideAndShowElement(jQuery(this), '.lr-comment-images');
    });

    hideAndShowElement(jQuery('#lr-comment-formatting'), '.lr-comment-images');

});
