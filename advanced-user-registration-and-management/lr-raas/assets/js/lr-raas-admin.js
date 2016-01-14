jQuery(document).ready(function ($) {

    //tabs
    $('.lr-options-tab-btns li').click(function () {
        var tab_id = $(this).attr('data-tab');

        $('.lr-options-tab-btns li').removeClass('lr-active');
        $('.lr-tab-frame').removeClass('lr-active');

        $(this).addClass('lr-active');
        $("#" + tab_id).addClass('lr-active');
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
    function showAndHideElement(element, inputBoxName) {
        if (element.is(':checked')) {
            $(inputBoxName).show();
        } else {
            $(inputBoxName).hide();
        }
    }
    // Hide/Show Options if enabled/disabled on change
    $('#lr-raas-autopage').change(function() {
            hideAndShowElement( $(this), '.lr-custom-page-settings' );
    });
    $('#lr-v2captcha-enable').change(function() {
            showAndHideElement( $(this), '.lr-v2captcha-key' );
    });
    hideAndShowElement( $('#lr-raas-autopage'), '.lr-custom-page-settings' );

    showAndHideElement( $('#lr-v2captcha-enable'), '.lr-v2captcha-key' );
});