<?php
/**
 * Plugin Name:       WP Front Login
 * Plugin URI:        https://github.com/otavianopires/wp-front-login
 * Description:       Customize you login and profile forms.
 * Version:           0.2
 * Author:            Otaviano Pires Amancio
 * Author URI:        https://github.com/otavianopires
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpfl
 * Domain Path:       /lang
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WPFL_VERSION', '0.2' );
define( 'WPFL_PATH', plugin_dir_path(__FILE__) );
define( 'WPFL_URL', plugin_dir_url(__FILE__) );
define( 'WPFL_TEXT_DOMAIN', 'wpfl' );
 
require_once WPFL_PATH . 'includes/core.php';