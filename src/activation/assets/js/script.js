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

           $("#error_msg").css({'font-weight':'600','font-size':'0.8em','padding':'.5em 0 .5em 13px'}).text('All fields are required!').show().fadeOut(5000);;

          return false;

       }      

    });

});



