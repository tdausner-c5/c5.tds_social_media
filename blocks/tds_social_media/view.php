<?php defined('C5_EXECUTE') or die('Access Denied.');

echo $this->controller->getIconStylesExpanded($bUID);

?>

<div class="ccm-block-tds-social-media block-<?php echo $bUID ?>">
	<div class="icon-container <?php echo $align ?>-align">

<?php
foreach ($this->controller->getMediaList() as $key => $props)
{
	if (!empty($props['checked']))
		echo $props['html'];
}
?>
		<div class="speech-bubble">
            <button title="<?php echo h(t('Close bubble text and deactivate icon')) ?>" 
                    aria-label="<?php echo h(t('Close')) ?>"><i class="fa fa-times-circle-o"></i></button>
			<label></label>
			<span class="arrow"></span><span class="arrow-inner"></span>
		</div>
	</div>
</div>
<script type="text/javascript">
-( function( $ ) {
    $( document ).ready( function() {
        TdsSocialMedia.init( '<?php echo $bUID ?>', '<?php echo h($bubbleText) ?>' );
    } );
} )( window.jQuery );
</script>
