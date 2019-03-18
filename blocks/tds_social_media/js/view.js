/**
 * TDS social media block view.
 *
 * Copyright 2019 - TDSystem Beratung & Training - Thomas Dausner
 *
 *
 * global TdsSocialMedia
 */
if ( typeof TdsSocialMedia === "undefined" )
{
    TdsSocialMedia = {};
}

TdsSocialMedia.init = function ( bUID, bubbleText ) {
    var $allButtons = $( '.ccm-block-tds-social-media.block-' + bUID + ' .svc span' );
    var $bubble = $( '.ccm-block-tds-social-media.block-' + bUID + ' .speech-bubble' );
    var bubbleText = bubbleText.replace( /\&lt;strong\&gt;\s*X\s*\&lt;\/strong\&gt;/i,
        '<i class="fa fa-times"></i>' );
    var $btn = null;
    /*
     * close all buttons handler
     *
     * @returns {undefined}
     */
    $( 'html' ).click( function () {
        if ( $allButtons.hasClass( 'activated' ) )
        {
            $allButtons.removeClass( 'activated' );
            $bubble.hide();
        }
    } );
    $( $bubble ).click( function ( e ) {
        e.stopPropagation();
    } );
    /*
     * set position of bubble (and arrow)
     */
    var showBubble = function () {
        if ( $allButtons.hasClass( 'activated' ) )
        {
            // reset bubble arrow class
            var arrow = ['left', 'center', 'right'];
            for ( var i = 0; i < arrow.length; i++ )
            {
                $bubble.removeClass( arrow[i] );
            }
            // get horizontal bubble position
            var docWidth = $( document ).outerWidth( true );
            var $iCont = $btn.parents( '.icon-container' );
            var bubblePos = $iCont.find( '.svc:first-child' ).offset();
            var bbLeft = bubblePos.left;
            if ( bbLeft + 310 > docWidth )
            {
                bbLeft = docWidth - 310;
            }
            // determine arrow position and arrow class
            var width = $btn.innerWidth();
            var arrowOffs = $btn.offset().left - bbLeft;
            var i = 0;
            while ( i * 100 <= arrowOffs )
            {
                i++;
            }
            i = ( i > 3 ) ? 3 : i;
            if ( arrowOffs > 260 )
            {
                var delta = arrowOffs - 260;
                bbLeft += delta;
                arrowOffs -= delta;
            }
            // set/show bubble and arrow
            $bubble
                .addClass( arrow[i - 1] )
                .css( {
                    'top': ( -1 * ( bubblePos.top - $btn.offset().top + $bubble.outerHeight()
                        - parseInt( $iCont.css( 'paddingTop' ) ) + 32 - 8 ) ) + 'px',
                    'left': ( bbLeft - $iCont.offset().left ) + 'px'
                } )
                .show();
            switch ( i )
            {
                case 1:
                    arrowOffs += width - 8;
                    break;
                case 2:
                    arrowOffs += width / 2 - 12;
                    break;
                case 3:
                    arrowOffs += -24 + 8;
                    break;
            }
            $( 'span', $bubble ).css( {
                'left': arrowOffs + 'px'
            } );
        }
    };
    $( window ).resize( showBubble );
    /*
     * button click handler
     *
     * @returns {undefined}
     */
    $allButtons.click( function ( e ) {
        e.stopPropagation();
        $btn = $( this );
        if ( $btn.hasClass( 'local' ) )
        {
            window.open( $btn.data( 'href' ), '_self' );
            $allButtons.removeClass( 'activated' );
            $bubble.hide();
        }
        else if ( $btn.hasClass( 'activated' ) )
        {
            if ( $( ':visible', $bubble ).length > 0 )
            {
                window.open( $btn.data( 'href' ), $btn.data( 'target' ) );
            }
            $allButtons.removeClass( 'activated' );
            $bubble.hide();
        }
        else
        {
            // activate just clicked button
            $allButtons.removeClass( 'activated' );
            $btn.addClass( 'activated' );
            // set bubble text, check box and set check box change handler
            $( 'label', $bubble ).html( bubbleText.replace( /%s/g, $btn.data( 'key' ) ) );
            $( 'button', $bubble ).click( function () {
                $btn.removeClass( 'activated' );
                $bubble.hide();
            } );
            showBubble();
        }
    } );
};
