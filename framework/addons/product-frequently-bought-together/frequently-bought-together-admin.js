( function ( $ ) {
    $( document ).on( 'click', '#wolmart_fbt_options .wolmart-fbt-checkbox', function ( e ) {
        var $this = $( this ),
            $input = $this.find( 'input[type="checkbox"]' ),
            checked = $input.prop( 'checked' );

        if ( checked ) {
            $input.attr( 'value', 'yes' );
        } else {
            $input.attr( 'value', 'no' );
        }
    } )

    $( window ).on( 'load', function () {
        $( '#wolmart_fbt_options' ).find( '[data-events]' ).each( function () {
            var $item = $( this ),
                actions = $item.attr( 'data-events' ).split( ',' ),
                values = $item.attr( 'data-value' ).split( ',' ),
                cond = [];

            $.each( actions, function ( i, action ) {
                $( '[name="' + action + '"]' ).on( 'change', function () {
                    var $this = $( this ),
                        value = $this.val();

                    if ( this.type == 'checkbox' ) {
                        value = $( this ).prop( 'checked' ) ? 'yes' : 'no';
                    }

                    cond[ i ] = values[ i ].indexOf( value );

                    if ( $.inArray( -1, cond ) === -1 ) {
                        $item.show();
                    } else {
                        $item.hide();
                    }
                } ).change();
            } )
        } )
    } )
} )( jQuery );