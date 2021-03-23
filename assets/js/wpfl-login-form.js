( function($, window) {

    $( function() {
        let wpflLoginForm = $('#wpfl-login-form');
        let wpflAlert = $('.wpfl-alert');

        wpflLoginForm.on('submit', function(event) {
            event.preventDefault();

            wpflLoginForm.find( '.wpfl-submit' ).prop( 'disabled', true );
            wpflAlert.removeClass('wpfl-alert-error wpfl-alert-success').html('').hide();

            let data = {
                _ajax_nonce: wpfl_login_form_obj.nonce,
                action: 'wpfl_login_form',
                form_data: $( this ).serialize(),
                querystring: location.search
            }
            
            $.post( wpfl_login_form_obj.ajax_url, data )
            .done( function( response ) {
                if ( response.success == true ) {
                    wpflAlert.addClass('wpfl-alert-success').html(response.data.message).show();
                    window.location.replace( response.data.redirect_url );
                } else {
                    wpflAlert.addClass('wpfl-alert-error').html(response.data.errors.join('<br>')).show();
                }
            })
            .always( function() {
                wpflLoginForm.find( '.wpfl-submit' ).prop( 'disabled', false );
            })

        });
    } );

} )( jQuery, window );