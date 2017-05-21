<?php
/**
 * Plugin Name:     Hide Post
 * Plugin URI:      https://www.brainstormforce.com
 * Description:     Hide post from dispying anywhere on the site, only access the post with a direct link to the post.
 * Author:          Brainstorm Force
 * Author URI:      https://www.brainstormforce.com
 * Text Domain:     hide-post
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Hide_Post
 */

defined( 'ABSPATH' ) or exit;

define( 'ODA_DIR', plugin_dir_path( __FILE__ ) );
define( 'ODA_URI', plugins_url( '/', __FILE__ ) );
define( 'ODA_VER', '0.1.0' );


/**
 * Hide_Posts setup
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Hide_Posts' ) ) {
	class Hide_Posts {

		/**
		 * Instance of Hide_Posts
		 *
		 * @since  1.0.0
		 * @var Hide_Posts
		 */
		private static $_instance = null;

		/**
		 * Instance of Hide_Posts
		 *
		 * @since  1.0.0
		 * @return Hide_Posts Instance of Hide_Posts
		 */
		public static function instance() {
			if ( ! isset( self::$_instance ) ) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			$this->includes();
		}

		/**
		 * Initialize the plugin only when the WP_Query is ready.
		 *
		 * @since  1.0.0
		 */
		public function init() {
			$hidden_posts = get_option( 'hf_hide_post', array() );

			// bail if none of the posts are hidden or we are on admin page or singular page.
			if ( is_admin() || is_singular() || empty( $hidden_posts ) ) {
				return '';
			}

			add_filter( 'posts_where', array( $this, 'where_clause' ), 20, 2 );
			add_filter( 'get_next_post_where', array( $this, 'post_navigation_clause' ), 20, 1 );
			add_filter( 'get_previous_post_where', array( $this, 'post_navigation_clause' ), 20, 1 );
		}

		/**
		 * Include required files.
		 *
		 * @since 1.0.0
		 */
		public function includes() {

			if ( is_admin() ) {
				require_once ODA_DIR . 'admin-functions.php';
			}

		}

		/**
		 * Filter where clause to hide selected posts.
		 *
		 * @since  1.0.0
		 *
		 * @param  String $where Where clause.
		 * @param  WP_Query $query WP_Query &$this The WP_Query instance (passed by reference).
		 *
		 * @return String $where Where clause.
		 */
		function where_clause( $where, $query ) {
			global $wpdb;
			$where .= ' AND ' . $wpdb->prefix . 'posts.ID NOT IN ( ' . esc_sql( $this->hidden_post_string() ) . ' )';

			return $where;
		}

		/**
		 * Filter post navigation query to hide the selected posts.
		 *
		 * @since  1.0.0
		 *
		 * @param  String $where Where clause.
		 *
		 * @param  String $where Where clause.
		 */
		function post_navigation_clause( $where ) {
			$where .= ' AND p.ID NOT IN ( ' . esc_sql( $this->hidden_post_string() ) . ' )';

			return $where;
		}

		/**
		 * Convert the array of posts to comma separated string to make it compatible to wpdb query.
		 *
		 * @since  1.0.0
		 *
		 * @return String Comma separated string of post id's.
		 */
		function hidden_post_string() {
			$hidden_posts = get_option( 'hf_hide_post', array() );

			return implode( ', ', $hidden_posts );
		}

	}

	Hide_Posts::instance();
}