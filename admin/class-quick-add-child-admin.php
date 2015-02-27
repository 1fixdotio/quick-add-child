<?php
/**
 * Quick Add Child.
 *
 * @package   Quick_Add_Child_Admin
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://1fix.io/quick-add-child
 * @copyright 2014 1Fix.io
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-quick-add-child.php`
 *
 * @package Quick_Add_Child_Admin
 * @author  1fixdotio <1fixdotio@gmail.com>
 */
class Quick_Add_Child_Admin {

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * Call $plugin_slug from public plugin class later.
	 *
	 * @since    0.5.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = null;

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 */
		$plugin = Quick_Add_Child::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		require_once( plugin_dir_path( __FILE__ ) . 'includes/settings.php' );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Display the admin notification
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );

		add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'page_attributes_dropdown_pages_args' ), 10, 2 );

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
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.2.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts( $hook ) {

		global $post;

		if ( 'post.php' == $hook && is_post_type_hierarchical( $post->post_type ) ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Quick_Add_Child::VERSION, true );

			$options = get_option( $this->plugin_slug );
			$params = array(
				'add_new_child' => __( 'Add New Child', $this->plugin_slug ),
				'add_new_sibling' => __( 'Add New Sibling', $this->plugin_slug ),
				'hide_add_new' => ( isset( $options['hide_add_new'] ) ) ? $options['hide_add_new'] : 'off',
				);
			$params = apply_filters( 'quick_add_child_js_params', $params );

			wp_localize_script( $this->plugin_slug . '-admin-script', 'quick_add_child_js_params', $params );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Quick Add Child Settings', $this->plugin_slug ),
			__( 'Quick Add Child', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {

		include_once( 'views/admin.php' );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings' ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Display admin notice when activating the plugin.
	 *
	 * @since 0.6.0
	 */
	public function admin_notice() {

		$screen = get_current_screen();

		if ( isset( $_GET['parent_id'] ) && ! empty( $_GET['parent_id'] ) && 'post' == $screen->base ) {
			$post_parent = get_post( $_GET['parent_id'] );

			$post_edit_url = sprintf( '<a href="%s" target="_blank">%s</a>', admin_url( 'post.php?post=' . $post_parent->ID . '&action=edit' ), esc_html( $post_parent->post_title ) );
			$html  = '<div class="update-nag">';
				$html .= sprintf( __( 'You are about to create a child post of %s.', $this->plugin_slug ), $post_edit_url );
			$html .= '</div><!-- /.update-nag -->';

			echo $html;
		}

	}

	/**
	 * Set default parent by the `parent_id` URL Parameter
	 *
	 * @since    0.2.0
	 */
	public function page_attributes_dropdown_pages_args( $dropdown_args, $post ) {

		if ( isset( $_GET['parent_id'] ) && ! empty( $_GET['parent_id'] ) ) {
			$dropdown_args['selected'] = $_GET['parent_id'];
		}

		return $dropdown_args;

	}

}
