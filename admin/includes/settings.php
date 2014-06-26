<?php

class Quick_Add_Child_Settings {

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * Call $plugin_slug from public plugin class later.
	 *
	 * @since    0.3.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = null;

	/**
	 * Instance of this class.
	 *
	 * @since    0.3.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.3.0
	 */
	private function __construct() {

		$plugin = Quick_Add_Child::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		add_action( 'admin_init', array( $this, 'admin_init' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.3.0
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
	 * Registering the Sections, Fields, and Settings.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function admin_init() {

		if ( false == get_option( $this->plugin_slug ) ) {
			add_option( $this->plugin_slug, $this->default_settings() );
		} // end if

		add_settings_section(
			'general',
			__( 'General', $this->plugin_slug ),
			'',
			$this->plugin_slug
		);

		add_settings_field(
			'hide_add_new',
			__( 'Hide Add New', $this->plugin_slug ),
			array( $this, 'hide_add_new_callback' ),
			$this->plugin_slug,
			'general'
		);

		register_setting(
			$this->plugin_slug,
			$this->plugin_slug
		);

	} // end admin_init

	/**
	 * Provides default values for the plugin settings.
	 *
	 * @return  array<string> Default settings
	 */
	public function default_settings() {

		$defaults = array(
			'hide_add_new' => 'off',
		);

		return apply_filters( 'default_settings', $defaults );

	} // end default_settings

	public function hide_add_new_callback() {

		$options = get_option( $this->plugin_slug );
		$option  = isset( $options['hide_add_new'] ) ? $options['hide_add_new'] : '';

		$html  = '<input type="checkbox" id="hide_add_new" name="' . $this->plugin_slug . '[hide_add_new]" value="on"' . checked( 'on', $option, false ) . '/>';
		$html .= '<label for="hide_add_new">' . __( 'Hide <code>Add New</code> link on hierarchical post editing screen.', $this->plugin_slug ) . '</label>';

		echo $html;

	} // end hide_add_new_callback
}

Quick_Add_Child_Settings::get_instance();
?>