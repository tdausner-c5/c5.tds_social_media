<?php defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Facade::getFacadeApplication();
$color = $app->make('helper/form/color');

$hidden = $mediaType == 'undef' ? 'hidden' : '';
echo '<div class="form-group pull-left media-select ' . ($mediaType == 'undef' ? '' : 'hidden') . '">',
$form->label('mediaType', t('Select social media.')),
$form->select('mediaType', $mdTypes, $mediaType),
    '<div class="comment">'
    . t('This is a one way select. To change type of social media you must delete '
        . 'this block and add a new one.'),
    '</div>
     </div>
     <div class="media-types ' . $hidden . '">';

echo
$app->make('helper/concrete/ui')->tabs([
    ['service', t('Social media services') . ' <span>(' . ($mediaType == 'undef' ? '' : t($mediaType)) . ')</span>', true],
    ['colorstyle', t('Color and style')],
]),

$this->controller->getIconStylesExpanded(0), '


<div id="ccm-tab-content-service" class="ccm-tab-content ccm-block-tds-social-media block-' . $bUID . '">

	<div class="form-group pull-left half">
		', $form->label('linkTarget', t('Open Links in...')),
$form->select('linkTarget', $targets, $linkTarget), '
	</div>
	<div class="form-group pull-right half">',
$form->label('align', t('Icon orientation')),
$form->select('align', $orientation, $align), '
	</div>
	<div class="clearfix"></div>
 
	<div class="form-group">
		<ul id="sortable">';

$preview = '';
foreach ($this->controller->getMediaList() as $svc => $props)
{
    $checked = !empty($props['checked']);
    if (!empty($props['ph']) && $mediaType != 'share')
    { #------------------ visit
        echo '
		<li id="lv_' . $svc . '" class="ui-state-default visit ' . $hidden . '">',
        $form->checkbox("mediaList[$svc][checked]", $svc, $checked),
        $form->label($svc, t($svc)), '
			<button type="button" class="btn pull-right btn-primary edit">' . t('URL') . '</button>
			<div class="input-group hidden">
				<input id="', $svc, '" type="text" name="mediaList[' . $svc . '][url]" value="' . $props['url'] . '"',
            ' placeholder="' . $props['ph'] . '" class="form-control ccm-input-text" data-regex="' . $props['rx'] . '">
				<button type="button" class="btn pull-left btn-primary cancel"><i class="fa fa-close"></i></button>
				<button type="button" class="btn pull-right btn-primary check"><i class="fa fa-check"></i></button>
			</div>
		</li>';

        $preview .= '
		<li id="pv' . $svc . '" class="icon-box' . ($checked ? '' : ' hidden') . '" title="' . $svc . '">
			' . $props['visit-icon'] . '
		</li>';
    }
    if (!empty($props['sa']) && $mediaType != 'visit')
    { #------------------ share
        echo '
		<li id="ls_' . $svc . '" class="ui-state-default share ' . $hidden . '">',
        $form->checkbox("mediaList[$svc][checked]", $svc, $checked),
        $form->label($svc, t($svc)), '
		</li>';

        $preview .= '
		<li id="ps' . $svc . '" class="icon-box' . ($checked ? '' : ' hidden') . '" title="' . $svc . '">
			' . $props['share-icon'] . '
		</li>';
    }
}

echo '
		</ul>
	</div>
</div>

<div class="ccm-tab-content ccm-block-tds-social-media block-0" id="ccm-tab-content-colorstyle" style="position: relative; height: 475px;">

	<div id="icon-set-container" class="form-group pull-left">',

        $form->label('iconShape', t('Icon shape')),
        $form->select('iconShape', ['round' => t('round'), 'square' => t('square')], $iconShape),

        '<div class="lineup">',
            $form->label('iconStyle', t('Icon style')), '
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="',
            t("'logo / logo inverse' selects the social media own color(s)") . '"></i>
		</div>',
        $form->select('iconStyle', $iconStyleList, $iconStyle),

        '<div class="color-sel">',
            $form->label('iconColor', t('Icon color')),
            $color->output('iconColor', $iconColor, ['preferredFormat' => 'hex']),
        '</div>
	
		<div class="lineup">',
            $form->label('iconSize', t('Icon size')), '
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="', t('Icon size [20px...200px].') . '"></i>
		</div>
		<div class="input-group">',
            $form->number('iconSize', $iconSize, ['min' => '20', 'max' => '200', 'style' => 'text-align: center;']), '
			<span class="input-group-addon">px</span>
		</div>',

        $form->label('hoverIcon', t('Icon hover color')),
        $color->output('hoverIcon', $hoverIcon, ['preferredFormat' => 'hex']),

        $form->label('activeIcon', t('Icon activated color')),
        $color->output('activeIcon', $activeIcon, ['preferredFormat' => 'hex']),

        '<div class="lineup">',
            $form->label('iconMargin', t('Icon spacing')), '
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="', t('Space between icons (margin left + right) [0...50px].') . '"></i>
		</div>
		<div class="input-group">',
            $form->number('iconMargin', $iconMargin, ['min' => '0', 'max' => '50', 'style' => 'text-align: center;']), '
			<span class="input-group-addon">px</span>
		</div>

	</div>

	<div id="icon-preview-container" class="form-group pull-right">
    
		<div class="lineup">',
            $form->label('titleText', t('Icon hover title') . '
			    <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="' .
                    h(t('The expression "%s" is replaced by the social service name.', '%s')) . '"></i>'), '
		</div>',
            $form->text('titleText', $titleText), '

		<div class="lineup">',
            $form->label('bubbleText', t('Bubble text') . '
                <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="' .
                    t('This is the text popping up in a bubble on clicking at a social media "visit" icon') . '"></i>'), '
			<div class="bubbletext">
				<button type="button" class="btn pull-right btn-primary edit">', t('Edit'), '</button>
				<div class="input-group hidden">
					<div class="lineup">
						<label class="control-label">', t('Bubble text on clicking at a social media "visit" icon') . ' 
                            <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="',
                                h(t('The expression "%s" is replaced by the social service name.', '%s')) . '"></i></label>
					</div>
					<div class="ta">',
                        $form->textarea('bubbleText', $bubbleText), '
					</div>
					<button type="button" title="', t('Reset bubble text to recommended default.'), '" 
																	class="btn pull-left btn-primary undo"><i class="fa fa-undo"></i></button>
					<button type="button" title="', t('Save'), '" class="btn pull-right btn-primary save"><i class="fa fa-check"></i></button>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

		<label class="control-label">', t('Icon Preview'), '</label>
		<ul>
			', $preview, '
		</ul>
	</div>

</div>

</div>'; // class="media-types"

?>

<script type="text/javascript">
    ( function ( $ ) {

        var data = {
            '#titleText': <?php echo json_encode($titleTextTemplate, JSON_PARTIAL_OUTPUT_ON_ERROR); ?>,
            '#bubbleText': <?php echo json_encode($bubbleTextTemplate, JSON_PARTIAL_OUTPUT_ON_ERROR); ?>,
            '.ccm-ui .nav-tabs a[data-tab=service] span': {
                'visit': '(<?php echo t('visit'); ?>)',
                'share': '(<?php echo t('share'); ?>)',
            }
        };

        /*
         * set titleText & bubbleText according to mediaType
         */
        var setTitleAndBubble = function ( mediaType ) {
            for ( var key in data )
            {
                var $fld = $( key );
                if ( key === '#titleText' )
                {
                    if ( $fld.val() == '' )
                    {
                        $fld.val( data[key][mediaType] );
                    }
                }
                else
                {
                    if ( $fld.text() == '' )
                    {
                        $fld.text( data[key][mediaType] );
                    }
                }
            }
        };

        $( document ).ready( function () {
            window.initIconStyles( '<?php echo str_replace([ "\r", "\n" ], [ '', '' ], $this->controller->getIconStyles(0)) ?>' );

            /*
             * mediaType selector change handler and/or mediaType initialization
             */
            var mediaType = 'undef';
            var mediaChange = function () {
                mediaType = $( '#mediaType' ).val();
                $( '.media-select' ).hide();
                $( '.media-types, #sortable li.' + mediaType ).removeClass( 'hidden' );
                setTitleAndBubble( mediaType );
            };
            if ( $( '#mediaType' ).val() === 'undef' )
            {   // add block
                $( '#mediaType' ).change( mediaChange );
            }
            else
            {   // edit block
                mediaType = $( '#mediaType' ).val();
                mediaChange();
            }
            /*
             * service checkbox click handler --> preview
             */
            $( '#ccm-tab-content-service .ccm-input-checkbox' ).change( function () {
                var $preview = $( '#p' + mediaType.substr( 0, 1 ) + $( this ).val() );
                if ( $( this ).prop( 'checked' ) )
                {
                    $preview.removeClass( 'hidden' );
                }
                else
                {
                    $preview.addClass( 'hidden' );
                }
            } );
            /*
             * open bubbleText edit modal
             */
            $( 'button.edit' ).click( function () {
                $( this ).next().removeClass( 'hidden' );
                var $txt = $( this ).parent().find( 'textarea' );
                if ( $txt.text() === '' )
                {
                    $txt.text( data['#bubbleText'][mediaType] );
                }
                $txt.focus();
            } );
            /*
             * undo bubbleText edit modal
             */
            $( 'button.undo' ).click( function () {
                $( this ).parent()
                    .find( 'textarea' )
                    .text( data['#bubbleText'][mediaType] )
                    .focus()
                ;
                $( 'button.save' ).prop( 'disabled', false );
            } );
            /*
             * save bubbleText edit modal
             */
            $( 'button.save' ).click( function () {
                $( this ).parent().addClass( 'hidden' );
            } );
            /*
             * bubbleText change handler
             */
            $( '#bubbleText' ).change( function () {
                $( 'button.save' ).prop( 'disabled', $( this ).val() === '' );
            } );
            /*
             * open URL edit modal
             */
            $( 'button.edit' ).click( function () {
                $( 'button.edit' ).next().addClass( 'hidden' );
                $( this ).next().removeClass( 'hidden' );
                var $txt = $( this ).parent().find( 'input[type=text]' );
                if ( $txt.val() === '' )
                {
                    $txt.val( $txt.attr( 'placeholder' ) );
                }
                $txt.focus();
            } );
            /*
             * close / cancel URL edit modal
             */
            $( 'button.cancel' ).click( function () {
                var $txt = $( this ).parent().find( 'input[type=text]' );
                $txt.val( '' );
                $( this ).parent().addClass( 'hidden' );
                $( this ).parent().parent().find( '.ccm-input-checkbox' ).prop( 'checked', '' );
                var $preview = $( '#p' + $txt.attr( 'id' ) );
                $preview.addClass( 'hidden' );
            } );
            /*
             * close / save URL edit modal
             */
            $( 'button.check' ).click( function () {
                $( this ).parent().addClass( 'hidden' );
                var $txt = $( this ).parent().find( 'input[type=text]' );
                $( this ).parent().parent().find( '.ccm-input-checkbox' ).prop( 'checked', $txt.val() !== '' ? 'checked' : '' );
                var $preview = $( '#p' + $txt.attr( 'id' ) );
                if ( $txt.val() !== '' )
                {
                    $preview.removeClass( 'hidden' );
                }
                else
                {
                    $preview.addClass( 'hidden' );
                }
            } );
            /*
             * setup URL validation
             */
            var checkUrl = function () {
                var $inp = $( this );
                var value = $inp.val().replace( /^\s+|\s+$/gm, '' );
                var regex = new RegExp( $inp.data( 'regex' ) );
                var match = value.match( regex );
                $inp.siblings( '.check' ).prop( 'disabled', match ? '' : 'disabled' );
                $inp.parent().parent().find( '.ccm-input-checkbox' ).prop( 'checked', match ? 'checked' : '' );
            };
            $( '#sortable input.ccm-input-text' )
                .each( checkUrl )
                .keyup( checkUrl );
            /*
             * click handler for form pseudo submit button
             */
            $( '#ccm-form-submit-button' ).click( function ( e ) {
                var checked = 0;
                var empty = [];
                $( '.ccm-block-tds-social-media #sortable li' ).each( function () {
                    if ( $( 'input[type=checkbox]', this ).prop( 'checked' ) )
                    {
                        $inp = $( 'input[type=text]', this );
                        if ( $inp.val() === '' )
                        {
                            empty.push( $inp.attr( 'id' ) );
                        }
                        checked++;
                    }
                } );
                if ( checked === 0 )
                {
                    ConcreteAlert.error( {
                        message: '<?php echo $messages["no_svc_selected"] ?>'
                    } );
                }
                else if ( empty.length !== 0 )
                {
                    ConcreteAlert.error( {
                        message: '<?php echo $messages["missing_urls"] ?>'.replace( /%s/, empty.join( ', ' ) ),
                        delay: 5000
                    } );
                }
                else
                {
                    return true;
                }
                e.preventDefault();
                return false;
            } );
        } );
    }( window.jQuery ) );
</script>
