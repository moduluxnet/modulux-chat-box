<?php
/**
 * Plugin Name: Modulux Chat Box
 * Plugin URI: https://modulux.net/modulux-chat-box/
 * Description: Floating Q&A chat box with optional WhatsApp (or custom) contact link. Supports multilingual setups (Polylang/WPML) via standard WordPress i18n and translatable CPT content.
 * Version: 1.0.0
 * Author: Modulux
 * Author URI: https://modulux.net
 * Text Domain: modulux-chat-box
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MLX_CHAT_BOX_VERSION', '1.0.0' );
define( 'MLX_CHAT_BOX_FILE', __FILE__ );
define( 'MLX_CHAT_BOX_DIR', plugin_dir_path( __FILE__ ) );
define( 'MLX_CHAT_BOX_URL', plugin_dir_url( __FILE__ ) );

require_once MLX_CHAT_BOX_DIR . 'includes/class-mlx-chat-box.php';

register_activation_hook( __FILE__, array( 'MLX_Chat_Box', 'activate' ) );

function mlx_chat_box_boot() {
	MLX_Chat_Box::instance();
}
add_action( 'plugins_loaded', 'mlx_chat_box_boot' );
