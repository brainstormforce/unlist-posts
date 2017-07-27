<?php
/**
 * Admin functions for the plugin.
 *
 * @package  hide-post
 */

defined( 'ABSPATH' ) or exit;

/**
 * Unlist_Posts_Admin setup
 *
 * @since 1.0
 */
class Unlist_Posts_Admin {

	/**
	 * Instance of Unlist_Posts_Admin
	 *
	 * @var Unlist_Posts_Admin
	 */
	private static $_instance = null;

	/**
	 * Instance of Unlist_Posts_Admin
	 *
	 * @return Unlist_Posts_Admin Instance of Unlist_Posts_Admin
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );
	}

	/**
	 * Register meta box(es).
	 */
	function register_metabox() {
		$args = array(
			'public' => true,
		);

		$post_types = get_post_types( $args, 'names', 'and' );

		add_meta_box(
			'ehf-meta-box', __( 'Unlist Post', 'unlist-posts' ), array(
				$this,
				'metabox_render',
			), $post_types, 'side', 'high'
		);
	}

	/**
	 * Render Meta field.
	 *
	 * @param  POST $post Currennt post object which is being displayed.
	 */
	function metabox_render( $post ) {

		$hidden_posts   = get_option( 'unlist_posts', array() );

		if ( '' == $hidden_posts ) {
			$hidden_posts = array();
		}

		$checked        = '';

		if ( in_array( $post->ID, $hidden_posts ) ) {
			$checked = 'checked';
		}

		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'unlist_post_nounce', 'unlist_post_nounce' );
		?>
		<p>
			<label class="checkbox-inline">
				<input name="unlist_posts" type="checkbox" <?php echo esc_attr( $checked ); ?> value=""><?php _e( 'Unlist this post?', 'unlist-posts' ); ?>
			</label>
		</p>
		<p class="description"><?php _e( 'This will hide the post from your site, The post can only be accessed from direct URL.', 'unlist-posts' ); ?> </p>
		<?php
	}

	/**
	 * Save meta field.
	 *
	 * @param  POST $post_id Currennt post object which is being displayed.
	 *
	 * @return Void
	 */
	public function save_meta( $post_id ) {
		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['unlist_post_nounce'] ) || ! wp_verify_nonce( $_POST['unlist_post_nounce'], 'unlist_post_nounce' ) ) {
			return;
		}

		// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$hidden_posts = get_option( 'unlist_posts', array() );

		if ( '' == $hidden_posts ) {
			$hidden_posts = array();
		}

		if ( isset( $_POST['unlist_posts'] ) ) {
			$hidden_posts[] = $post_id;

			// Get only the unique post id's in the option array.
			$hidden_posts   = array_unique( $hidden_posts );
		} elseif ( in_array( $post_id, $hidden_posts ) ) {

			// Get only the unique post id's in the option array.
			$hidden_posts   = array_unique( $hidden_posts );

			$key = array_search( $post_id, $hidden_posts );
			unset( $hidden_posts[ $key ] );
		}

		update_option( 'unlist_posts', $hidden_posts );
	}

}

Unlist_Posts_Admin::instance();
