jQuery(document).ready(function(){
    jQuery(function ($) {
    if ($('#lr-enable-custom-obj').is(':checked')) {
        $(".lr-option-disabled-hr.lr-customobject").hide();
    } else {
        $(".lr-option-disabled-hr.lr-customobject").show();
    }

    $('#lr-enable-custom-obj').change(function () {
        if ($(this).is(':checked')) {
            $(".lr-option-disabled-hr.lr-customobject").hide();
        } else {
            $(".lr-option-disabled-hr.lr-customobject").show();
        }
    });
});
});
