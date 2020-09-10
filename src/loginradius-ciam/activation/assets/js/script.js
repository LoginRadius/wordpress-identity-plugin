jQuery(document).ready(function ($) {

    //tabs

    $('.active-options-tab-btns li').click(function () {
        var tab_id = $(this).attr('data-tab');
        $('.active-options-tab-btns li').removeClass('active-active');
        $('.active-tab-frame').removeClass('active-active');
        $(this).addClass('active-active');
        $("#" + tab_id).addClass('active-active');
    });    

    $(".submit").on('click',function(){         
       if(($("#apikey").val() == "") || ($("#secret").val() == "")){
           $("#error_msg").css({'font-weight':'600','font-size':'0.8em','padding':'.5em 0 .5em 13px'}).text('All fields are required!').show().fadeOut(5000);;
          return false;
       }       
    });

    $(".submitAuth").on('click',function(){  
        var profile = jQuery('#registation_form_schema').val();
        if(typeof profile !== 'undefined' && profile != '') {
        try
        {
            var response = '';
            response = jQuery.parseJSON(profile);     
            if (response != true && response != false) {
                var validjson = JSON.stringify(response, null, '\t').replace(/</g, '&lt;');
                if (validjson != 'null') {
                    jQuery('#registation_form_schema').val(validjson);
                    jQuery(".registation_form_schema").hide();         
                    jQuery('#registation_form_schema').css("border", "1px solid green");                 
                } else {
                    jQuery('#registation_form_schema').css("border", "1px solid green");                 
                }
            } else {
                jQuery(".registation_form_schema").hide();           
            }
        } catch (e)
        {
            jQuery('#registation_form_schema').css("border", "1px solid red");
            jQuery(".registation_form_schema").show();        
            jQuery('.registation_form_schema').html('<div style="color:red;">Please enter a valid Json. '+e.message+'</div>');
            return false;
        }}
    });

});

    function ciamsecrettoggle() {
        if(jQuery("#secret").prop("type") == 'password'){
            jQuery("#secret").prop("type",'text');
        }else{
            jQuery("#secret").prop("type",'password');
        }
    }
