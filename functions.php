<?php
/**
 * Blue Sage — Theme Entry Point
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://www.iserter.com
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BLUE_SAGE_VERSION', '1.0.1' );
define( 'BLUE_SAGE_DIR', get_template_directory() );
define( 'BLUE_SAGE_URI', get_template_directory_uri() );

require_once BLUE_SAGE_DIR . '/inc/setup.php';
require_once BLUE_SAGE_DIR . '/inc/enqueue.php';
require_once BLUE_SAGE_DIR . '/inc/helpers.php';
require_once BLUE_SAGE_DIR . '/inc/custom-blocks.php';
require_once BLUE_SAGE_DIR . '/inc/admin.php';
require_once BLUE_SAGE_DIR . '/inc/block-patterns.php';
require_once BLUE_SAGE_DIR . '/inc/performance.php';
require_once BLUE_SAGE_DIR . '/inc/seo.php';
