<?php
/**
 * Plugin Name:     Unlist Posts
 * Plugin URI:      https://github.com/Nikschavan/hide-post
 * Description:     Unlist Posts from dispying anywhere on the site, only access the post with a direct link.
 * Author:          Brainstorm Force
 * Author URI:      https://www.brainstormforce.com
 * Text Domain:     unlist-posts
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Hide_Post
 */

defined( 'ABSPATH' ) or exit;

define( 'UNLIST_POSTS_DIR', plugin_dir_path( __FILE__ ) );
define( 'UNLIST_POSTS_URI', plugins_url( '/', __FILE__ ) );
define( 'UNLIST_POSTS_VER', '0.1.0' );


/**
 * Unlist_Posts setup
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Unlist_Posts' ) ) {
	class Unlist_Posts {

		/**
		 * Instance of Unlist_Posts
		 *
		 * @since  1.0.0
		 * @var Unlist_Posts
		 */
		private static $_instance = null;

		/**
		 * Instance of Unlist_Posts
		 *
		 * @since  1.0.0
		 * @return Unlist_Posts Instance of Unlist_Posts
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
				require_once UNLIST_POSTS_DIR . 'admin-functions.php';
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
			$hidden_posts = get_option( 'unlist_posts', array() );

			// bail if none of the posts are hidden or we are on admin page or singular page.
			if ( is_admin() || $query->is_singular || empty( $hidden_posts ) ) {
				return $where;
			}

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
			$hidden_posts = get_option( 'unlist_posts', array() );

			// bail if none of the posts are hidden or we are on admin page or singular page.
			if ( is_admin() || in_array( get_the_ID(), $hidden_posts ) || empty( $hidden_posts ) ) {
				return $where;
			}

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
			$hidden_posts = get_option( 'unlist_posts', array() );

			return implode( ', ', $hidden_posts );
		}

	}

	Unlist_Posts::instance();
}