( function($, window) {

    $( function() {
        let wpflLoginForm = $('#wpfl-login-form');

        wpflLoginForm.on('submit', function(event) {
            event.preventDefault();

            wpflLoginForm.find( '.wpfl-submit' ).prop( 'disabled', true );

            let data = {
                _ajax_nonce: wpfl_login_form_obj.nonce,
                action: 'wpfl_login_form',
                form_data: $( this ).serialize(),
                querystring: location.search
            }
            
            $.post( wpfl_login_form_obj.ajax_url, data )
            .done( function( response ) {
                console.log( response );
                if ( response.success == true ) {
                    window.location.replace( response.data.redirect_url );
                }
            })
            .always( function() {
                wpflLoginForm.find( '.wpfl-submit' ).prop( 'disabled', false );
            })

        });
    } );

} )( jQuery, window );