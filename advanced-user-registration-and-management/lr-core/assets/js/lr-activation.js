jQuery(document).ready(function($) {

	//tabs
    $('.lr-options-tab-btns li').click(function(){
        var tab_id = $(this).attr('data-tab');

        $('.lr-options-tab-btns li').removeClass('lr-active');
        $('.lr-tab-frame').removeClass('lr-active');

        $(this).addClass('lr-active');
        $("#"+tab_id).addClass('lr-active');
    });

});