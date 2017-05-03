jQuery(document).ready(function ($) {
    manageUserServices($);
    $('#lr-hosted-enable').change(function () {
        manageUserServices($);
    });
    function manageUserServices($) {
        if ($('#lr-hosted-enable').is(':checked')) {
            $('#lr-raas-autopage').closest('.lr_options_container').hide();
            $('.lr-shortcode').closest('.lr_options_container').hide();
            $('input[name="LR_Raas_Settings[LoginRadius_socialLinking]"]').each(function () {
                if ($(this).val() == '0') {
                    $(this).attr("checked", "checked");
                    $(this).closest('div').hide();
                }
            });
            $('#lr-raas-autopage').attr('checked', false);
        } else {
            $('#lr-raas-autopage').closest('.lr_options_container').show();
            $('.lr-shortcode').closest('.lr_options_container').show();
            $('input[name="LR_Raas_Settings[LoginRadius_socialLinking]"]').each(function () {
                if ($(this).val() == '0') {
                    $(this).closest('div').show();
                }
            });
        }
    }
});