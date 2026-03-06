<?php
/**
 * Blue Sage — Theme Entry Point
 *
 * @package BlueSage
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BLUE_SAGE_VERSION', '1.0.0' );
define( 'BLUE_SAGE_DIR', get_template_directory() );
define( 'BLUE_SAGE_URI', get_template_directory_uri() );

require_once BLUE_SAGE_DIR . '/inc/setup.php';
require_once BLUE_SAGE_DIR . '/inc/enqueue.php';
require_once BLUE_SAGE_DIR . '/inc/helpers.php';
require_once BLUE_SAGE_DIR . '/inc/custom-blocks.php';
require_once BLUE_SAGE_DIR . '/inc/block-patterns.php';
require_once BLUE_SAGE_DIR . '/inc/performance.php';
require_once BLUE_SAGE_DIR . '/inc/seo.php';
