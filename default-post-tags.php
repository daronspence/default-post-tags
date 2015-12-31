<?php
/**
 * Plugin Name: Default Post Tags
 * Description: Adds default tags to your the <code>post_tag</code> taxonomy when creating a new post.
 * Author: Daron Spence
 * Version: 1.0
 * Author URI: http://daronspence.com/
 * Text Domain: dgs-default-post-tags
 */

function dgs_default_tag_php_version(){ ?>
	<div class="error notice">
		<p><?php _e('The Default Tags Plugin requires PHP Version 5.3.0 or higher. Please contact your hosting provider to upgrade.', 'dgs-default-post-tags' ); ?></p>
	</div>
<?php }

if ( version_compare(PHP_VERSION, '5.3.0') <= 0) {
	add_action( 'admin_notices', 'dgs_default_tag_php_version' );
	// don't run the plugin
	return;
}

if ( ! class_exists( 'DGS_Default_Post_Tags' ) ) :

class DGS_Default_Post_Tags {

	function __construct() {
		$this->init();
	}

	private function init() {
		add_action( 'admin_init', array( $this, 'show_admin_notices' ), 10 );
		add_action( 'admin_footer', array( $this, 'render_footer_js' ), 1 );

		add_action( 'after-post_tag-table', array( $this, 'save_default_tags' ), 9 );
		add_action( 'after-post_tag-table', array( $this, 'render_view' ), 10 );
	}

	function show_admin_notices(){

		if ( isset( $_POST['dgs-default-post-tags'] ) ) {

			add_action( 'admin_notices', function(){ ?>
				
				<div class="updated notice is-dismissible">
					<p><?php _e('Default tags updated.', 'dgs-default-post-tags' ); ?></p>
				</div>

			<?php });
			
		}

	}

	function render_footer_js(){

		global $post;
		global $pagenow;
		
		$pages = array('post-new.php');

		// Bail early if we are not on an edit screen and editng a blog post
		if ( ! in_array($pagenow, $pages ) || $post->post_type !== 'post' )
			return;

		$new_default_tags = get_option('dgs_default_post_tags');

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

	}

	function render_view(){

		?>

		<br />
		<hr />
		
		<form method="POST" action="<?php echo wp_nonce_url( admin_url('edit-tags.php?taxonomy=post_tag'), 'dgs-save-post-tags' ); ?>" class="form-wrap">
			<div class="form-field">
				<label for="dgs-default-post-tags"><?php _e( 'Default Tags', 'dgs-default-post-tags' ); ?></label>
				<textarea cols="2" name="dgs-default-post-tags"><?php echo esc_html( get_option('dgs_default_post_tags') ); ?></textarea>
				<p><?php _e( 'Use a comma seperated list.', 'dgs-default-post-tags' ); ?></p>
			</div>

			<input type="submit" class="button button-primary" value="<?php _e('Set Default Tags', 'dgs-default-post-tags'); ?>" />

		</form>
		<br />
		<br /><?php

	}

	function save_default_tags(){

		if ( isset( $_REQUEST['dgs-default-post-tags'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'dgs-save-post-tags' ) ){
			update_option( 'dgs_default_post_tags', trim( esc_html( $_REQUEST['dgs-default-post-tags'] ) ) );
		}

	}
}

endif;

$GLOBALS['dgs_default_post_tags'] = new DGS_Default_Post_Tags();

