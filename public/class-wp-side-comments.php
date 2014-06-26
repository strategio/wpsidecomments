<?php
/**
 * WP_Side_Comments
 *
 * @package   WP_Side_Comments
 * @author    Pierre SYLVESTRE <pierre@strategio.fr>
 * @license   GPL-2.0+
 * @link      http://www.strategio.fr
 * @copyright 2014 Strategio
 */

class WP_Side_Comments {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-side-comments';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'wp_ajax_addWPSideComment', array( $this, 'add_comment' ) );
		add_action( 'wp_ajax_nopriv_addWPSideComment', array( $this, 'add_comment' ) );
		add_action( 'wp_ajax_removeWPSideComment', array( $this, 'remove_comment' ) );
		add_action( 'wp_ajax_nopriv_removeWPSideComment', array( $this, 'remove_comment' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// Just for posts, pages and CPTs
		if(!is_singular())
			return;

		wp_enqueue_style( $this->plugin_slug . '-vendor', plugins_url( 'assets/vendor/side-comments/release/side-comments.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_style( $this->plugin_slug . '-vendor-default', plugins_url( 'assets/vendor/side-comments/release/themes/default-theme.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;

		// Just for posts, pages and CPTs
		if(!is_singular())
			return;

		$settings = get_option($this->plugin_slug.'-settings');

		wp_enqueue_script( $this->plugin_slug . '-vendor', plugins_url( 'assets/vendor/side-comments/release/side-comments.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-app', plugins_url( 'assets/js/app.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-vendor' ), self::VERSION );

		wp_localize_script($this->plugin_slug . '-app', 'wpSideComments', array(
				'url' => admin_url( 'admin-ajax.php' ),
				'contentSelector' => $settings['contentSelector'],
				'commentSelector' => $settings['commentSelector'],
				'currentUser' => $this->get_user(),
				'existingComments' => $this->get_comments(),
				'postId' => $post->ID,
				'wpNonce' => wp_create_nonce( 'wpSideComments' ),
				'loginLink' => wp_login_url(),
				'commentsAllowed' => comments_open($post->ID),
				'translations' => array(
						'leaveAComment' => __('Leave a comment', $this->plugin_slug ),
						'post' => __('Post', $this->plugin_slug ),
						'cancel' => __('Cancel', $this->plugin_slug ),
						'logInOrSignInToComment' => __('Login or Sign in to comment', $this->plugin_slug),
						'commentsAreNotAllowed' => __('Comments are not allowed', $this->plugin_slug)
					)
			));
	}


	/**
	 * Add a new comment (AJAX action)
	 *
	 * @since    1.0.0
	 */
	public function add_comment() {

		// Prevent CSRF
		if(!isset($_POST['wpNonce']) ||!wp_verify_nonce( $_POST['wpNonce'], 'wpSideComments' ))
			return false;

		// sectionId, comment, authorAvatarUrl, authorName, authorId
		if(!isset($_POST['postId']) || !isset($_POST['sectionId']) || !isset($_POST['comment']) || !isset($_POST['authorAvatarUrl']) || !isset($_POST['authorName']) || !isset($_POST['authorId']))
			return false;

		$user = wp_get_current_user();
		$time = current_time('mysql');

		$data = array(
		    'comment_post_ID' => $_POST['postId'],
		    'comment_author' => $user->data->display_name,
		    'comment_author_email' => $user->data->user_email,
		    'comment_author_url' => '',
		    'comment_content' => $_POST['comment'],
		    'comment_type' => '',
		    'comment_parent' => 0,
		    'user_id' => $_POST['authorId'],
		    'comment_author_IP' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
		    'comment_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
		    'comment_date' => $time,
		    // 'comment_approved' => 1,
		);

		$comment_id = wp_new_comment($data);

		add_comment_meta( $comment_id, 'sectionId', $_POST['sectionId'], true);

		echo $comment_id;
		die();
	}


	/**
	 * Remove a comment (AJAX action)
	 *
	 * @since    1.0.0
	 */
	public function remove_comment() {
		// @TODO
	}



	/**
	 * Get Current User Data
	 *
	 *	var currentUser = {
	 *	  "id": 1,
	 *	  "avatarUrl": "users/avatars/user1.png",
	 *	  "name": "Jim Jones"
	 *	};
	 */
	private function get_user() {
		$user = wp_get_current_user();

		if($user->ID != 0) {
			$currentUser = array(
					'id' => $user->ID,
					'avatarUrl' => $this->get_avatar_url(get_avatar($user->data->user_email)),
					'name' => $user->data->display_name
				);
		} else {
			$currentUser = null;
		}


		return $currentUser;
	}

	/**
	 * To get just the link to gravatar image
	 */
	private function get_avatar_url($get_avatar){
	    preg_match("/src='(.*?)'/i", $get_avatar, $matches);
	    return $matches[1];
	}

	/**
	 * Get Post Comments
	 *
	 *	var existingComments = [
	 *	  {
	 *	    "sectionId": "1",
	 *	    "comments": [
	 *	      {
	 *	        "authorAvatarUrl": "http://f.cl.ly/items/1W303Y360b260u3v1P0T/jon_snow_small.png",
	 *	        "authorName": "Jon Sno",
	 *	        "comment": "I'm Ned Stark's bastard. Related: I know nothing."
	 *	      },
	 *	      {
	 *	        "authorAvatarUrl": "http://f.cl.ly/items/2o1a3d2f051L0V0q1p19/donald_draper.png",
	 *	        "authorName": "Donald Draper",
	 *	        "comment": "I need a scotch."
	 *	      }
	 *	    ]
	 *	  },
	 */
	private function get_comments() {
		global $post, $wpdb;

		$query = $wpdb->prepare( "SELECT *
				FROM $wpdb->comments c
				LEFT JOIN $wpdb->commentmeta cm ON c.comment_ID = cm.comment_id
				LEFT JOIN $wpdb->users u ON c.user_id = u.ID
				WHERE c.comment_post_ID = %s
					AND c.comment_approved = 1
				",
				$post->ID);

		$comments = $wpdb->get_results($query);

		$existingComments = array();
		foreach ($comments as $k => $comment) {
			// Just care about sectionId meta's OR empty ones
			if($comment->meta_key != 'sectionId' && !empty($comment->meta_key))
				continue;

			// If no section defined, put in the first one (start form 0)
			if(!isset($comment->meta_value) || empty($comment->meta_value))
				$sectionId = '0';
			else
				$sectionId = $comment->meta_value;

			// Check section
			if(!isset($existingComments[$sectionId])) {
				$existingComments[$sectionId] = new stdClass();
				$existingComments[$sectionId]->sectionId = $sectionId;
			}

			$existingComments[$sectionId]->comments[] = array(
					'authorAvatarUrl'=> !empty($comment->user_email) ? $this->get_avatar_url(get_avatar($comment->user_email)) : $this->get_avatar_url(get_avatar($comment->comment_author_email)),
					'authorName'=> !empty($comment->display_name) ? $comment->display_name : $comment->comment_author,
					'comment'=> $comment->comment_content
				);

		}

		$existingComments = array_values($existingComments);

		return $existingComments;
	}

}
