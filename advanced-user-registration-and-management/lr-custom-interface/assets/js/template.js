




(function ($) {
    $(document).ready(function () {
        selectSocialProvider();
    });
    hideAndShowElement($('#lr-custom-interface-enable'), '.lr_custom_interface');

    $('#lr-custom-interface-enable').change(function () {
        hideAndShowElement($(this), '.lr_custom_interface');
    });

})(jQuery);

function selectSocialProvider() {
    var socialProvider = '<select name="socialProvider" class="lr-row-field" id="lr-ci-upload-file-name">';
    for (var i = 0; i < phpvar.providers.length; i++) {
        socialProvider += '<option value="' + phpvar.providers[i] + '">' + phpvar.providers[i] + '</option>';
    }
    socialProvider += '</select>';
    jQuery('#select-provider').html(socialProvider);
}
function hideAndShowElement(element, inputBoxName) {
    if (element.is(':checked')) {
        jQuery(inputBoxName).hide();
    } else {
        jQuery(inputBoxName).show();
    }
}
