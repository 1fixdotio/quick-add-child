<?php
/**
 * Quick Add Child.
 *
 * @package   Quick_Add_Child
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://1fix.io/quick-add-child
 * @copyright 2014 1Fix.io
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-quick-add-child-admin.php`
 *
 * @package Quick_Add_Child
 * @author  1fixdotio <1fixdotio@gmail.com>
 */
class Quick_Add_Child {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	const VERSION = '0.7.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'quick-add-child';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Display the admin notification
		add_action( 'admin_notices', array( $this, 'admin_notice_activation' ) );

		// Hook into the 'wp_before_admin_bar_render' action
		add_action( 'wp_before_admin_bar_render', array( $this, 'add_adminbar_menus' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    0.1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {

		return $this->plugin_slug;

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
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
	 * @since    0.1.0
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

				if ( $blog_ids ) {
					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_activate();

						restore_current_blog();
					}
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
	 * @since    0.1.0
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

				if ( $blog_ids ) {
					foreach ( $blog_ids as $blog_id ) {

						switch_to_blog( $blog_id );
						self::single_deactivate();

						restore_current_blog();

					}
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
	 * @since    0.1.0
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
	 * @since    0.1.0
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
	 * @since    0.5.0
	 */
	private static function single_activate() {

		if ( false == get_option( 'qac-display-activation-message' ) ) {
			add_option( 'qac-display-activation-message', true );
		}
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    0.5.0
	 */
	private static function single_deactivate() {

		delete_option( 'qac-display-activation-message' );

	}

	/**
	 * Display admin notice when activating the plugin.
	 *
	 * @since 0.5.0
	 */
	public function admin_notice_activation() {

		$screen = get_current_screen();

		if ( true == get_option( 'qac-display-activation-message' ) && 'plugins' == $screen->id ) {
			$plugin = self::get_instance();

			$html  = '<div class="updated">';
			$html .= '<p>';
				$html .= sprintf( __( 'Visit the <strong><a href="%s">Quick Add Child Settings</a></strong> page to enable advanced funtions from this plugin.', $plugin->get_plugin_slug() ), admin_url( 'options-general.php?page=' . $plugin->get_plugin_slug() ) );
			$html .= '</p>';
			$html .= '</div><!-- /.updated -->';

			echo $html;

			delete_option( 'qac-display-activation-message' );
		}

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.5.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_PLUGIN_DIR ) . 'quick-add-child/languages/' . $locale . '.mo' );

	}

	/**
	 * Add Adminbar Menus
	 *
	 * @since 0.7.0
	 */
	public function add_adminbar_menus() {

		global $wp_admin_bar, $post;

		if ( isset( $post ) && is_post_type_hierarchical( $post->post_type ) ) {
			$args = array(
				'id'     => 'qac-add-silbling',
				'parent' => 'new-content',
				'title'  => __( 'Add New Sibling', $this->plugin_slug ),
				'href'   => admin_url( 'post-new.php?post_type=' . $post->post_type . '&parent_id=' . $post->post_parent ),
				'meta'   => array(
					'target'   => '_blank',
				)
			);
			$wp_admin_bar->add_menu( $args );

			$args = array(
				'id'     => 'qac-add-child',
				'parent' => 'new-content',
				'title'  => __( 'Add New Child', $this->plugin_slug ),
				'href'   => admin_url( 'post-new.php?post_type=' . $post->post_type . '&parent_id=' . $post->ID ),
				'meta'   => array(
					'target'   => '_blank',
				)
			);
			$wp_admin_bar->add_menu( $args );
		}

	}

}
