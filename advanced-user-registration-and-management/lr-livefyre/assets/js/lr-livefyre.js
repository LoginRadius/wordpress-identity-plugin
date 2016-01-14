Livefyre.require(['auth'], function (auth) {

    var setCookie = function( cname, cvalue, exdays ) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    var getCookie = function( cname ) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
        }
        return "";
    }

    var lr_livefyre_auth = function( errback, func ) {

        var token = sessionStorage.getItem("LRTokenKey");

        if( token ) {
            if( '1' === phpvar.enable_login && '' === phpvar.logged_in ) {
                var form = document.createElement('form');
                form.action = window.location.href;
                form.method = 'POST';

                var hiddenToken = document.createElement('input');
                hiddenToken.type = 'hidden';
                hiddenToken.value = token;
                hiddenToken.name = 'token';
                form.appendChild(hiddenToken);

                document.body.appendChild(form);
                form.submit();
            } else {
                LoginRadiusSDK.getUserprofile( function( profile ) {

                    if( profile.ID ) {
                       jQuery.ajax( {
                           type: 'POST',
                           url: phpvar.url,
                           data: {
                               action: 'lr_livefyre_login',
                               ID: profile.ID,
                               name: profile.FirstName
                           },
                           success: function( data, textStatus, XMLHttpRequest ) {
                               console.log( data );
                               var response = JSON.parse(data);

                               if( response.token ) {
                                   setCookie( 'livefyre_token', response.token, 7 )
                                   jQuery('.lr-livefyre-container').remove();
                                   errback( null, {
                                     livefyre: getCookie( 'livefyre_token' )
                                   });

                                   
                               }else{
                                   errback( 'Log In Error' );
                               }
                           }
                       }); 
                    }else{
                        errback( 'Could not get LoginRadius Profile' );
                    } 
                });  
            }
        } else{
            errback( 'Log In Error' );
        }         
    }

    var display_lr_livefyre_form = function( errback ) {

        var div_pop_group = document.createElement('div');
        div_pop_group.id = 'lr-pop-group';
        div_pop_group.className = 'lr-livefyre-container lr-show-layover';

        var div_overlay = document.createElement('div');
        div_overlay.id = 'lr-overlay';
        div_overlay.className = 'lr-livefyre-overlay'

        var div_one = document.createElement('div');
        div_one.className = "lr-livefyre-popup lr-popup-container lr-show";

        var div_header = document.createElement('div');
        div_header.className = 'lr-popup-header';

        var span_close = document.createElement('span');
        span_close.className = 'lr-popup-close-span';

        var div_header_logo = document.createElement('div');
        div_header_logo.className = 'lr-header-logo';

        //Header Caption Title
        var p_header_caption = document.createElement('p');
        p_header_caption.className = 'lr-header-caption';
        p_header_caption.appendChild( document.createTextNode('Login') );

        div_header_logo.appendChild( p_header_caption );
        
        var a_close = document.createElement('a');
        a_close.className = 'lr-popup-close-btn';
        var a_close_content = document.createTextNode('x');
        a_close.appendChild( a_close_content );

        span_close.appendChild( a_close );
        div_header.appendChild( span_close );
        div_header.appendChild( div_header_logo );

        var div = document.createElement('div');
        div.className = "lr_livefyre_interface";
        
        div_one.appendChild( div_header );
        div_one.appendChild( div );

        div_pop_group.appendChild( div_overlay );
        div_pop_group.appendChild( div_one );
        
        document.body.appendChild( div_pop_group );
        
        function loginradius_interface(){ 
            var options = {};
            options.apikey = phpvar.apiKey;
            options.appname = phpvar.siteName;
            options.providers = phpvar.providers;
            options.templatename = "lr_livefyre_template"; 
            $LRIC.renderInterface("lr_livefyre_interface", options);
            
        }
        $LRIC.util.ready(loginradius_interface);
        LoginRadiusSDK.setLoginCallback( function() {
            lr_livefyre_auth( errback, 'lr-livefyre' );
        });

        //LoginRadiusSDK.setLoginCallback

        jQuery('.lr-livefyre-overlay,.lr-livefyre-popup .lr-popup-close-span').click(function(){
            window.location.hash = '';
            jQuery('.lr-livefyre-container').remove();
            errback('Popup Window Closed');
        });
    }

    var authDelegate = {
        login: function( errback ) {

            if( '' != getCookie( 'livefyre_token' ) ) {
                errback( null, {
                  livefyre: getCookie( 'livefyre_token' )
                });
            }else {
                display_lr_livefyre_form( errback );
            }
        },
        logout: function( errback ) {            
            localStorage.removeItem('fyre-auth');
            localStorage.removeItem('fyre-authentication-creds');
            setCookie( 'livefyre_token', '', -7 );
            if( '1' === phpvar.logged_in ) {
                window.location.href = phpvar.logout_url;
            }
            errback(null);
        },
        viewProfile: function( user ) {
            window.location.href = phpvar.profile_url;
            
        },         
        editProfile: function( user ) {
            window.location.href = phpvar.profile_url;
        },
        forEachAuthentication: function (authenticate) {
          window.addEventListener('userAuthenticated', function(data) {
            alert( data );
            console.log( data );
            authenticate( {livefyre: data.token} );
          });
        }
    };
    
    auth.delegate(authDelegate);

    if( '1' === phpvar.enable_login ) {
        if( '1' === phpvar.logged_in ) {

            jQuery.ajax( {
                type: 'POST',
                url: phpvar.url,
                data: {
                    action: 'lr_livefyre_login',
                    ID: phpvar.provider_id,
                    name: phpvar.profile_name
                },
                success: function( data, textStatus, XMLHttpRequest ) {
                    var response = JSON.parse(data);

                    if( response.token ) {
                        setCookie( 'livefyre_token', response.token, 7 );
                        auth.login();
                    }
                }
            }); 
        }else{
            auth.logout();
        }
    }

    jQuery(document).ready(function($) {
        $('#wp-admin-bar-logout').click( function() {
            auth.logout();
        });
    });
});