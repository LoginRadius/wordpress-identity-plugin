jQuery(function($) {

    if ($('#lr-google_analytics-enable').is(':checked')) {
        $(".lr-option-disabled-hr.lr-google_analytics").hide();
    } else {
        $(".lr-option-disabled-hr.lr-google_analytics").show();
    }

    $('#lr-google_analytics-enable').change(function () {
        if ($(this).is(':checked')) {
            $(".lr-option-disabled-hr.lr-google_analytics").hide();
        } else {
            $(".lr-option-disabled-hr.lr-google_analytics").show();
        }
    });

});