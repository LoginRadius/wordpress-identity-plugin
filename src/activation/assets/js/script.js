jQuery(document).ready(function ($) {
    //tabs
    $('.active-options-tab-btns li').click(function () {
        var tab_id = $(this).attr('data-tab');
        $('.active-options-tab-btns li').removeClass('active-active');
        $('.active-tab-frame').removeClass('active-active');
        $(this).addClass('active-active');
        $("#" + tab_id).addClass('active-active');
    });    
    $("#submit").on('click',function(){ 
       
       if(($("#sitename").val() == "") || ($("#apikey").val() == "") || ($("#secret").val() == "")){
           $("#error_msg").css({'color':'#FF0000','margin-left':'400px'}).text('All fields are required!').show().fadeOut(5000);;
          return false;
       }      
    });
});

