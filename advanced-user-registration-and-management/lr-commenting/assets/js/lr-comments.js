/*
 Author: LoginRadius Team
 Author URI: http://www.LoginRadius.com
 */

jQuery(function ($) {

    $(document).on('change', '.jqte_editor', function(event) {
        event.preventDefault();

        var share_twitter_token = sessionStorage.getItem("share_twitter_token");

        var content = $('.jqte_editor').text();
        $('.lr-comment-editor-length').html(content.length);
        
        if( 140 <= content.length && null !== share_twitter_token ){
            $('.lr-comment-editor-length').addClass('error');  
        }else{
            $('.lr-comment-editor-length').removeClass('error');
        }
    });

    function GetMentions() {
        var share_twitter_token = sessionStorage.getItem("share_twitter_token");
        if (share_twitter_token != null) {
            $.ajax({
                type: 'POST',
                url: commentvar.ajaxurl,
                data: {
                    action: 'loginradius_get_mentions',
                    share_twitter_token: share_twitter_token
                },
                success: function (data) {
                    data = $.parseJSON(data);
                    $('.lr-mention-menu-content').html("");
                    for (var i = 0; i < data.Twitter.length; i++) {
                        var mention = data.Twitter[i].ProfileUrl;
                        mention = mention.split('twitter.com/')[1];

                        var mention_item = $("<div></div>").addClass("lr-mention-item lr-mention-item-twitter").attr("data-mention", "@" + mention).append($("<img />").attr("src", data.Twitter[i].ImageUrl).addClass("lr-mention-item-avatar"));
                        mention_item.append($("<h4></h4>").addClass("lr-mention-item-display-name").text(data.Twitter[i].Name));
                        mention_item.append($("<span></span>").addClass("lr-img-icon-twitter"));
                        $('.lr-mention-menu-content').append(mention_item);
                        $('.lr-editor-btn.mention').show();
                    }

                    $('.lr-mention-item').click(function () {
                        var insert = this.attributes[1].value;
                        $('.jqte_editor').append(" " + insert + " ");
                        this.remove();
                        if ($('.lr-mention-item').length == 0) {
                            $('.lr-mention-menu').remove();
                        }
                    });
                }
            });
        }
    };
    GetMentions();

    function SuccessLogin(element) {
        var images = new Array();
        if (commentvar.image_upload_enable === "true") {
            $('#images img').each(function () {
                images.push($(this).attr("src"));
            });
        }

        //TODO : Add Email Form
        var token = sessionStorage.getItem("LRTokenKey");
        var share_facebook_token = sessionStorage.getItem("share_facebook_token");
        var share_twitter_token = sessionStorage.getItem("share_twitter_token");
        var share_linkedin_token = sessionStorage.getItem("share_linkedin_token");

        var comment = $.trim($('#comment').val());
        var comment_post_ID = $('#comment_post_ID').val();
        var comment_parent = $('#comment_parent').val();

        var author = $('#author').val();
        var email = $('#email').val();
        var url = $('#url').val();

        var share_selected = sessionStorage.getItem("share_selected");

        $('.lr-comment-login-container').hide();
        $('.lr-comment-overlay').hide();

        if (share_selected != "true") {

            $('.lr_comment_loader').show();
            $.ajax({
                type: 'POST',
                url: commentvar.ajaxurl,
                data: {
                    token: (commentvar.is_user_logged_in == "false") ? token : null,
                    action: 'loginradius_post_comment',
                    share_facebook_token: share_facebook_token,
                    share_twitter_token: share_twitter_token,
                    share_linkedin_token: share_linkedin_token,
                    author: author,
                    email: email,
                    images: images,
                    url: url,
                    comment: comment,
                    comment_parent: comment_parent,
                    comment_post_ID: comment_post_ID
                },
                success: function (data, textStatus, XMLHttpRequest) {
                    var error;
                    try {
                        data = $.parseJSON(data);
                        error = data.Error;
                    } catch (e) {
                        // error
                        error = data;
                    }

                    if (commentvar.debugging == "true") {
                        console.log(data);
                    }
                    if ( '' != error ) {
                        $('.lr_comment_loader').hide();
                        $('#lr-comment-error-msg div').html(error);
                        $('#lr-comment-error-msg').show();
                    } else {
                        $('.jqte_editor').empty();
                        document.location.reload();
                    }
                }
            });
        } else {
            //var token = sessionStorage.getItem("LRTokenKey");
            var provider = sessionStorage.getItem("share_provider");
            var time = new Date();
            time = time.getTime();

            switch (provider) {
                case "facebook":
                    sessionStorage.setItem("share_facebook_token", token);
                    sessionStorage.setItem("share_facebook_start", time);
                    break;
                case "twitter":
                    sessionStorage.setItem("share_twitter_token", token);
                    sessionStorage.setItem("share_twitter_start", time);
                    GetMentions();
                    break;
                case "linkedin":
                    sessionStorage.setItem("share_linkedin_token", token);
                    sessionStorage.setItem("share_linkedin_start", time);
                    break;
                default:
                    break;
            }
            sessionStorage.removeItem("LRTokenKey");
            sessionStorage.removeItem("share_selected");
            sessionStorage.removeItem("share_provider");
        }
    };

    function sharePost(element) {

        var element_switch = $('#lr-' + element.name + '-share-switch');
        var element_trigger = $('#lr-' + element.name + '-share-trigger');

        if (element_switch.is(':checked')) {
            sessionStorage.setItem("share_selected", "true");
            sessionStorage.setItem("share_provider", element.name);
            element_trigger.trigger("click");
        } else {
            sessionStorage.removeItem("share_" + element.name + "_token");
            sessionStorage.removeItem("share_" + element.name + "_start");
            if (element.name == "twitter") {
                $('.lr-mention-button').hide();
            }
        }
    };

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };

    $(document).on('change', '.lr-share-switch', function () {
        LoginRadiusSDK.onlogin = SuccessLogin;
        sharePost(this);
    });

    $('.lr-editor-btn.format').click(function () {
        $('.lr-editor-format-container').toggle();
        $('.lr-share-container').hide();
    });

    $('.lr-editor-btn.share').click(function () {
        $('#lr-comment-error-msg').hide();
        $('.lr-editor-format-container').hide();
        $('.lr-share-container').toggle();
    });

    $('.lr-editor-btn.mention').click(function () {
        $('.lr-mention-menu').toggle();
    });

    $('.lr_logininterface_container').click(function () {
        sessionStorage.removeItem("LRTokenKey");
        sessionStorage.removeItem("share_selected");
        sessionStorage.removeItem("share_provider");
    });

    $('.lr-post-button.login-btn').click(function () {
        LoginRadiusSDK.onlogin = SuccessLogin;
        sessionStorage.removeItem("LRTokenKey");
        $('.lr-editor-format-container').hide();
        $('.lr-share-container').hide();
        if ($.trim($('#comment').val()) != '') {
            $('#lr-comment-error-msg div').html("");
            $('#lr-comment-error-msg').hide();

            $('.lr-comment-login-container,.lr-comment-overlay').show();
        } else {
            $('#lr-comment-error-msg div').html(commentvar.empty_msg);
            $('#lr-comment-error-msg').show();
        }
    });

    $('.lr-post-button.post-btn').click(function () {
        LoginRadiusSDK.onlogin = SuccessLogin;
        sessionStorage.removeItem("share_selected");
        sessionStorage.removeItem("share_provider");
        if ($.trim($('#comment').val()) != '') {
            $('#lr-comment-error-msg').hide();
            SuccessLogin();
        } else {
            $('#lr-comment-error-msg div').html(commentvar.empty_msg);
            $('#lr-comment-error-msg').show();
        }
    });

    $('.lr-close-popup,.lr-comment-overlay').click(function () {
        $('.lr-comment-login-container,.lr-comment-overlay').hide();
    });

    $('.jqte_editor').click(function () {
        $('html').trigger();
    });

    $('html').click(function () {
        $('.lr-share-container,.lr-editor-format-container,.lr-mention-menu,#lr-comment-error-msg').hide();
    });

    $('.lr-comment-editor-toolbar').click(function (event) {
        event.stopPropagation();
    });

    if (commentvar.editor_enable === "true") {
        $("#comment").jqte({
            format: false,
            sub: false,
            remove: false,
            sup: false,
            center: false,
            color: false,
            fsize: false,
            indent: false,
            outdent: false,
            left: false,
            right: false,
            link: false,
            strike: false,
            unlink: false,
            source: false,
            rule: false
        });

        if (commentvar.image_upload_enable === "true") {

            //$('.jqte_toolbar').append('<div class="jqte_tool jqte_tool_22 unselectable" role="button"><a class="jqte_tool_icon unselectable"></a></div>')
            
            $('.lr-editor-btn.img').click(function () {

                $('[type=file]:enabled').click()
            });

            var droppedImage;
            $(document).on('drop', '.jqte [contenteditable]', function () {
                if (droppedImage) {
                    $(droppedImage).remove();
                    droppedImage = undefined;
                }
                setTimeout(function () {
                    document.execCommand('unselect')
                }, 100);
            });

            $(document).on('dragstart', '#images img', function () {
                droppedImage = this;
            }).on('dragend', '#images img', function () {});

            $(document).on('change', '[type=file]', function (e) {
                var here = $(this);
                here.parent().append(here.clone());
                here.hide().prop('disabled', true);
                $.each($(this).prop('files'), function (i, file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('<img/>', {src: e.target.result}).css({maxWidth: 250, padding: "10px"}).appendTo(here.parent());
                    }
                    reader.readAsDataURL(file);
                });
            });
        }
    }

    $('.post_comment_form').click(function () {

        LoginRadiusSDK.onlogin = SuccessLogin;

        sessionStorage.removeItem("share_selected");
        sessionStorage.removeItem("share_provider");
        var author = $.trim($('#author').val());
        var email = $.trim($('#email').val());

        if (author == '') {
            $('.comment-form-author label,.required.name').show();
        } else {
            $('.required.name').hide();
        }

        if (email == '') {
            $('.comment-form-email label,.required.email').show();
        } else {
            $('.required.email').hide();
        }

        if (!validateEmail(email)) {
            $('.required.email').show();
        } else {
            $('.required.email').hide();
        }

        if (author != '' && email != '' && validateEmail(email)) {
            SuccessLogin();
        }
    });

    function time_elapsed(time) {
        var min = (time/1000/60) << 0;
        var sec = (time/1000) % 60;
        if( min < 15 ) {
            return true;
        }else {
            return false;
        }
    }

    $(window).load(function () {
        var share_facebook_token = sessionStorage.getItem("share_facebook_token");
        var share_twitter_token = sessionStorage.getItem("share_twitter_token");
        var share_linkedin_token = sessionStorage.getItem("share_linkedin_token");

        var tw_token_time = sessionStorage.getItem("share_twitter_start");
        var li_token_time = sessionStorage.getItem("share_linkedin_start");
        
        var time = new Date();
        time = time.getTime();

        if (share_facebook_token !== null) {

            var response = time_elapsed( time - sessionStorage.getItem("share_facebook_start") );
            if( true == response ){
                $("#lr-facebook-share-switch").prop('checked', true);
            }else {
                sessionStorage.removeItem("share_facebook_token");
                sessionStorage.removeItem("share_facebook_start");
            }  
        }
        if (share_twitter_token !== null) {
            var response = time_elapsed( time - sessionStorage.getItem("share_twitter_start") );
            if( true == response ){
                $('.lr-mention-button').show();
                $("#lr-twitter-share-switch").prop('checked', true);
            } else {
                sessionStorage.removeItem("share_twitter_token");
                sessionStorage.removeItem("share_twitter_start");
            }
        }
        if (share_linkedin_token !== null) {
            $("#lr-linkedin-share-switch").prop('checked', true);
        }
    });
});
