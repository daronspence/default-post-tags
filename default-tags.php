<?php
/**
 * Plugin Name: Default Tags
 * Description: Adds default tags to your the <code>post_tag</code> taxonomy when creating a new post.
 * Author: Daron Spence
 * Version: 1.0
 * Author URI: http://daronspence.com/
 * Text Domain: dgs-default-tags
 */

function dgs_default_tag_php_version(){ ?>
	<div class="error notice">
		<p><?php _e('The Default Tags Plugin requires PHP Version 5.3.0 or higher. Please contact your hosting provider to upgrade.', 'dgs-default-tags' ); ?></p>
	</div>
<?php }

if ( version_compare(PHP_VERSION, '5.3.0') <= 0) {
	add_action( 'admin_notices', 'dgs_default_tag_php_version' );
	// don't run the plugin
	return;
}

add_action( 'admin_init', function(){

	if ( isset( $_POST['dgs-default-tags'] ) ) {

		add_action( 'admin_notices', function(){ ?>
			
			<div class="updated notice is-dismissible">
				<p><?php _e('Default tags updated.', 'dgs-default-tags' ); ?></p>
			</div>

		<?php });
		
	}

});

add_action( 'after-post_tag-table', function( $tax ){
	global $pagenow;

	if ( isset( $_POST['dgs-default-tags'] ) ){
		update_option( 'dgs_default_tags', esc_html( $_POST['dgs-default-tags'] ) );
	}

	?>
	<br />
	<hr />
	
	<form method="POST" action="edit-tags.php?taxonomy=post_tag" class="form-wrap">
		<div class="form-field">
			<label for="dgs-default-tags">Default Tags</label>
			<textarea cols="2" name="dgs-default-tags"><?php echo esc_html( get_option('dgs_default_tags') ); ?></textarea>
			<p>Use a comma seperated list.</p>
		</div>

		<input type="submit" class="button button-primary" value="<?php _e('Set Default Tags', 'dgs-default-tags'); ?>" />

	</form><?php

} );

add_action( 'admin_footer', function(){
	global $post;
	global $pagenow;
	
	$pages = array('post-new.php');

	// Bail early if we are not on an edit screen and editng a blog post
	if ( ! in_array($pagenow, $pages ) || $post->post_type !== 'post' )
		return;

	$new_default_tags = get_option('dgs_default_tags');

	if ( empty($new_default_tags) )
		$new_default_tags = "blah, blurb, blehga";

	?>
		<script type="text/javascript">

			(function(){

				var newDefaultTags = '<?php echo $new_default_tags; ?>';

				var $defaultTagEl = jQuery('#tax-input-post_tag');

				var $defaultTagVal = jQuery.trim( $defaultTagEl.val() );

				$defaultTagEl.val( $defaultTagVal + "," + newDefaultTags );

			})();

		</script>

	<?php

}, 1 );