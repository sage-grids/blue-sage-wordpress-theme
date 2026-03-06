<?php
/**
 * Blue Sage — Theme Admin Page
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://github.com/sage-grids/blue-sage-wordpress-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the theme admin page.
 */
function blue_sage_admin_menu(): void {
	add_theme_page(
		__( 'Blue Sage Theme', 'blue-sage' ),
		__( 'Blue Sage Theme', 'blue-sage' ),
		'edit_theme_options',
		'blue-sage-theme',
		'blue_sage_admin_page_render'
	);
}
add_action( 'admin_menu', 'blue_sage_admin_menu' );

/**
 * Render the theme admin page.
 */
function blue_sage_admin_page_render(): void {
	?>
	<div class="wrap blue-sage-admin">
		<h1><?php _e( 'Blue Sage Theme', 'blue-sage' ); ?></h1>
		<p><strong><?php _e( 'AI-Powered Systems. Built with Expertise.', 'blue-sage' ); ?></strong></p>

		<div class="card">
			<h2><?php _e( 'About SAGE GRIDS', 'blue-sage' ); ?></h2>
			<p><?php _e( 'Blue Sage is an elegant WordPress theme developed by SAGE GRIDS LTD. We build AI-powered systems with expertise and precision.', 'blue-sage' ); ?></p>
			<p>
				<a href="https://www.sagegrids.com" class="button button-primary" target="_blank"><?php _e( 'Visit SAGE GRIDS', 'blue-sage' ); ?></a>
				<a href="https://www.iserter.com" class="button" target="_blank"><?php _e( 'Author Website', 'blue-sage' ); ?></a>
			</p>
		</div>

		<div class="card">
			<h2><?php _e( 'Social & Support', 'blue-sage' ); ?></h2>
			<p><?php _e( 'Connect with us or get support for your AI-powered systems.', 'blue-sage' ); ?></p>
			<ul>
				<li><strong>LinkedIn:</strong> <a href="https://www.linkedin.com/company/sagegrids/" target="_blank">SAGE GRIDS</a> | <a href="https://www.linkedin.com/in/iserter" target="_blank">Ilyas Serter</a></li>
				<li><strong>X / Twitter:</strong> <a href="https://x.com/sagegrids" target="_blank">@sagegrids</a> | <a href="https://x.com/iSerter" target="_blank">@iSerter</a></li>
				<li><strong>Business Website:</strong> <a href="https://www.sagegrids.com" target="_blank">www.sagegrids.com</a></li>
				<li><strong>E-mail:</strong> <a href="mailto:hello@sagegrids.com">hello@sagegrids.com</a></li>
			</ul>
		</div>
	</div>

	<style>
		.blue-sage-admin .card {
			max-width: 600px;
			margin-top: 20px;
			padding: 20px;
		}
		.blue-sage-admin h2 {
			margin-top: 0;
		}
		.blue-sage-admin ul {
			margin-top: 10px;
		}
		.blue-sage-admin li {
			margin-bottom: 8px;
		}
	</style>
	<?php
}
