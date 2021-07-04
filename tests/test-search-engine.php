<?php
/**
 * Class SearchEngine
 *
 * @package Unlist_Posts
 */

/**
 * Make sure unlisted posts are hidden from search engine.
 */
class SearchEngine extends WP_UnitTestCase {

	/**
	 * User ID for a editor user..
	 *
	 * @var int
	 */
	private $editor_user_id;

	/**
	 * Setup the tests class.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->editor_user_id = self::factory()->user->create(
			array(
				'role' => 'editor',
			)
		);
	}

	/**
	 * Check search engine robots tag for unlisted post.
	 */
	public function test_unlisted_noindex() {
		_delete_all_posts();
		wp_set_current_user( $this->editor_user_id );

		// Create an unlisted post.
		$unlisted_post = self::factory()->post->create();

		// Set the post as unlisted.
		$_POST['unlist_posts']       = true;
		$_POST['unlist_post_nounce'] = wp_create_nonce( 'unlist_post_nounce' );
		Unlist_Posts_Admin::instance()->save_meta( $unlisted_post );

		// Set unlisted post as a current post.
		$GLOBALS['post'] = get_post( $unlisted_post );

		// Set default contain_noindex to false.
		$contain_noindex = false;

		// Get wp_robots string of a current post.
		ob_start();
		wp_robots();
		$output = ob_get_clean();

		// Set default contain_noindex to false.
		$contain_noindex = false;

		// Set contain_noindex to true, if it adds noindex string to meta.
		if ( false !== strpos( $output, 'noindex' ) ) {
			$contain_noindex = true;
		}

		// Unlisted post should contain noindex string in meta.
		$this->assertEquals( $contain_noindex, true );
	}

	/**
	 * Check search engine robots tag for listed post.
	 */
	public function test_listed_noindex() {
		_delete_all_posts();
		wp_set_current_user( $this->editor_user_id );

		// Create an listed post.
		$listed_post = self::factory()->post->create();

		// Set listed post as a current post.
		$GLOBALS['post'] = get_post( $listed_post );

		// Set default contain_noindex to false.
		$contain_noindex = false;

		// Get wp_robots string of a current post.
		ob_start();
		wp_robots();
		$output = ob_get_clean();

		// Set default contain_noindex to false.
		$contain_noindex = false;

		// Set contain_noindex to true, if it adds noindex string to meta.
		if ( false !== strpos( $output, 'noindex' ) ) {
			$contain_noindex = true;
		}

		// Listed post should not contain noindex string in meta.
		$this->assertEquals( $contain_noindex, false );
	}
}
