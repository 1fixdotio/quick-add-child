<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Quick_Add_Child
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://1fix.io/quick-add-child
 * @copyright 2014 1Fix.io
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form method="post" action="options.php">
		<?php
			$plugin = Quick_Add_Child::get_instance();

			settings_fields( $plugin->get_plugin_slug() );
			do_settings_sections( $plugin->get_plugin_slug() );

			submit_button();

		?>
	</form>

</div>
