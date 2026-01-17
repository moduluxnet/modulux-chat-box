<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove options.
delete_option( 'mlx_chat_box_options' );

// We intentionally DO NOT delete CPT posts by default (WordPress.org friendly).
// If you ever want a "hard delete" option later, add a setting and delete only when user opts in.
